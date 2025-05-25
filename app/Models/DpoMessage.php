<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode DPO Message Model
 * 🎯 Purpose: Handle communications with Data Protection Officer
 * 🧱 Core Logic: Track messages, inquiries and consultations with DPO
 * 📡 API: Full CRUD for DPO communication system
 * 🛡️ GDPR: Article 37-39 DPO consultation and communication
 *
 * @package App\Models
 * @version 1.0
 */
class DpoMessage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dpo_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'message_type',
        'subject',
        'message_content',
        'priority',
        'status',
        'category',
        'related_request_id',
        'related_breach_id',
        'attachments',
        'dpo_response',
        'response_date',
        'estimated_response_time',
        'escalated',
        'escalation_reason',
        'resolved_at',
        'satisfaction_rating',
        'internal_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attachments' => 'array',
        'response_date' => 'datetime',
        'estimated_response_time' => 'datetime',
        'escalated' => 'boolean',
        'resolved_at' => 'datetime',
        'satisfaction_rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Message types for DPO communication
     *
     * @var array<string>
     */
    public const MESSAGE_TYPES = [
        'INQUIRY' => 'inquiry',
        'COMPLAINT' => 'complaint',
        'CONSULTATION' => 'consultation',
        'BREACH_REPORT' => 'breach_report',
        'CONSENT_QUESTION' => 'consent_question',
        'DATA_REQUEST' => 'data_request',
        'PRIVACY_CONCERN' => 'privacy_concern',
        'THIRD_PARTY_ISSUE' => 'third_party_issue',
        'LEGAL_ADVICE' => 'legal_advice',
        'POLICY_CLARIFICATION' => 'policy_clarification',
        'TRAINING_REQUEST' => 'training_request',
        'OTHER' => 'other',
    ];

    /**
     * Message status values
     *
     * @var array<string>
     */
    public const STATUS_VALUES = [
        'SUBMITTED' => 'submitted',
        'ACKNOWLEDGED' => 'acknowledged',
        'IN_REVIEW' => 'in_review',
        'PENDING_INFO' => 'pending_info',
        'RESPONDED' => 'responded',
        'RESOLVED' => 'resolved',
        'CLOSED' => 'closed',
        'ESCALATED' => 'escalated',
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
     * Message categories
     *
     * @var array<string>
     */
    public const CATEGORIES = [
        'GDPR_COMPLIANCE' => 'gdpr_compliance',
        'DATA_PROTECTION' => 'data_protection',
        'PRIVACY_RIGHTS' => 'privacy_rights',
        'SECURITY_INCIDENT' => 'security_incident',
        'POLICY_GUIDANCE' => 'policy_guidance',
        'LEGAL_INTERPRETATION' => 'legal_interpretation',
        'TECHNICAL_CONSULTATION' => 'technical_consultation',
        'AUDIT_SUPPORT' => 'audit_support',
        'TRAINING_SUPPORT' => 'training_support',
        'EXTERNAL_RELATIONS' => 'external_relations',
    ];

    /**
     * Get the user who sent the message.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related GDPR request if applicable.
     *
     * @return BelongsTo
     */
    public function relatedRequest(): BelongsTo
    {
        return $this->belongsTo(GdprRequest::class, 'related_request_id');
    }

    /**
     * Get the related breach report if applicable.
     *
     * @return BelongsTo
     */
    public function relatedBreach(): BelongsTo
    {
        return $this->belongsTo(BreachReport::class, 'related_breach_id');
    }

    /**
     * Check if message is open/pending
     *
     * @return bool
     */
    public function isOpen(): bool
    {
        return in_array($this->status, [
            self::STATUS_VALUES['SUBMITTED'],
            self::STATUS_VALUES['ACKNOWLEDGED'],
            self::STATUS_VALUES['IN_REVIEW'],
            self::STATUS_VALUES['PENDING_INFO'],
            self::STATUS_VALUES['ESCALATED']
        ]);
    }

    /**
     * Check if message is closed
     *
     * @return bool
     */
    public function isClosed(): bool
    {
        return in_array($this->status, [
            self::STATUS_VALUES['RESOLVED'],
            self::STATUS_VALUES['CLOSED']
        ]);
    }

    /**
     * Check if message has DPO response
     *
     * @return bool
     */
    public function hasResponse(): bool
    {
        return !empty($this->dpo_response);
    }

    /**
     * Check if response is overdue
     *
     * @return bool
     */
    public function isOverdue(): bool
    {
        if ($this->isClosed() || !$this->estimated_response_time) {
            return false;
        }

        return now()->isAfter($this->estimated_response_time);
    }

    /**
     * Calculate estimated response time based on priority and type
     *
     * @return \Carbon\Carbon
     */
    public function calculateEstimatedResponseTime(): \Carbon\Carbon
    {
        $baseHours = match($this->priority) {
            self::PRIORITY_LEVELS['URGENT'] => 4,
            self::PRIORITY_LEVELS['HIGH'] => 24,
            self::PRIORITY_LEVELS['MEDIUM'] => 72,
            self::PRIORITY_LEVELS['LOW'] => 168, // 1 week
            default => 72,
        };

        // Adjust for message type complexity
        $typeMultiplier = match($this->message_type) {
            self::MESSAGE_TYPES['BREACH_REPORT'] => 0.5, // Faster response
            self::MESSAGE_TYPES['LEGAL_ADVICE'] => 2.0, // Slower response
            self::MESSAGE_TYPES['CONSULTATION'] => 1.5,
            self::MESSAGE_TYPES['COMPLAINT'] => 0.8,
            default => 1.0,
        };

        $adjustedHours = intval($baseHours * $typeMultiplier);

        return $this->created_at->addHours($adjustedHours);
    }

    /**
     * Escalate the message
     *
     * @param string $reason Escalation reason
     * @return bool
     */
    public function escalate(string $reason): bool
    {
        $this->escalated = true;
        $this->escalation_reason = $reason;
        $this->status = self::STATUS_VALUES['ESCALATED'];
        $this->priority = self::PRIORITY_LEVELS['URGENT'];
        return $this->save();
    }

    /**
     * Add DPO response
     *
     * @param string $response DPO response content
     * @return bool
     */
    public function addResponse(string $response): bool
    {
        $this->dpo_response = $response;
        $this->response_date = now();
        $this->status = self::STATUS_VALUES['RESPONDED'];
        return $this->save();
    }

    /**
     * Mark as resolved
     *
     * @param int|null $satisfactionRating User satisfaction rating 1-5
     * @return bool
     */
    public function markAsResolved(?int $satisfactionRating = null): bool
    {
        $this->status = self::STATUS_VALUES['RESOLVED'];
        $this->resolved_at = now();
        if ($satisfactionRating && $satisfactionRating >= 1 && $satisfactionRating <= 5) {
            $this->satisfaction_rating = $satisfactionRating;
        }
        return $this->save();
    }

    /**
     * Generate unique message reference
     *
     * @return string
     */
    public function getMessageReferenceAttribute(): string
    {
        return 'DPO-' . $this->created_at->format('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get response time in hours
     *
     * @return int|null
     */
    public function getResponseTimeHoursAttribute(): ?int
    {
        if (!$this->response_date) {
            return null;
        }

        return $this->created_at->diffInHours($this->response_date);
    }

    /**
     * Boot method to set estimated response time
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->estimated_response_time) {
                $model->estimated_response_time = $model->calculateEstimatedResponseTime();
            }
        });
    }

    /**
     * Scope for open messages
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', [
            self::STATUS_VALUES['SUBMITTED'],
            self::STATUS_VALUES['ACKNOWLEDGED'],
            self::STATUS_VALUES['IN_REVIEW'],
            self::STATUS_VALUES['PENDING_INFO'],
            self::STATUS_VALUES['ESCALATED']
        ]);
    }

    /**
     * Scope for overdue messages
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverdue($query)
    {
        return $query->open()
            ->where('estimated_response_time', '<', now());
    }

    /**
     * Scope for urgent messages
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', self::PRIORITY_LEVELS['URGENT'])
            ->orWhere('escalated', true);
    }

    /**
     * Scope for messages by category
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
