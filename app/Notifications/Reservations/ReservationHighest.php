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
 * @purpose Notification when a reservation becomes the highest offer
 */
class ReservationHighest extends Notification
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
        Log::channel('florenceegi')->info('ReservationHighest: Creating notification', [
            'payload_id' => $this->payload->id,
            'user_id' => $notifiable->id,
            'reservation_id' => $this->payload->reservation_id,
        ]);

        return [
            'model_type' => get_class($this->payload),
            'model_id' => $this->payload->id,
            'view' => 'notifications.reservations.highest',
            'sender_id' => 1, // System notification
            'data' => [
                'reservation_id' => $this->payload->reservation_id,
                'egi_id' => $this->payload->egi_id,
                'egi_title' => $this->payload->data['egi_title'] ?? 'EGI #' . $this->payload->egi_id,
                'amount_eur' => $this->payload->data['amount_eur'] ?? 0,
                'previous_highest' => $this->payload->data['previous_highest'] ?? null,
                'total_competitors' => $this->payload->data['total_competitors'] ?? 0,
                'message' => $this->payload->getFormattedMessage(),
                'icon' => 'trophy',
                'color' => 'green',
                'cta_text' => 'Vedi Dettagli',
                'cta_url' => route('egi.show', $this->payload->egi_id),
            ],
            'outcome' => null, // This is informative, no response needed
        ];
    }
}
