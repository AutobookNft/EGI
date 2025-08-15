<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Reservation Notifications)
 * @date 2025-08-15
 * @purpose Payload model for reservation-related notifications
 *
 * @property int $id
 * @property int $reservation_id
 * @property int $egi_id
 * @property int $user_id
 * @property string $type
 * @property string $status
 * @property array $data
 * @property string|null $message
 * @property \Carbon\Carbon|null $read_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read Reservation $reservation
 * @property-read Egi $egi
 * @property-read User $user
 * @property-read CustomDatabaseNotification|null $notification
 */
class NotificationPayloadReservation extends Model
{
    /**
     * Table name
     */
    protected $table = 'notification_payload_reservations';

    /**
     * Notification types
     */
    const TYPE_RESERVATION_EXPIRED = 'reservation_expired';
    const TYPE_SUPERSEDED = 'superseded';
    const TYPE_HIGHEST = 'highest';
    const TYPE_RANK_CHANGED = 'rank_changed';
    const TYPE_COMPETITOR_WITHDREW = 'competitor_withdrew';
    const TYPE_PRE_LAUNCH_REMINDER = 'pre_launch_reminder';
    const TYPE_MINT_WINDOW_OPEN = 'mint_window_open';
    const TYPE_MINT_WINDOW_CLOSING = 'mint_window_closing';

    /**
     * Status values
     */
    const STATUS_INFO = 'info';
    const STATUS_SUCCESS = 'success';
    const STATUS_WARNING = 'warning';
    const STATUS_ERROR = 'error';
    const STATUS_PENDING = 'pending';

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'reservation_id',
        'egi_id',
        'user_id',
        'type',
        'status',
        'data',
        'message',
        'read_at',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Get the associated reservation
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get the associated EGI
     */
    public function egi(): BelongsTo
    {
        return $this->belongsTo(Egi::class);
    }

    /**
     * Get the user who receives this notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the notification record (polymorphic)
     */
    public function notification(): MorphOne
    {
        return $this->morphOne(CustomDatabaseNotification::class, 'model');
    }

    /**
     * Mark as read
     */
    public function markAsRead(): bool
    {
        if ($this->read_at) {
            return true;
        }

        $this->read_at = now();
        return $this->save();
    }

    /**
     * Check if read
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Get the view name for rendering
     */
    public function getView(): string
    {
        return 'reservations.' . str_replace('_', '-', $this->type);
    }

    /**
     * Get formatted message
     */
    public function getFormattedMessage(): string
    {
        if ($this->message) {
            return $this->message;
        }

        return $this->getDefaultMessage();
    }

    /**
     * Get default message based on type
     */
    protected function getDefaultMessage(): string
    {
        $egiTitle = $this->data['egi_title'] ?? 'EGI #' . $this->egi_id;
        $amount = $this->data['amount_eur'] ?? 0;

        return match($this->type) {
            self::TYPE_RESERVATION_EXPIRED =>
                "La tua prenotazione di €{$amount} per {$egiTitle} è scaduta.",

            self::TYPE_SUPERSEDED =>
                "La tua offerta per {$egiTitle} è stata superata. Nuova offerta più alta: €" .
                ($this->data['new_highest_amount'] ?? $amount),

            self::TYPE_HIGHEST =>
                "Congratulazioni! La tua offerta di €{$amount} per {$egiTitle} è ora la più alta!",

            self::TYPE_RANK_CHANGED =>
                "La tua posizione per {$egiTitle} è cambiata: sei ora in posizione #" .
                ($this->data['new_rank'] ?? '?'),

            self::TYPE_COMPETITOR_WITHDREW =>
                "Un concorrente si è ritirato. Sei salito in posizione #" .
                ($this->data['new_rank'] ?? '?') . " per {$egiTitle}",

            self::TYPE_PRE_LAUNCH_REMINDER =>
                "Il mint on-chain inizierà presto! Conferma la tua prenotazione per {$egiTitle}.",

            self::TYPE_MINT_WINDOW_OPEN =>
                "È il tuo turno! Hai 48 ore per completare il mint di {$egiTitle}.",

            self::TYPE_MINT_WINDOW_CLOSING =>
                "Attenzione! Restano solo " . ($this->data['hours_remaining'] ?? 24) .
                " ore per completare il mint di {$egiTitle}.",

            default => "Aggiornamento sulla tua prenotazione per {$egiTitle}"
        };
    }

    /**
     * Get icon for notification type
     */
    public function getIcon(): string
    {
        return match($this->type) {
            self::TYPE_HIGHEST => 'trophy',
            self::TYPE_SUPERSEDED => 'trending-down',
            self::TYPE_RANK_CHANGED => 'activity',
            self::TYPE_COMPETITOR_WITHDREW => 'user-minus',
            self::TYPE_RESERVATION_EXPIRED => 'clock',
            self::TYPE_PRE_LAUNCH_REMINDER => 'bell',
            self::TYPE_MINT_WINDOW_OPEN => 'unlock',
            self::TYPE_MINT_WINDOW_CLOSING => 'alert-triangle',
            default => 'info'
        };
    }

    /**
     * Get color for notification
     */
    public function getColor(): string
    {
        return match($this->status) {
            self::STATUS_SUCCESS => 'green',
            self::STATUS_WARNING => 'yellow',
            self::STATUS_ERROR => 'red',
            self::STATUS_PENDING => 'blue',
            default => 'gray'
        };
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for user notifications
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get data for notification service
     */
    public function toNotificationArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'status' => $this->status,
            'reservation_id' => $this->reservation_id,
            'egi_id' => $this->egi_id,
            'user_id' => $this->user_id,
            'message' => $this->getFormattedMessage(),
            'data' => $this->data,
            'icon' => $this->getIcon(),
            'color' => $this->getColor(),
            'view' => $this->getView(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
