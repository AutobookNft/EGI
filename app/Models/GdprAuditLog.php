<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use const JSON_SORT_KEYS;

/**
 * @Oracode GDPR Audit Log Model
 * 🎯 Purpose: Immutable audit trail for GDPR Article 30 compliance
 * 🧱 Core Logic: Records all GDPR-related activities with tamper-proof logging
 * 📡 API: Read-only model for audit compliance
 * 🛡️ GDPR: Critical for Article 30 Records of Processing Activities
 *
 * @package App\Models
 * @version 1.0
 */
class GdprAuditLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gdpr_audit_logs';

    /**
     * The attributes that are mass assignable.
     * Note: This model is immutable after creation
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action_type',
        'category',
        'description',
        'legal_basis',
        'data_subject_id',
        'data_controller',
        'data_processor',
        'purpose_of_processing',
        'data_categories',
        'recipient_categories',
        'international_transfers',
        'retention_period',
        'security_measures',
        'context_data',
        'ip_address',
        'user_agent',
        'checksum',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_categories' => 'array',
        'recipient_categories' => 'array',
        'international_transfers' => 'array',
        'security_measures' => 'array',
        'context_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * GDPR action types
     *
     * @var array<string>
     */
    public const ACTION_TYPES = [
        'DATA_COLLECTION' => 'data_collection',
        'DATA_PROCESSING' => 'data_processing',
        'DATA_ACCESS' => 'data_access',
        'DATA_RECTIFICATION' => 'data_rectification',
        'DATA_ERASURE' => 'data_erasure',
        'DATA_PORTABILITY' => 'data_portability',
        'DATA_RESTRICTION' => 'data_restriction',
        'CONSENT_GIVEN' => 'consent_given',
        'CONSENT_WITHDRAWN' => 'consent_withdrawn',
        'LEGITIMATE_INTEREST' => 'legitimate_interest',
        'BREACH_DETECTED' => 'breach_detected',
        'BREACH_REPORTED' => 'breach_reported',
        'DPO_CONSULTATION' => 'dpo_consultation',
        'SUPERVISORY_AUTHORITY' => 'supervisory_authority',
        'IMPACT_ASSESSMENT' => 'impact_assessment',
    ];

    /**
     * GDPR audit categories
     *
     * @var array<string>
     */
    public const CATEGORIES = [
        'CONSENT_MANAGEMENT' => 'consent_management',
        'DATA_SUBJECT_RIGHTS' => 'data_subject_rights',
        'PROCESSING_ACTIVITIES' => 'processing_activities',
        'SECURITY_INCIDENTS' => 'security_incidents',
        'BREACH_MANAGEMENT' => 'breach_management',
        'COMPLIANCE_MONITORING' => 'compliance_monitoring',
        'THIRD_PARTY_SHARING' => 'third_party_sharing',
        'INTERNATIONAL_TRANSFERS' => 'international_transfers',
        'RETENTION_MANAGEMENT' => 'retention_management',
        'PRIVACY_BY_DESIGN' => 'privacy_by_design',
    ];

    /**
     * Legal basis for processing (GDPR Article 6)
     *
     * @var array<string>
     */
    public const LEGAL_BASIS = [
        'CONSENT' => 'consent',
        'CONTRACT' => 'contract',
        'LEGAL_OBLIGATION' => 'legal_obligation',
        'VITAL_INTERESTS' => 'vital_interests',
        'PUBLIC_TASK' => 'public_task',
        'LEGITIMATE_INTERESTS' => 'legitimate_interests',
    ];

    /**
     * Data categories as per GDPR
     *
     * @var array<string>
     */
    public const DATA_CATEGORIES = [
        'PERSONAL_DETAILS' => 'personal_details',
        'CONTACT_INFORMATION' => 'contact_information',
        'FINANCIAL_DATA' => 'financial_data',
        'BIOMETRIC_DATA' => 'biometric_data',
        'HEALTH_DATA' => 'health_data',
        'BEHAVIORAL_DATA' => 'behavioral_data',
        'LOCATION_DATA' => 'location_data',
        'EMPLOYMENT_DATA' => 'employment_data',
        'EDUCATION_DATA' => 'education_data',
        'SPECIAL_CATEGORIES' => 'special_categories',
    ];

    /**
     * Get the user who performed the action.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the data subject (may be different from user).
     *
     * @return BelongsTo
     */
    public function dataSubject(): BelongsTo
    {
        return $this->belongsTo(User::class, 'data_subject_id');
    }

    /**
     * Prevent updates to maintain immutability
     *
     * @param array $attributes
     * @param array $options
     * @return bool
     */
    public function update(array $attributes = [], array $options = []): bool
    {
        // Immutable model - no updates allowed
        return false;
    }

    /**
     * Prevent deletion to maintain audit trail
     *
     * @return bool|null
     */
    public function delete(): ?bool
    {
        // Immutable model - no deletion allowed
        return false;
    }

    /**
     * Generate integrity checksum for tamper detection
     *
     * @return string
     */
    public function generateChecksum(): string
    {
        $data = [
            'user_id' => $this->user_id,
            'action_type' => $this->action_type,
            'category' => $this->category,
            'description' => $this->description,
            'legal_basis' => $this->legal_basis,
            'data_subject_id' => $this->data_subject_id,
            'created_at' => $this->created_at?->toISOString(),
        ];

        return hash('sha256', json_encode($data));
    }

    /**
     * Verify integrity of the audit log entry
     *
     * @return bool
     */
    public function verifyIntegrity(): bool
    {
        if (!$this->checksum) {
            return false;
        }

        return hash_equals($this->checksum, $this->generateChecksum());
    }

    /**
     * Boot method to automatically generate checksum
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ip_address = $model->ip_address ?? request()->ip();
            $model->user_agent = $model->user_agent ?? request()->userAgent();
            $model->checksum = $model->generateChecksum();
        });
    }

    /**
     * Check if this log entry indicates a high-risk activity
     *
     * @return bool
     */
    public function isHighRiskActivity(): bool
    {
        return in_array($this->action_type, [
            self::ACTION_TYPES['DATA_ERASURE'],
            self::ACTION_TYPES['BREACH_DETECTED'],
            self::ACTION_TYPES['BREACH_REPORTED'],
            self::ACTION_TYPES['SUPERVISORY_AUTHORITY'],
        ]) || in_array($this->category, [
            self::CATEGORIES['SECURITY_INCIDENTS'],
            self::CATEGORIES['BREACH_MANAGEMENT'],
            self::CATEGORIES['INTERNATIONAL_TRANSFERS'],
        ]);
    }

    /**
     * Check if log entry involves special category data
     *
     * @return bool
     */
    public function involvesSpecialCategoryData(): bool
    {
        $specialCategories = [
            self::DATA_CATEGORIES['BIOMETRIC_DATA'],
            self::DATA_CATEGORIES['HEALTH_DATA'],
            self::DATA_CATEGORIES['SPECIAL_CATEGORIES'],
        ];

        if (!is_array($this->data_categories)) {
            return false;
        }

        return !empty(array_intersect($this->data_categories, $specialCategories));
    }

    /**
     * Scope for specific action types
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $actionType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActionType($query, string $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Scope for specific categories
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for high-risk activities
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHighRisk($query)
    {
        return $query->whereIn('action_type', [
            self::ACTION_TYPES['DATA_ERASURE'],
            self::ACTION_TYPES['BREACH_DETECTED'],
            self::ACTION_TYPES['BREACH_REPORTED'],
            self::ACTION_TYPES['SUPERVISORY_AUTHORITY'],
        ])->orWhereIn('category', [
            self::CATEGORIES['SECURITY_INCIDENTS'],
            self::CATEGORIES['BREACH_MANAGEMENT'],
            self::CATEGORIES['INTERNATIONAL_TRANSFERS'],
        ]);
    }

    /**
     * Scope for integrity verification
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithIntegrityIssues($query)
    {
        return $query->whereRaw('checksum != ?', [
            DB::raw("SHA2(CONCAT(user_id, action_type, category, description, legal_basis, data_subject_id, created_at), 256)")
        ]);
    }
}
