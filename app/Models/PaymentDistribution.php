<?php

namespace App\Models;

use App\Enums\PaymentDistribution\UserTypeEnum;
use App\Enums\PaymentDistribution\DistributionStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * @Oracode Model: Payment Distribution
 * 🎯 Purpose: Core payment distribution tracking for FlorenceEGI
 * 🛡️ Privacy: Financial distribution with GDPR compliance integration
 * 🧱 Core Logic: Percentage-based automatic distribution system
 *
 * @package App\Models
 * @author GitHub Copilot for Fabio Cherici
 * @version 1.0.0
 * @date 2025-08-20
 *
 * @property int $id
 * @property int $reservation_id
 * @property int $collection_id
 * @property int $user_id
 * @property UserTypeEnum $user_type
 * @property DistributionStatusEnum $distribution_status
 * @property float $percentage
 * @property float $amount_eur
 * @property float $exchange_rate
 * @property bool $is_epp
 * @property array $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class PaymentDistribution extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable
     * @var array<string>
     */
    protected $fillable = [
        'reservation_id',
        'collection_id',
        'user_id',
        'user_type',
        'percentage',
        'amount_eur',
        'exchange_rate',
        'is_epp',
        'metadata',
        'distribution_status',
    ];

    /**
     * The attributes that should be cast
     * @var array<string, string>
     */
    protected $casts = [
        'user_type' => UserTypeEnum::class,
        'distribution_status' => DistributionStatusEnum::class,
        'percentage' => 'decimal:2',
        'amount_eur' => 'decimal:2',
        'exchange_rate' => 'decimal:10',
        'is_epp' => 'boolean',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====

    /**
     * Get the reservation that owns this distribution
     * @return BelongsTo
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get the collection that owns this distribution
     * @return BelongsTo
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    /**
     * Get the user that receives this distribution
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ===== SCOPES FOR ANALYTICS =====

    /**
     * Scope for specific user type
     * @param Builder $query
     * @param UserTypeEnum $userType
     * @return Builder
     */
    public function scopeByUserType(Builder $query, UserTypeEnum $userType): Builder
    {
        return $query->where('user_type', $userType);
    }

    /**
     * Scope for specific collection
     * @param Builder $query
     * @param int $collectionId
     * @return Builder
     */
    public function scopeByCollection(Builder $query, int $collectionId): Builder
    {
        return $query->where('collection_id', $collectionId);
    }

    /**
     * Scope for EPP distributions only
     * @param Builder $query
     * @return Builder
     */
    public function scopeIsEPP(Builder $query): Builder
    {
        return $query->where('is_epp', true);
    }

    /**
     * Scope for non-EPP distributions
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotEPP(Builder $query): Builder
    {
        return $query->where('is_epp', false);
    }

    /**
     * Scope for specific status
     * @param Builder $query
     * @param DistributionStatusEnum $status
     * @return Builder
     */
    public function scopeByStatus(Builder $query, DistributionStatusEnum $status): Builder
    {
        return $query->where('distribution_status', $status);
    }

    /**
     * Scope for date range
     * @param Builder $query
     * @param string $startDate
     * @param string $endDate
     * @return Builder
     */
    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // ===== BUSINESS LOGIC METHODS =====

    /**
     * Calculate total distributions for a reservation
     * @param int $reservationId
     * @return float
     */
    public static function getTotalForReservation(int $reservationId): float
    {
        return static::where('reservation_id', $reservationId)->sum('amount_eur');
    }

    /**
     * Get percentage total for a reservation (should be 100%)
     * @param int $reservationId
     * @return float
     */
    public static function getPercentageTotalForReservation(int $reservationId): float
    {
        return static::where('reservation_id', $reservationId)->sum('percentage');
    }

    /**
     * Get EPP impact for a collection
     * @param int $collectionId
     * @return float
     */
    public static function getEppImpactForCollection(int $collectionId): float
    {
        return static::where('collection_id', $collectionId)
            ->where('is_epp', true)
            ->sum('amount_eur');
    }

    /**
     * Get user earnings by type
     * @param UserTypeEnum $userType
     * @return float
     */
    public static function getUserTypeEarnings(UserTypeEnum $userType): float
    {
        return static::where('user_type', $userType)->sum('amount_eur');
    }

    /**
     * Check if percentages are valid for reservation
     * @param int $reservationId
     * @return bool
     */
    public static function validatePercentagesForReservation(int $reservationId): bool
    {
        $total = static::getPercentageTotalForReservation($reservationId);
        return abs($total - 100.00) < 0.01; // Allow for floating point precision
    }

    // ===== ACCESSOR METHODS =====

    /**
     * Get formatted amount in EUR
     * @return string
     */
    public function getFormattedAmountAttribute(): string
    {
        return '€ ' . number_format($this->amount_eur, 2);
    }

    /**
     * Get formatted percentage
     * @return string
     */
    public function getFormattedPercentageAttribute(): string
    {
        return number_format($this->percentage, 2) . '%';
    }

    /**
     * Get user type display name
     * @return string
     */
    public function getUserTypeDisplayAttribute(): string
    {
        return $this->user_type->getDisplayName();
    }

    /**
     * Get status display name
     * @return string
     */
    public function getStatusDisplayAttribute(): string
    {
        return $this->distribution_status->getDisplayName();
    }
}
