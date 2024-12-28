<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CollectionInvitationNotification extends Notification
{
    protected $invitationId;

    public function __construct($invitationId)
    {
        $this->invitationId = $invitationId;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'You have been invited to join a team.',
            'invitation_id' => $this->invitationId,
        ];
    }
}
