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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\{
    Log,
    Notification
};

/**
 * Gestore per le notifiche di invito.
 */
class InvitationNotificationHandler implements NotificationHandlerInterface {
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

            Log::channel('florenceegi')->info('Invio notifica di invito', [
                'action' => $action,
                'status' => $status->value,
                'user_id' => $message_to->id,
                'payload_type' => get_class($payload)
            ]);

            $this->sendNotification($message_to, $notificationData, $status);

            return [
                'success' => true,
                'message' => 'Notifica di invito inviata con successo'
            ];
        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Errore invio notifica di invito', [
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
     * Invia la notifica appropriata in base allo stato.
     *
     * @param User $message_to Destinatario della notifica
     * @param mixed $notificationData Payload della notifica (Model o DTO)
     * @param NotificationStatus $status Stato della notifica
     *
     * @throws Exception Se lo stato della notifica non è valido
     */
    private function sendNotification(
        User $message_to,
        $notificationData,
        NotificationStatus $status
    ): void {
        $notificationClass = match ($status) {
            NotificationStatus::ACCEPTED => InvitationAccepted::class,
            NotificationStatus::REJECTED => InvitationRejection::class,
            NotificationStatus::PENDING => InvitationRequest::class,
            default => throw new Exception("Stato notifica non valido: {$status->value}")
        };

        Notification::send($message_to, new $notificationClass($notificationData));
    }

    /**
     * Ottiene le azioni supportate da questo handler.
     *
     * @return array Lista delle azioni supportate
     */
    public function getSupportedActions(): array {
        return [
            'send_invitation',
            'accept_invitation',
            'reject_invitation'
        ];
    }
}
