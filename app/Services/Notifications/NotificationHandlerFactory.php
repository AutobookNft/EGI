<?php

namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;
use Illuminate\Support\Facades\Log;

class NotificationHandlerFactory
{
    public static function getHandler(string $type): NotificationHandlerInterface
    {
        $handlers = [
            'App\Notifications\WalletChangeResponseRejection' => WalletChangeRequestHandler::class,
            'App\Notifications\WalletChangeResponseApproval' => WalletChangeRequestHandler::class,
            'App\Notifications\WalletChangeRequestCreation' => WalletChangeRequestHandler::class,
            'App\Notifications\WalletChangeRequestUpdate' => WalletChangeRequestHandler::class,
            'App\Notifications\InvitationNotification' => InvitationNotificationHandler::class,
        ];

        if (!isset($handlers[$type])) {
            throw new \Exception("Gestore per il tipo '{$type}' non trovato.");
        }


        return app($handlers[$type]);
    }
}
