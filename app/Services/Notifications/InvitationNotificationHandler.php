<?php


namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\CollectionInvitation;
use App\Models\CollectionUser;
use Illuminate\Support\Facades\Auth;

class InvitationNotificationHandler implements NotificationHandlerInterface
{
    public function handle(DatabaseNotification $notification, string $action)
    {
        $invitationId = $notification->data['invitation_id'];

        if ($action === 'accept') {
            $invitation = CollectionInvitation::findOrFail($invitationId);
            $invitation->update(['status' => 'accepted']);

            CollectionUser::create([
                'collection_id' => $invitation->collection_id,
                'user_id' => Auth::id(),
                'role' => $invitation->role,
            ]);

            $notification->update(['outcome' => 'accepted']);
        } elseif ($action === 'decline') {
            $invitation = CollectionInvitation::findOrFail($invitationId);
            $invitation->update(['status' => 'declined']);

            $notification->update(['outcome' => 'declined']);
        } else {
            throw new \Exception("Azione '{$action}' non supportata per InvitationNotification.");
        }
    }
}
