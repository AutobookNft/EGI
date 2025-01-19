<?php


namespace App\Notifications;

use App\Contracts\NotificationHandlerInterface;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\CollectionInvitation;
use App\Models\CollectionUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class InvitationApproval implements NotificationHandlerInterface
{
    public function handle(User $user, DatabaseNotification $notification)
    {
        $invitationId = $notification->data['invitation_id'];
        $invitation = CollectionInvitation::findOrFail($invitationId);
        $invitation->update(['status' => 'accepted']);

        CollectionUser::create([
            'collection_id' => $invitation->collection_id,
            'user_id' => Auth::id(),
            'role' => $invitation->role,
        ]);

        $notification->update(['outcome' => 'accepted']);

    }
}
