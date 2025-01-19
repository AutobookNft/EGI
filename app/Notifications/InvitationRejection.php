<?php


namespace App\Notifications;

use App\Contracts\NotificationHandlerInterface;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\CollectionInvitation;
use App\Models\CollectionUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class InvitationRejection implements NotificationHandlerInterface
{
    public function handle(User $user, DatabaseNotification $notification)
    {
        $invitationId = $notification->data['invitation_id'];
        $invitation = CollectionInvitation::findOrFail($invitationId);
        $invitation->update(['status' => 'declined']);
        $notification->update(['outcome' => 'declined']);

    }
}
