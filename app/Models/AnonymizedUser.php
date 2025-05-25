<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Anonymized User Model
 * 🎯 Purpose: Track anonymized user data for statistical and compliance purposes
 * 🧱 Core Logic: GDPR Article 17 compliant data anonymization
 * 📡 API: Read-only for reporting and analytics
 * 🛡️ GDPR: Right to erasure implementation with data utility preservation
 *
 * @package App\Models
 * @version 1.0
 */
class AnonymizedUser extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'anonymized_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'original_user_id',
        'anonymization_id',
        'pseudonym',
        'anonymized_at',
        'anonymization_reason',
        'anonymization_method',
        'processed_by',
        'anonymization_steps',
        'fields_anonymized',
        'data_preserved',
        'original_registration_date',
        'original_last_login',
        'original_user_type',
        'original_subscription_level',
        'original_activity_score',
        'total_collections_created',
        'total_egis_created',
        'total_transactions',
        'total_transaction_value',
        'total_logins',
        'days_active',
        'region',
        'country_code',
        'timezone',
        'device_categories',
        'browser_families',
        'preferred_language',
        'consent_history_summary',
        'gdpr_requests_summary',
        'had_security_incidents',
        'last_privacy_policy_accepted',
        'verification_hash',
        'anonymization_verified',
        'verified_at',
        'verified_by',
        'expires_at',
        'retention_reason',
        'related_records_anonymized',
        'external_references',
        'blockchain_references_updated',
        'anonymization_quality',
        'quality_notes',
        'audit_trail',
        'is_recoverable',
        'recovery_key_hash',
        'recovery_expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'anonymized_at' => 'datetime',
        'original_registration_date' => 'date',
        'original_last_login' => 'date',
        'last_privacy_policy_accepted' => 'datetime',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'recovery_expires_at' => 'datetime',
        'anonymization_steps' => 'array',
        'fields_anonymized' => 'array',
        'data_preserved' => 'array',
        'device_categories' => 'array',
        'browser_families' => 'array',
        'consent_history_summary' => 'array',
        'gdpr_requests_summary' => 'array',
        'related_records_anonymized' => 'array',
        'external_references' => 'array',
        'audit_trail' => 'array',
        'total_transaction_value' => 'decimal:2',
        'anonymization_verified' => 'boolean',
        'had_security_incidents' => 'boolean',
        'blockchain_references_updated' => 'boolean',
        'is_recoverable' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Anonymization reason options
     *
     * @var array<string>
     */
    public const ANONYMIZATION_REASONS = [
        'user_request' => 'user_request',
        'account_closure' => 'account_closure',
        'consent_withdrawal' => 'consent_withdrawal',
        'retention_expired' => 'retention_expired',
        'legal_requirement' => 'legal_requirement',
        'admin_action' => 'admin_action',
        'automatic_cleanup' => 'automatic_cleanup',
    ];

    /**
     * Anonymization method options
     *
     * @var array<string>
     */
    public const ANONYMIZATION_METHODS = [
        'full_anonymization' => 'full_anonymization',
        'pseudonymization' => 'pseudonymization',
        'statistical_anonymization' => 'statistical_anonymization',
        'selective_anonymization' => 'selective_anonymization',
    ];

    /**
     * Anonymization quality levels
     *
     * @var array<string>
     */
    public const QUALITY_LEVELS = [
        'basic' => 'basic',
        'enhanced' => 'enhanced',
        'differential' => 'differential',
        'certified' => 'certified',
    ];

    /**
     * Retention reason options
     *
     * @var array<string>
     */
    public const RETENTION_REASONS = [
        'statistical_analysis' => 'statistical_analysis',
        'regulatory_requirement' => 'regulatory_requirement',
        'business_intelligence' => 'business_intelligence',
        'fraud_prevention' => 'fraud_prevention',
        'research_purposes' => 'research_purposes',
    ];

    /**
     * Get the user who processed the anonymization.
     *
     * @return BelongsTo
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get the user who verified the anonymization.
     *
     * @return BelongsTo
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if anonymization is verified.
     *
     * @return bool
     */
    public function isVerified(): bool
    {
        return $this->anonymization_verified && $this->verified_at !== null;
    }

    /**
     * Check if record is expired and should be deleted.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if record is recoverable.
     *
     * @return bool
     */
    public function isRecoverable(): bool
    {
        if (!$this->is_recoverable) {
            return false;
        }

        if ($this->recovery_expires_at && $this->recovery_expires_at->isPast()) {
            return false;
        }

        return $this->recovery_key_hash !== null;
    }

    /**
     * Generate verification hash for anonymization completeness.
     *
     * @return string
     */
    public function generateVerificationHash(): string
    {
        $data = [
            'anonymization_id' => $this->anonymization_id,
            'anonymized_at' => $this->anonymized_at?->toISOString(),
            'anonymization_method' => $this->anonymization_method,
            'fields_anonymized' => $this->fields_anonymized,
            'anonymization_steps' => $this->anonymization_steps,
        ];

        return hash('sha256', json_encode($data, JSON_SORT_KEYS));
    }

    /**
     * Verify anonymization integrity.
     *
     * @return bool
     */
    public function verifyAnonymization(): bool
    {
        if (!$this->verification_hash) {
            return false;
        }

        return hash_equals($this->verification_hash, $this->generateVerificationHash());
    }

    /**
     * Mark anonymization as verified.
     *
     * @param int $verifierId
     * @return bool
     */
    public function markAsVerified(int $verifierId): bool
    {
        return $this->update([
            'anonymization_verified' => true,
            'verified_at' => now(),
            'verified_by' => $verifierId,
            'verification_hash' => $this->generateVerificationHash(),
        ]);
    }

    /**
     * Get user activity summary.
     *
     * @return array
     */
    public function getActivitySummary(): array
    {
        return [
            'collections' => $this->total_collections_created,
            'egis' => $this->total_egis_created,
            'transactions' => $this->total_transactions,
            'transaction_value' => $this->total_transaction_value,
            'logins' => $this->total_logins,
            'days_active' => $this->days_active,
            'activity_score' => $this->original_activity_score,
        ];
    }

    /**
     * Get anonymization details for audit.
     *
     * @return array
     */
    public function getAnonymizationAudit(): array
    {
        return [
            'reason' => $this->anonymization_reason,
            'method' => $this->anonymization_method,
            'quality' => $this->anonymization_quality,
            'verified' => $this->isVerified(),
            'processed_at' => $this->anonymized_at,
            'processor' => $this->processor?->name,
            'verifier' => $this->verifier?->name,
            'steps_performed' => $this->anonymization_steps,
            'fields_anonymized' => $this->fields_anonymized,
        ];
    }

    /**
     * Scope for verified anonymizations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVerified($query)
    {
        return $query->where('anonymization_verified', true);
    }

    /**
     * Scope for expired records.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }

    /**
     * Scope for recoverable records.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecoverable($query)
    {
        return $query->where('is_recoverable', true)
            ->whereNotNull('recovery_key_hash')
            ->where(function ($q) {
                $q->whereNull('recovery_expires_at')
                  ->orWhere('recovery_expires_at', '>', now());
            });
    }

    /**
     * Scope by anonymization reason.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $reason
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByReason($query, string $reason)
    {
        return $query->where('anonymization_reason', $reason);
    }

    /**
     * Scope by user type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $userType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUserType($query, string $userType)
    {
        return $query->where('original_user_type', $userType);
    }

    /**
     * Scope by region.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $region
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByRegion($query, string $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Scope for statistical analysis.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForStatistics($query)
    {
        return $query->verified()
            ->whereIn('anonymization_quality', ['enhanced', 'differential', 'certified']);
    }
}
