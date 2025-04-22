<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;
use App\Enums\NotificationStatus;
use App\Models\User;
use App\Notifications\Invitations\{
    InvitationAccepted,
    InvitationRejection,
    InvitationRequest
};
use Exception;
use Illuminate\Support\Facades\{
    Log,
    Notification
};

/**
 * Gestore per le notifiche di invito.
 */
class InvitationNotificationHandler implements NotificationHandlerInterface
{
    /**
     * Gestisce l'invio di una notifica di invito all'utente specificato.
     *
     * @param User $message_to Destinatario della notifica
     * @param mixed $notification Dati della notifica
     *
     * @throws Exception Se lo stato della notifica non è valido o se si verifica un errore nell'invio
     */
    public function handle(User $message_to, $notification): void
    {
        try {
            $status = NotificationStatus::fromDatabase($notification->getStatus());

            Log::channel('florenceegi')->info('Invio notifica di invito', [
                'status' => $status->value,
                'user_id' => $message_to->id,
                'notification_type' => get_class($notification)
            ]);

            $this->sendNotification($message_to, $notification, $status);

        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Errore invio notifica di invito', [
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
     * @param User $message_to Destinatario della notifica
     * @param mixed $notification Dati della notifica
     * @param NotificationStatus $status Stato della notifica
     *
     * @throws Exception Se lo stato della notifica non è valido
     */
    private function sendNotification(
        User $message_to,
        $notification,
        NotificationStatus $status
    ): void {
        $notificationClass = match($status) {
            NotificationStatus::ACCEPTED => InvitationAccepted::class,
            NotificationStatus::REJECTED => InvitationRejection::class,
            NotificationStatus::PENDING => InvitationRequest::class,
            default => throw new Exception("Stato notifica non valido: {$status->value}")
        };

        Notification::send($message_to, new $notificationClass($notification));
    }
}

