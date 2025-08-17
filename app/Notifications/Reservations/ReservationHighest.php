<?php

namespace App\Notifications\Reservations;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\NotificationPayloadReservation;
use App\Notifications\Channels\CustomDatabaseChannel;

/**
 * Notification for when user becomes the highest bidder
 *
 * @package App\Notifications\Reservations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Pre-Launch Reservation System)
 * @date 2025-08-15
 * @purpose Notify user when their reservation becomes the highest
 */
class ReservationHighest extends Notification {
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
        return [
            'view' => 'reservations.highest',
            'model_type' => NotificationPayloadReservation::class,
            'model_id' => $this->payload->id,
            'sender_id' => 1, // Sistema
            'data' => [
                'type' => 'reservation_highest',
                'payload_id' => $this->payload->id,
                'payload_type' => NotificationPayloadReservation::class,
                'reservation_id' => $this->payload->reservation_id,
                'egi_id' => $this->payload->egi_id,
                'amount_eur' => $this->payload->data['amount_eur'] ?? 0,
                'egi_title' => $this->payload->data['egi_title'] ?? '',
                'message' => $this->payload->getMessage(),
                'icon' => '🏆',
                'color' => '#10B981'
            ]
        ];
    }
}
