<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Support\Facades\Log;
use App\Notifications\Channels\CustomDatabaseChannel;

class WalletChangeRequestCreation extends Notification
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
                'message' => __('A new wallet has been proposed for you.'),
                ],
            'outcome' => 'proposal',
        ];
    }
}
