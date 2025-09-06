<?php

declare(strict_types=1);

namespace App\Http\Controllers\Notifications\Invitations;

use App\DataTransferObjects\Payloads\Invitations\{
    InvitationAcceptRequest,
    InvitationResponse
};

use App\Enums\{
    Gdpr\GdprActivityCategory,
    NotificationHandlerType,
    NotificationStatus
};
use App\Http\Controllers\Controller;
use App\Models\CustomDatabaseNotification;
use App\Models\NotificationPayloadInvitation;
use App\Services\Gdpr\AuditLogService;
use App\Services\Notifications\InvitationService;
use App\Services\UltraErrorManager\Contracts\ErrorManagerInterface;
use App\Services\UltraLogManager\UltraLogManager;


use Exception;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Ultra\EgiModule\Contracts\UserRoleServiceInterface;

class NotificationInvitationResponseController extends Controller
{
    
    /** @var UserRoleServiceInterface Service for managing user roles */
    private UserRoleServiceInterface $roleService;

    
    public function __construct(
        private readonly InvitationService $responseInvitationService,
        private readonly AuditLogService $auditLogService,
        private readonly ErrorManagerInterface $errorManager,
        private readonly UltraLogManager $ultraLogManager,
        UserRoleServiceInterface $roleService
    ) {
        $this->roleService = $roleService;
    }

    /**
     * Gestisce la risposta a una notifica invitation (accettazione o rifiuto)
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function response(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'action' => 'required|string',
            'payloadId' => 'nullable',
            'notificationId' => 'nullable' // Campo opzionale
        ]);

        $action = $validated['action'];
        $payloadId = $validated['payloadId'] ?? null;
        $notificationId = $validated['notificationId'] ?? null;


        if (!in_array($action, [
            NotificationStatus::ACCEPTED->value,
            NotificationStatus::UPDATE->value,
            NotificationStatus::REJECTED->value,
            NotificationStatus::ARCHIVED->value,
            NotificationStatus::DONE->value])) {
                
                // GDPR: Log dell'errore di validazione
                $this->auditLogService->logUserAction(
                    user: Auth::user(),
                    action: 'invitation_response_invalid_action',
                    context: [
                        'action' => $action,
                        'payload_id' => $payloadId,
                        'notification_id' => $notificationId,
                        'error_type' => 'invalid_action',
                    ],
                    category: GdprActivityCategory::NOTIFICATION_MANAGEMENT
                );

                $this->errorManager->handle('INVITATION_RESPONSE_INVALID_ACTION', [
                    'user_id' => Auth::id(),
                    'action' => $action,
                    'payload_id' => $payloadId,
                    'notification_id' => $notificationId
                ]);
                return response()->json(
                InvitationResponse::error(
                        __('collection.invitation.validation.invalid_action')
                )->toArray(),
                400
            );
        }

        try {

            return match($action) {
                NotificationStatus::ACCEPTED->value => $this->handleAccept($payloadId, $notificationId),
                // NotificationStatus::REJECTED->value => $this->handleReject($notification, $request),
                NotificationStatus::ARCHIVED->value => $this->handleArchive($notificationId),
                // NotificationStatus::DONE->value => $this->handleDone($notification),
                default => $this->handleDone($payloadId, $notificationId)
            };


        } catch (Exception $e) {
            // GDPR: Log dell'errore di sistema
            $this->auditLogService->logUserAction(
                user: Auth::user(),
                action: 'invitation_response_system_error',
                context: [
                    'payload_id' => $payloadId,
                    'notification_id' => $notificationId,
                    'action' => $action,
                    'error_type' => 'Exception',
                    'error_message' => $e->getMessage(),
                ],
                category: GdprActivityCategory::NOTIFICATION_MANAGEMENT
            );

            $this->errorManager->handle('INVITATION_RESPONSE_SYSTEM_ERROR', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'payloadId' => $payloadId,
                'action' => $action
            ], $e);

            return response()->json(
                InvitationResponse::error($e->getMessage())->toArray(),
                500
            );
        }
    }

    /**
     * Trova una notifica per l'utente corrente
     *
     * @throws Exception Se la notifica non viene trovata
     */
    private function findNotification($payloadId): NotificationPayloadInvitation
    {

        /**
         * @var NotificationPayloadInvitation
         */
        $invitationPayload = NotificationPayloadInvitation::find($payloadId);

        if (!$invitationPayload) {
            throw new Exception(__('notification.not_found'));
        }

        return $invitationPayload;
    }


