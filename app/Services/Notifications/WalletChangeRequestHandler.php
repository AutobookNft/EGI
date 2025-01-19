<?php

namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;
use App\Notifications\WalletChangeRequestCreation;
use App\Notifications\WalletChangeResponseApproval;
use Illuminate\Support\Facades\Notification;
use App\Notifications\WalletChangeResponseRejection;

class WalletChangeRequestHandler implements NotificationHandlerInterface
{
    public function handle($message_to, $walletChangeApproval, $reason = null)
    {
        if ($walletChangeApproval['status'] === 'pending') {
            // Invio della notifica iniziale
            Notification::send($message_to, new WalletChangeRequestCreation($walletChangeApproval));
        } elseif ($walletChangeApproval['status'] === 'approved') {
            // Logica per accettare
            Notification::send($message_to, new WalletChangeResponseApproval ($walletChangeApproval));
        } elseif ($walletChangeApproval['status'] === 'rejected') {
            // Logica per declinare
            Notification::send($message_to, new WalletChangeResponseRejection($walletChangeApproval, $reason));
        } else {
            throw new \Exception("Azione '{$walletChangeApproval->status}' non supportata per WalletChangeRequest.");
        }
    }
}
