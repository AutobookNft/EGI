<?php

namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\WalletChangeApproval;

class WalletChangeRequestHandler implements NotificationHandlerInterface
{
    public function handle(DatabaseNotification $notification, string $action)
    {
        $approvalId = $notification->data['approval_id'];

        if ($action === 'accept') {
            // Logica per approvare
            WalletChangeApproval::findOrFail($approvalId)->update(['status' => 'approved']);
            $notification->update(['outcome' => 'accepted']);
        } elseif ($action === 'decline') {
            // Logica per declinare
            WalletChangeApproval::findOrFail($approvalId)->update(['status' => 'declined']);
            $notification->update(['outcome' => 'declined']);
        } else {
            throw new \Exception("Azione '{$action}' non supportata per WalletChangeRequest.");
        }
    }
}

