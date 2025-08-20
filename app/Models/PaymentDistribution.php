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
class PaymentDistribution extends Model {
    use HasFactory;

    /**
     * The table associated with the model
     * @var string
     */
    protected $table = 'payment_distributions';

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
    public function reservation(): BelongsTo {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Get the collection that owns this distribution
     * @return BelongsTo
     */
    public function collection(): BelongsTo {
        return $this->belongsTo(Collection::class);
    }

    /**
     * Get the user that receives this distribution
     * @return BelongsTo
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    // ===== SCOPES FOR ANALYTICS =====

    /**
     * Scope for specific user type
     * @param Builder $query
     * @param UserTypeEnum $userType
     * @return Builder
     */
    public function scopeByUserType(Builder $query, UserTypeEnum $userType): Builder {
        return $query->where('user_type', $userType);
    }

    /**
     * Scope for specific collection
     * @param Builder $query
     * @param int $collectionId
     * @return Builder
     */
    public function scopeByCollection(Builder $query, int $collectionId): Builder {
        return $query->where('collection_id', $collectionId);
    }

    /**
     * Scope for EPP distributions only
     * @param Builder $query
     * @return Builder
     */
    public function scopeIsEPP(Builder $query): Builder {
        return $query->where('is_epp', true);
    }

    /**
     * Scope for non-EPP distributions
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotEPP(Builder $query): Builder {
        return $query->where('is_epp', false);
    }

    /**
     * Scope for specific status
     * @param Builder $query
     * @param DistributionStatusEnum $status
     * @return Builder
     */
    public function scopeByStatus(Builder $query, DistributionStatusEnum $status): Builder {
        return $query->where('distribution_status', $status);
    }

    /**
     * Scope for date range
     * @param Builder $query
     * @param string $startDate
     * @param string $endDate
     * @return Builder
     */
    public function scopeByDateRange(Builder $query, string $startDate, string $endDate): Builder {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // ===== BUSINESS LOGIC METHODS =====

    /**
     * Calculate total distributions for a reservation
     * @param int $reservationId
     * @return float
     */
    public static function getTotalForReservation(int $reservationId): float {
        return static::where('reservation_id', $reservationId)->sum('amount_eur');
    }

    /**
     * Get percentage total for a reservation (should be 100%)
     * @param int $reservationId
     * @return float
     */
    public static function getPercentageTotalForReservation(int $reservationId): float {
        return static::where('reservation_id', $reservationId)->sum('percentage');
    }

    /**
     * Get EPP impact for a collection
     * @param int $collectionId
     * @return float
     */
    public static function getEppImpactForCollection(int $collectionId): float {
        return static::where('collection_id', $collectionId)
            ->where('is_epp', true)
            ->sum('amount_eur');
    }

    /**
     * Get user earnings by type
     * @param UserTypeEnum $userType
     * @return float
     */
    public static function getUserTypeEarnings(UserTypeEnum $userType): float {
        return static::where('user_type', $userType)->sum('amount_eur');
    }

    /**
     * Check if percentages are valid for reservation
     * @param int $reservationId
     * @return bool
     */
    public static function validatePercentagesForReservation(int $reservationId): bool {
        $total = static::getPercentageTotalForReservation($reservationId);
        return abs($total - 100.00) < 0.01; // Allow for floating point precision
    }

    // ===== ACCESSOR METHODS =====

    /**
     * Get formatted amount in EUR
     * @return string
     */
    public function getFormattedAmountAttribute(): string {
        return '€ ' . number_format($this->amount_eur, 2);
    }

    /**
     * Get formatted percentage
     * @return string
     */
    public function getFormattedPercentageAttribute(): string {
        return number_format($this->percentage, 2) . '%';
    }

    /**
     * Get user type display name
     * @return string
     */
    public function getUserTypeDisplayAttribute(): string {
        return $this->user_type->getDisplayName();
    }

    /**
     * Get status display name
     * @return string
     */
    public function getStatusDisplayAttribute(): string {
        return $this->distribution_status->getDisplayName();
    }

    // ================================
    // 📊 STATISTICS METHODS
    // ================================

    /**
     * Get total number of distributions created
     * @return int
     */
    public static function getTotalDistributionsCount(): int
    {
        return static::count();
    }

    /**
     * Get total amount distributed in EUR
     * @return float
     */
    public static function getTotalAmountDistributed(): float
    {
        return static::sum('amount_eur') ?? 0.0;
    }

    /**
     * Get average distribution amount
     * @return float
     */
    public static function getAverageDistributionAmount(): float
    {
        return static::avg('amount_eur') ?? 0.0;
    }

    /**
     * Get distributions grouped by period (day/week/month)
     * @param string $period ('day', 'week', 'month')
     * @param int $limit Number of periods to return
     * @return array
     */
    public static function getDistributionsByPeriod(string $period = 'day', int $limit = 30): array
    {
        $dateFormat = match($period) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        return static::selectRaw("
                DATE_FORMAT(created_at, '{$dateFormat}') as period,
                COUNT(*) as count,
                SUM(amount_eur) as total_amount,
                AVG(amount_eur) as avg_amount
            ")
            ->groupByRaw("DATE_FORMAT(created_at, '{$dateFormat}')")
            ->orderByRaw("DATE_FORMAT(created_at, '{$dateFormat}') DESC")
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get distributions totals grouped by user type
     * @return array
     */
    public static function getTotalByUserType(): array
    {
        return static::selectRaw('
                user_type,
                COUNT(*) as count,
                SUM(amount_eur) as total_amount,
                AVG(amount_eur) as avg_amount,
                AVG(percentage) as avg_percentage,
                MIN(percentage) as min_percentage,
                MAX(percentage) as max_percentage
            ')
            ->groupBy('user_type')
            ->orderBy('total_amount', 'DESC')
            ->get()
            ->map(function ($item) {
                return [
                    'user_type' => $item->user_type->value, // Convert enum to string
                    'count' => $item->count,
                    'total_amount' => round($item->total_amount, 2),
                    'avg_amount' => round($item->avg_amount, 2),
                    'avg_percentage' => round($item->avg_percentage, 2),
                    'min_percentage' => round($item->min_percentage, 2),
                    'max_percentage' => round($item->max_percentage, 2),
                ];
            })
            ->toArray();
    }

    /**
     * Get top users that generate most distributions for EPP type
     * @param int $limit
     * @return array
     */
    public static function getTopUsersForEPP(int $limit = 10): array
    {
        return static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->join('users', 'reservations.user_id', '=', 'users.id')
            ->where('payment_distributions.user_type', UserTypeEnum::EPP)
            ->selectRaw('
                users.id as user_id,
                users.name as user_name,
                users.email as user_email,
                COUNT(payment_distributions.id) as distributions_count,
                SUM(payment_distributions.amount_eur) as total_epp_amount,
                AVG(payment_distributions.amount_eur) as avg_epp_amount,
                AVG(payment_distributions.percentage) as avg_epp_percentage
            ')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_epp_amount', 'DESC')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'user_id' => $item->user_id,
                    'user_name' => $item->user_name,
                    'user_email' => $item->user_email,
                    'distributions_count' => $item->distributions_count,
                    'total_epp_amount' => round($item->total_epp_amount, 2),
                    'avg_epp_amount' => round($item->avg_epp_amount, 2),
                    'avg_epp_percentage' => round($item->avg_epp_percentage, 2),
                ];
            })
            ->toArray();
    }

    /**
     * Get total amount distributed per collection
     * @param int $limit
     * @return array
     */
    public static function getTotalByCollection(int $limit = 20): array
    {
        return static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->join('egis', 'reservations.egi_id', '=', 'egis.id')
            ->join('collections', 'egis.collection_id', '=', 'collections.id')
            ->selectRaw('
                collections.id as collection_id,
                collections.collection_name as collection_name,
                COUNT(DISTINCT payment_distributions.reservation_id) as reservations_count,
                COUNT(payment_distributions.id) as distributions_count,
                SUM(payment_distributions.amount_eur) as total_distributed,
                AVG(payment_distributions.amount_eur) as avg_distribution,
                SUM(CASE WHEN payment_distributions.user_type = "creator" THEN payment_distributions.amount_eur ELSE 0 END) as total_to_creators,
                SUM(CASE WHEN payment_distributions.user_type = "epp" THEN payment_distributions.amount_eur ELSE 0 END) as total_to_epp,
                SUM(CASE WHEN payment_distributions.user_type = "collector" THEN payment_distributions.amount_eur ELSE 0 END) as total_to_collectors
            ')
            ->groupBy('collections.id', 'collections.collection_name')
            ->orderBy('total_distributed', 'DESC')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'collection_id' => $item->collection_id,
                    'collection_name' => $item->collection_name,
                    'reservations_count' => $item->reservations_count,
                    'distributions_count' => $item->distributions_count,
                    'total_distributed' => round($item->total_distributed, 2),
                    'avg_distribution' => round($item->avg_distribution, 2),
                    'total_to_creators' => round($item->total_to_creators, 2),
                    'total_to_epp' => round($item->total_to_epp, 2),
                    'total_to_collectors' => round($item->total_to_collectors, 2),
                ];
            })
            ->toArray();
    }

