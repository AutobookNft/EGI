<?php


namespace App\Notifications;

use App\Notifications\Channels\CustomDatabaseChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class WalletChangeResponseApproval extends Notification
{
    private $walletChangeApproval;
    private $status;

    public function __construct($walletChangeApproval)
    {
        $this->walletChangeApproval = $walletChangeApproval;

    }

    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class];;
    }

    public function toCustomDatabase($notifiable)
    {
        return [
            'model_type' => get_class($this->walletChangeApproval),
            'model_id'   => $this->walletChangeApproval->id,        // L'ID del record
            'data' =>[
                'message' => __('collection.wallet.change_approved'),
            ],
            'outcome' => 'approved',
        ];
    }
}
