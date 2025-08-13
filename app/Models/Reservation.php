<?php

namespace App\Models;

// Assumo che questi use statements siano già presenti
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Eloquent Model: Reservation (Extended)
 * 🎯 Purpose: Manages reservations for EGIs with multi-currency support
 * 🧱 Core Logic: Tracks reservation status, priority, and certificate generation
 * 🛡️ GDPR: Minimizes data collection, records only what's needed for the reservation
 * 💱 Currency Logic: Think FIAT, Operate ALGO - ALGO as immutable source of truth
 *
 * @property int $id
 * @property int $user_id
 * @property int $egi_id
 * @property string $type
 * @property string $status
 * @property float $offer_amount_fiat Amount in FIAT currency (user-friendly)
 * @property string $fiat_currency FIAT currency code (EUR, USD, etc.)
 * @property int $offer_amount_algo Amount in microALGO (source of truth)
 * @property float $exchange_rate Exchange rate ALGO->FIAT at transaction time
 * @property \Illuminate\Support\Carbon $exchange_timestamp Timestamp of exchange rate
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property bool $is_current
 * @property int|null $superseded_by_id
 * @property json|null $contact_data
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Egi $egi
 * @property-read \App\Models\Reservation|null $supersededBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Reservation[] $supersededReservations
 * @property-read \App\Models\EgiReservationCertificate|null $certificate
 */
class Reservation extends Model {
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'egi_id',
        'type',
        'status',
        'offer_amount_fiat',
        'fiat_currency',
        'offer_amount_algo',
        'exchange_rate',
        'exchange_timestamp',
        'expires_at',
        'is_current',
        'superseded_by_id',
        'contact_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'exchange_timestamp' => 'datetime',
        'contact_data' => 'json',
        'offer_amount_fiat' => 'decimal:2',
        'offer_amount_algo' => 'integer', // Trattato come intero (microALGO)
        'exchange_rate' => 'decimal:8',
        'is_current' => 'boolean',
    ];

    /**
     * Define relationship to the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Define relationship to the Egi model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function egi() {
        return $this->belongsTo(Egi::class);
    }

    /**
     * Define relationship to the reservation that superseded this one.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supersededBy() {
        return $this->belongsTo(Reservation::class, 'superseded_by_id');
    }

    /**
     * Define relationship to all reservations superseded by this one.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function supersededReservations() {
        return $this->hasMany(Reservation::class, 'superseded_by_id');
    }

    /**
     * Define relationship to the certificate.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function certificate() {
        return $this->hasOne(EgiReservationCertificate::class);
    }

    /**
     * Check if this reservation is expired.
     *
     * @return bool
     */
    public function isExpired(): bool {
        // Strong reservations don't expire
        if ($this->type === 'strong') {
            return false;
        }

        // Check if expiration date exists and is in the past
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if this reservation is active.
     *
     * @return bool
     */
    public function isActive(): bool {
        return $this->status === 'active' && !$this->isExpired() && $this->is_current;
    }

    /**
     * Get the reservation priority value.
     * Higher value means higher priority.
     *
     * @return int
     */
    public function getReservationPriority(): int {
        $priority = 0;

        // Type priority: Strong always has higher base priority than weak
        $priority += ($this->type === 'strong') ? 1000 : 0;

        // Amount priority: Higher offer gets higher priority (use FIAT amount)
        $priority += (int)($this->offer_amount_fiat * 10);

        // Time priority: Earlier reservations get slightly higher priority
        $ageInDays = Carbon::now()->diffInDays($this->created_at);
        $priority += min($ageInDays, 10); // Cap at 10 days to prevent very old reservations from having too high priority

        return $priority;
    }

    /**
     * Compare priority with another reservation.
     *
     * @param Reservation $otherReservation
     * @return int Positive if this reservation has higher priority, negative if lower, 0 if equal
     */
    public function comparePriorityWith(Reservation $otherReservation): int {
        return $this->getReservationPriority() - $otherReservation->getReservationPriority();
    }

    /**
     * Check if this reservation has higher priority than another.
     *
     * @param Reservation $otherReservation
     * @return bool
     */
    public function hasHigherPriorityThan(Reservation $otherReservation): bool {
        return $this->comparePriorityWith($otherReservation) > 0;
    }

    /**
     * Mark this reservation as superseded by another.
     *
     * @param Reservation $newReservation
     * @return bool
     */
    public function markAsSuperseded(Reservation $newReservation): bool {
        $this->is_current = false;
        $this->superseded_by_id = $newReservation->id;

        // Also mark the certificate as superseded if it exists
        if ($this->certificate) {
            $this->certificate->markAsSuperseded();
        }

        return $this->save();
    }

    /**
     * Create a certificate for this reservation.
     *
     * @param array $additionalData Additional data for the certificate
     * @return \App\Models\EgiReservationCertificate
     */
    public function createCertificate(array $additionalData = []): EgiReservationCertificate {
        // Create base certificate data
        $certificateData = [
            'reservation_id' => $this->id,
            'egi_id' => $this->egi_id,
            'user_id' => $this->user_id,
            'wallet_address' => $this->user->wallet ?? $additionalData['wallet_address'] ?? '',
            'reservation_type' => $this->type,
            'offer_amount_fiat' => $this->offer_amount_fiat,
            'fiat_currency' => $this->fiat_currency,
            'offer_amount_algo' => $this->offer_amount_algo,
            'exchange_rate' => $this->exchange_rate,
            'is_current_highest' => $this->is_current,
        ];

        // Merge with additional data
        $certificateData = array_merge($certificateData, $additionalData);

        // Create and save the certificate
        $certificate = new EgiReservationCertificate($certificateData);

        // Generate signature hash
        if (!isset($certificateData['signature_hash'])) {
            $verificationData = $certificate->generateVerificationData();
            $certificate->signature_hash = hash('sha256', $verificationData);
        }

        $certificate->save();

        return $certificate;
    }

    /**
     * Scope query to only include active reservations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query) {
        return $query->where('status', 'active')
            ->where('is_current', true)
            ->where(function ($query) {
                $query->where('type', 'strong')
                    ->orWhere(function ($query) {
                        $query->where('type', 'weak')
                            ->where(function ($query) {
                                $query->whereNull('expires_at')
                                    ->orWhere('expires_at', '>', Carbon::now());
                            });
                    });
            });
    }

    /**
     * Scope query to only include weak reservations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWeak($query) {
        return $query->where('type', 'weak');
    }

    /**
     * Scope query to only include strong reservations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStrong($query) {
        return $query->where('type', 'strong');
    }

    /**
     * Scope query to only include current reservations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrent($query) {
        return $query->where('is_current', true);
    }
}