    /**
     * Get most profitable collections
     * @param int $limit
     * @return array
     */
    public static function getMostProfitableCollections(int $limit = 10): array
    {
        return static::getTotalByCollection($limit);
    }

    /**
     * Get ROI per collection (simplified calculation)
     * @param int $limit
     * @return array
     */
    public static function getCollectionROI(int $limit = 10): array
    {
        return static::join('reservations', 'payment_distributions.reservation_id', '=', 'reservations.id')
            ->join('egis', 'reservations.egi_id', '=', 'egis.id')
            ->join('collections', 'egis.collection_id', '=', 'collections.id')
            ->selectRaw('
                collections.id as collection_id,
                collections.collection_name as collection_name,
                collections.floor_price as floor_price,
                COUNT(DISTINCT payment_distributions.reservation_id) as reservations_count,
                SUM(payment_distributions.amount_eur) as total_distributed,
                SUM(reservations.amount_eur) as total_reservations_value,
                (SUM(payment_distributions.amount_eur) / NULLIF(collections.floor_price, 0)) * 100 as roi_percentage
            ')
            ->groupBy('collections.id', 'collections.collection_name', 'collections.floor_price')
            ->having('collections.floor_price', '>', 0)
            ->orderBy('roi_percentage', 'DESC')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'collection_id' => $item->collection_id,
                    'collection_name' => $item->collection_name,
                    'floor_price' => round($item->floor_price, 2),
                    'reservations_count' => $item->reservations_count,
                    'total_distributed' => round($item->total_distributed, 2),
                    'total_reservations_value' => round($item->total_reservations_value, 2),
                    'roi_percentage' => round($item->roi_percentage, 2),
                ];
            })
            ->toArray();
    }

    /**
     * Get comprehensive statistics dashboard
     * @return array
     */
    public static function getDashboardStats(): array
    {
        return [
            'overview' => [
                'total_distributions' => static::getTotalDistributionsCount(),
                'total_amount_distributed' => static::getTotalAmountDistributed(),
                'average_distribution' => static::getAverageDistributionAmount(),
            ],
            'by_user_type' => static::getTotalByUserType(),
            'recent_activity' => static::getDistributionsByPeriod('day', 7),
            'top_collections' => static::getMostProfitableCollections(5),
            'top_epp_generators' => static::getTopUsersForEPP(5),
        ];
    }
}
