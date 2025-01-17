<?php

namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;
use App\Notifications\WalletChangeRequestCreation;
use App\Notifications\WalletChangeResponseApproval;
use Illuminate\Support\Facades\Notification;
use App\Notifications\WalletChangeResponseRejection;

class WalletChangeRequestHandler implements NotificationHandlerInterface
{
    public function handle($walletChangeApproval, $reason = null)
    {
        if ($walletChangeApproval->status === 'creation') {
            // Invio della notifica iniziale
            Notification::send($walletChangeApproval->receiver_id, new WalletChangeRequestCreation($walletChangeApproval));
        } elseif ($walletChangeApproval->status === 'approved') {
            // Logica per accettare
            Notification::send($walletChangeApproval->receiver_id, new WalletChangeResponseApproval ($walletChangeApproval));
        } elseif ($walletChangeApproval->status === 'rejected') {
            // Logica per declinare
            Notification::send($walletChangeApproval->receiver_id, new WalletChangeResponseRejection($walletChangeApproval, $reason));
        } else {
            throw new \Exception("Azione '{$walletChangeApproval->status}' non supportata per WalletChangeRequest.");
        }
    }
}
