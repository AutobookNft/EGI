<?php

namespace App\Models;

use App\Enums\Gdpr\GdprActivityCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Model: User Activity
 * 🎯 Purpose: Comprehensive user activity audit trail
 * 🛡️ Privacy: Privacy-conscious activity logging with retention
 * 🧱 Core Logic: Categorized activity tracking with automated cleanup
 *
 * @package App\Models
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-22
 */
class UserActivity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'category',
        'context',
        'metadata',
        'privacy_level',
        'ip_address',
        'user_agent',
        'session_id',
        'expires_at'
    ];

    /**
     * The attributes that should be cast
     * @var array<string, string>
     */
    protected $casts = [
        'context' => 'array',
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'category' => GdprActivityCategory::class,
    ];

    /**
     * Activity categories with retention periods
     * @var array<string, array>
     */
    public static array $categories = [
        'authentication' => ['name' => 'Authentication', 'retention_days' => 365],
        'gdpr_actions' => ['name' => 'GDPR Actions', 'retention_days' => 2555],
        'data_access' => ['name' => 'Data Access', 'retention_days' => 1095],
        'platform_usage' => ['name' => 'Platform Usage', 'retention_days' => 730],
        'security_events' => ['name' => 'Security Events', 'retention_days' => 2555],
        'blockchain_activity' => ['name' => 'Blockchain Activity', 'retention_days' => 2555]
    ];

    /**
     * Privacy levels
     * @var array<string, string>
     */
    public static array $privacyLevels = [
        'standard' => 'Standard Privacy',
        'high' => 'High Privacy',
        'critical' => 'Critical Privacy',
        'immutable' => 'Immutable Record'
    ];

    /**
     * Get the user that owns the activity
     * @return BelongsTo
     * @privacy-safe Returns owning user relationship
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for specific category
     * @param $query
     * @param string $category
     * @return mixed
     * @privacy-safe Filters by activity category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for specific privacy level
     * @param $query
     * @param string $level
     * @return mixed
     * @privacy-safe Filters by privacy level
     */
    public function scopePrivacyLevel($query, string $level)
    {
        return $query->where('privacy_level', $level);
    }

    /**
     * Scope for expired activities
     * @param $query
     * @return mixed
     * @privacy-safe Filters for expired activities
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Scope for recent activities
     * @param $query
     * @param int $days
     * @return mixed
     * @privacy-safe Filters for recent activities
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get category display name
     * @return string
     * @privacy-safe Returns friendly category name
     */
    public function getCategoryNameAttribute(): string
    {
        return self::$categories[$this->category]['name'] ?? 'Unknown Category';
    }

    /**
     * Get privacy level display name
     * @return string
     * @privacy-safe Returns friendly privacy level name
     */
    public function getPrivacyLevelNameAttribute(): string
    {
        return self::$privacyLevels[$this->privacy_level] ?? 'Unknown Privacy Level';
    }

    /**
     * Check if activity is expired
     * @return bool
     * @privacy-safe Checks expiration status
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
