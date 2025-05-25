<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Privacy Policy Acceptance Model
 * 🎯 Purpose: Track and audit privacy policy acceptances
 * 🧱 Core Logic: GDPR transparency and consent documentation
 * 📡 API: Read-heavy for compliance reporting
 * 🛡️ GDPR: Article 12-14 information and transparency compliance
 *
 * @package App\Models
 * @version 1.0
 */
class PrivacyPolicyAcceptance extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'privacy_policy_acceptances';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'privacy_policy_id',
        'accepted_at',
        'acceptance_method',
        'acceptance_type',
        'policy_version',
        'policy_summary',
        'changes_highlighted',
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'session_data',
        'explicit_checkbox',
        'read_full_policy',
        'time_spent_reading',
        'interaction_evidence',
        'was_notified',
        'notification_sent_at',
        'notification_method',
        'notification_acknowledged_at',
        'withdrawn_at',
        'withdrawal_method',
        'withdrawal_reason',
        'current_acceptance',
        'legal_basis',
        'compliance_notes',
        'requires_new_consent',
        'acceptance_hash',
        'verification_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'accepted_at' => 'datetime',
        'notification_sent_at' => 'datetime',
        'notification_acknowledged_at' => 'datetime',
        'withdrawn_at' => 'datetime',
        'changes_highlighted' => 'array',
        'session_data' => 'array',
        'interaction_evidence' => 'array',
        'verification_data' => 'array',
        'explicit_checkbox' => 'boolean',
        'read_full_policy' => 'boolean',
        'was_notified' => 'boolean',
        'current_acceptance' => 'boolean',
        'requires_new_consent' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Acceptance method options
     *
     * @var array<string>
     */
    public const ACCEPTANCE_METHODS = [
        'registration' => 'registration',
        'update_prompt' => 'update_prompt',
        'forced_update' => 'forced_update',
        'voluntary' => 'voluntary',
        'api' => 'api',
    ];

    /**
     * Acceptance type options
     *
     * @var array<string>
     */
    public const ACCEPTANCE_TYPES = [
        'initial' => 'initial',
        'update' => 'update',
        'renewal' => 'renewal',
        'explicit_consent' => 'explicit_consent',
        'implied_consent' => 'implied_consent',
    ];

    /**
     * Notification method options
     *
     * @var array<string>
     */
    public const NOTIFICATION_METHODS = [
        'email' => 'email',
        'in_app' => 'in_app',
        'popup' => 'popup',
        'sms' => 'sms',
        'banner' => 'banner',
    ];

    /**
     * Get the user who accepted the policy.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the privacy policy that was accepted.
     *
     * @return BelongsTo
     */
    public function privacyPolicy(): BelongsTo
    {
        return $this->belongsTo(PrivacyPolicy::class);
    }

    /**
     * Check if this acceptance is still current.
     *
     * @return bool
     */
    public function isCurrent(): bool
    {
        return $this->current_acceptance && !$this->withdrawn_at;
    }

    /**
     * Check if acceptance was explicit (checkbox + reading).
     *
     * @return bool
     */
    public function isExplicitAcceptance(): bool
    {
        return $this->explicit_checkbox &&
               ($this->read_full_policy || $this->time_spent_reading > 30);
    }

    /**
     * Check if user was properly notified of changes.
     *
     * @return bool
     */
    public function wasProperlyNotified(): bool
    {
        if ($this->acceptance_type === 'initial') {
            return true; // No notification needed for initial acceptance
        }

        return $this->was_notified &&
               $this->notification_sent_at &&
               $this->notification_acknowledged_at;
    }

    /**
     * Withdraw this acceptance.
     *
     * @param string $method
     * @param string|null $reason
     * @return bool
     */
    public function withdraw(string $method, ?string $reason = null): bool
    {
        $this->withdrawn_at = now();
        $this->withdrawal_method = $method;
        $this->withdrawal_reason = $reason;
        $this->current_acceptance = false;

        return $this->save();
    }

    /**
     * Generate integrity hash for this acceptance.
     *
     * @return string
     */
    public function generateHash(): string
    {
        $data = [
            'user_id' => $this->user_id,
            'privacy_policy_id' => $this->privacy_policy_id,
            'accepted_at' => $this->accepted_at?->toISOString(),
            'policy_version' => $this->policy_version,
            'acceptance_method' => $this->acceptance_method,
            'ip_address' => $this->ip_address,
        ];

        return hash('sha256', json_encode($data, JSON_SORT_KEYS));
    }

    /**
     * Verify integrity of this acceptance record.
     *
     * @return bool
     */
    public function verifyIntegrity(): bool
    {
        if (!$this->acceptance_hash) {
            return false;
        }

        return hash_equals($this->acceptance_hash, $this->generateHash());
    }

    /**
     * Boot method to automatically generate hash.
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->acceptance_hash = $model->generateHash();
        });
    }

    /**
     * Get reading engagement score (0-100).
     *
     * @return int
     */
    public function getEngagementScore(): int
    {
        $score = 0;

        // Base score for explicit checkbox
        if ($this->explicit_checkbox) {
            $score += 30;
        }

        // Score for reading full policy
        if ($this->read_full_policy) {
            $score += 40;
        }

        // Score based on time spent reading
        if ($this->time_spent_reading) {
            $timeScore = min(30, ($this->time_spent_reading / 120) * 30); // Max 30 points for 2+ minutes
            $score += $timeScore;
        }

        return min(100, intval($score));
    }

    /**
     * Scope for current acceptances.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrent($query)
    {
        return $query->where('current_acceptance', true)
            ->whereNull('withdrawn_at');
    }

    /**
     * Scope for withdrawn acceptances.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithdrawn($query)
    {
        return $query->whereNotNull('withdrawn_at');
    }

    /**
     * Scope for explicit acceptances.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExplicit($query)
    {
        return $query->where('explicit_checkbox', true);
    }

    /**
     * Scope for acceptances by method.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $method
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByMethod($query, string $method)
    {
        return $query->where('acceptance_method', $method);
    }

    /**
     * Scope for acceptances by type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('acceptance_type', $type);
    }

    /**
     * Scope for acceptances requiring new consent.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequiresNewConsent($query)
    {
        return $query->where('requires_new_consent', true);
    }
}
