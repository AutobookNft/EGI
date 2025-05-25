<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * @Oracode Data Retention Policy Model
 * 🎯 Purpose: Manage automated data retention and deletion policies
 * 🧱 Core Logic: GDPR Article 5(e) storage limitation implementation
 * 📡 API: Configuration for automated data lifecycle management
 * 🛡️ GDPR: Data minimization and retention compliance
 *
 * @package App\Models
 * @version 1.0
 */
class DataRetentionPolicy extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'data_retention_policies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'data_category',
        'data_type',
        'applicable_tables',
        'applicable_fields',
        'retention_trigger',
        'retention_days',
        'retention_period',
        'grace_period_days',
        'deletion_method',
        'anonymization_rules',
        'deletion_exceptions',
        'legal_basis',
        'legal_justification',
        'regulatory_requirements',
        'user_can_request_deletion',
        'requires_admin_approval',
        'notify_user_before_deletion',
        'notification_days_before',
        'is_automated',
        'execution_schedule',
        'execution_time',
        'batch_size',
        'is_active',
        'last_executed_at',
        'last_execution_count',
        'execution_log',
        'policy_effective_date',
        'policy_review_date',
        'created_by',
        'approved_by',
        'approved_at',
        'risk_level',
        'risk_assessment',
        'mitigation_measures',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'applicable_tables' => 'array',
        'applicable_fields' => 'array',
        'anonymization_rules' => 'array',
        'deletion_exceptions' => 'array',
        'regulatory_requirements' => 'array',
        'execution_log' => 'array',
        'mitigation_measures' => 'array',
        'user_can_request_deletion' => 'boolean',
        'requires_admin_approval' => 'boolean',
        'notify_user_before_deletion' => 'boolean',
        'is_automated' => 'boolean',
        'is_active' => 'boolean',
        'last_executed_at' => 'datetime',
        'policy_effective_date' => 'datetime',
        'policy_review_date' => 'datetime',
        'approved_at' => 'datetime',
        'execution_time' => 'datetime:H:i:s',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Retention trigger options
     *
     * @var array<string>
     */
    public const RETENTION_TRIGGERS = [
        'time_based' => 'time_based',
        'inactivity_based' => 'inactivity_based',
        'consent_withdrawal' => 'consent_withdrawal',
        'account_closure' => 'account_closure',
        'legal_basis_ends' => 'legal_basis_ends',
        'custom_event' => 'custom_event',
    ];

    /**
     * Deletion method options
     *
     * @var array<string>
     */
    public const DELETION_METHODS = [
        'hard_delete' => 'hard_delete',
        'soft_delete' => 'soft_delete',
        'anonymize' => 'anonymize',
        'pseudonymize' => 'pseudonymize',
        'archive' => 'archive',
    ];

    /**
     * Execution schedule options
     *
     * @var array<string>
     */
    public const EXECUTION_SCHEDULES = [
        'daily' => 'daily',
        'weekly' => 'weekly',
        'monthly' => 'monthly',
    ];

    /**
     * Risk level options
     *
     * @var array<string>
     */
    public const RISK_LEVELS = [
        'low' => 'low',
        'medium' => 'medium',
        'high' => 'high',
        'critical' => 'critical',
    ];

    /**
     * Get the user who created this policy.
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this policy.
     *
     * @return BelongsTo
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if policy is ready for execution.
     *
     * @return bool
     */
    public function isReadyForExecution(): bool
    {
        if (!$this->is_active || !$this->is_automated) {
            return false;
        }

        if (!$this->approved_at) {
            return false;
        }

        if ($this->policy_effective_date && $this->policy_effective_date->isFuture()) {
            return false;
        }

        return $this->shouldExecuteToday();
    }

    /**
     * Check if policy should execute today based on schedule.
     *
     * @return bool
     */
    public function shouldExecuteToday(): bool
    {
        $now = now();
        $lastExecution = $this->last_executed_at;

        if (!$lastExecution) {
            return true; // Never executed, should run
        }

        return match($this->execution_schedule) {
            'daily' => $lastExecution->isYesterday() || $lastExecution->isBefore($now->subDay()),
            'weekly' => $lastExecution->isBefore($now->subWeek()),
            'monthly' => $lastExecution->isBefore($now->subMonth()),
            default => false,
        };
    }

    /**
     * Calculate deletion date for a given record creation date.
     *
     * @param Carbon $createdAt
     * @param Carbon|null $lastActivity
     * @return Carbon|null
     */
    public function calculateDeletionDate(Carbon $createdAt, ?Carbon $lastActivity = null): ?Carbon
    {
        if (!$this->retention_days) {
            return null;
        }

        $baseDate = match($this->retention_trigger) {
            'time_based' => $createdAt,
            'inactivity_based' => $lastActivity ?? $createdAt,
            default => $createdAt,
        };

        return $baseDate->addDays($this->retention_days + $this->grace_period_days);
    }

    /**
     * Check if this policy requires user notification.
     *
     * @return bool
     */
    public function requiresUserNotification(): bool
    {
        return $this->notify_user_before_deletion && $this->notification_days_before > 0;
    }

    /**
     * Get notification date for deletion.
     *
     * @param Carbon $deletionDate
     * @return Carbon
     */
    public function getNotificationDate(Carbon $deletionDate): Carbon
    {
        return $deletionDate->subDays($this->notification_days_before);
    }

    /**
     * Mark policy as executed.
     *
     * @param int $recordsProcessed
     * @param array $executionDetails
     * @return void
     */
    public function markAsExecuted(int $recordsProcessed, array $executionDetails = []): void
    {
        $this->update([
            'last_executed_at' => now(),
            'last_execution_count' => $recordsProcessed,
            'execution_log' => array_merge($this->execution_log ?? [], [
                'timestamp' => now()->toISOString(),
                'records_processed' => $recordsProcessed,
                'details' => $executionDetails,
            ]),
        ]);
    }

    /**
     * Check if policy needs review.
     *
     * @return bool
     */
    public function needsReview(): bool
    {
        if (!$this->policy_review_date) {
            return true; // No review date set
        }

        return $this->policy_review_date->isPast();
    }

    /**
     * Scope for active policies.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for automated policies.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAutomated($query)
    {
        return $query->where('is_automated', true);
    }

    /**
     * Scope for approved policies.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    /**
     * Scope for policies ready for execution.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReadyForExecution($query)
    {
        return $query->active()
            ->automated()
            ->approved()
            ->where('policy_effective_date', '<=', now());
    }

    /**
     * Scope for policies by data category.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDataCategory($query, string $category)
    {
        return $query->where('data_category', $category);
    }

    /**
     * Scope for policies needing review.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNeedsReview($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('policy_review_date')
              ->orWhere('policy_review_date', '<', now());
        });
    }
}
