<?php

namespace App\Notifications\Wallets;

use App\Enums\NotificationStatus;
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

        Log::channel('florenceegi')->info('WalletCreation', [
            'notification' => $this->notification
        ]);

        return [
            'model_type'    => $this->notification->getModelType(), // Esempio: App\Models\WalletChangeApproval
            'model_id'      => $this->notification->getModelId(),   // L'ID del record
            'view'          =>  $this->notification->getView(),
            'sender_id'         => $this->notification->getSenderId(),
            'data'          => [
                'message'       => $this->notification->getMessage(),
                'sender'     => $this->notification->getSenderName(),
                'email'    => $this->notification->getSenderEmail(),
                'collection_name' => $this->notification->getCollectionName(),
            ],
            'outcome' => $this->notification->getStatus(),
        ];
    }
}
