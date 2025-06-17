<?php

namespace App\Services\Notifications;

use App\Models\GdprNotificationPayload;
use App\Models\User;
use App\Notifications\Gdpr\AccountDeletionProcessedNotification;
use App\Notifications\Gdpr\AccountDeletionRequestedNotification;
use App\Notifications\Gdpr\BreachReportReceivedNotification;
use App\Notifications\Gdpr\ConsentUpdatedNotification;
use App\Notifications\Gdpr\DataExportedNotification;
use App\Notifications\Gdpr\ProcessingRestrictedNotification;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Throwable;

/**
 * Service to handle the business logic for creating and sending all GDPR notifications.
 * Correctly integrated with the Ultra Ecosystem (ULM & UEM Interface).
 *
 * --- OS1 DOCUMENTATION ---
 * @oracode-dimension governance
 * @oracode-value-flow Orchestrates the secure and auditable creation and dispatch of critical compliance alerts to users.
 * @oracode-community-impact Ensures the platform can reliably fulfill its informational duties towards users, strengthening legal compliance and user trust.
 * @oracode-transparency-level High - All operations are logged via ULM, and errors are handled by UEM.
 * @oracode-sustainability-factor High - Decoupled logic, transactional safety, and robust error handling via the Ultra Ecosystem.
 * @oracode-intent To provide a single, reliable entry point for the system to send all one-way informational GDPR alerts to users.
 * @os1-compliance Full
 */
class GdprNotificationService
{
    /**
     * Mappa centralizzata dei tipi di notifica. Unica fonte di verità.
     * @var array<string, string>
     */
    private const NOTIFICATION_MAP = [
        'consent_updated' => ConsentUpdatedNotification::class,
        'data_exported' => DataExportedNotification::class,
        'processing_restricted' => ProcessingRestrictedNotification::class,
        'account_deletion_requested' => AccountDeletionRequestedNotification::class,
        'account_deletion_processed' => AccountDeletionProcessedNotification::class,
        'breach_report_received' => BreachReportReceivedNotification::class,
    ];

    public function __construct(
        private UltraLogManager $ulm,
        private ErrorManagerInterface $uem
    ) {
    }

    /**
     * Central private method to create the payload and send the notification.
     */
    private function send(User $user, string $notificationClass, string $title, string $content, ?array $specificData = null, ?string $action_url = null): void
    {
        $this->ulm->info('Attempting to send notification.', [
            'user_id' => $user->id,
            'notification_class' => $notificationClass,
        ]);

        try {
            DB::transaction(function () use ($user, $notificationClass, $title, $content, $specificData, $action_url) {
                $payload = GdprNotificationPayload::create([
                    'title'      => $title,
                    'content'    => $content,
                    'action_url' => $action_url,
                    'data'       => $specificData,
                ]);

                $user->notify(new $notificationClass($payload));
            });

            $this->ulm->info('Notification dispatched successfully.', [
                'user_id' => $user->id,
                'notification_class' => $notificationClass
            ]);
        } catch (Throwable $e) {
            $errorContext = [
                'user_id'              => $user->id,
                'notification_class'   => $notificationClass,
                'title'                => $title,
                'content'              => $content,
                'specific_data'        => $specificData,
                'action_url'           => $action_url,
            ];

            $this->uem->handle(
                'GDPR_NOTIFICATION_SEND_FAILED',
                $errorContext,
                $e
            );
        }
    }

    // --- METODI PUBBLICI PER LA LOGICA DI BUSINESS ---

    public function sendConsentUpdated(User $user): void
    {
        $this->send(
            $user,
            ConsentUpdatedNotification::class,
            __('notification.gdpr.consent_updated.title'),
            __('notification.gdpr.consent_updated.content')
        );
    }

    public function sendDataExported(User $user, string $downloadUrl): void
    {
        $this->send(
            $user,
            DataExportedNotification::class,
            __('notification.gdpr.data_exported.title'),
            __('notification.gdpr.data_exported.content'),
            ['download_link' => $downloadUrl],
            $downloadUrl
        );
    }

    public function sendProcessingRestricted(User $user, string $restrictionType): void
    {
        $this->send(
            $user,
            ProcessingRestrictedNotification::class,
            __('notification.gdpr.processing_restricted.title'),
            __('notification.gdpr.processing_restricted.content', ['type' => $restrictionType]),
            ['restriction_type' => $restrictionType]
        );
    }

    public function sendAccountDeletionRequested(User $user, int $daysToProcess): void
    {
        $this->send(
            $user,
            AccountDeletionRequestedNotification::class,
            __('notification.gdpr.account_deletion_requested.title'),
            __('notification.gdpr.account_deletion_requested.content', ['days' => $daysToProcess]),
            ['processing_days' => $daysToProcess]
        );
    }

    public function sendAccountDeletionProcessed(User $user): void
    {
        $this->send(
            $user,
            AccountDeletionProcessedNotification::class,
            __('notification.gdpr.account_deletion_processed.title'),
            __('notification.gdpr.account_deletion_processed.content')
        );
    }

    public function sendBreachReportReceived(User $user, string $reportId): void
    {
        $this->send(
            $user,
            BreachReportReceivedNotification::class,
            __('notification.gdpr.breach_report_received.title'),
            __('notification.gdpr.breach_report_received.content', ['report_id' => $reportId]),
            ['report_id' => $reportId]
        );
    }

    // --- METODI DI SUPPORTO PER IL COMANDO DI TEST ---

    /**
     * Returns an array of available GDPR notification types for testing.
     *
     * @return array<int, string>
     */
    public function getAvailableTestTypes(): array
    {
        return array_keys(self::NOTIFICATION_MAP);
    }

    /**
     * Sends a test notification of a specific type to a user with mock data.
     * Centralizes test logic away from the Artisan command.
     *
     * @param User $user
     * @param string $type The notification type, must be one from getAvailableTestTypes().
     * @return void
     * @throws InvalidArgumentException if the type is invalid.
     */
    public function sendTestNotification(User $user, string $type): void
    {
        if (!array_key_exists($type, self::NOTIFICATION_MAP)) {
            throw new InvalidArgumentException("Invalid test notification type provided: {$type}");
        }

        // Centralizes the test data and logic for each notification type
        match ($type) {
            'consent_updated' => $this->sendConsentUpdated($user),
            'data_exported' => $this->sendDataExported($user, url('/storage/exports/fake_export.zip')),
            'processing_restricted' => $this->sendProcessingRestricted($user, 'Marketing Emails'),
            'account_deletion_requested' => $this->sendAccountDeletionRequested($user, 14),
            'account_deletion_processed' => $this->sendAccountDeletionProcessed($user),
            'breach_report_received' => $this->sendBreachReportReceived($user, 'BR' . rand(1000, 9999)),
        };
    }
}
