<?php

namespace App\Notifications\Channels;

use App\Models\CustomDatabaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CustomDatabaseChannel
{
    public function send($notifiable, Notification $notification)
    {
        // Recupera i dati dal metodo "toCustomDatabase()" della notifica
        $data = $notification->toCustomDatabase($notifiable);

        Log::channel('florenceegi')->info('CustomDatabaseChannel: send', [
            'notifiable' => $notifiable,
            'notification' => $notification,
            'data' => $data,
        ]);

        // Creiamo manualmente il record nella tabella notifications
        return CustomDatabaseNotification::create([
            'id'             => $notification->id,
            'type'           => get_class($notification),
            'notifiable_type'=> get_class($notifiable),
            'notifiable_id'  => $notifiable->getKey(),
            'model_type'     => $data['model_type'] ?? null,
            'model_id'       => $data['model_id']   ?? null,
            'data'           => $data['data']       ?? [],
            'read_at'        => null,
        ]);
    }
}
