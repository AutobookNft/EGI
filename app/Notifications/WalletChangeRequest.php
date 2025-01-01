<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Support\Facades\Log;

class WalletChangeRequest extends Notification
{
    private $approval;

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
            'message' => $this->approval->type === 'wallet_create'
                ? __('A new wallet has been proposed for you.')
                : __('A change has been requested for your wallet.'),
            'wallet_address' => $this->approval->change_details['wallet_address'],
            'royalty_mint' => $this->approval->change_details['royalty_mint'],
            'royalty_rebind' => $this->approval->change_details['royalty_rebind'],
            'approval_id' => $this->approval->id,
            'requested_by' => $this->approval->requested_by_user_id,
            'type' => $this->approval->change_type, // Aggiunto per distinguere i tipi di notifica
        ];
    }
}
