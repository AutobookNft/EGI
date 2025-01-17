<?php


namespace App\Notifications;

use App\Notifications\Channels\CustomDatabaseChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class WalletChangeResponseRejection extends Notification
{
    private $walletChangeApproval;
    private $reason;

    public function __construct($walletChangeApproval, $reason)
    {
        $this->walletChangeApproval = $walletChangeApproval;
        $this->reason = $reason;

    }

    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class];;
    }

    public function toCustomDatabase($notifiable)
    {
        return [
            'model_type' => get_class($this->walletChangeApproval),
            'model_id'   => $this->walletChangeApproval->id,
            'data' =>[
                'message' => __('collection.wallet_change_rejected, ') . __('collection.reason') . $this->reason
            ],
            'outcome' => 'rejected',
        ];
    }
}
