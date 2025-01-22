<?php

namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;

use Illuminate\Support\Facades\Log;

/**
 * Class NotificationHandlerFactory
 *
 * Questa classe è responsabile della creazione di istanze di handler
 * per la gestione delle notifiche. Ogni tipo di notifica è associato
 * a un handler specifico che implementa l'interfaccia NotificationHandlerInterface.
 *
 * Funzionalità principali:
 * - Mappa i tipi di notifica ai rispettivi handler.
 * - Restituisce l'istanza appropriata dell'handler per gestire una notifica.
 *
 * @package App\Services\Notifications
 */
class NotificationHandlerFactory
{
    /**
     * Restituisce l'istanza dell'handler per il tipo di notifica specificato.
     *
     * @param string $type Il tipo di notifica (classe completa con namespace).
     * @return NotificationHandlerInterface L'istanza dell'handler appropriato.
     * @throws \Exception Se il tipo di notifica non è mappato a un handler.
     */
    public static function getHandler(string $type): NotificationHandlerInterface
    {
        // Mappa i tipi di notifica ai rispettivi handler.

        Log::channel('florenceegi')->info('NotificationHandlerFactory', [
            'type' => $type,
        ]);


        $handlers = [
            // Handler per le notifiche di gestione delle modifiche ai wallet.
            'App\Notifications\WalletChangeResponseRejection' => WalletChangeRequestHandler::class,
            'App\Notifications\WalletChangeResponseApproval' => WalletChangeRequestHandler::class,
            'App\Notifications\WalletChangeRequestCreation' => WalletChangeRequestHandler::class,
            'App\Notifications\WalletChangeRequestUpdate' => WalletChangeRequestHandler::class,

            // Handler per la notifica di proposta di invito a una collezione.
            'App\Services\Notifications\InvitationNotificationHandler' => InvitationNotificationHandler::class,
        ];

        // Verifica se il tipo di notifica è supportato.
        if (!isset($handlers[$type])) {
            // Se non supportato, lancia un'eccezione con un messaggio descrittivo.
            throw new \Exception("Gestore per il tipo '{$type}' non trovato.");
        }

        // Restituisce l'istanza dell'handler appropriato utilizzando la funzione app().
        return app($handlers[$type]);
    }
}

