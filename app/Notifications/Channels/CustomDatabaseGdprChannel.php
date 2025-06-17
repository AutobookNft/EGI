<?php

declare(strict_types=1);

namespace App\Notifications\Channels;

use App\Enums\Gdpr\GdprNotificationStatus;
use App\Models\CustomDatabaseNotificationGdpr;
use App\Models\GdprNotificationPayload;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Throwable;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @package   App\Notifications\Channels
 * @author    Padmin D. Curtis (for Fabio Cherici)
 * @version   1.2.0
 * @date      2025-06-14
 * @solution  Custom notification channel for atomic persistence of GDPR notifications with comprehensive logging and error handling.
 *
 * --- OS1 DOCUMENTATION ---
 * @oracode-intent: To provide atomic, transactional persistence of GDPR notifications, ensuring data consistency between payload and notification records while maintaining full observability and robust error handling.
 * @oracode-value-flow:
 * 1.  INPUT: Receives a notifiable entity and a structured GDPR notification with payload data.
 * 2.  PROCESS: Extracts notification data, creates payload and notification records within a single database transaction, with comprehensive logging throughout.
 * 3.  OUTPUT: Returns the created notification record on success, or delegates error handling to UEM on failure.
 * @oracode-arch-pattern: Channel Pattern with Transaction Boundary. Implements Laravel's notification channel interface while ensuring ACID compliance for related data persistence.
 * @oracode-transparency-level: HIGH. All operations are logged via ULM with detailed context, and all failures are comprehensively handled via UEM.
 * @oracode-sustainability-factor: HIGH. Atomic operations prevent data inconsistency, while structured logging enables effective debugging and audit trails.
 * @os1-compliance: Full. Demonstrates Explicitly Intentional transactional design, Simplicity Empowerment (clear single responsibility), Semantic Consistency (naming and data flow), and Proactive Security (atomic operations and comprehensive error handling).
 *
 * 🎯 **CustomDatabaseGdprChannel - OS1.5 Compliant Persistence Layer**
 *
 * 🧱 **Core Logic:** This channel implements the final persistence layer for GDPR notifications,
 * ensuring that both the payload data and notification metadata are stored atomically.
 * Following OS1.5 principles, it maintains single responsibility (persistence only) while providing
 * full transparency through comprehensive logging and robust error handling.
 *
 * 📡 **Communicates With:**
 * - `UltraLogManager`: For detailed operation logging and audit trail creation
 * - `ErrorManagerInterface`: For centralized error handling and user feedback
 * - `NotificationPayloadGdpr`: For structured payload data persistence
 * - `CustomDatabaseNotificationGdpr`: For notification metadata persistence
 *
 * 🧪 **Testability:** Fully testable through dependency injection. Database operations can be tested
 * with transactions, while ULM and UEM can be mocked for isolated unit testing.
 *
 * 🛡️ **Security Considerations:** Uses database transactions to ensure ACID compliance,
 * logs all operations for audit purposes, and safely handles sensitive payload data
 * through structured models with appropriate fillable attributes.
 */
class CustomDatabaseGdprChannel
{
    /**
     * @param UltraLogManager $logger The centralized logging service for operation tracking.
     * @param ErrorManagerInterface $errorManager The centralized error management service for robust failure handling.
     */
    public function __construct(
        protected UltraLogManager $logger,
        protected ErrorManagerInterface $errorManager
    ) {}

    /**
     * Send the notification via custom database channel with atomic persistence.
     *
     * 🎯 **Method Intent:** Provides atomic persistence of GDPR notification data,
     * ensuring consistency between payload and notification records while maintaining
     * full observability through comprehensive logging.
     *
     * @param mixed $notifiable The entity that should receive the notification.
     * @param Notification $notification The notification instance to be persisted.
     * @return CustomDatabaseNotificationGdpr|null The created notification record on success, null on failure.
     */
    public function send($notifiable, Notification $notification): ?CustomDatabaseNotificationGdpr
    {

        $this->logger->info('Starting GDPR notification persistence.', [
            'notifiable_type' => $notifiable->getMorphClass(),
            'notifiable_id' => $notifiable->getKey(),
            'notification_class' => get_class($notification),
        ]);

        try {
            // Extract structured data from notification with OS1.5 Semantic Consistency
            $data = $notification->toCustomDatabaseGdpr($notifiable);

            $this->logger->info('Starting GDPR notification persistence.', [
                'data_keys' => array_keys($data),
            ]);

            // OS1.5 Explicitly Intentional: Atomic persistence with transaction boundary
            return DB::transaction(function () use ($notifiable, $notification, $data) {

                // 1. Create payload record with structured data
                $createPayload = GdprNotificationPayload::create([
                    'gdpr_notification_type' => $data['gdpr_notification_type'] ?? null,
                    'previous_value' => $data['payload']['previous_value'] ?? null,
                    'new_value' => $data['payload']['new_value'] ?? null,
                    'email' => $data['payload']['email'] ?? $notifiable->getEmail(),
                    'role' => $data['payload']['role'] ?? null,
                    'message' => $data['payload']['message'] ?? null,
                    'ip_address' => $data['payload']['ip_address'] ?? null,
                    'user_agent' => $data['payload']['user_agent'] ?? null,
                    'payload_status' => $data['payload']['payload_status'] ?? GdprNotificationStatus::PENDING_USER_CONFIRMATION->value,
                ]);

                $this->logger->info('GDPR payload created successfully.', [
                    'payload_id' => $createPayload->id,
                    'notification_id' => $notification->id,
                    'payload_status' => $createPayload->payload_status->value ?? 'unknown',
                ]);

                // 2. Create notification record with payload reference
                $createdNotification = CustomDatabaseNotificationGdpr::create([
                    'id' => $notification->id,
                    'type' => $data['type'],
                    'notifiable_type' => $notifiable->getMorphClass(),
                    'notifiable_id' => $notifiable->getKey(),
                    'model_type' => get_class($createPayload),
                    'model_id' => $createPayload->id,
                    'view' => config("notification-views.gdpr.{$data['type']}.view", 'default'),
                    'outcome' => $data['outcome'] ?? null,
                ]);

                $this->logger->info('GDPR notification persisted successfully.', [
                    'notification_id' => $createdNotification->id,
                    'notification_type' => $createdNotification->type,
                    'payload_id' => $createPayload->id,
                    'transaction_completed' => true,
                ]);

                return $createdNotification;
            });

        } catch (Throwable $e) {
            // OS1.5 Proactive Security: Comprehensive error handling with context preservation
            $this->errorManager->handle('GDPR_NOTIFICATION_PERSISTENCE_FAILED', [
                'error_message' => $e->getMessage(),
                'notification_id' => $notification->id ?? 'unknown',
                'notification_class' => get_class($notification),
            ], $e);

            // Return null to indicate failure - caller can handle appropriately
            return $e instanceof CustomDatabaseNotificationGdpr
                ? $e
                : null; // If the exception is a CustomDatabaseNotificationGdpr, return it; otherwise, return null.
        }
    }
}
