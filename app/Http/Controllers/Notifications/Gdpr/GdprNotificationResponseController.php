<?php

namespace App\Http\Controllers\Notifications\Gdpr;

use App\Enums\NotificationStatus;
use App\Helpers\FegiAuth;
use App\Models\CustomDatabaseNotification;
use App\Models\User;
use App\Models\UserConsentConfirmation;
use App\Notifications\Gdpr\SecurityAlertNotification;
use App\Services\Gdpr\ConsentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use App\Http\Controllers\Controller;

/**
 * @package   App\Http\Controllers\Notifications\Gdpr
 * @author    Padmin D. Curtis (for Fabio Cherici)
 * @version   1.0.0
 * @date      2025-06-12
 * @solution  Handles user responses to interactive GDPR notifications, integrating business logic and security protocols.
 *
 * --- OS1.5 DOCUMENTATION ---
 * @oracode-intent: To provide a secure and robust endpoint for processing user actions (confirm, revoke, disavow) on GDPR notifications, interfacing with the ConsentService and triggering security protocols when necessary.
 * @oracode-security: Implements the "Protocollo Fortino Digitale". Each method performs strict authorization checks to ensure a user can only act on their own notifications. Rate limiting is applied at the route level to prevent abuse.
 * @os1-compliance: Full.
 */
class GdprNotificationResponseController extends Controller
{
    protected $user_id;

    public function __construct(
        protected ConsentService $consentService,
        protected UltraLogManager $logger,
        protected ErrorManagerInterface $errorManager
    ) {}

    /**
     * Confirms that the user acknowledges a consent change.
     *
     * @param CustomDatabaseNotification $notification
     * @return JsonResponse
     */
    public function confirm($notificationId): JsonResponse
    {

        $notification = CustomDatabaseNotification::findOrFail($notificationId);

        $user = FegiAuth::user();

        $this->user_id = $user?->id;

        $this->logger->info('GDPR Consent Confirmation Attempt', [
            'user_id' => $this->user_id,
            'notification_id' => $notification->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Fortino Digitale #1: Authorization Check
        if ($notification->notifiable_id !== Auth::id()) {
            return $this->errorManager->handle('AUTHORIZATION_ERROR', ['details' => 'User cannot confirm a notification that does not belong to them.']);
        }

        try {
            DB::transaction(function () use ($notification) {
                $payload = $notification->model;

                $this->logger->info('Processing GDPR payload', [
                    'payload' => $payload,

                ]);

                // Assicuriamoci che ci sia un user_consent_id da confermare
                $userConsentId = $payload->data['user_consent_id'] ?? null;
                if (!$userConsentId) {
                    $this->logger->warning('GDPR Consent Confirmation Failed', [
                        'user_id' => $this->user_id,
                        'notification_id' => $notification->id,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                    throw new \Exception('Missing user_consent_id in notification payload for confirmation.');
                }

                // Log di audit
                UserConsentConfirmation::create([
                    'user_id' => $this->user_id,
                    'user_consent_id' => $userConsentId,
                    'notification_id' => $notification->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);

                // Aggiornamento stati
                $payload->update(['status' => NotificationStatus::CONFIRMED]);
                $notification->markAsRead();
            });

            return response()->json(['message' => 'Confirmation registered successfully.']);

        } catch (\Throwable $e) {
            return $this->errorManager->handle('GDPR_CONSENT_UPDATE_ERROR', ['notification_id' => $notification->id], $e);
        }
    }

    /**
     * Revokes a previously given consent as a user "re-think".
     *
     * @param CustomDatabaseNotification $notification
     * @return JsonResponse
     */
    public function revoke(CustomDatabaseNotification $notification): JsonResponse
    {
        // Fortino Digitale #1: Authorization Check
        if ($notification->notifiable_id !== Auth::id()) {
            return $this->errorManager->handle('AUTHORIZATION_ERROR');
        }

        try {
            $payload = $notification->model;
            $user = $notification->notifiable;
            $consentType = $payload->type;

            // Logica di business: revoca il consenso
            $this->consentService->withdrawConsent($user, $consentType, 'notification_rethink');

            // Aggiornamento stati
            $payload->update(['status' => NotificationStatus::REVOKED]);
            $notification->markAsRead();

            return response()->json(['message' => 'Consent successfully revoked.']);

        } catch (\Throwable $e) {
            return $this->errorManager->handle('GDPR_CONSENT_UPDATE_ERROR', ['notification_id' => $notification->id], $e);
        }
    }

    /**
     * Disavows a consent change and triggers the "Code Red" security protocol.
     *
     * @param CustomDatabaseNotification $notification
     * @return JsonResponse
     */
    public function disavow(CustomDatabaseNotification $notification): JsonResponse
    {
        // Fortino Digitale #1: Authorization Check
        if ($notification->notifiable_id !== Auth::id()) {
            return $this->errorManager->handle('AUTHORIZATION_ERROR');
        }

        try {
            DB::transaction(function () use ($notification) {
                $payload = $notification->model;
                $user = $notification->notifiable;
                $consentType = $payload->type;

                // 1. Contenimento: Revoca immediata del consenso
                $this->consentService->withdrawConsent($user, $consentType, 'disavowal');

                // 2. Log di Sicurezza Critico
                $this->logger->critical('SECURITY ALERT: User disavowed a consent change.', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'disavowed_consent_type' => $consentType,
                    'ip_address' => request()->ip(),
                ]);

                // 3. Allerta Amministratori "Codice Rosso"
                Notification::route('security_alert', config('notifications.channels.security_alert.to'))
                    ->notify(new SecurityAlertNotification($user, $payload));

                // 4. Allerta Utente (opzionale, ma consigliato)
                // $user->notify(new UserSecurityWarningNotification());

                // 5. Invalida altre sessioni (misura di sicurezza aggiuntiva)
                // Auth::logoutOtherDevices(request('password')); // Da valutare attentamente la UX

                // 6. Aggiorna stati
                $payload->update(['status' => NotificationStatus::DISAVOWED]);
                $notification->markAsRead();
            });

            return response()->json(['message' => 'Security protocol initiated. Please check your email for further instructions.']);

        } catch (\Throwable $e) {
            return $this->errorManager->handle('GDPR_CONSENT_UPDATE_ERROR', ['notification_id' => $notification->id, 'context' => 'disavowal_protocol'], $e);
        }
    }
}