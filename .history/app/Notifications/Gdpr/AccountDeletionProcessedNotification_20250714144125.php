<?php

namespace App\Notifications\Gdpr;

/**
 * @package   App\Notifications\Gdpr
 * @author    Padmin D. Curtis (for Fabio Cherici)
 * @version   1.0.0
 * @date      2025-06-11
 * @solution  Represents the final notification sent after an account has been permanently deleted.
 *
 * --- OS1 DOCUMENTATION ---
 * @oracode-intent: To encapsulate the data and delivery channels for an 'account deletion processed' notification.
 * @os1-compliance: Full.
 */
class AccountDeletionProcessedNotification extends AbstractGdprNotification
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
