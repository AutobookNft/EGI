<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Contracts\MultiChannelNotificationHandlerInterface;
use App\Enums\NotificationHandlerType;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * @package   App\Services\Notifications
 * @author    Padmin D. Curtis (for Fabio Cherici)
 * @version   1.0.0
 * @date      2025-06-11
 * @solution  A specialized factory for creating notification handlers that support dynamic, multi-channel dispatching.
 *
 * --- OS1 DOCUMENTATION ---
 * @oracode-intent: To provide a dedicated instantiation mechanism for handlers that implement the MultiChannelNotificationHandlerInterface, leaving the original factory untouched to ensure backward compatibility and system stability.
 * @oracode-arch-pattern: Factory Pattern (Specialized). This avoids modifying a legacy factory, reducing regression risk.
 * @oracode-sustainability-factor: HIGH. Protects existing code from breaking changes while allowing new features to use a more advanced architecture.
 * @os1-compliance: Full.
 */
class MultiChannelHandlerFactory
{
    /**
     * Creates and returns a handler that supports multi-channel dispatch.
     *
     * @param NotificationHandlerType $type The type of notification to handle.
     * @return MultiChannelNotificationHandlerInterface The instantiated handler.
     *
     * @throws Exception If the handler class does not exist or does not implement the required multi-channel interface.
     */
    public static function getHandler(NotificationHandlerType $type): MultiChannelNotificationHandlerInterface
    {
        $handlerClass = $type->getHandlerClass();

        try {
            if (!class_exists($handlerClass)) {
                throw new Exception("Handler not found: {$handlerClass}");
            }

            // CONTROLLO SPECIFICO: Validiamo SOLO contro la nuova interfaccia multi-canale.
            if (!is_subclass_of($handlerClass, MultiChannelNotificationHandlerInterface::class)) {
                throw new Exception("The class {$handlerClass} does not implement the required MultiChannelNotificationHandlerInterface.");
            }

            /** @var MultiChannelNotificationHandlerInterface $handler */
            $handler = app($handlerClass);

            return $handler;

        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Error creating multi-channel notification handler', [
                'type' => $type->value,
                'handler_class' => $handlerClass,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
