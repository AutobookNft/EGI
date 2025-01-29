<?php

namespace App\Livewire\Proposals;

use App\Models\NotificationPayloadWallet;

use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class AcceptProposalModal extends Component
{
    public $isVisible = false;
    public $context;
    public $type;
    public $notificationPayloadWallet;
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
        $proposal = NotificationPayloadWallet::findOrFail($this->notification['data']['notification_payload_wallet_id']);

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
        $this->notification['approver_user_name'] = $proposal->approver_user_name;
        $this->notification['collection_name'] = $proposal->collection_name;
        $this->notification['message'] = 'Proposta di collaborazione accettata.';
        $this->notification['view'] = 'wallets.' . $proposal->type;


        /**
         * Gestione della compensazione del wallet del proposer
         *
         */

        // Invia la notifica di risposta

        //------------------------------


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
