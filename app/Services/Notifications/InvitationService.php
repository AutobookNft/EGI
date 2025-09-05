<?php

namespace App\Services\Notifications;

use App\DataTransferObjects\Notifications\Invitations\InvitationNotificationData;
use App\DataTransferObjects\Notifications\NotificationData;
use App\DataTransferObjects\Payloads\Invitations\{
    InvitationAcceptRequest,
    InvitationDeclineRequest
    // InvitationResponse --- IGNORE ---
};
use App\Enums\Gdpr\GdprActivityCategory;
use App\Enums\InvitationStatus;
use App\Enums\NotificationHandlerType;
use App\Enums\NotificationStatus;
use App\Models\Collection;
use App\Models\CollectionUser;
use App\Models\CustomDatabaseNotification;
use App\Models\NotificationPayloadInvitation;
use App\Models\NotificationPayloadWallet;
use App\Models\User;
use App\Models\UserActivity;
use App\Services\Notifications\InvitationNotificationHandler;
use App\Services\Notifications\NotificationHandlerFactory;
use App\Services\Gdpr\ConsentService;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ultra\EgiModule\Contracts\UserRoleServiceInterface;

/**
 * Class InvitationService
 * @package App\Services
 */
class InvitationService {

    private ?UltraLogManager $logger;
    private ?ErrorManagerInterface $errorManager;

    /** @var UserRoleServiceInterface Service for managing user roles */
    private UserRoleServiceInterface $roleService;

    /** @var ConsentService Service for GDPR consent management */
    private ConsentService $consentService;

