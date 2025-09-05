<?php

declare(strict_types=1);

namespace App\Http\Controllers\Notifications\Invitations;

use App\DataTransferObjects\Payloads\Invitations\{
    InvitationAcceptRequest,
    InvitationResponse
};

use App\Enums\{
    NotificationHandlerType,
    NotificationStatus
};
use App\Http\Controllers\Controller;
use App\Models\CustomDatabaseNotification;
use App\Models\NotificationPayloadInvitation;
use App\Services\Notifications\InvitationService;


use Exception;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Auth, Log};
use Illuminate\View\View;
use Ultra\EgiModule\Contracts\UserRoleServiceInterface;

class NotificationInvitationResponseController extends Controller
{
    
    /** @var UserRoleServiceInterface Service for managing user roles */
    private UserRoleServiceInterface $roleService;

    
    public function __construct(
        private readonly InvitationService $responseInvitationService,
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
                Log::channel('florenceegi')->error('Azione non valida', [
                    'action' => $action
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

            Log::channel('florenceegi')->error('Errore elaborazione notifica', [
                'error' => $e->getMessage(),
                'payloadId' => $payloadId,
                'action' => $action
            ]);

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
        Log::channel('florenceegi')->info('Notifica di accettazione invitations', [
            'notificationId' => $notificationId,
        ]);

        $invitationPayload = $this->findNotification($payloadId);

        /**
         * Accetta l'invito
         * @var NotificationPayloadInvitation
         * @exception Exception
         */
        $this->responseInvitationService->acceptInvitation($invitationPayload, $notificationId);
               

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
        Log::channel('florenceegi')->info('Archiviazione notifica', [
            'notificationId' => $notifificationId
        ]);

        try {

            $notification = CustomDatabaseNotification::find($notifificationId);

            if (!$notification) {
                Log::channel('florenceegi')->error('Errore nella ricerca della notifica', [
                    'notificationId' => $notifificationId
                ]);
                throw new Exception('Errore nella ricerca della notifica.');
            }

            $notification->update([
                'read_at' => now()
            ]);

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

            Log::channel('florenceegi')->error('Errore archiviazione notifica', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);

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

        Log::channel('florenceegi')->info('Notifica completata', [
            'notificationId' => $notificationId,
            'payloadId' => $payloadId
        ]);

        return response()->json(
            InvitationResponse::success(

                NotificationStatus::DONE->value
            )->toArray()
        );
    }



}