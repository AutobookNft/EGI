<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Breach Report Model
 * 🎯 Purpose: Handle user-reported security breaches and incidents
 * 🧱 Core Logic: Track breach reports from users with investigation workflow
 * 📡 API: Full CRUD for breach reporting system
 * 🛡️ GDPR: Article 33 breach notification support
 *
 * @package App\Models
 * @version 1.0
 */
class BreachReport extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'breach_reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'report_type',
        'severity',
        'title',
        'description',
        'affected_data',
        'incident_datetime',
        'discovery_datetime',
        'additional_info',
        'contact_email',
        'contact_phone',
        'status',
        'priority',
        'assigned_to',
        'investigation_notes',
        'resolution',
        'closed_at',
        'notified_authorities',
        'authority_reference',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'affected_data' => 'array',
        'additional_info' => 'array',
        'incident_datetime' => 'datetime',
        'discovery_datetime' => 'datetime',
        'closed_at' => 'datetime',
        'notified_authorities' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Breach report types
     *
     * @var array<string>
     */
    public const REPORT_TYPES = [
        'PERSONAL_DATA_BREACH' => 'personal_data_breach',
        'UNAUTHORIZED_ACCESS' => 'unauthorized_access',
        'DATA_LOSS' => 'data_loss',
        'SYSTEM_COMPROMISE' => 'system_compromise',
        'PHISHING_ATTACK' => 'phishing_attack',
        'MALWARE_INFECTION' => 'malware_infection',
        'SOCIAL_ENGINEERING' => 'social_engineering',
        'INSIDER_THREAT' => 'insider_threat',
        'GDPR_VIOLATION' => 'gdpr_violation',
        'OTHER' => 'other',
    ];

    /**
     * Severity levels
     *
     * @var array<string>
     */
    public const SEVERITY_LEVELS = [
        'LOW' => 'low',
        'MEDIUM' => 'medium',
        'HIGH' => 'high',
        'CRITICAL' => 'critical',
    ];

    /**
     * Report status values
     *
     * @var array<string>
     */
    public const STATUS_VALUES = [
        'OPEN' => 'open',
        'IN_PROGRESS' => 'in_progress',
        'UNDER_INVESTIGATION' => 'under_investigation',
        'PENDING_VERIFICATION' => 'pending_verification',
        'RESOLVED' => 'resolved',
        'CLOSED' => 'closed',
        'ESCALATED' => 'escalated',
        'DUPLICATE' => 'duplicate',
        'INVALID' => 'invalid',
    ];

    /**
     * Priority levels
     *
     * @var array<string>
     */
    public const PRIORITY_LEVELS = [
        'LOW' => 'low',
        'MEDIUM' => 'medium',
        'HIGH' => 'high',
        'URGENT' => 'urgent',
    ];

    /**
     * Get the user who submitted the breach report.
     *
     * @return BelongsTo
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user assigned to investigate this report.
     *
     * @return BelongsTo
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Check if report is open/active
     *
     * @return bool
     */
    public function isOpen(): bool
    {
        return in_array($this->status, [
            self::STATUS_VALUES['OPEN'],
            self::STATUS_VALUES['IN_PROGRESS'],
            self::STATUS_VALUES['UNDER_INVESTIGATION'],
            self::STATUS_VALUES['PENDING_VERIFICATION'],
            self::STATUS_VALUES['ESCALATED']
        ]);
    }

    /**
     * Check if report is closed
     *
     * @return bool
     */
    public function isClosed(): bool
    {
        return in_array($this->status, [
            self::STATUS_VALUES['RESOLVED'],
            self::STATUS_VALUES['CLOSED'],
            self::STATUS_VALUES['DUPLICATE'],
            self::STATUS_VALUES['INVALID']
        ]);
    }

    /**
     * Check if this breach requires authority notification (72h rule)
     *
     * @return bool
     */
    public function requiresAuthorityNotification(): bool
    {
        return in_array($this->severity, [
            self::SEVERITY_LEVELS['HIGH'],
            self::SEVERITY_LEVELS['CRITICAL']
        ]) && in_array($this->report_type, [
            self::REPORT_TYPES['PERSONAL_DATA_BREACH'],
            self::REPORT_TYPES['UNAUTHORIZED_ACCESS'],
            self::REPORT_TYPES['DATA_LOSS'],
            self::REPORT_TYPES['SYSTEM_COMPROMISE'],
            self::REPORT_TYPES['GDPR_VIOLATION']
        ]);
    }

    /**
     * Check if 72-hour notification deadline is approaching
     *
     * @return bool
     */
    public function isNotificationDeadlineApproaching(): bool
    {
        if (!$this->requiresAuthorityNotification() || $this->notified_authorities) {
            return false;
        }

        $deadline = $this->discovery_datetime->addHours(72);
        $hoursRemaining = now()->diffInHours($deadline, false);

        return $hoursRemaining <= 24 && $hoursRemaining > 0;
    }

    /**
     * Check if notification deadline has passed
     *
     * @return bool
     */
    public function isNotificationOverdue(): bool
    {
        if (!$this->requiresAuthorityNotification() || $this->notified_authorities) {
            return false;
        }

        $deadline = $this->discovery_datetime->addHours(72);
        return now()->isAfter($deadline);
    }

    /**
     * Close the breach report
     *
     * @param string $resolution Resolution description
     * @return bool
     */
    public function close(string $resolution): bool
    {
        $this->status = self::STATUS_VALUES['CLOSED'];
        $this->resolution = $resolution;
        $this->closed_at = now();
        return $this->save();
    }

    /**
     * Mark authorities as notified
     *
     * @param string|null $reference Authority reference number
     * @return bool
     */
    public function markAuthoritiesNotified(?string $reference = null): bool
    {
        $this->notified_authorities = true;
        if ($reference) {
            $this->authority_reference = $reference;
        }
        return $this->save();
    }

    /**
     * Generate unique report identifier
     *
     * @return string
     */
    public function getReportIdentifierAttribute(): string
    {
        return 'BR-' . $this->created_at->format('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Scope for open reports
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', [
            self::STATUS_VALUES['OPEN'],
            self::STATUS_VALUES['IN_PROGRESS'],
            self::STATUS_VALUES['UNDER_INVESTIGATION'],
            self::STATUS_VALUES['PENDING_VERIFICATION'],
            self::STATUS_VALUES['ESCALATED']
        ]);
    }

    /**
     * Scope for high priority reports
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', [
            self::PRIORITY_LEVELS['HIGH'],
            self::PRIORITY_LEVELS['URGENT']
        ]);
    }

    /**
     * Scope for reports requiring authority notification
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequiresNotification($query)
    {
        return $query->whereIn('severity', [
            self::SEVERITY_LEVELS['HIGH'],
            self::SEVERITY_LEVELS['CRITICAL']
        ])->whereIn('report_type', [
            self::REPORT_TYPES['PERSONAL_DATA_BREACH'],
            self::REPORT_TYPES['UNAUTHORIZED_ACCESS'],
            self::REPORT_TYPES['DATA_LOSS'],
            self::REPORT_TYPES['SYSTEM_COMPROMISE'],
            self::REPORT_TYPES['GDPR_VIOLATION']
        ]);
    }

    /**
     * Scope for overdue notifications
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverdueNotification($query)
    {
        return $query->requiresNotification()
            ->where('notified_authorities', false)
            ->where('discovery_datetime', '<', now()->subHours(72));
    }
}
