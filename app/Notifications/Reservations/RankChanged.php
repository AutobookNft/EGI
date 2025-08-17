<?php

namespace App\Notifications\Reservations;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\NotificationPayloadReservation;
use App\Notifications\Channels\CustomDatabaseChannel;

/**
 * Notification for when user's rank changes significantly
 *
 * @package App\Notifications\Reservations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Pre-Launch Reservation System)
 * @date 2025-08-15
 * @purpose Notify user when their reservation rank changes significantly
 */
class RankChanged extends Notification {
    use Queueable;

    /**
     * The notification payload
     *
     * @var NotificationPayloadReservation
     */
    protected NotificationPayloadReservation $payload;

    /**
     * Create a new notification instance.
     *
     * @param NotificationPayloadReservation $payload
     */
    public function __construct(NotificationPayloadReservation $payload) {
        $this->payload = $payload;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array {
        return [CustomDatabaseChannel::class];
    }

    /**
     * Get the data for the custom database channel.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toCustomDatabase($notifiable): array {
        $improved = $this->payload->data['direction'] === 'up';

        return [
            'view' => 'reservations.rank-changed',
            'model_type' => NotificationPayloadReservation::class,
            'model_id' => $this->payload->id,
            'sender_id' => 1, // Sistema
            'data' => [
                'type' => 'rank_changed',
                'payload_id' => $this->payload->id,
                'payload_type' => NotificationPayloadReservation::class,
                'reservation_id' => $this->payload->reservation_id,
                'egi_id' => $this->payload->egi_id,
                'previous_rank' => $this->payload->data['previous_rank'] ?? 0,
                'new_rank' => $this->payload->data['new_rank'] ?? 0,
                'direction' => $this->payload->data['direction'] ?? 'unknown',
                'positions_changed' => $this->payload->data['positions_changed'] ?? 0,
                'egi_title' => $this->payload->data['egi_title'] ?? '',
                'message' => $this->payload->getMessage(),
                'icon' => $improved ? '📈' : '📉',
                'color' => $improved ? '#10B981' : '#EF4444'
            ]
        ];
    }
}
