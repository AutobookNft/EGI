<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Contracts\MultiChannelNotificationHandlerInterface;
use App\Contracts\NotificationGdprDataInterface;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Throwable;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @package   App\Services\Notifications
 * @author    Padmin D. Curtis (for Fabio Cherici)
 * @version   1.0.0
 * @date      2025-06-11
 * @solution  Provides the specific logic for dispatching various GDPR-related notifications based on configuration.
 *
 * --- OS1 DOCUMENTATION ---
 * @oracode-intent: To act as the specialized processor for all notifications of type 'gdpr', dynamically selecting and dispatching the correct notification class based on the specific GDPR event.
 * @oracode-value-flow:
 * 1.  INPUT: Receives a generic `NotificationDataInterface` DTO.
 * 2.  PROCESS: It inspects the DTO's 'view' property (e.g., 'gdpr.consent_updated'), extracts the specific event type ('consent_updated'), looks up the corresponding Notification class in `config/gdpr.php`, and instantiates it.
 * 3.  OUTPUT: Dispatches the concrete notification (e.g., `ConsentUpdatedNotification`) to the specified user via Laravel's notification facade.
 * @oracode-arch-pattern: Strategy Pattern (Concrete Strategy). It implements the `NotificationHandlerInterface` and is instantiated by the `NotificationHandlerFactory`.
 * @os1-compliance: Full.
 *
 * 🎯 **GdprNotificationHandler**
 *
 * 🧱 **Core Logic:** This handler is responsible for dispatching the correct GDPR notification. It determines the specific GDPR event type from the `view` property of the notification data and uses the `config/gdpr.php` file to find the appropriate notification class to instantiate and send.
 *
 * 📡 **Communicates With:**
 * - `GdprNotificationService`: Is called by this service.
 * - `config/gdpr.php`: Reads notification class mappings.
 * - `Illuminate\Support\Facades\Notification`: Dispatches notifications.
 * - `UltraLogManager`: For structured logging.
 * - `ErrorManagerInterface`: For centralized error handling.
 *
 * 🧪 **Testability:** Fully testable. Dependencies are injected, allowing for easy mocking of the logger, error manager, and configuration.
 */
class GdprNotificationHandler implements MultiChannelNotificationHandlerInterface
{
    /**
     * @param UltraLogManager $logger The centralized logging service.
     * @param ErrorManagerInterface $errorManager The centralized error management service.
     */
    public function __construct(
        protected UltraLogManager $logger,
        protected ErrorManagerInterface $errorManager
    ) {}

    /**
     * Handles the sending of a GDPR notification to a user.
     *
     * @param User $message_to The recipient user.
     * @param NotificationDataInterface $notification The notification data DTO.
     * @param array $channels The channels to send the notification through (e.g., ['database', 'mail']).
     * @throws Throwable
     */
    public function handle(User $message_to, NotificationGdprDataInterface $notificationData, array $channels = []): void
    {
        try {
            $gdprEventType  = $notificationData->getType(); // e.g., 'consent_updated'

            if (!$gdprEventType) {
                // This is a critical configuration or logic error. The view identifier is malformed.
                $this->errorManager->handle('GDPR_VIOLATION_ATTEMPT', [
                    'details' => 'Invalid GDPR view identifier format.',
                    'user_id' => $message_to->id,
                ], null, true); // Throw an exception
            }

            // Extract the view identifier from the notification data
            $notificationClass = config('gdpr.notifications.classes.' . $gdprEventType);


            $this->logger->info('GDPR Notification Handler: Preparing to dispatch notification.', [
                'user_id' => $message_to->id,
                'gdpr_event_type' => $gdprEventType,
                'notification_class' => $notificationClass,
            ]);

            if (!$notificationClass || !class_exists($notificationClass)) {
                // This is also a critical configuration error. The class defined in the config does not exist.
                $this->errorManager->handle('GDPR_VIOLATION_ATTEMPT', [
                    'details' => 'GDPR notification class not found or not configured.',
                    'gdpr_event_type' => $gdprEventType,
                    'configured_class' => $notificationClass,
                    'user_id' => $message_to->id
                ], null, true); // Throw an exception
            }

            /** @var AbstractGdprNotification $notificationInstance */
            $notificationInstance = new $notificationClass($notificationData);

            // ✨ L'INIEZIONE DINAMICA DEI CANALI ✨
            $notificationInstance->channels = $channels;

            $this->logger->info('Dispatching GDPR notification.', [
                'user_id' => $message_to->id,
                'event_type' => $gdprEventType,
                'notification' => $notificationData
            ]);

            // Dispatch the specific notification class
            Notification::send($message_to, new $notificationClass($notificationData));

        } catch (Throwable $e) {
            // If an exception was thrown by UEM or another part of the process, handle it.
            // We re-throw it to ensure the calling service knows the operation failed.
            $this->errorManager->handle('GDPR_NOTIFICATION_SEND_FAILED', [
                'error' => $e->getMessage(),
                'user_id' => $message_to->id,
                'notification_data' => $notificationData
            ], $e, true);
        }
    }
}