<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Support\Facades\Log;

class WalletChangeRequest extends Notification
{
    protected $approval; 

    public function __construct($approval)
    {
        $this->approval = $approval;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        Log::channel('florenceegi')->info('WalletChangeRequest:toDatabase', [
            'notifiable' => $notifiable
        ]);

        return [
            'message' => $this->approval->change_type === 'create'
                ? __('A new wallet has been proposed for you.')
                : __('A change has been requested for your wallet.'),
            'wallet_change_approvals_id' => $this->approval->id,
            'outcome' => 'proposal', // Sostituito "status" con "outcome"
        ];
    }
}
