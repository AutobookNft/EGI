<?php

declare(strict_types=1);

namespace App\Notifications\Gdpr;

use Illuminate\Support\Facades\Log;

/**
 * @package   App\Notifications\Gdpr
 * @author    Padmin D. Curtis (for Fabio Cherici)
 * @version   1.0.0
 * @date      2025-06-11
 * @solution  Represents the notification sent when a user's GDPR consent preferences are updated.
 *
 * --- OS1 DOCUMENTATION ---
 * @oracode-intent: To encapsulate the data and delivery channels for a 'consent updated' notification.
 * @oracode-value-flow: Instantiated by GdprNotificationHandler, it carries the NotificationData DTO to the CustomDatabaseChannel for persistence.
 * @os1-compliance: Full.
 */
class ConsentUpdatedNotification extends AbstractGdprNotification
{
    /**
     * Get the custom database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toCustomDatabaseGdpr($notifiable): array
    {
        return [
            'type'              => $this->notificationData->getType(),
            'outcome'           => $this->notificationData->getOutcome(),
            'payload'           => $this->notificationData->getPayload()->toArray(),
            'gdpr_notification_type' => static::class,
        ];
    }
}
