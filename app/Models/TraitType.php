<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * TraitType Model
 * 
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI Traits System)
 * @date 2024-12-27
 */
class TraitType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'display_type',
        'unit',
        'allowed_values',
        'is_system',
        'collection_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'allowed_values' => 'array',
        'is_system' => 'boolean'
    ];

    /**
     * Get the category this type belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TraitCategory::class, 'category_id');
    }

    /**
     * Get the collection this type belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    /**
     * Get the EGI traits using this type
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function egiTraits(): HasMany
    {
        return $this->hasMany(EgiTrait::class, 'trait_type_id');
    }

    /**
     * Check if this type has predefined values
     *
     * @return bool
     */
    public function hasPredefinedValues(): bool
    {
        return !empty($this->allowed_values);
    }

    /**
     * Validate a value against allowed values
     *
     * @param mixed $value
     * @return bool
     */
    public function isValidValue($value): bool
    {
        if (!$this->hasPredefinedValues()) {
            return true;
        }

        return in_array($value, $this->allowed_values);
    }
}