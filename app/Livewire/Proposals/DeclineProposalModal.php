<?php

namespace App\Livewire\Proposals;

use App\Models\WalletChangeApproval;

use App\Models\WalletChangeApprovalModel;
use App\Services\Notifications\NotificationHandlerFactory;
use App\Services\Notifications\WalletChangeRequestHandler;
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
        Log::channel('florenceegi')->info('DeclineProposalModal: openModal', [
            '$this->notification_data' => $notification,
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
     * @method static WalletChangeApprovalModel findOrFail(int|string $id)
     * @return void
     */
    public function decline()
    {

        $this->validate();

        // Recupera il record da WalletChangeApproval per l'ID del proposer
        $walletChangeApproval = Auth::user()->walletChangeProposer;

        // Aggiorna lo stato della proposta a "rejected"
        $walletChangeApproval->handleRejection();

        // Gestione della notifica
        $handler = NotificationHandlerFactory::getHandler(WalletChangeRequestHandler::class);
        $handler->handle($walletChangeApproval, $this->reason);

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
