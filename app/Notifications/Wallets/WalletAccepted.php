<?php


namespace App\Notifications\Wallets;

use App\Enums\NotificationStatus;
use App\Enums\WalletStatus;
use App\Notifications\Channels\CustomDatabaseChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Support\Facades\Log;

class WalletAccepted extends Notification
{
    private $notification;
    private $status;

    public function __construct($notification)
    {
        $this->notification = $notification;

    }

    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class];;
    }

    public function toCustomDatabase($notifiable)
    {

        Log::channel('florenceegi')->info('WalletAccepted', [
            'notification' => $this->notification
        ]);

        return [
            'model_type'    => $this->notification->model_type, // Esempio: App\Models\WalletChangeApproval
            'model_id'      => $this->notification->model_id,   // L'ID del record
            'view'          =>  $this->notification->view,
            'prev_id'       => $this->notification->prev_id, // L'id di notification appartiene alla notifica di creazione, qui stiamo creando una notifica di accettazione e dobbiamo passare l'id della notifica di creazione per poterne aggiornare lo stato
            'sender_id'     => $this->notification->receiver_id,
            'data'          => [
                'message'       => $this->notification->message,
                'sender'     => $this->notification->proposer_name,
                'email'    => $this->notification->proposer_email,
                'collection_name' => $this->notification->collection_name
            ],
            'outcome' => NotificationStatus::ACCEPTED->value,
        ];
    }
}
