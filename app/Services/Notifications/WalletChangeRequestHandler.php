<?php

namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;
use App\Notifications\WalletChangeRequestCreation;
use App\Notifications\WalletChangeResponseApproval;
use Illuminate\Support\Facades\Notification;
use App\Notifications\WalletChangeResponseRejection;

class WalletChangeRequestHandler implements NotificationHandlerInterface
{
    public function handle($message_to, $walletChange, $reason = null)
    {
        if ($walletChange['status'] === 'pending') {
            // Invio della notifica iniziale
            Notification::send($message_to, new WalletChangeRequestCreation($walletChange));
        } elseif ($walletChange['status'] === 'approved') {
            // Logica per accettare
            Notification::send($message_to, new WalletChangeResponseApproval ($walletChange));
        } elseif ($walletChange['status'] === 'rejected') {
            // Logica per declinare
            Notification::send($message_to, new WalletChangeResponseRejection($walletChange, $reason));
        } else {
            throw new \Exception("Azione '{$walletChange->status}' non supportata per WalletChangeRequest.");
        }
    }
}
