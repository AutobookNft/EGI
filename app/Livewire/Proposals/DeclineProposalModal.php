<?php

namespace App\Livewire\Proposals;

use App\Models\NotificationPayloadWallet;
use App\Notifications\WalletChangeRequestCreation;
use App\Notifications\WalletChangeResponseRejection;
use App\Services\Notifications\NotificationHandlerFactory;
use App\Services\Notifications\WalletChangeRequestHandler;
use App\Models\User;
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
    public $walletChangeApprovalId;

    #[Validate('required|string|max:500')]
    public $reason = '';
    public $notification;

    #[On('open-decline-modal')]
    public function openDeclineModal($notification)
    {
        $this->notification = $notification;
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
     * @method static NotificationPayloadWallet findOrFail(int|string $id)
     * @return void
     */
    public function decline()
    {

        $this->validate();

        // Ottiene il recordo del payload della proposta
        $walletChangeApproval = NotificationPayloadWallet::find($this->notification['notification_payload_wallets_id'])->first(); // Recupera un singolo record

        // Aggiorna lo stato della proposta a "rejected"
        if ($walletChangeApproval) {

            Log::channel('florenceegi')->info('DeclineProposalModal: decline', [
                'walletChangeApproval' => $walletChangeApproval,
            ]);
            $walletChangeApproval->handleRejection();

        } else {
            // Gestisci il caso in cui il record non viene trovato
            Log::channel('florenceegi')->error('DeclineProposalModal: decline', [
                'message' => 'WalletChangeApproval record not found.',
                'walletChangeApprovalId' => $walletChangeApproval,
            ]);
        }

        $proposer = User::find($walletChangeApproval->proposer_id)->first();

        // Gestione della notifica
        $handler = NotificationHandlerFactory::getHandler(WalletChangeResponseRejection::class);
        $handler->handle($proposer, $walletChangeApproval, $this->reason);

        session()->flash('message', __('collection.wallet.wallet_change_rejected'));

        // Nasconde la modale
        $this->isVisible = false;

        // Invia l'evento per aggiornare la dashboard
        $this->dispatch('proposal-declined');
    }

    public function render()
    {
        return view('livewire.proposals.decline-proposal-modal');
    }
}
