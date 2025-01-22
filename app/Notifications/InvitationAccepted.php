<?php

namespace App\Notifications;

use App\Notifications\Channels\CustomDatabaseChannel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class InvitationAccepted extends Notification
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

        Log::channel('florenceegi')->info('InvitationApproval:toCustomDatabase', [
            'notificationPayloadInvitation' => $this->notification,
        ]);

        return [
            'model_type' => $this->notification->model_type, // Esempio: App\Models\NotificationPayloadInvitation
            'model_id'   => $this->notification->model_id,   // L'ID del record
            'view'       => $this->notification->view,
            'data'       => [
                'message' => $this->notification->message,
                'user_name' => $this->notification->receiver_name,
                'user_id' => $this->notification->receiver_id,
                'collection_name' => $this->notification->collection_name,
             ],
            'read_at' => now(),
            'outcome' => $this->notification->status,
        ];
    }
}
