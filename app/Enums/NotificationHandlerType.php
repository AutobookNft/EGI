<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Enum NotificationHandlerType
 *
 * Rappresenta i tipi di handler disponibili per le notifiche.
 * Utilizzato dal NotificationHandlerFactory per istanziare l'handler corretto.
 *
 * @package App\Enums
 */
enum NotificationHandlerType: string
{
    /**
     * Handler per le notifiche relative ai wallet
     */
    case WALLET = 'wallet';

    /**
     * Handler per le notifiche di invito
     */
    case INVITATION = 'invitation';

    /**
     * Ottiene il nome della classe handler associata al tipo
     *
     * @return class-string La classe dell'handler
     */
    public function getHandlerClass(): string
    {
        return match($this) {
            self::WALLET => \App\Services\Notifications\WalletNotificationHandler::class,
            self::INVITATION => \App\Services\Notifications\InvitationNotificationHandler::class,
        };
    }
}
