<?php

namespace App\Notifications\Gdpr;

/**
 * @package   App\Notifications\Gdpr
 * @author    Padmin D. Curtis (for Fabio Cherici)
 * @version   1.0.0
 * @date      2025-06-11
 * @solution  Represents the notification sent to an admin/DPO when a data breach report is submitted.
 *
 * --- OS1 DOCUMENTATION ---
 * @oracode-intent: To encapsulate the data and delivery channels for a 'breach report received' notification.
 * @os1-compliance: Full.
 */
class BreachReportReceivedNotification extends AbstractGdprNotification
{
    /**
     * Get the custom database representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toCustomDatabase($notifiable): array
    {
        return [
            'model_type' => $this->notificationData->getModelType(),
            'model_id'   => $this->notificationData->getModelId(),
            'view'       => $this->notificationData->getView(),
            'sender_id'  => $this->notificationData->getSenderId(),
            'data'       => [
                'message' => $this->notificationData->getMessage(),
                'email'   => $this->notificationData->getSenderEmail(),
            ],
            'outcome'    => $this->notificationData->getStatus(),
        ];
    }
}
