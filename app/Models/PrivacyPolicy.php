<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @Oracode Privacy Policy Model
 * 🎯 Purpose: Manage versioned privacy policies and terms of service
 * 🧱 Core Logic: Version control for legal documents with user consent tracking
 * 📡 API: Read-only for users, full CRUD for admins
 * 🛡️ GDPR: Article 12-14 transparency and information requirements
 *
 * @package App\Models
 * @version 1.0
 */
class PrivacyPolicy extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'privacy_policies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'version',
        'title',
        'content',
        'summary',
        'document_type',
        'language',
        'status',
        'effective_date',
        'expiry_date',
        'created_by',
        'approved_by',
        'approval_date',
        'legal_review_status',
        'legal_reviewer',
        'review_notes',
        'change_description',
        'previous_version_id',
        'notification_sent',
        'notification_date',
        'requires_consent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'effective_date' => 'datetime',
        'expiry_date' => 'datetime',
        'approval_date' => 'datetime',
        'notification_date' => 'datetime',
        'notification_sent' => 'boolean',
        'requires_consent' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Document types
     *
     * @var array<string>
     */
    public const DOCUMENT_TYPES = [
        'PRIVACY_POLICY' => 'privacy_policy',
        'TERMS_OF_SERVICE' => 'terms_of_service',
        'COOKIE_POLICY' => 'cookie_policy',
        'DATA_PROCESSING_AGREEMENT' => 'data_processing_agreement',
        'CONSENT_FORM' => 'consent_form',
        'GDPR_NOTICE' => 'gdpr_notice',
        'RETENTION_POLICY' => 'retention_policy',
        'SECURITY_POLICY' => 'security_policy',
    ];

    /**
     * Document status values
     *
     * @var array<string>
     */
    public const STATUS_VALUES = [
        'DRAFT' => 'draft',
        'UNDER_REVIEW' => 'under_review',
        'APPROVED' => 'approved',
        'ACTIVE' => 'active',
        'SUPERSEDED' => 'superseded',
        'ARCHIVED' => 'archived',
        'REJECTED' => 'rejected',
    ];

    /**
     * Legal review status
     *
     * @var array<string>
     */
    public const LEGAL_REVIEW_STATUS = [
        'PENDING' => 'pending',
        'IN_PROGRESS' => 'in_progress',
        'APPROVED' => 'approved',
        'REQUIRES_CHANGES' => 'requires_changes',
        'REJECTED' => 'rejected',
    ];

    /**
     * Supported languages
     *
     * @var array<string>
     */
    public const LANGUAGES = [
        'EN' => 'en',
        'IT' => 'it',
        'FR' => 'fr',
        'DE' => 'de',
        'ES' => 'es',
    ];

    /**
     * Get the user who created this policy version.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this policy version.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the legal reviewer for this policy.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function legalReviewer()
    {
        return $this->belongsTo(User::class, 'legal_reviewer');
    }

    /**
     * Get the previous version of this policy.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function previousVersion()
    {
        return $this->belongsTo(PrivacyPolicy::class, 'previous_version_id');
    }

    /**
     * Get consent versions linked to this policy.
     *
     * @return HasMany
     */
    public function consentVersions(): HasMany
    {
        return $this->hasMany(ConsentVersion::class, 'policy_id');
    }

    /**
     * Get user consents for this policy version.
     *
     * @return HasMany
     */
    public function userConsents(): HasMany
    {
        return $this->hasMany(UserConsent::class, 'policy_version_id');
    }

    /**
     * Check if policy is currently active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_VALUES['ACTIVE'] &&
               $this->effective_date <= now() &&
               (is_null($this->expiry_date) || $this->expiry_date > now());
    }

    /**
     * Check if policy is effective
     *
     * @return bool
     */
    public function isEffective(): bool
    {
        return $this->effective_date <= now() &&
               (is_null($this->expiry_date) || $this->expiry_date > now());
    }

    /**
     * Check if policy has expired
     *
     * @return bool
     */
    public function hasExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date <= now();
    }

    /**
     * Check if legal review is complete
     *
     * @return bool
     */
    public function isLegallyApproved(): bool
    {
        return $this->legal_review_status === self::LEGAL_REVIEW_STATUS['APPROVED'];
    }

    /**
     * Activate this policy version
     *
     * @return bool
     */
    public function activate(): bool
    {
        if (!$this->isLegallyApproved()) {
            return false;
        }

        // Supersede previous active versions of same type and language
        self::where('document_type', $this->document_type)
            ->where('language', $this->language)
            ->where('status', self::STATUS_VALUES['ACTIVE'])
            ->update(['status' => self::STATUS_VALUES['SUPERSEDED']]);

        $this->status = self::STATUS_VALUES['ACTIVE'];
        $this->effective_date = $this->effective_date ?? now();

        return $this->save();
    }

    /**
     * Archive this policy version
     *
     * @return bool
     */
    public function archive(): bool
    {
        $this->status = self::STATUS_VALUES['ARCHIVED'];
        $this->expiry_date = $this->expiry_date ?? now();
        return $this->save();
    }

    /**
     * Mark notifications as sent
     *
     * @return bool
     */
    public function markNotificationSent(): bool
    {
        $this->notification_sent = true;
        $this->notification_date = now();
        return $this->save();
    }

    /**
     * Generate semantic version number
     *
     * @return string
     */
    public function generateVersion(): string
    {
        $latestVersion = self::where('document_type', $this->document_type)
            ->where('language', $this->language)
            ->orderBy('version', 'desc')
            ->first();

        if (!$latestVersion) {
            return '1.0.0';
        }

        $versionParts = explode('.', $latestVersion->version);
        $major = intval($versionParts[0] ?? 0);
        $minor = intval($versionParts[1] ?? 0);
        $patch = intval($versionParts[2] ?? 0);

        // Increment based on change significance
        if ($this->requires_consent) {
            // Major changes require new consent
            $major++;
            $minor = 0;
            $patch = 0;
        } else {
            // Minor changes
            $patch++;
        }

        return "{$major}.{$minor}.{$patch}";
    }

    /**
     * Get content excerpt for summaries
     *
     * @param int $length Maximum length
     * @return string
     */
    public function getExcerpt(int $length = 200): string
    {
        if ($this->summary) {
            return strlen($this->summary) > $length
                ? substr($this->summary, 0, $length) . '...'
                : $this->summary;
        }

        $plainContent = strip_tags($this->content);
        return strlen($plainContent) > $length
            ? substr($plainContent, 0, $length) . '...'
            : $plainContent;
    }

    /**
     * Boot method to auto-generate version
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->version) {
                $model->version = $model->generateVersion();
            }
        });
    }

    /**
     * Scope for active policies
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_VALUES['ACTIVE'])
            ->where('effective_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>', now());
            });
    }

    /**
     * Scope for specific document type
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDocumentType($query, string $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope for specific language
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $language
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Scope for legally approved policies
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLegallyApproved($query)
    {
        return $query->where('legal_review_status', self::LEGAL_REVIEW_STATUS['APPROVED']);
    }

    /**
     * Scope for policies requiring consent
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequiresConsent($query)
    {
        return $query->where('requires_consent', true);
    }
}
