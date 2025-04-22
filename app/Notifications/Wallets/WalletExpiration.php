<?php

namespace App\Notifications\Wallets;

use App\DataTransferObjects\Notifications\NotificationData;
use App\Notifications\Channels\CustomDatabaseChannel;
use Illuminate\Notifications\Notification;

class WalletExpiration extends Notification
{
    private NotificationData $notification;

    public function __construct(NotificationData $notification)
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
            'model_type'    => $this->notification->getModelType(), // Esempio: App\Models\WalletChangeApproval
            'model_id'      => $this->notification->getModelId(),   // L'ID del record
            'view'          =>  $this->notification->getView(),
            'sender_id'         => $this->notification->getSenderId(),
            'data'          => [
                'message'       => $this->notification->getMessage(),
                'sender'     => $this->notification->getSenderName(),
                'email'    => $this->notification->getSenderEmail(),
                'collection_name' => $this->notification->getCollectionName(),
                'old_royalty_mint' => $this->notification->getOldRoyaltyMint(),
                'old_royalty_rebind' => $this->notification->getOldRoyaltyRebind(),
            ],
            'outcome' => $this->notification->getStatus(),
        ];;
    }
}
