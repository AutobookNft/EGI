<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Model: Data Export
 * 🎯 Purpose: GDPR data portability export tracking
 * 🛡️ Privacy: Secure export management with token-based access
 * 🧱 Core Logic: Export lifecycle with expiration and access control
 *
 * @package App\Models
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-22
 */
class DataExport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'token',
        'format',
        'categories',
        'status',
        'progress',
        'file_path',
        'file_size',
        'file_hash',
        'download_count',
        'last_downloaded_at',
        'expires_at',
        'completed_at',
        'failed_at',
        'error_message',
        'metadata'
    ];

    /**
     * The attributes that should be cast
     * @var array<string, string>
     */
    protected $casts = [
        'categories' => 'array',
        'metadata' => 'array',
        'last_downloaded_at' => 'datetime',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Export statuses
     * @var array<string, string>
     */
    public static array $statuses = [
        'pending' => 'Pending Processing',
        'processing' => 'Currently Processing',
        'completed' => 'Ready for Download',
        'failed' => 'Processing Failed',
        'expired' => 'Export Expired'
    ];

    /**
     * Supported formats
     * @var array<string, string>
     */
    public static array $formats = [
        'json' => 'JSON Format',
        'csv' => 'CSV Format',
        'pdf' => 'PDF Format'
    ];

    /**
     * Get the user that owns the export
     * @return BelongsTo
     * @privacy-safe Returns owning user relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for completed exports
     * @param $query
     * @return mixed
     * @privacy-safe Filters for completed exports
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for expired exports
     * @param $query
     * @return mixed
     * @privacy-safe Filters for expired exports
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Scope for active exports (not expired)
     * @param $query
     * @return mixed
     * @privacy-safe Filters for active exports
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Check if export is expired
     * @return bool
     * @privacy-safe Checks expiration status
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if export is ready for download
     * @return bool
     * @privacy-safe Checks download readiness
     */
    public function isReady(): bool
    {
        return $this->status === 'completed' && !$this->isExpired();
    }

    /**
     * Get human-readable file size
     * @return string
     * @privacy-safe Returns formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    /**
     * Get status display name
     * @return string
     * @privacy-safe Returns friendly status name
     */
    public function getStatusNameAttribute(): string
    {
        return self::$statuses[$this->status] ?? 'Unknown Status';
    }

    /**
     * Get format display name
     * @return string
     * @privacy-safe Returns friendly format name
     */
    public function getFormatNameAttribute(): string
    {
        return self::$formats[$this->format] ?? 'Unknown Format';
    }
}
