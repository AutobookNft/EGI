<?php

namespace App\Livewire\Notifications\Wallets;

use App\Enums\NotificationStatus;
use App\Models\NotificationPayloadWallet;
use App\Models\walletChangeRejection;
use App\Notifications\WalletChangeRequestCreation;
use App\Notifications\WalletChangeResponseRejection;
use App\Services\Notifications\NotificationHandlerFactory;
use App\Services\Notifications\WalletChangeRequestHandler;
use App\Models\User;
use App\Services\Notifications\WalletNotificationHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Validate;

class DeclineProposalModal extends Component
{
    public $isVisible = false;
    public $context;
    public $type;
    public $walletChangeRejectionId;

    #[Validate('required|string|max:500')]
    public $reason = '';
    public $notification;

    #[On('open-decline-modal')]
    public function openDeclineModal($notification)
    {
        $this->notification = $notification;
        Log::channel('florenceegi')->info('DeclineProposalModal: openDeclineModal', [
            'notification' => $notification,
        ]);
        $this->isVisible = true;
    }

    public function closeModal()
    {
        $this->isVisible = false;
    }

     /**
     * Declina una richiesta di modifica del wallet.
     * @param int $approvalId
     * @param string|null $reason
     * @param \App\Models\User $proposer
     * @return void
     */
    public function decline()
    {

        $this->validate();

        // Ottiene il record del payload della proposta
        $new_notification = NotificationPayloadWallet::find($this->notification['model_id']); // Recupera un singolo record

        // Aggiorna lo stato della proposta a "rejected"
        if ($new_notification) {

            Log::channel('florenceegi')->info('DeclineProposalModal: decline', [
                'walletChangeApproval' => $new_notification,
            ]);

            $new_notification->handleRejection();

        } else {
            // Gestisci il caso in cui il record non viene trovato
            Log::channel('florenceegi')->error('DeclineProposalModal: decline', [
                'message' => 'WalletChangeApproval record not found.',
                'walletChangeApprovalId' => $new_notification,
            ]);
        }

        $proposer = User::find($new_notification->proposer_id);

        // si aggiungono i dati per la notifica di risposta
        $new_notification['proposer_name'] = Auth::user()->name . ' ' . Auth::user()->last_name; // Nome di chi fa la proposta
        $new_notification['model_id'] = $this->notification['model_id'];
        $new_notification['model_type'] = $this->notification['model_type'];
        $new_notification['message'] = __('collection.wallet.wallet_change_rejected');
        $new_notification['view'] = 'wallets.' . NotificationStatus::REJECTED->value; // La vista da mostrare
        $new_notification['prev_id'] = $this->notification['id'];
        $new_notification['reason'] = $this->reason;


        // Gestione della notifica
        $handler = NotificationHandlerFactory::getHandler(WalletNotificationHandler::class);
        $handler->handle($proposer, $new_notification);

        // session()->flash('message', __('collection.wallet.wallet_change_rejected'));

        // Nasconde la modale
        $this->isVisible = false;

        // Invia l'evento per aggiornare la dashboard
        // $this->dispatch('proposal-declined');
    }

    public function render()
    {
        return view('livewire.notifications.wallets.decline-proposal-modal');
    }
}
