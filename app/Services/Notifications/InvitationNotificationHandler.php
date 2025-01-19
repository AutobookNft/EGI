<?php


namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;
use App\Enums\InvitationStatus;

use App\Models\CollectionInvitation;
use App\Notifications\InvitationApproval;
use App\Notifications\InvitationProposal;
use App\Notifications\InvitationRejection;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class InvitationNotificationHandler implements NotificationHandlerInterface
{
    public function handle(User $message_to, $collectionInvitation)
    {
        $action = match ($collectionInvitation->status_enum) {
            InvitationStatus::PENDING => 'proposal',
            InvitationStatus::ACCEPTED => 'approved',
            InvitationStatus::REJECTED => 'rejected',
            default => throw new \Exception("Azione non supportata per CollectionInvitation."),
        };
        
        try {
            match ($action) {  // Qui usiamo $action invece di $collectionInvitation->status
                'proposal' => Notification::send($message_to, new InvitationProposal($collectionInvitation)),
                'approved' => Notification::send($message_to, new InvitationApproval($collectionInvitation)),
                'rejected' => Notification::send($message_to, new InvitationRejection($collectionInvitation)),
            };

        } catch (\Exception $e) {

            Log::channel('florenceegi')->error('Errore notifica', [
                'error' => $e->getMessage(),
                'action' => $action,
                'status' => $collectionInvitation->status,
                'collection_invitation_id' => $collectionInvitation->id
            ]);
            throw $e;
        }
    }
}

