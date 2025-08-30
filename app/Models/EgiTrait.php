<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * EgiTrait Model
 * 
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI Traits System)
 * @date 2024-12-27
 */
class EgiTrait extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'egi_id',
        'category_id',
        'trait_type_id',
        'value',
        'display_value',
        'rarity_percentage',
        'ipfs_hash',
        'is_locked',
        'sort_order'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rarity_percentage' => 'decimal:2',
        'is_locked' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get the EGI this trait belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function egi(): BelongsTo
    {
        return $this->belongsTo(Egi::class);
    }

    /**
     * Get the category of this trait
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TraitCategory::class, 'category_id');
    }

    /**
     * Get the type of this trait
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function traitType(): BelongsTo
    {
        return $this->belongsTo(TraitType::class, 'trait_type_id');
    }

    /**
     * Format the display value based on type
     *
     * @return string
     */
    public function getFormattedValueAttribute(): string
    {
        $type = $this->traitType;
        
        if (!$type) {
            return $this->value;
        }

        switch ($type->display_type) {
            case 'percentage':
                return $this->value . '%';
            
            case 'number':
            case 'boost_number':
                return $this->value . ($type->unit ? ' ' . $type->unit : '');
            
            case 'date':
                return \Carbon\Carbon::parse($this->value)->format('Y');
            
            default:
                return $this->value;
        }
    }

    /**
     * Check if trait is rare (less than 10% have it)
     *
     * @return bool
     */
    public function isRare(): bool
    {
        return $this->rarity_percentage && $this->rarity_percentage < 10;
    }
}