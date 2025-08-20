<?php

namespace App\Services\Notifications;

use App\DataTransferObjects\Notifications\Invitations\InvitationNotificationData;
use App\DataTransferObjects\Notifications\NotificationData;
use App\DataTransferObjects\Payloads\Invitations\{
    InvitationAcceptRequest,
    InvitationResponse
};
use App\Enums\InvitationStatus;
use App\Enums\NotificationHandlerType;
use App\Enums\NotificationStatus;
use App\Models\Collection;
use App\Models\CollectionUser;
use App\Models\CustomDatabaseNotification;
use App\Models\NotificationPayloadInvitation;
use App\Models\NotificationPayloadWallet;
use App\Models\User;
use App\Services\Notifications\InvitationNotificationHandler;
use App\Services\Notifications\NotificationHandlerFactory;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class InvitationService
 * @package App\Services
 */

class InvitationService {
    public function createInvitation(Collection $collection, string $email, string $role): NotificationPayloadInvitation {
        return DB::transaction(function () use ($collection, $email, $role) {

            try {

                Log::channel('florenceegi')->info('InvitationService:creazione invito', [
                    'collection' => $collection,
                    'email' => $email,
                    'role' => $role
                ]);

                // User destinatario della risposta, si verifica se esiste
                $user = User::where('email', '=', $email)->first();

                Log::channel('florenceegi')->info('InvitationService:utente destinatario', [
                    'user' => $user
                ]);

                // Status dell'invito
                $status = NotificationStatus::PENDING->value;

                $invitationData = new InvitationNotificationData(
                    collection_id: $collection->id,
                    proposerId: Auth::id(),
                    receiverId: $user->id,
                    email: $email,
                    role: $role,
                    status: $status
                );

                Log::channel('florenceegi')->info(
                    'createInvitation: dati di payload',
                    [
                        'invitation' => $invitationData
                    ]
                );

                /**
                 * @var array $NotificationPayloadInvitation
                 */
                $invitation = NotificationPayloadInvitation::create($invitationData->toPayloadInArray());

                $notification = new NotificationData(
                    model_type: get_class($invitation),
                    model_id: $invitation->id,
                    view: 'invitations.' . NotificationStatus::REQUEST->value,
                    prev_id: null,
                    sender_id: Auth::id(),
                    message: __('collection.invitation.proposal_collaboration'),
                    reason: '',
                    sender_name: Auth::user()->name . ' ' . Auth::user()->last_name,
                    sender_email: Auth::user()->email,
                    collection_name: $collection->collection_name,
                    status: NotificationStatus::PENDING->value
                );

                // Gestione notifica
                $handler = NotificationHandlerFactory::getHandler(NotificationHandlerType::INVITATION);
                $result = $handler->handle('send_invitation', $invitation, ['user' => $user, 'notification_data' => $notification]);

                if (!$result['success']) {
                    throw new Exception($result['message']);
                }

                return $invitation;
            } catch (Exception $e) {
                Log::channel('florenceegi')->error('Errore creazione invito', [
                    'error' => $e->getMessage(),
                    'data' => $notification ?? null
                ]);
                throw $e;
            }
        });
    }

    public function acceptInvitation(NotificationPayloadInvitation $notificationPayloadInvitation, $notifificationId): mixed {
        return DB::transaction(function () use ($notificationPayloadInvitation, $notifificationId): void {

            try {

                // L'utente che sta approvando l'invito
                $receiver = Auth::user();

                // Aggiornamento stati
                $notificationPayloadInvitation->update(['status' => NotificationStatus::ACCEPTED->value]);

                /**
                 * Aggiorna lo stato della notifica di CustomDatabaseNotification
                 * @var NotificationPayloadInvitation
                 * @var CustomDatabaseNotification
                 * @exception Exception
                 */
                $this->updateStatusNotification($notifificationId);

                Log::channel('florenceegi')->info('acceptInvitation', [
                    'notification' => $notificationPayloadInvitation->notifications
                ]);

                // Aggiornamento lo stato della otifica di invito
                $invitationData = new InvitationNotificationData(
                    collection_id: $notificationPayloadInvitation->collection_id,
                    proposerId: null,
                    receiverId: $receiver->id,
                    email: '',
                    role: $notificationPayloadInvitation->role,
                    status: $notificationPayloadInvitation->status,
                    metadata: $notificationPayloadInvitation->metadata
                );

                Log::channel('florenceegi')->info('acceptInvitation', [
                    'invitationData' => $invitationData->toCollectionUser()
                ]);

                // die();

                // // Aggiungi l'utente alla collezione
                $collectionUser = CollectionUser::create($invitationData->toCollectionUser());

                if (!$collectionUser) {
                    throw new Exception('Errore nella creazione del record CollectionUser.');
                }

                Log::channel('florenceegi')->info('Invito creato', $invitationData->toCollectionUser());
            } catch (Exception $e) {
                Log::channel('florenceegi')->error('Errore creazione invito', [
                    'error' => $e->getMessage(),
                    // 'data' => $notification->toArray()
                ]);
                throw $e;
            }
        });
    }

    public function updateStatusNotification($notifificationId): void {
        $notification = CustomDatabaseNotification::find($notifificationId);

        if (!$notification) {
            Log::channel('florenceegi')->error('Errore nella ricerca della notifica', [
                'notificationId' => $notifificationId
            ]);
            throw new Exception('Errore nella ricerca della notifica.');
        }

        $notification->update(['outcome' => NotificationStatus::ACCEPTED->value]);
    }
}
