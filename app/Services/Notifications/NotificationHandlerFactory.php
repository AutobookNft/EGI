<?php

namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;

class NotificationHandlerFactory
{
    public static function getHandler(string $type): NotificationHandlerInterface
    {
        $handlers = [
            'App\Notifications\WalletChangeRequest' => WalletChangeRequestHandler::class,
            'App\Notifications\InvitationNotification' => InvitationNotificationHandler::class,
        ];

        if (!isset($handlers[$type])) {
            throw new \Exception("Gestore per il tipo '{$type}' non trovato.");
        }

        return app($handlers[$type]);
    }
}
