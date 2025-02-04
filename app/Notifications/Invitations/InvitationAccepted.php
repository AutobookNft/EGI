<?php

namespace App\Notifications\Invitations;

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
            'sender_id'     => $this->notification->receiver_id,
            'data'       => [
                'message' => $this->notification->message,
                'sender' => $this->notification->receiver_name,
                'email'    => $this->notification->receiver_email,
                'collection_name' => $this->notification->collection_name,
             ],
            'prev_id' => $this->notification->id,
            'outcome' => $this->notification->status,
        ];
    }
}
