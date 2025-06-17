<?php

namespace App\Notifications\Gdpr;

use App\Contracts\NotificationGdprDataInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * @package   App\Notifications\Gdpr
 * @author    Padmin D. Curtis (for Fabio Cherici)
 * @version   1.0.0
 * @date      2025-06-11
 * @solution  Provides a base structure for all GDPR notifications, enabling dynamic channel selection.
 *
 * --- OS1 DOCUMENTATION ---
 * @oracode-intent: To centralize common logic for all GDPR notification classes, reduce code duplication, and introduce a mechanism for dynamically injecting delivery channels at runtime.
 * @oracode-arch-pattern: Abstract Base Class. It defines a common contract and shared functionality for a family of related classes.
 * @os1-compliance: Full. Embodies DRY principles and enhances Semplicità Potenziante.
 */
abstract class AbstractGdprNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The channels the notification should be sent to.
     * This property is public to be set dynamically by the handler.
     *
     * @var array
     */
    public array $channels = [];

    /**
     * Create a new notification instance.
     *
     * @param NotificationGdprDataInterface $notificationData
     */
    public function __construct(public NotificationGdprDataInterface $notificationData)
    {
    }

    /**
     * Get the notification's delivery channels.
     * Uses the dynamically set channels, with a safe fallback to 'database'.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        // If the channels array has been populated at runtime, use it.
        // Otherwise, default to the custom database channel.

        $channels = !empty($this->channels) ? $this->channels : [\App\Notifications\Channels\CustomDatabaseGdprChannel::class];

        Log::channel('florenceegi')->info('Determining channels for notification', [
            'channels' => $channels
        ]);

        return $channels;
    }

    /**
     * Defines the structure for the custom database channel.
     * This method must be implemented by concrete notification classes.
     *
     * @param mixed $notifiable
     * @return array
     */
    abstract public function toCustomDatabaseGdpr($notifiable): array;
}
