<?php

namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;
use App\Models\WalletChangeApproval;
use Illuminate\Support\Facades\Notification;
use App\Notifications\WalletChangeRequest;

class WalletChangeRequestHandler implements NotificationHandlerInterface
{
    public function handle($data, string $action)
    {
        if ($action === 'proposal') {
            // Invio della notifica iniziale
            $approval = $data; // Il modello WalletChangeApproval
            $receiver = $approval->approver; // Utente destinatario (approver) definito nella relazione in WalletChangeApproval
            Notification::send($receiver, new WalletChangeRequest($approval));
        } elseif ($action === 'accept') {
            // Logica per accettare
            WalletChangeApproval::findOrFail($data['approval_id'])->update(['status' => 'approved']);
        } elseif ($action === 'decline') {
            // Logica per declinare
            WalletChangeApproval::findOrFail($data['approval_id'])->update(['status' => 'declined']);
        } else {
            throw new \Exception("Azione '{$action}' non supportata per WalletChangeRequest.");
        }
    }
}
    