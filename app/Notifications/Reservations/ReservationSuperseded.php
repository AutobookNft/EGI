<?php

namespace App\Notifications\Reservations;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\NotificationPayloadReservation;

/**
 * Notification for when user's reservation is superseded
 *
 * @package App\Notifications\Reservations
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Pre-Launch Reservation System)
 * @date 2025-08-15
 * @purpose Notify user when their reservation is no longer the highest
 */
class ReservationSuperseded extends Notification
{
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
    public function __construct(NotificationPayloadReservation $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'reservation_superseded',
            'payload_id' => $this->payload->id,
            'payload_type' => NotificationPayloadReservation::class,
            'reservation_id' => $this->payload->reservation_id,
            'egi_id' => $this->payload->egi_id,
            'amount_eur' => $this->payload->data['amount_eur'] ?? 0,
            'new_highest_amount' => $this->payload->data['new_highest_amount'] ?? 0,
            'egi_title' => $this->payload->data['egi_title'] ?? '',
            'message' => $this->payload->getMessage(),
            'icon' => '⚠️',
            'color' => '#F59E0B'
        ];
    }
}
