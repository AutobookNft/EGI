<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Model: User Documents (GDPR Ultra-Sensitive)
 * 🎯 Purpose: Manages encrypted document storage and verification
 * 🛡️ Privacy: Ultra-sensitive documents with encryption and access tracking
 * 🧱 Core Logic: Handles document verification and compliance retention
 */
class UserDocuments extends Model
{
    protected $fillable = [
        'user_id', 'doc_typo', 'doc_num', 'doc_issue_date', 'doc_expired_date',
        'doc_issue_from', 'doc_photo_path_f', 'doc_photo_path_r',
        'verification_status', 'verification_notes', 'verified_at', 'verified_by',
        'is_encrypted', 'document_purpose', 'retention_until'
    ];

    protected $casts = [
        'doc_issue_date' => 'date',
        'doc_expired_date' => 'date',
        'verified_at' => 'datetime',
        'is_encrypted' => 'boolean',
        'retention_until' => 'date',
        'scheduled_for_deletion' => 'boolean',
        'last_accessed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $hidden = [
        'doc_num', 'doc_photo_path_f', 'doc_photo_path_r'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->doc_expired_date && $this->doc_expired_date->isPast();
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified' && !$this->isExpired();
    }

    public function needsRenewal(): bool
    {
        if (!$this->doc_expired_date) return false;

        return $this->doc_expired_date->diffInDays(now()) <= 30;
    }

    public function canBeDeleted(): bool
    {
        return $this->retention_until && $this->retention_until->isPast();
    }

    public function trackAccess(?string $accessedBy = null): void
    {
        $this->increment('access_count');
        $this->update([
            'last_accessed_at' => now(),
            'last_accessed_by' => $accessedBy
        ]);
    }
}