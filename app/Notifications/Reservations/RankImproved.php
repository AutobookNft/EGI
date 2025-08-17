<?php

namespace App\Notifications\Reservations;

use App\Notifications\Channels\CustomDatabaseChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * @package App\Notifications\Reservations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Reservation Notifications)
 * @date 2025-08-15
 * @purpose Notification when reservation rank improves
 */
class RankImproved extends Notification
{
    protected $notification;

    /**
     * Create a new notification instance
     *
     * @param mixed $notification The NotificationPayloadReservation object
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the notification's delivery channels
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class];
    }

    /**
     * Get the data for custom database channel
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toCustomDatabase($notifiable)
    {
        Log::channel('florenceegi')->info('RankImproved:toCustomDatabase', [
            'notificationPayloadReservation' => $this->notification,
        ]);

        return [
            'model_type' => get_class($this->notification),
            'model_id' => $this->notification->id,
            'view' => 'reservations.rank-improved',
            'sender_id' => 1, // System notification
            'data' => $this->notification->data,
            'outcome' => null, // This is informative only
        ];
    }
}
