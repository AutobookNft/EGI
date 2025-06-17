<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Contracts\NotificationDataInterface;
use App\Models\User;

/**
 * @package   App\Contracts\Notifications
 * @author    Padmin D. Curtis (for Fabio Cherici)
 * @version   1.0.0
 * @date      2025-06-11
 * @solution  Defines a contract for notification handlers capable of dispatching to multiple channels dynamically.
 *
 * --- OS1 DOCUMENTATION ---
 * @oracode-intent: To establish a standardized, reusable contract for any notification handler that needs to support dynamic, runtime-defined delivery channels (e.g., database, mail, Slack).
 * @oracode-arch-pattern: Interface Segregation Principle. This provides a specific, opt-in interface for multi-channel capability without polluting the generic handler interface.
 * @os1-compliance: Full.
 */
interface MultiChannelNotificationHandlerInterface
{
    /**
     * Handles the sending of a notification to a user via specified channels.
     *
     * @param User $message_to The recipient user.
     * @param NotificationDataInterface $notification The notification data DTO.
     * @param array $channels The channels to send the notification through (e.g., ['database', 'mail']).
     * @return void
     */
    public function handle(User $message_to, NotificationGdprDataInterface $notification, array $channels = ['database']): void;
}
