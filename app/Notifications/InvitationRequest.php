<?php

namespace App\Notifications;

use App\Notifications\Channels\CustomDatabaseChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class InvitationRequest extends Notification
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

        Log::channel('florenceegi')->info('InvitationRequest: toCustomDatabase', [
            'notification' => $this->notification,
        ]);

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
