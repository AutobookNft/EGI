<?php

namespace App\Livewire\Notifications\Invitations;

use App\Enums\InvitationStatus;
use App\Models\CollectionUser;
use App\Models\NotificationPayloadInvitation;
use App\Models\User;
use Livewire\Attributes\On;
use App\Services\Notifications\InvitationNotificationHandler;
use App\Services\Notifications\NotificationHandlerFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Exception;

class Request extends Component
{

    public mixed $notification;

    public function mount(mixed $notification)
    {
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
    public function response($option)
    {

        // Log::channel('florenceegi')->info('InvitationResponse:response', [
        //     'option' => $option,
        //     'notification' => $this->notification,
        // ]);

        try {
            // Inizio della transazione
            DB::beginTransaction();

            // L'utente che ha proposto la collaborazione
            $proposer_id = $this->notification->data['user_id'] ?? null;

            // Si crea l'oggetto User da usare per inviare la notifica
            $message_to = User::find($proposer_id);

            // Si recupera l'oggetto NotificationPayloadInvitation creato al momento dell'invio della proposta
            $notificationPayloadInvitation = NotificationPayloadInvitation::find($this->notification->model_id);

            // Accetta o rifiuta l'invito
            if ($option === 'accepted') {
                $this->accept($notificationPayloadInvitation);
            } else {
                $this->reject($notificationPayloadInvitation);
            }

            // Invia la notifica
            $handler = NotificationHandlerFactory::getHandler(InvitationNotificationHandler::class);
            $handler->handle($message_to, $this->notification);

            Log::channel('florenceegi')->info('InvitationResponse:response DOPO', [
                'notification->id' => $this->notification->id,
            ]);


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

    public function accept($notificationPayloadInvitation)
    {

        Log::channel('florenceegi')->info('InvitationAccepted:accept', [
            'notificationPayloadInvitation' => $notificationPayloadInvitation,
        ]);

        // Validazione preliminare del payload
        if (!$notificationPayloadInvitation->collection_id || !$notificationPayloadInvitation->role) {
            throw new \Exception('Dati mancanti nel payload dell\'invito.');
        }

        try {
            // Aggiorna lo stato dell'invito come approvato
            $notificationPayloadInvitation->handleApproval();

            // L'utente che ha approvato l'invito
            $user = Auth::user();

            $data = [
                'collection_id' => $notificationPayloadInvitation->collection_id,
                'user_id' => $user->id,
                'role' => $notificationPayloadInvitation->role,
                'status' => InvitationStatus::ACCEPTED->value,
            ];

            Log::channel('florenceegi')->info('Dati di payload', $data);

            // Aggiungi l'utente alla collezione
            $collectionUser = CollectionUser::create($data);

            if (!$collectionUser) {
                throw new \Exception('Errore nella creazione del record CollectionUser.');
            }

            // Aggiorna tutti i dati della notifica
            $this->notification['model_type'] = get_class($notificationPayloadInvitation);
            $this->notification['status'] = InvitationStatus::ACCEPTED->value;
            $this->notification['view'] = InvitationStatus::ACCEPTED->value;
            $this->notification['message'] = __('Proposta di collaborazione approvata.');
            $this->notification['collection_name'] = $this->notification->data['collection_name'] ?? null;
            $this->notification['receiver_id'] = $user->id;
            $this->notification['receiver_name'] = $user->name . ' ' . $user->last_name;

        } catch (\Exception $e) {
            Log::error('Errore durante l\'accettazione dell\'invito:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Rilancia l'eccezione per una gestione ulteriore
        }
    }

    public function reject($notificationPayloadInvitation)
    {


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
            $this->notification['status'] = InvitationStatus::REJECTED->value;
            $this->notification['view'] = InvitationStatus::REJECTED->value;
            $this->notification['collection_name'] = $collectionName;
            $this->notification['receiver_name'] = $receiverName;

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

    public function render()
    {
        return view('livewire.notifications.invitations.request');
    }
}
