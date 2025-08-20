<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Contracts\{
    NotificationDataInterface,
    NotificationHandlerInterface,
};

use App\Enums\NotificationStatus;
use App\Models\User;
use App\Notifications\Wallets\{
    WalletAccepted,
    WalletCreation,
    WalletUpdate,
    WalletRejection,
    WalletExpiration,
};
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\{
    Log,
    Notification,
};

/**
 * Class WalletNotificationHandler
 *
 * Handler specializzato per le notifiche relative ai wallet.
 * Gestisce l'invio di notifiche specifiche per le operazioni sui wallet.
 *
 * @package App\Services\Notifications
 */
class WalletNotificationHandler implements NotificationHandlerInterface {
    /**
     * Gestisce l'azione di notifica secondo l'interfaccia standard.
     *
     * @param string $action L'azione da eseguire
     * @param Model $payload Il payload della notifica
     * @param array $data Dati aggiuntivi
     * @return array Risposta con stato di successo e messaggio
     *
     * @throws Exception Se lo stato della notifica non è valido o se si verifica un errore nell'invio
     */
    public function handle(string $action, Model $payload, array $data = []): array {
        try {
            // Estrae l'utente destinatario dai dati
            $message_to = $data['user'] ?? null;
            if (!$message_to instanceof User) {
                throw new Exception('User destinatario richiesto nei dati');
            }

            // Estrae i dati di notifica se presenti, altrimenti usa il payload
            $notificationData = $data['notification_data'] ?? $payload;

            $status = NotificationStatus::fromDatabase($payload->status);

            Log::channel('florenceegi')->info('Invio notifica wallet', [
                'action' => $action,
                'status' => $status->value,
                'user_id' => $message_to->id,
                'payload_type' => get_class($payload)
            ]);

            $this->sendNotification($message_to, $notificationData, $status);

            return [
                'success' => true,
                'message' => 'Notifica wallet inviata con successo'
            ];
        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Errore invio notifica wallet', [
                'action' => $action,
                'message' => $e->getMessage(),
                'user_id' => $message_to->id ?? 'unknown',
                'payload_data' => $payload
            ]);

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
            'send_wallet_creation',
            'send_wallet_update',
            'accept_wallet',
            'reject_wallet',
            'expire_wallet'
        ];
    }

    /**
     * Invia la notifica appropriata in base allo stato.
     *
     * @param User $message_to Destinatario della notifica
     * @param mixed $notificationData Payload della notifica (Model o DTO)
     * @param NotificationStatus $action Stato/azione della notifica
     *
     * @throws Exception Se lo stato della notifica non è valido
     */
    private function sendNotification(
        User $message_to,
        $notificationData,
        NotificationStatus $action
    ): void {
        $notificationClass = match ($action) {
            NotificationStatus::PENDING_CREATE => WalletCreation::class,
            NotificationStatus::PENDING_UPDATE => WalletUpdate::class,
            NotificationStatus::ACCEPTED => WalletAccepted::class,
            NotificationStatus::REJECTED => WalletRejection::class,
            NotificationStatus::EXPIRED => WalletExpiration::class,
            default => throw new Exception("Stato notifica non valido: {$action->value}")
        };

        Notification::send($message_to, new $notificationClass($notificationData));
    }
}