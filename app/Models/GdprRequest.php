<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Model: GDPR Request
 * 🎯 Purpose: Data subject request tracking and management
 * 🛡️ Privacy: Complete GDPR request lifecycle management
 * 🧱 Core Logic: Status tracking with audit trail
 *
 * @package App\Models
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-22
 */
class GdprRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'status',
        'request_data',
        'response_data',
        'notes',
        'rejection_reason',
        'requested_at',
        'acknowledged_at',
        'processed_at',
        'completed_at',
        'expires_at',
        'processed_by',
        'processor_role'
    ];

    /**
     * The attributes that should be cast
     * @var array<string, string>
     */
    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'requested_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * GDPR request types with descriptions
     * @var array<string, string>
     */
    public static array $types = [
        'access' => 'Right of Access (Article 15)',
        'rectification' => 'Right to Rectification (Article 16)',
        'erasure' => 'Right to Erasure (Article 17)',
        'portability' => 'Right to Data Portability (Article 20)',
        'restriction' => 'Right to Restriction (Article 18)',
        'objection' => 'Right to Object (Article 21)',
        'data_update' => 'Data Update Request',
        'deletion' => 'Account Deletion Request',
        'deletion_executed' => 'Account Deletion Executed'
    ];

    /**
     * Request statuses with descriptions
     * @var array<string, string>
     */
    public static array $statuses = [
        'pending' => 'Awaiting Processing',
        'in_progress' => 'Currently Processing',
        'completed' => 'Successfully Completed',
        'rejected' => 'Rejected with Reason',
        'cancelled' => 'Cancelled by User',
        'expired' => 'Expired Without Completion'
    ];

    /**
     * Get the user that owns the request
     * @return BelongsTo
     * @privacy-safe Returns owning user relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who processed the request
     * @return BelongsTo
     * @privacy-safe Returns processor relationship
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope for pending requests
     * @param $query
     * @return mixed
     * @privacy-safe Filters for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for active requests (pending or in progress)
     * @param $query
     * @return mixed
     * @privacy-safe Filters for active requests
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'in_progress']);
    }

    /**
     * Scope for completed requests
     * @param $query
     * @return mixed
     * @privacy-safe Filters for completed requests
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for specific request type
     * @param $query
     * @param string $type
     * @return mixed
     * @privacy-safe Filters by request type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if request is expired
     * @return bool
     * @privacy-safe Checks expiration status
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get human-readable type name
     * @return string
     * @privacy-safe Returns friendly type name
     */
    public function getTypeNameAttribute(): string
    {
        return self::$types[$this->type] ?? 'Unknown Request Type';
    }

    /**
     * Get human-readable status name
     * @return string
     * @privacy-safe Returns friendly status name
     */
    public function getStatusNameAttribute(): string
    {
        return self::$statuses[$this->status] ?? 'Unknown Status';
    }
}
