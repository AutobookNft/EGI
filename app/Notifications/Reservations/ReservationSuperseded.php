<?php

namespace App\Notifications\Reservations;

use App\Models\NotificationPayloadReservation;
use App\Notifications\Channels\CustomDatabaseChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * @package App\Notifications\Reservations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Reservation Notifications)
 * @date 2025-08-15
 * @purpose Notification when a reservation is superseded by a higher offer
 */
class ReservationSuperseded extends Notification
{
    protected NotificationPayloadReservation $payload;

    /**
     * Create a new notification instance
     */
    public function __construct(NotificationPayloadReservation $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Get the notification's delivery channels
     */
    public function via($notifiable): array
    {
        return [CustomDatabaseChannel::class];
    }

    /**
     * Get the data for the custom database channel
     */
    public function toCustomDatabase($notifiable): array
    {
        Log::channel('florenceegi')->info('ReservationSuperseded: Creating notification', [
            'payload_id' => $this->payload->id,
            'user_id' => $notifiable->id,
            'reservation_id' => $this->payload->reservation_id,
        ]);

        return [
            'model_type' => get_class($this->payload),
            'model_id' => $this->payload->id,
            'view' => 'notifications.reservations.superseded',
            'sender_id' => 1, // System notification
            'data' => [
                'reservation_id' => $this->payload->reservation_id,
                'egi_id' => $this->payload->egi_id,
                'egi_title' => $this->payload->data['egi_title'] ?? 'EGI #' . $this->payload->egi_id,
                'previous_amount' => $this->payload->data['previous_amount'] ?? 0,
                'new_highest_amount' => $this->payload->data['new_highest_amount'] ?? 0,
                'new_rank' => $this->payload->data['new_rank'] ?? null,
                'superseded_by_user' => $this->payload->data['superseded_by_user'] ?? 'Un altro utente',
                'message' => $this->payload->getFormattedMessage(),
                'icon' => 'trending-down',
                'color' => 'yellow',
            ],
            'outcome' => null, // This is informative, no response needed
        ];
    }
}
