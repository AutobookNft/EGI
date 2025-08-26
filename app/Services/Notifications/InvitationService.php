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
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class InvitationService
 * @package App\Services
 */
class InvitationService {

    private ?UltraLogManager $logger;
    private ?ErrorManagerInterface $errorManager;

    public function __construct(
        ?UltraLogManager $logger = null,
        ?ErrorManagerInterface $errorManager = null
    ) {
        $this->logger = $logger ?? app(UltraLogManager::class);
        $this->errorManager = $errorManager ?? app(ErrorManagerInterface::class);
    }

    public function createInvitation(Collection $collection, string $email, string $role): NotificationPayloadInvitation {
        $startTime = microtime(true);
        $operationId = uniqid('inv_create_', true);

        // ULM: Log inizio operazione
        if ($this->logger) {
            $this->logger->info('[INVITATION] Starting invitation creation', [
                'operation_id' => $operationId,
                'collection_id' => $collection->id,
                'collection_name' => $collection->collection_name,
                'proposer_id' => Auth::id(),
                'target_email' => $email,
                'target_role' => $role,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }

        return DB::transaction(function () use ($collection, $email, $role, $startTime, $operationId) {
            try {
                // ULM: Log step - ricerca utente destinatario
                if ($this->logger) {
                    $this->logger->debug('[INVITATION] Looking up target user', [
                        'operation_id' => $operationId,
                        'target_email' => $email
                    ]);
                }

                // User destinatario della risposta, si verifica se esiste
                $user = User::where('email', '=', $email)->first();

                if (!$user) {
                    // UEM: Gestione errore utente non trovato (non bloccante)
                    $this->errorManager->handle('INVITATION_USER_NOT_FOUND', [
                        'operation_id' => $operationId,
                        'collection_id' => $collection->id,
                        'proposer_id' => Auth::id(),
                        'target_email' => $email,
                        'ip_address' => request()->ip()
                    ]);

                    throw new Exception("User with email {$email} not found");
                }

                // ULM: Log utente trovato
                if ($this->logger) {
                    $this->logger->info('[INVITATION] Target user found', [
                        'operation_id' => $operationId,
                        'target_user_id' => $user->id,
                        'target_user_name' => $user->name
                    ]);
                }

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

                // ULM: Log creazione payload
                if ($this->logger) {
                    $this->logger->debug('[INVITATION] Creating invitation payload', [
                        'operation_id' => $operationId,
                        'invitation_data' => $invitationData->toPayloadInArray()
                    ]);
                }

                /**
                 * @var NotificationPayloadInvitation $invitation
                 */
                $invitation = NotificationPayloadInvitation::create($invitationData->toPayloadInArray());

                if (!$invitation) {
                    throw new Exception('Failed to create invitation payload in database');
                }

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

                // ULM: Log invio notifica
                if ($this->logger) {
                    $this->logger->info('[INVITATION] Sending notification', [
                        'operation_id' => $operationId,
                        'invitation_id' => $invitation->id,
                        'target_user_id' => $user->id
                    ]);
                }

                // Gestione notifica
                $handler = NotificationHandlerFactory::getHandler(NotificationHandlerType::INVITATION);
                $result = $handler->handle('send_invitation', $invitation, ['user' => $user, 'notification_data' => $notification]);

                if (!$result['success']) {
                    throw new Exception($result['message']);
                }

                // ULM: Log successo completo
                $executionTime = microtime(true) - $startTime;
                if ($this->logger) {
                    $this->logger->info('[INVITATION] Invitation created successfully', [
                        'operation_id' => $operationId,
                        'invitation_id' => $invitation->id,
                        'collection_id' => $collection->id,
                        'target_user_id' => $user->id,
                        'execution_time_ms' => round($executionTime * 1000, 2),
                        'status' => 'success'
                    ]);
                }

                return $invitation;
            } catch (Exception $e) {
                // UEM: Gestione errore completa con contesto dettagliato
                $executionTime = microtime(true) - $startTime;

                $this->errorManager->handle('INVITATION_CREATION_ERROR', [
                    'operation_id' => $operationId,
                    'collection_id' => $collection->id,
                    'collection_name' => $collection->collection_name,
                    'proposer_id' => Auth::id(),
                    'proposer_name' => Auth::user()->name ?? 'unknown',
                    'target_email' => $email,
                    'target_role' => $role,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'execution_time_ms' => round($executionTime * 1000, 2),
                    'error_message' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'timestamp' => now()->toIso8601String()
                ], $e);

                // Re-throw per mantenere il comportamento esistente
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

                // Controlla se l'utente è già membro della collezione
                $existingMember = CollectionUser::where('collection_id', $invitationData->collection_id)
                    ->where('user_id', $receiver->id)
                    ->first();

                if ($existingMember) {
                    // Utente già membro - returna errore specifico per frontend
                    throw new Exception('ALREADY_MEMBER:' . __('collection.invitation.already_member'));
                }

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
