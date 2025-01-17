<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Support\Facades\Log;
use App\Notifications\Channels\CustomDatabaseChannel;

class WalletChangeRequestUpdate extends Notification
{
    protected $walletChangeApproval;

    public function __construct($walletChangeApproval)
    {
        $this->walletChangeApproval = $walletChangeApproval;
    }

    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class];
    }

    public function toCustomDatabase($notifiable)
    {
        return [
            'model_type' => get_class($this->walletChangeApproval), // Esempio: App\Models\WalletChangeApproval
            'model_id'   => $this->walletChangeApproval->id,        // L'ID del record
            'data'       => [
                'message' => $this->walletChangeApproval->change_type === 'create'
                ? __('A new wallet has been proposed for you.')
                : __('A change has been requested for your wallet.'),
                'outcome' => 'proposal',
            ],
        ];
    }
}
