<?php

namespace App\Models;

use App\Enums\Gdpr\ProcessingRestrictionType;
use App\Enums\Gdpr\ProcessingRestrictionReason;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @oracode Model for processing restrictions
 * @oracode-dimension technical
 * @value-flow Manages user-requested limitations on data processing
 * @community-impact Gives users control over their data usage
 * @transparency-level All restrictions are visible to users and admins
 * @narrative-coherence Implements GDPR Article 18 right to restriction
 */
class ProcessingRestriction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'restriction_type',
        'reason',
        'details',
        'affected_data_categories',
        'is_active',
        'lifted_at',
        'lifted_by',
        'lift_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'restriction_type' => ProcessingRestrictionType::class,
        'reason' => ProcessingRestrictionReason::class,
        'affected_data_categories' => 'array',
        'is_active' => 'boolean',
        'lifted_at' => 'datetime',
    ];

    /**
     * Get the user that owns the restriction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for active restrictions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->whereNull('lifted_at');
    }

    /**
     * Scope for lifted restrictions.
     */
    public function scopeLifted($query)
    {
        return $query->where('is_active', false)
                     ->orWhereNotNull('lifted_at');
    }

    /**
     * Lift the restriction.
     */
    public function lift(string $liftedBy, string $reason): void
    {
        $this->update([
            'is_active' => false,
            'lifted_at' => now(),
            'lifted_by' => $liftedBy,
            'lift_reason' => $reason,
        ]);
    }

    /**
     * Check if restriction affects a specific data category.
     */
    public function affectsCategory(string $category): bool
    {
        if (empty($this->affected_data_categories)) {
            return true; // If no categories specified, affects all
        }

        return in_array($category, $this->affected_data_categories);
    }
}
