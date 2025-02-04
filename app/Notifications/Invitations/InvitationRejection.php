<?php

namespace App\Notifications\Invitations;

use App\Enums\NotificationStatus;
use App\Notifications\Channels\CustomDatabaseChannel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class InvitationRejection extends Notification
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

        Log::channel('florenceegi')->info('InvitationRejected:toCustomDatabase', [
            'notificationPayloadInvitation' => $this->notification,
        ]);

        return [
            'model_type' => get_class($this->notification), // Esempio: App\Models\WalletChangeApproval
            'model_id'   => $this->notification->model_id,        // L'ID del record
            'view'       => $this->notification->view,
            'sender_id'  => $this->notification->receiver_id,
            'data'       => [
                'message' => __('collection.invitation_rejected'),
                'sender' => $this->notification->receiver_name, // L'utente che ha rigettato l'invito
                'email'    => $this->notification->receiver_email,
                'collection_name' => $this->notification->collection_name,
             ],
            'outcome' => NotificationStatus::REJECTED->value,
        ];
    }
}
