<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * EgiTrait Model
 *
 * @package FlorenceEGI\Models
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (FlorenceEGI Traits System with Media Support)
 * @date 2025-09-01
 */
class EgiTrait extends Model implements HasMedia {
    use InteractsWithMedia;
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
        'sort_order',
        // Nuovi campi per le immagini
        'image_description',
        'image_alt_text',
        'image_updated_at',
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
    public function egi(): BelongsTo {
        return $this->belongsTo(Egi::class);
    }

    /**
     * Register Media Collections for trait images
     */
    public function registerMediaCollections(): void {
        $this->addMediaCollection('trait_images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
            ->singleFile(); // Un'immagine per trait
    }

    /**
     * Register Media Conversions for optimization
     */
    public function registerMediaConversions(Media $media = null): void {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10)
            ->performOnCollections('trait_images');

        $this->addMediaConversion('modal')
            ->width(400)
            ->height(400)
            ->sharpen(10)
            ->performOnCollections('trait_images');
    }

    /**
     * Get trait image URL with fallback
     */
    public function getImageUrlAttribute(): ?string {
        $media = $this->getFirstMedia('trait_images');
        return $media ? $media->getUrl() : null;
    }

    /**
     * Get trait thumbnail URL with fallback
     */
    public function getThumbnailUrlAttribute(): ?string {
        $media = $this->getFirstMedia('trait_images');
        return $media ? $media->getUrl('thumb') : null;
    }

    /**
     * Get trait modal image URL with fallback
     */
    public function getModalImageUrlAttribute(): ?string {
        $media = $this->getFirstMedia('trait_images');
        return $media ? $media->getUrl('modal') : null;
    }

    /**
     * Get the category of this trait
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo {
        return $this->belongsTo(TraitCategory::class, 'category_id');
    }

    /**
     * Get the type of this trait
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function traitType(): BelongsTo {
        return $this->belongsTo(TraitType::class, 'trait_type_id');
    }

    /**
     * Format the display value based on type
     *
     * @return string
     */
    public function getFormattedValueAttribute(): string {
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
    public function isRare(): bool {
        return $this->rarity_percentage && $this->rarity_percentage < 10;
    }
}