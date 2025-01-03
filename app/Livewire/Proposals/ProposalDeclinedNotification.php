<?php

namespace App\Livewire\Proposals;

use App\Models\Notification as NotificationModel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Support\Facades\Log;

class ProposalDeclinedNotification extends Notification
{
    use Queueable;

    public $notification;
    public $reason;

    public function __construct(array $notification)
    {
        $this->notification = $notification;

    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {

        // Trova l'utente che ha richiesto la modifica per comporre il nominativo
        $approver = User::find($this->notification['notifiable_id']);
        $approver_name = $approver->name . ' ' . $approver->last_name;

        Log::channel('florenceegi')->info('ProposalDeclinedNotification:toDatabase', [
            'notification id' => $this->notification['id']
        ]);

        // Aggiorna la notifica di richiesta nel database per segnalarla come completata
        $updated_notification = NotificationModel::find($this->notification['id']);

        // Ottieni i dati JSON esistenti
        $data = $updated_notification->data;

        // Aggiungi una nuova chiave al JSON
        $data['reason'] = $this->notification['reason'];

        // Aggiorna il modello con i nuovi dati
        $updated_notification->update([
            'outcome' => 'declined',
            'read_at' => now(),
            'data' => $data, // Salva i dati aggiornati
        ]);

        return [
            'message' => $this->notification['data']['type'] === 'create'
                ? __('notification.proposed_creation_new_wallet')
                : __('notification.proposed_change_to_a_wallet'),
            'type' => 'proposal_declined',
            'reason' => $this->notification['reason'],
            'wallet_address' => $this->notification['data']['wallet_address'],
            'royalty_mint' => $this->notification['data']['royalty_mint'],
            'royalty_rebind' => $this->notification['data']['royalty_rebind'],
            'wallet_change_approvals_id' => $this->notification['data']['wallet_change_approvals_id'],
            'requested_by' => $this->notification['data']['requested_by'],
            'approver' => $approver_name,
            'status' => 'response', // Definisce questa notifica come una [risposta]
            'timestamp' => now(),
        ];
    }
}
