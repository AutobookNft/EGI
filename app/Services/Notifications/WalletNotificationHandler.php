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
    WalletRejection
};
use Exception;
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
class WalletNotificationHandler implements NotificationHandlerInterface
{
    /**
     * Gestisce l'invio di una notifica wallet all'utente specificato.
     *
     * @param User                      $message_to   Destinatario della notifica
     * @param NotificationDataInterface $notification Dati della notifica
     *
     * @throws Exception Se lo stato della notifica non è valido o se si verifica un errore nell'invio
     */
    public function handle(User $message_to, NotificationDataInterface $notification): void
    {
        try {
            $action = NotificationStatus::fromDatabase($notification->getStatus());

            Log::channel('florenceegi')->info('Invio notifica wallet', [
                'action' => $action->value,
                'user_id' => $message_to->id,
                'notification_type' => $notification::class
            ]);

            $this->sendNotification($message_to, $notification, $action);

        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Errore invio notifica wallet', [
                'message' => $e->getMessage(),
                'user_id' => $message_to->id,
                'notification_data' => $notification
            ]);
            throw $e;
        }
    }

    /**
     * Invia la notifica appropriata in base allo stato.
     *
     * @param User               $message_to   Destinatario della notifica
     * @param NotificationStatus $action       Stato/azione della notifica
     *
     * @throws Exception Se lo stato della notifica non è valido
     */
    private function sendNotification(
        User $message_to,
        NotificationDataInterface $notification,
        NotificationStatus $action
    ): void {
        $notificationClass = match($action) {
            NotificationStatus::PENDING_CREATE => WalletCreation::class,
            NotificationStatus::PENDING_UPDATE => WalletUpdate::class,
            NotificationStatus::ACCEPTED => WalletAccepted::class,
            NotificationStatus::REJECTED => WalletRejection::class,
            default => throw new Exception("Stato notifica non valido: {$action->value}")
        };

        Notification::send($message_to, new $notificationClass($notification));
    }
}
