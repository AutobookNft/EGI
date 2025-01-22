<?php

namespace App\Notifications\Channels;

use App\Enums\InvitationStatus;
use App\Models\CustomDatabaseNotification;
use App\Models\Notification as ModelsNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CustomDatabaseChannel
{
    public function send($notifiable, Notification $notification)
    {
        // Recupera i dati dal metodo "toCustomDatabase()" della notifica
        $data = $notification->toCustomDatabase($notifiable);

        // Ottieni il nome della classe della notifica
        $action = get_class($notification);

        // Mappatura delle classi di notifica agli stati di InvitationStatus
        $actionMap = [
            'App\Notifications\InvitationAccepted' => InvitationStatus::ACCEPTED,
            'App\Notifications\InvitationRejection' => InvitationStatus::REJECTED,
        ];

        // Controlla se l'azione corrisponde a una chiave nella mappatura
        if (isset($actionMap[$action])) {
            $action = $actionMap[$action];
        }

        // Se l'azione è ACCEPTED o REJECTED, aggiorna la notifica precedente
        if ($action === InvitationStatus::ACCEPTED || $action === InvitationStatus::REJECTED) {
            $this->updatePreviousNotification($notification->id, $action);

            Log::channel('florenceegi')->info('CustomDatabaseChannel:send', [
                'notification' => $notification,
                'data' => $data,
            ]);
        }

        // Validazione dei dati
        if (!isset($data['view'], $data['model_type'], $data['model_id'])) {
            Log::error('Dati mancanti per la notifica', ['data' => $data]);
            return null;
        }

        // Creiamo manualmente il record nella tabella notifications
        $createdNotification = CustomDatabaseNotification::create([
            'id'             => $notification->id,
            'type'           => get_class($notification),
            'view'           => $data['view'],
            'notifiable_type'=> get_class($notifiable),
            'notifiable_id'  => $notifiable->getKey(),
            'model_type'     => $data['model_type'],
            'model_id'       => $data['model_id'],
            'data'           => $data['data'] ?? [],
            'read_at'        => $data['read_at'] ?? null,
            'outcome'        => $data['outcome'] ?? null,
        ]);

        Log::info('Notifica creata', [
            'id' => $createdNotification->id,
            'type' => $createdNotification->type,
        ]);

        return $createdNotification;
    }

    /**
     * Aggiorna il record della notifica precedente con lo stato di risposta.
     *
     * Una volta aggiornato, la notifica viene considerata archiviata.
     *
     * @param string $notificationId L'ID della notifica da aggiornare
     * @param string $outcome Lo stato di risposta (ACCEPTED o REJECTED)
     * @return void
     */
    private function updatePreviousNotification($notificationId, $outcome)
    {
        $prev_notification = ModelsNotification::where('id', $notificationId)->first();

        if (!$prev_notification) {
            Log::warning('Notifica precedente non trovata', ['id' => $notificationId]);
            return;
        }

        $prev_notification->update([
            'read_at' => now(),
            'outcome' => $outcome,
        ]);

        Log::info('Notifica precedente aggiornata', [
            'id' => $notificationId,
            'outcome' => $outcome,
        ]);
    }
}
