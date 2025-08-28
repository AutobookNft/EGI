<?php

namespace App\Livewire\Notifications\Invitations;

use App\DataTransferObjects\Payloads\Invitations\InvitationAcceptRequest;
use App\Enums\NotificationStatus;
use App\Models\CollectionUser;
use App\Models\NotificationPayloadInvitation;
use App\Services\Notifications\InvitationService;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Exception;

class Request extends Component {

    public mixed $notification;

    public function mount(mixed $notification) {
        $this->notification = $notification;
    }

    /**
     * Gestisce la risposta a una notifica di invito (accettazione o rifiuto).
     *
     * Questo metodo:
     * - Recupera la notifica associata all'invito e il payload corrispondente.
     * - Esegue la logica di accettazione o rifiuto in base all'opzione scelta dall'utente.
     * - Aggiorna lo stato della notifica e invia una notifica di aggiornamento al proponente.
     * - Utilizza una transazione per garantire l'integrità dei dati.
     *
     * @param string $option L'opzione scelta dall'utente: 'accept' o 'reject'.
     *
     * @throws \Exception Se si verifica un errore durante la transazione o l'elaborazione della risposta.
     *
     * @return void
     *
     * Dettagli tecnici:
     * - La transazione garantisce che tutte le modifiche al database siano atomiche.
     * - In caso di errore, tutte le operazioni vengono annullate e l'eccezione è loggata.
     * - Il metodo utilizza la factory NotificationHandlerFactory per gestire l'invio della notifica.
     */
    #[On('response')]
    public function response($option) {

        try {
            // Inizio della transazione
            DB::beginTransaction();


            // Si recupera l'oggetto NotificationPayloadInvitation creato al momento dell'invio della proposta
            $notificationPayloadInvitation = NotificationPayloadInvitation::find($this->notification->model_id);

            /**
             * @var NotificationPayloadInvitation $notificationPayloadInvitation
             */
            $invitationAcceptRequest = InvitationAcceptRequest::fromNotification($notificationPayloadInvitation);

            $invitationService = new InvitationService;

            // Accetta o rifiuta l'invito
            if ($option === 'accepted') {
                // CONTROLLO PREVENTIVO: Verifica se l'utente invitato è già membro della collezione
                // L'email dell'invitato è nel payload dell'invito
                $invitedUser = \App\Models\User::where('email', $notificationPayloadInvitation->email)->first();

                if ($invitedUser) {
                    $existingMember = CollectionUser::where('collection_id', $notificationPayloadInvitation->collection_id)
                        ->where('user_id', $invitedUser->id)
                        ->first();

                    if ($existingMember) {
                        // Utente invitato già membro - annulla transazione e invia errore al frontend
                        DB::rollBack();

                        Log::channel('florenceegi')->warning('Tentativo di accettare invito per utente già membro della collezione', [
                            'invited_email' => $notificationPayloadInvitation->email,
                            'invited_user_id' => $invitedUser->id,
                            'collection_id' => $notificationPayloadInvitation->collection_id,
                            'existing_role' => $existingMember->role,
                            'acceptor_user_id' => Auth::id()
                        ]);

                        // Dispatcha evento di errore specifico al frontend
                        $this->dispatch(
                            'notification-response',
                            option: $option,
                            success: false,
                            error: 'ALREADY_MEMBER',
                            message: __('collection.invitation.already_member')
                        );

                        return;
                    }
                }

                $invitationService->acceptInvitation($notificationPayloadInvitation, $this->notification->id);
            } else {
                $this->reject($notificationPayloadInvitation);
            }

            // NOTA: notifica per accepted soppressa!
            // $handler = NotificationHandlerFactory::getHandler(InvitationNotificationHandler::class);
            // $handler->handle($message_to, $this->notification);

            // Log::channel('florenceegi')->info('InvitationResponse:response DOPO', [
            //     'notification->id' => $this->notification->id,
            // ]);

            // Conferma la transazione
            DB::commit();

            Log::channel('florenceegi')->info('Transazione completata con successo per la risposta all\'invito.');

            // Dispatcha un evento di successo al frontend
            $this->dispatch('notification-response', option: $option, success: true);
        } catch (Exception $e) {
            // Annulla la transazione in caso di errore
            DB::rollBack();

            // Log dell'errore
            Log::channel('florenceegi')->error('Errore durante la gestione della risposta all\'invito:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Rilancia l'eccezione per gestirla altrove, se necessario
            throw $e;
        }
    }

    public function accept($notificationPayloadInvitation) {

        Log::channel('florenceegi')->info('InvitationAccepted:accept', [
            'notificationPayloadInvitation' => $notificationPayloadInvitation,
        ]);

        // Validazione preliminare del payload
        if (!$notificationPayloadInvitation || !$notificationPayloadInvitation->collection_id || !$notificationPayloadInvitation->role) {
            throw new Exception('Dati mancanti nel payload dell\'invito.');
        }

        try {
            // Aggiorna lo stato dell'invito come approvato
            $notificationPayloadInvitation->handleApproval();

            // L'utente che sta approvando l'invito
            $receiver = Auth::user();

            $metadata = ""; // Eventuali dati aggiuntivi da salvare

            $data = [
                'collection_id' => $notificationPayloadInvitation->collection_id,
                'user_id' => $receiver->id,
                'role' => $notificationPayloadInvitation->role,
                'joined_at' => now(), // Data di accettazione
                'metadata' => $metadata,
            ];

            Log::channel('florenceegi')->info('Dati di payload', $data);

            // Aggiungi l'utente alla collezione
            $collectionUser = CollectionUser::create($data);

            if (!$collectionUser) {
                throw new Exception('Errore nella creazione del record CollectionUser.');
            }

            // Aggiorna tutti i dati della notifica
            $this->notification['model_type'] = get_class($notificationPayloadInvitation);
            $this->notification['status'] = NotificationStatus::ACCEPTED->value;
            $this->notification['view'] = 'invitations.' . NotificationStatus::ACCEPTED->value;
            $this->notification['message'] = __('collection.collaborators.proposal_approved');
            $this->notification['collection_name'] = $this->notification->data['collection_name'] ?? null;
            $this->notification['receiver_id'] = $receiver->id;
            $this->notification['receiver_name'] = $receiver->name . ' ' . $receiver->last_name;
            $this->notification['receiver_email'] = $receiver->email;
        } catch (Exception $e) {
            Log::error('Errore durante l\'accettazione dell\'invito:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Rilancia l'eccezione per una gestione ulteriore
        }
    }

    public function reject($notificationPayloadInvitation) {


        $user = Auth::user();
        $receiverName = $user->name . ' ' . $user->last_name;
        $collectionName = $this->notification->data['collection_name'] ?? null;


        // Validazione preliminare dei parametri
        if (!$notificationPayloadInvitation || !$collectionName || !$receiverName) {
            throw new Exception('Parametri mancanti o non validi per la gestione del rifiuto.');
        }

        try {
            // Aggiorna lo stato dell'invito come rifiutato
            $notificationPayloadInvitation->handleRejection();

            // Verifica che lo stato sia stato aggiornato correttamente
            if (!$notificationPayloadInvitation->isRejected()) { // Metodo ipotetico
                throw new Exception('Errore durante l\'aggiornamento dello stato dell\'invito.');
            }

            // Aggiorna lo stato della notifica
            $this->notification['status'] = NotificationStatus::REJECTED->value;
            $this->notification['view'] = 'invitations.' . NotificationStatus::REJECTED->value;
            $this->notification['message'] = __('collection.collaborators.proposal_rejected');
            $this->notification['collection_name'] = $collectionName;
            $this->notification['receiver_name'] = $receiverName;
            $this->notification['receiver_id'] = $user->id;
            $this->notification['receiver_name'] = $user->name . ' ' . $user->last_name;
            $this->notification['receiver_email'] = $user->email;


            // Aggiungi un messaggio personalizzato
            $this->notification['message'] = __('Proposta di collaborazione rifiutata.');

            // Log dell'operazione
            Log::info('Invito rifiutato', [
                'notification_id' => $this->notification['id'] ?? null,
                'collection_name' => $collectionName,
                'receiver_name' => $receiverName,
            ]);
        } catch (Exception $e) {
            // Log dell'errore
            Log::error('Errore durante il rifiuto dell\'invito:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Rilancia l'eccezione per una gestione ulteriore
            throw $e;
        }
    }

    public function render() {
        return view('livewire.notifications.invitations.request');
    }
}