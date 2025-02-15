<?php

namespace App\Notifications\Wallets;

use App\Enums\NotificationStatus;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Support\Facades\Log;
use App\Notifications\Channels\CustomDatabaseChannel;

class WalletUpdate extends Notification
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
            'model_type'    => $this->notification->getModelType(), // Esempio: App\Models\WalletChangeApproval
            'model_id'      => $this->notification->getModelId(),   // L'ID del record
            'view'          =>  $this->notification->getView(),
            'prev_id'       => $this->notification->getPrevId(), // L'id di notification appartiene alla notifica di creazione, qui stiamo creando una notifica di accettazione e dobbiamo passare l'id della notifica di creazione per poterne aggiornare lo stato
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
        ];
    }
}