    private function handleAccept($payloadId, $notificationId): JsonResponse
    {
        $this->ultraLogManager->info('Processing invitation acceptance', [
            'user_id' => Auth::id(),
            'notificationId' => $notificationId,
            'payloadId' => $payloadId
        ]);

        $invitationPayload = $this->findNotification($payloadId);

        /**
         * Accetta l'invito
         * @var NotificationPayloadInvitation
         * @exception Exception
         */
        $this->responseInvitationService->acceptInvitation($invitationPayload, $notificationId);

        // GDPR: Log dell'accettazione invito
        $this->auditLogService->logUserAction(
            user: Auth::user(),
            action: 'invitation_accepted',
            context: [
                'payload_id' => $payloadId,
                'notification_id' => $notificationId,
                'invitation_type' => $invitationPayload->type ?? 'unknown',
            ],
            category: GdprActivityCategory::NOTIFICATION_MANAGEMENT
        );
               

        return response()->json(
            InvitationResponse::success(
                NotificationStatus::ACCEPTED->value
            )->toArray()
    );
    }


    private function handleReject(NotificationPayloadInvitation $notification): JsonResponse
    {
        return response()->json(
            InvitationResponse::success(

                NotificationStatus::REJECTED->value
            )->toArray()
        );
    }

    private function handleArchive($notifificationId): JsonResponse
    {
        $this->ultraLogManager->info('Processing notification archive', [
            'user_id' => Auth::id(),
            'notificationId' => $notifificationId
        ]);

        try {

            $notification = CustomDatabaseNotification::find($notifificationId);

            if (!$notification) {
                // GDPR: Log errore notifica non trovata
                $this->auditLogService->logUserAction(
                    user: Auth::user(),
                    action: 'invitation_archive_not_found',
                    context: [
                        'notification_id' => $notifificationId,
                        'error_type' => 'notification_not_found',
                    ],
                    category: GdprActivityCategory::NOTIFICATION_MANAGEMENT
                );

                $this->errorManager->handle('INVITATION_ARCHIVE_NOT_FOUND', [
                    'user_id' => Auth::id(),
                    'notificationId' => $notifificationId
                ]);
                throw new Exception('Errore nella ricerca della notifica.');
            }

            $notification->update([
                'read_at' => now()
            ]);

            // GDPR: Log dell'archiviazione
            $this->auditLogService->logUserAction(
                user: Auth::user(),
                action: 'invitation_archived',
                context: [
                    'notification_id' => $notifificationId,
                    'archived_at' => now()->toDateTimeString(),
                ],
                category: GdprActivityCategory::NOTIFICATION_MANAGEMENT
            );

            if (!$notification) {
                return response()->json(
                    InvitationResponse::error(
                        __('notifications.not_found')
                    )->toArray(),
                    404
                );
            }

            return response()->json(
                InvitationResponse::success(
                    NotificationStatus::ARCHIVED->value
                )->toArray(),
                200
            );

        } catch (Exception $e) {
            // GDPR: Log dell'errore archiviazione
            $this->auditLogService->logUserAction(
                user: Auth::user(),
                action: 'invitation_archive_error',
                context: [
                    'notification_id' => $notifificationId,
                    'error_type' => 'Exception',
                    'error_message' => $e->getMessage(),
                ],
                category: GdprActivityCategory::NOTIFICATION_MANAGEMENT
            );

            $this->errorManager->handle('INVITATION_ARCHIVE_ERROR', [
                'user_id' => Auth::id(),
                'notification_id' => $notifificationId,
                'error' => $e->getMessage()
            ], $e);

            return response()->json(
                InvitationResponse::error(
                        $e->getMessage()
                )->toArray(),
                500
            );
        }
    }

    private function handleDone($payloadId, $notificationId): JsonResponse
    {
        $this->ultraLogManager->info('Processing invitation completion', [
            'user_id' => Auth::id(),
            'notificationId' => $notificationId,
            'payloadId' => $payloadId
        ]);

        // GDPR: Log del completamento invito
        $this->auditLogService->logUserAction(
            user: Auth::user(),
            action: 'invitation_completed',
            context: [
                'payload_id' => $payloadId,
                'notification_id' => $notificationId,
                'completed_at' => now()->toDateTimeString(),
            ],
            category: GdprActivityCategory::NOTIFICATION_MANAGEMENT
        );

        return response()->json(
            InvitationResponse::success(

                NotificationStatus::DONE->value
            )->toArray()
        );
    }



}