<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Security Event Model
 * 🎯 Purpose: Track security-related events for GDPR Article 33 breach notifications
 * 🧱 Core Logic: Immutable security event logging with automatic risk assessment
 * 📡 API: Read-only model for security audit trail
 * 🛡️ GDPR: Critical for breach notification compliance
 *
 * @package App\Models
 * @version 1.0
 */
class SecurityEvent extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'security_events';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'event_type',
        'severity',
        'source_ip',
        'user_agent',
        'description',
        'context_data',
        'risk_score',
        'requires_notification',
        'notified_at',
        'resolved_at',
        'resolution_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'context_data' => 'array',
        'risk_score' => 'integer',
        'requires_notification' => 'boolean',
        'notified_at' => 'datetime',
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Security event types enumeration
     *
     * @var array<string>
     */
    public const EVENT_TYPES = [
        'LOGIN_FAILURE' => 'login_failure',
        'LOGIN_SUCCESS' => 'login_success',
        'PASSWORD_CHANGE' => 'password_change',
        'DATA_ACCESS' => 'data_access',
        'DATA_EXPORT' => 'data_export',
        'DATA_DELETION' => 'data_deletion',
        'UNAUTHORIZED_ACCESS' => 'unauthorized_access',
        'SUSPICIOUS_ACTIVITY' => 'suspicious_activity',
        'DATA_BREACH' => 'data_breach',
        'MALWARE_DETECTED' => 'malware_detected',
        'SQL_INJECTION' => 'sql_injection',
        'XSS_ATTEMPT' => 'xss_attempt',
        'CSRF_ATTACK' => 'csrf_attack',
        'RATE_LIMIT_EXCEEDED' => 'rate_limit_exceeded',
    ];

    /**
     * Security severity levels
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
     * Get the user that owns the security event.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this event requires breach notification
     *
     * @return bool
     */
    public function requiresBreachNotification(): bool
    {
        return $this->requires_notification &&
               in_array($this->severity, [self::SEVERITY_LEVELS['HIGH'], self::SEVERITY_LEVELS['CRITICAL']]);
    }

    /**
     * Check if event is resolved
     *
     * @return bool
     */
    public function isResolved(): bool
    {
        return !is_null($this->resolved_at);
    }

    /**
     * Mark event as resolved
     *
     * @param string|null $notes Resolution notes
     * @return bool
     */
    public function markAsResolved(?string $notes = null): bool
    {
        $this->resolved_at = now();
        if ($notes) {
            $this->resolution_notes = $notes;
        }
        return $this->save();
    }

    /**
     * Calculate automatic risk score based on event type and context
     *
     * @return int Risk score from 1-100
     */
    public function calculateRiskScore(): int
    {
        $baseScores = [
            self::EVENT_TYPES['DATA_BREACH'] => 90,
            self::EVENT_TYPES['UNAUTHORIZED_ACCESS'] => 80,
            self::EVENT_TYPES['SQL_INJECTION'] => 85,
            self::EVENT_TYPES['XSS_ATTEMPT'] => 70,
            self::EVENT_TYPES['MALWARE_DETECTED'] => 95,
            self::EVENT_TYPES['DATA_DELETION'] => 75,
            self::EVENT_TYPES['DATA_EXPORT'] => 60,
            self::EVENT_TYPES['SUSPICIOUS_ACTIVITY'] => 50,
            self::EVENT_TYPES['LOGIN_FAILURE'] => 30,
            self::EVENT_TYPES['RATE_LIMIT_EXCEEDED'] => 40,
        ];

        $baseScore = $baseScores[$this->event_type] ?? 25;

        // Adjust based on severity
        $severityMultiplier = match($this->severity) {
            self::SEVERITY_LEVELS['CRITICAL'] => 1.2,
            self::SEVERITY_LEVELS['HIGH'] => 1.1,
            self::SEVERITY_LEVELS['MEDIUM'] => 1.0,
            self::SEVERITY_LEVELS['LOW'] => 0.8,
            default => 1.0,
        };

        return min(100, intval($baseScore * $severityMultiplier));
    }

    /**
     * Scope for unresolved events
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnresolved($query)
    {
        return $query->whereNull('resolved_at');
    }

    /**
     * Scope for high-risk events
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHighRisk($query)
    {
        return $query->where('risk_score', '>=', 70);
    }

    /**
     * Scope for breach notification required
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRequiresNotification($query)
    {
        return $query->where('requires_notification', true);
    }
}
