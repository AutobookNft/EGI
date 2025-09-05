<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;
use App\Enums\NotificationHandlerType;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * Class NotificationHandlerFactory
 *
 * Factory per la creazione di handler di notifiche.
 * Gestisce la creazione degli handler appropriati in base al tipo di notifica.
 *
 * @package App\Services\Notifications
 */
class NotificationHandlerFactory
{
    /**
     * Crea e restituisce l'handler appropriato per il tipo di notifica specificato.
     *
     * @param NotificationHandlerType $type Il tipo di notifica da gestire
     * @return NotificationHandlerInterface L'handler istanziato
     *
     * @throws Exception Se l'handler richiesto non esiste o non implementa l'interfaccia corretta
     */
    public static function getHandler(NotificationHandlerType $type): NotificationHandlerInterface
    {
        $handlerClass = $type->getHandlerClass();

        try {
            if (!class_exists($handlerClass)) {
                throw new Exception("Handler non trovato: {$handlerClass}");
            }

            if (!is_subclass_of($handlerClass, NotificationHandlerInterface::class)) {
                throw new Exception("La classe {$handlerClass} non implementa NotificationHandlerInterface");
            }

            return app($handlerClass);
        
        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Errore creazione handler', [
                'type' => $type->value,
                'handler_class' => $handlerClass,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}