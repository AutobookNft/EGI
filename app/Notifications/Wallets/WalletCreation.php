<?php

namespace App\Notifications\Wallets;

use App\Enums\WalletStatus;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Support\Facades\Log;
use App\Notifications\Channels\CustomDatabaseChannel;

class WalletCreation extends Notification
{
    protected $notification;

    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class];
    }

    public function toCustomDatabase($notifiable)
    {
        return [
            'model_type'    => $this->notification->model_type, // Esempio: App\Models\WalletChangeApproval
            'model_id'      => $this->notification->model_id,   // L'ID del record
            'view'          =>  $this->notification->view,
            'data'          => [
                'message'       => $this->notification->message,
                'user_name'     => $this->notification->proposer_name,
                'user_id'       => $this->notification->proposer_id,
                'collection_name' => $this->notification->collection_name,
                ],
            'outcome' => $this->notification->status,
        ];
    }
}