    public function __construct(
        ?UltraLogManager $logger = null,
        ?ErrorManagerInterface $errorManager = null,
        UserRoleServiceInterface $roleService,
        ConsentService $consentService = null
    ) {
        $this->logger = $logger ?? app(UltraLogManager::class);
        $this->errorManager = $errorManager ?? app(ErrorManagerInterface::class);
        $this->roleService = $roleService;
        $this->consentService = $consentService ?? app(ConsentService::class);
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

                // Set default permissions based on role if none provided
                if (empty($permissions)) {
                    $permissions = $this->roleService->getPermissionsFromSpatieRole($role);
                }

                $metadata = [
                    'created_via' => 'invitation_service',
                    'permissions_assigned' => $permissions,
                    'creation_timestamp' => now()->toISOString(),
                    'created_by_service' => static::class,
                ];

                $invitationData = new InvitationNotificationData(
                    collection_id: $collection->id,
                    proposerId: Auth::id(),
                    receiverId: $user->id,
                    email: $email,
                    role: $role,
                    is_owner: false,
                    joined_at: now()->toISOString(),
                    status: $status,
                    metadata: $metadata

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

                // GDPR: Registra elaborazione dati per invio invito
                $this->logInvitationDataProcessing($collection, $user, $invitation, $operationId);

                // USER ACTIVITY: Registra attività di invio invito
                $this->logInvitationSentActivity($collection, $user, $invitation, $operationId);

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
                    is_owner: false,
                    joined_at: '',
                    status: NotificationStatus::ACTIVE->value,  // Lo status sulla tabella collection_users di un invitato deve essere "active" per essere valido
                    metadata: $notificationPayloadInvitation->metadata
                );

                Log::channel('florenceegi')->info('acceptInvitation', [
                    'invitationData' => $invitationData->toCollectionUser()
                ]);

                // die();

                // Controlla se l'utente è già membro della collezione
                $existingMember = CollectionUser::where('collection_id', $invitationData->getCollectionId())
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

                // GDPR: Registra consenso per partecipazione alla collaborazione (solo se primo invito)
                $this->recordCollaborationConsentIfFirst($receiver, $notificationPayloadInvitation);

                // USER ACTIVITY: Registra attività di accettazione invito
                $this->logInvitationAcceptedActivity($receiver, $notificationPayloadInvitation);

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

    public function updateStatusNotification($notificationId, $status = null): void {
        $notification = CustomDatabaseNotification::find($notificationId);

        if (!$notification) {
            Log::channel('florenceegi')->error('Errore nella ricerca della notifica', [
                'notificationId' => $notificationId
            ]);
            throw new Exception('Errore nella ricerca della notifica.');
        }

        $notification->update(['outcome' => $status ?? NotificationStatus::ACCEPTED->value]);
    }

    /**
     * Gestisce il rifiuto di un invito con logging completo
     */
    public function rejectInvitation(NotificationPayloadInvitation $notificationPayloadInvitation, $notificationId = null): void {
        try {
            $receiver = Auth::user();

            // Aggiorna lo stato dell'invito
            $notificationPayloadInvitation->handleRejection();

            // Aggiorna lo stato della notifica se fornito
            if ($notificationId) {
                $this->updateStatusNotification($notificationId, NotificationStatus::REJECTED->value);
            }

            // USER ACTIVITY: Registra attività di rifiuto invito
            $this->logInvitationRejectedActivity($receiver, $notificationPayloadInvitation);

            // GDPR: Log del rifiuto per compliance
            if ($this->logger) {
                $this->logger->info('[GDPR] Invitation rejection logged', [
                    'user_id' => $receiver->id,
                    'invitation_id' => $notificationPayloadInvitation->id,
                    'collection_id' => $notificationPayloadInvitation->collection_id,
                    'legal_basis' => 'user_choice',
                    'action' => 'invitation_declined'
                ]);
            }
        } catch (Exception $e) {
            if ($this->logger) {
                $this->logger->error('[INVITATION] Failed to reject invitation', [
                    'user_id' => Auth::id(),
                    'invitation_id' => $notificationPayloadInvitation->id,
                    'error' => $e->getMessage()
                ]);
            }
            throw $e;
        }
    }

    /**
     * USER ACTIVITY: Registra attività di invio invito
     */
    private function logInvitationSentActivity(Collection $collection, User $targetUser, NotificationPayloadInvitation $invitation, string $operationId): void {
        try {
            UserActivity::create([
                'user_id' => Auth::id(),
                'action' => 'invitation_sent',
                'category' => 'content_creation',
                'context' => [
                    'invitation_id' => $invitation->id,
                    'collection_id' => $collection->id,
                    'collection_name' => $collection->collection_name,
                    'target_user_id' => $targetUser->id,
                    'target_email' => $targetUser->email,
                    'role_offered' => $invitation->role,
                    'operation_id' => $operationId
                ],
                'metadata' => [
                    'action_type' => 'collaboration_invitation',
                    'source' => 'invitation_service',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'session_id' => session()->getId(),
                    'timestamp' => now()->toIso8601String()
                ],
                'privacy_level' => 'standard',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId(),
                'expires_at' => now()->addYears(2) // Retention per audit
            ]);

            if ($this->logger) {
                $this->logger->info('[USER_ACTIVITY] Invitation sent activity logged', [
                    'user_id' => Auth::id(),
                    'operation_id' => $operationId,
                    'invitation_id' => $invitation->id,
                    'target_user_id' => $targetUser->id
                ]);
            }
        } catch (\Exception $e) {
            // Log senza bloccare il flusso principale
            if ($this->logger) {
                $this->logger->error('[USER_ACTIVITY] Failed to log invitation sent activity', [
                    'user_id' => Auth::id(),
                    'operation_id' => $operationId,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * USER ACTIVITY: Registra attività di accettazione invito
     */
    private function logInvitationAcceptedActivity(User $user, NotificationPayloadInvitation $invitation): void {
        try {
            $collection = Collection::find($invitation->collection_id);

            UserActivity::create([
                'user_id' => $user->id,
                'action' => 'invitation_accepted',
                'category' => GdprActivityCategory::CONTENT_CREATION,
                'context' => [
                    'invitation_id' => $invitation->id,
                    'collection_id' => $invitation->collection_id,
                    'collection_name' => $collection?->collection_name ?? 'Unknown',
                    'proposer_id' => $invitation->proposer_id,
                    'role_accepted' => $invitation->role,
                    'collaboration_started' => true
                ],
                'metadata' => [
                    'action_type' => 'collaboration_acceptance',
                    'source' => 'invitation_response',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'session_id' => session()->getId(),
                    'timestamp' => now()->toIso8601String(),
                    'gdpr_basis' => 'consent'
                ],
                'privacy_level' => GdprActivityCategory::CONTENT_CREATION->privacyLevel(),   
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId(),
                'expires_at' => now()->addYears(2) // Retention per audit
            ]);

            if ($this->logger) {
                $this->logger->info('[USER_ACTIVITY] Invitation accepted activity logged', [
                    'user_id' => $user->id,
                    'invitation_id' => $invitation->id,
                    'collection_id' => $invitation->collection_id
                ]);
            }
        } catch (\Exception $e) {
            // Log senza bloccare il flusso principale
            if ($this->logger) {
                $this->logger->error('[USER_ACTIVITY] Failed to log invitation accepted activity', [
                    'user_id' => $user->id,
                    'invitation_id' => $invitation->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * GDPR: Registra elaborazione dati per invio invito
     * Base giuridica: Interesse legittimo (Art. 6(1)(f) GDPR)
     */
    private function logInvitationDataProcessing(Collection $collection, User $targetUser, NotificationPayloadInvitation $invitation, string $operationId): void {
        try {
            if ($this->logger) {
                $this->logger->info('[GDPR] Invitation data processing logged', [
                    'operation_id' => $operationId,
                    'legal_basis' => 'legitimate_interest',
                    'article' => '6(1)(f) GDPR',
                    'processing_purpose' => 'collaboration_invitation',
                    'data_subject_id' => $targetUser->id,
                    'data_subject_email' => $targetUser->email,
                    'data_processed' => ['email', 'collection_metadata', 'invitation_details'],
                    'collection_id' => $collection->id,
                    'invitation_id' => $invitation->id,
                    'proposer_id' => Auth::id(),
                    'retention_period' => '2_years',
                    'automated_processing' => false,
                    'data_sharing' => false,
                    'cross_border_transfer' => false,
                    'profiling' => false,
                    'ip_address' => request()->ip(),
                    'timestamp' => now()->toIso8601String()
                ]);
            }
        } catch (\Exception $e) {
            // Log senza bloccare il flusso principale
            if ($this->logger) {
                $this->logger->error('[GDPR] Failed to log invitation data processing', [
                    'operation_id' => $operationId,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * GDPR: Registra consenso per partecipazione alla collaborazione solo se è il primo
     * Base giuridica: Consenso esplicito (Art. 6(1)(a) GDPR)
     */
    private function recordCollaborationConsentIfFirst(User $user, NotificationPayloadInvitation $invitation): void {
        try {
            // Controlla se l'utente ha già dato il consenso per le collaborazioni
            $hasExistingConsent = $this->consentService->hasConsent($user, 'collaboration_participation');

            if (!$hasExistingConsent) {
                // È il primo invito accettato - registra il consenso
                $this->consentService->grantConsent($user, 'collaboration_participation', [
                    'collection_id' => $invitation->collection_id,
                    'invitation_id' => $invitation->id,
                    'proposer_id' => $invitation->proposer_id,
                    'role' => $invitation->role,
                    'source' => 'first_invitation_acceptance',
                    'legal_basis' => 'consent',
                    'article' => '6(1)(a) GDPR',
                    'processing_purposes' => [
                        'collaboration',
                        'data_sharing_within_collection',
                        'notifications',
                        'activity_tracking'
                    ],
                    'first_collaboration_timestamp' => now()->toIso8601String(),
                    'acceptance_method' => 'invitation_response',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);

                if ($this->logger) {
                    $this->logger->info('[GDPR] First collaboration consent recorded', [
                        'user_id' => $user->id,
                        'collection_id' => $invitation->collection_id,
                        'invitation_id' => $invitation->id,
                        'consent_type' => 'collaboration_participation',
                        'trigger' => 'first_invitation_acceptance'
                    ]);
                }
            } else {
                // Consenso già esistente - log per tracciabilità
                if ($this->logger) {
                    $this->logger->info('[GDPR] Using existing collaboration consent', [
                        'user_id' => $user->id,
                        'collection_id' => $invitation->collection_id,
                        'invitation_id' => $invitation->id,
                        'consent_already_granted' => true
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Log l'errore ma non bloccare l'accettazione dell'invito
            if ($this->logger) {
                $this->logger->error('[GDPR] Failed to handle collaboration consent', [
                    'user_id' => $user->id,
                    'invitation_id' => $invitation->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * GDPR: Registra consenso per partecipazione alla collaborazione
     * Base giuridica: Consenso esplicito (Art. 6(1)(a) GDPR)
     */
    private function recordCollaborationConsent(User $user, NotificationPayloadInvitation $invitation): void {
        try {
            $this->consentService->grantConsent($user, 'collaboration_participation', [
                'collection_id' => $invitation->collection_id,
                'invitation_id' => $invitation->id,
                'proposer_id' => $invitation->proposer_id,
                'role' => $invitation->role,
                'source' => 'invitation_acceptance',
                'legal_basis' => 'consent',
                'article' => '6(1)(a) GDPR',
                'processing_purposes' => [
                    'collaboration',
                    'data_sharing_within_collection',
                    'notifications',
                    'activity_tracking'
                ],
                'acceptance_method' => 'invitation_response',
                'acceptance_timestamp' => now()->toIso8601String(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            if ($this->logger) {
                $this->logger->info('[GDPR] Collaboration consent recorded', [
                    'user_id' => $user->id,
                    'collection_id' => $invitation->collection_id,
                    'invitation_id' => $invitation->id,
                    'consent_type' => 'collaboration_participation',
                    'legal_basis' => 'consent'
                ]);
            }
        } catch (\Exception $e) {
            // Log l'errore ma non bloccare l'accettazione dell'invito
            if ($this->logger) {
                $this->logger->error('[GDPR] Failed to record collaboration consent', [
                    'user_id' => $user->id,
                    'invitation_id' => $invitation->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * USER ACTIVITY: Registra attività di rifiuto invito
     */
    private function logInvitationRejectedActivity(User $user, NotificationPayloadInvitation $invitation): void {
        try {
            $collection = Collection::find($invitation->collection_id);

            UserActivity::create([
                'user_id' => $user->id,
                'action' => 'invitation_rejected',
                'category' => 'system_interaction',
                'context' => [
                    'invitation_id' => $invitation->id,
                    'collection_id' => $invitation->collection_id,
                    'collection_name' => $collection?->collection_name ?? 'Unknown',
                    'proposer_id' => $invitation->proposer_id,
                    'role_declined' => $invitation->role,
                    'collaboration_declined' => true
                ],
                'metadata' => [
                    'action_type' => 'collaboration_rejection',
                    'source' => 'invitation_response',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'session_id' => session()->getId(),
                    'timestamp' => now()->toIso8601String(),
                    'gdpr_basis' => 'user_choice'
                ],
                'privacy_level' => 'standard',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId(),
                'expires_at' => now()->addYears(2) // Retention per audit
            ]);

            if ($this->logger) {
                $this->logger->info('[USER_ACTIVITY] Invitation rejected activity logged', [
                    'user_id' => $user->id,
                    'invitation_id' => $invitation->id,
                    'collection_id' => $invitation->collection_id
                ]);
            }
        } catch (\Exception $e) {
            // Log senza bloccare il flusso principale
            if ($this->logger) {
                $this->logger->error('[USER_ACTIVITY] Failed to log invitation rejected activity', [
                    'user_id' => $user->id,
                    'invitation_id' => $invitation->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}