<?php

namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;
use App\Notifications\Wallets\WalletCreation;
use App\Notifications\Wallets\WalletAccepted;
use App\Notifications\Wallets\WalletRejection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Exception;

class WalletChangeRequestHandler implements NotificationHandlerInterface {
    /**
     * Gestisce l'azione di notifica secondo l'interfaccia standard.
     *
     * @param string $action L'azione da eseguire
     * @param Model $payload Il payload della notifica
     * @param array $data Dati aggiuntivi
     * @return array Risposta con stato di successo e messaggio
     *
     * @throws Exception Se lo status non è valido o se si verifica un errore nell'invio
     */
    public function handle(string $action, Model $payload, array $data = []): array {
        try {
            // Estrae l'utente destinatario dai dati
            $message_to = $data['user'] ?? null;
            if (!$message_to instanceof User) {
                throw new Exception('User destinatario richiesto nei dati');
            }

            // Estrae i dati di notifica se presenti, altrimenti usa il payload
            $walletChange = $data['notification_data'] ?? $payload;
            $reason = $data['reason'] ?? null;

            // Determina lo status dal payload
            $status = $payload->status ?? $walletChange['status'] ?? null;

            if ($status === 'pending') {
                // Invio della notifica iniziale
                Notification::send($message_to, new WalletCreation($walletChange));
            } elseif ($status === 'approved') {
                // Logica per accettare
                Notification::send($message_to, new WalletAccepted($walletChange));
            } elseif ($status === 'rejected') {
                // Logica per declinare
                Notification::send($message_to, new WalletRejection($walletChange));
            } else {
                throw new Exception("Status '{$status}' non supportato per WalletChangeRequest.");
            }

            return [
                'success' => true,
                'message' => 'Notifica wallet change request inviata con successo'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Errore invio notifica: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Ottiene le azioni supportate da questo handler.
     *
     * @return array Lista delle azioni supportate
     */
    public function getSupportedActions(): array {
        return [
            'send_wallet_change_request',
            'approve_wallet_change',
            'reject_wallet_change'
        ];
    }
}
