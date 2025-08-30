<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * TraitCategory Model
 * 
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI Traits System)
 * @date 2024-12-27
 */
class TraitCategory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'is_system',
        'collection_id',
        'sort_order'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_system' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get the trait types for this category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function traitTypes(): HasMany
    {
        return $this->hasMany(TraitType::class, 'category_id');
    }

    /**
     * Get the collection this category belongs to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    /**
     * Get the EGI traits using this category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function egiTraits(): HasMany
    {
        return $this->hasMany(EgiTrait::class, 'category_id');
    }
}