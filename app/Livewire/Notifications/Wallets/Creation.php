<?php

namespace App\Livewire\Notifications\Wallets;

use App\Enums\WalletStatus;
use App\Models\CollectionUser;
use App\Models\NotificationPayloadWallet;
use App\Models\User;
use App\Services\Notifications\NotificationHandlerFactory;
use App\Services\Notifications\WalletNotificationHandler;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Creation extends Component
{
    public $notification;

    public function mount($notification)
    {
        $this->notification = $notification;
    }

    /**
     * Gestisce la risposta a una notifica di proposta di creazione di un nuovo wallet (accettazione o rifiuto).
     *
     * Questo metodo:
     * - Recupera la notifica associata alla proposta e il payload corrispondente.
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
    
    public function response($option)
    {

        try {
            // Inizio della transazione
            DB::beginTransaction();

            // L'utente che ha proposto il wallet
            $proposer_id = $this->notification->data['user_id'] ?? null;

            // Si crea l'oggetto User da usare per inviare la notifica
            $message_to = User::find($proposer_id);

            // Si recupera l'oggetto NotificationPayloadInvitation creato al momento dell'invio della proposta
            $notificationPayloadWallet = NotificationPayloadWallet::find($this->notification->model_id);

            // Accetta o rifiuta l'invito
            if ($option === 'accepted') {
                $this->accept($notificationPayloadWallet);
            } else {
                $this->reject($notificationPayloadWallet);
            }

            // Invia la notifica
            $handler = NotificationHandlerFactory::getHandler(WalletNotificationHandler::class);
            $handler->handle($message_to, $this->notification);

            Log::channel('florenceegi')->info('WalletResponse: response DOPO', [
                'notification->id' => $this->notification->id,
            ]);


            // Conferma la transazione
            DB::commit();

            Log::channel('florenceegi')->info('Transazione completata con successo per la risposta alla creazione del wallet.');

             // Dispatcha un evento di successo al frontend
            $this->dispatch('notification-response', option: $option, success: true);

        } catch (Exception $e) {
            // Annulla la transazione in caso di errore
            DB::rollBack();

            // Log dell'errore
            Log::channel('florenceegi')->error('Errore durante la gestione della risposta alla creazione del wallet:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Rilancia l'eccezione per gestirla altrove, se necessario
            throw $e;
        }
    }

    public function accept($notificationPayloadWallet)
    {

        Log::channel('florenceegi')->info('WalletAccepted:accept', [
            'notificationPayloadWallet' => $notificationPayloadWallet,
        ]);

        // Validazione preliminare del payload
        if (!$notificationPayloadWallet || !$notificationPayloadWallet->collection_id || !$notificationPayloadWallet->role) {
            throw new Exception('Dati mancanti nel payload dell\'invito.');
        }

        try {
            // Aggiorna lo stato dell'invito come approvato
            $notificationPayloadWallet->handleApproval();

            // L'utente che ha approvato l'invito
            $user = Auth::user();

            $data = [
                'collection_id' => $notificationPayloadWallet->collection_id,
                'user_id' => $user->id,
                'role' => $notificationPayloadWallet->role,
                'status' => WalletStatus::ACCEPTED->value,
            ];

            Log::channel('florenceegi')->info('Dati di payload', $data);

            // Aggiungi l'utente alla collezione
            $collectionUser = CollectionUser::create($data);

            if (!$collectionUser) {
                throw new Exception('Errore nella creazione del record CollectionUser.');
            }

            // Aggiorna tutti i dati della notifica
            $this->notification['model_type'] = get_class($notificationPayloadWallet);
            $this->notification['status'] = WalletStatus::ACCEPTED->value;
            $this->notification['view'] = 'wallets.' . WalletStatus::ACCEPTED->value;
            $this->notification['message'] = __('collection.wallet.wallet_change_approved');
            $this->notification['collection_name'] = $this->notification->data['collection_name'] ?? null;
            $this->notification['receiver_id'] = $user->id;
            $this->notification['receiver_name'] = $user->name . ' ' . $user->last_name;

        } catch (Exception $e) {
            Log::error('Errore durante l\'accettazione della creazione di un wallet:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Rilancia l'eccezione per una gestione ulteriore
        }
    }

    public function reject($notificationPayloadWallet)
    {


        $user = Auth::user();
        $receiverName = $user->name . ' ' . $user->last_name;
        $collectionName = $this->notification->data['collection_name'] ?? null;


        // Validazione preliminare dei parametri
        if (!$notificationPayloadWallet || !$collectionName || !$receiverName) {
            throw new Exception('Parametri mancanti o non validi per la gestione del rifiuto.');
        }

        try {
            // Aggiorna lo stato dell'invito come rifiutato
            $notificationPayloadWallet->handleRejection();

            // Verifica che lo stato sia stato aggiornato correttamente
            if (!$notificationPayloadWallet->isRejected()) { // Metodo ipotetico
                throw new Exception('Errore durante l\'aggiornamento dello stato dell\'invito.');
            }

            // Aggiorna lo stato della notifica
            $this->notification['status'] = WalletStatus::REJECTED->value;
            $this->notification['view'] = 'invitations.' . WalletStatus::REJECTED->value;
            $this->notification['collection_name'] = $collectionName;
            $this->notification['receiver_name'] = $receiverName;
            $this->notification['receiver_id'] = $user->id;

            // Aggiungi un messaggio personalizzato
            $this->notification['message'] = __('collection.wallet.wallet_change_rejected');

            // Log dell'operazione
            Log::info('Proposta creazione wallet rifiutata', [
                'notification_id' => $this->notification['id'] ?? null,
                'collection_name' => $collectionName,
                'receiver_name' => $receiverName,
            ]);
        } catch (Exception $e) {
            // Log dell'errore
            Log::error('Errore durante il rifiuto della proposta di creazione di un nuovo wallet:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Rilancia l'eccezione per una gestione ulteriore
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.notifications.wallets.creation');
    }
}
