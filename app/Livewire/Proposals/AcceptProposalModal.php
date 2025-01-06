<?php

namespace App\Livewire\Proposals;

use App\Models\WalletChangeApproval;

use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class AcceptProposalModal extends Component
{
    public $isVisible = false;
    public $context;
    public $type;
    public $walletChangeApprovalId;
    public $reason = '';
    public $notification;

    protected $rules = [
        'reason' => 'required|string|max:500',
    ];

    #[On('open-accept-modal')]
    public function openAcceptModal($notification)
    {

        $this->notification = $notification;

        Log::channel('florenceegi')->info('AcceptProposalModal: openModal', [
            '$this->notification' => $notification,
        ]);

        $this->isVisible = true;

    }

    public function closeModal()
    {
        $this->isVisible = false;
    }


    public function accept()
    {

        Log::channel('florenceegi')->info('AcceptProposalModal: accept', [
            'notification' =>  json_encode($this->notification),
            'type' => $this->type,
            'reason' => $this->reason,
        ]);

        $this->validate();

        // Trova la proposta nel database
        $proposal = WalletChangeApproval::findOrFail($this->notification['data']['wallet_change_approvals_id']);

        // Aggiorna lo stato della proposta a "accepted" e salva la motivazione
        $proposal->update([
            'type' => $this->notification['data']['type'],
            'status' => 'accepted',
            'rejection_reason' => $this->reason,
            'read_at' => now(),
            'notification_id' => $this->notification['id']
        ]);

        // Aggiungo i dati alla notifica di risposta
        $this->notification['type'] = $proposal->type;
        $this->notification['reason'] = $this->reason;
        $this->notification['approver_user_id'] = $proposal->approver_user_id;


        // Crea la notifica di declino per il proponente
        $proposal->requestedBy->notify(new ProposalAcceptedNotification($this->notification));

        // Compensazione del wallet del proponente

        // Nasconde la modale
        $this->isVisible = false;

        // Invia l'evento per aggiornare la dashboard
        $this->dispatch('proposal-accepted');
    }




    public function render()
    {
        return view('livewire.proposals.decline-proposal-modal');
    }
}
