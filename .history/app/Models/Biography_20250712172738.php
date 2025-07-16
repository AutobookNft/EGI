<?php

/**
 * @Oracode Model: User Biography with Flexible Structure and Media Support
 * 🎯 Purpose: Main biography model supporting both single-text and chapter modes
 * 🛡️ Privacy: GDPR-compliant with user ownership and granular visibility control
 * 🧱 Core Logic: Polymorphic media relations, smart scopes, and type-aware behavior
 *
 * @package App\Models
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI MVP Biography)
 * @date 2025-07-02
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $title
 * @property string|null $content
 * @property bool $is_public
 * @property bool $is_completed
 * @property string $slug
 * @property string|null $excerpt
 * @property array|null $settings
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|BiographyChapter[] $chapters
 * @property-read \Illuminate\Database\Eloquent\Collection|BiographyChapter[] $publishedChapters
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Biography public()
 * @method static \Illuminate\Database\Eloquent\Builder|Biography completed()
 * @method static \Illuminate\Database\Eloquent\Builder|Biography byType(string $type)
 */

class Biography extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * @Oracode Property: Mass Assignment Protection
     * 🛡️ Security: Explicit fillable fields for data integrity
     */
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'content',
        'is_public',
        'is_completed',
        'slug',
        'excerpt',
        'settings',
        'media',
    ];

    /**
     * @Oracode Property: Attribute Casting for Type Safety
     * 🎯 Purpose: Ensure proper data types and JSON handling
     */
    protected $casts = [
        'is_public' => 'boolean',
        'is_completed' => 'boolean',
        'settings' => 'array',
        'media' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * @Oracode Relationship: Biography Owner
     * 🔗 Purpose: Connect biography to its user owner
     * 🎯 Optimization: Eager loading ready for performance
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @Oracode Relationship: Biography Chapters
     * 🔗 Purpose: One-to-many relationship with ordered chapters
     * 📊 Ordering: Default by sort_order for timeline consistency
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(BiographyChapter::class)
            ->orderBy('sort_order')
            ->orderBy('date_from');
    }

    /**
     * @Oracode Relationship: Published Chapters Only
     * 🔗 Purpose: Public-facing chapter access with filtering
     * 🛡️ Privacy: Respects chapter-level visibility settings
     */
    public function publishedChapters(): HasMany
    {
        return $this->chapters()->where('is_published', true);
    }

    /**
     * @Oracode Scope: Public Biographies Filter
     * 🎯 Purpose: Query scope for public biography listings
     * 🔍 Usage: Biography::public()->get()
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * @Oracode Scope: Completed Biographies Filter
     * 🎯 Purpose: Filter biographies marked as completed by users
     * 🔍 Usage: Biography::completed()->get()
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('is_completed', true);
    }

    /**
     * @Oracode Scope: By Type Filter
     * 🎯 Purpose: Filter biographies by structure type (single/chapters)
     * 🔍 Usage: Biography::byType('chapters')->get()
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * @Oracode Scope: With Full Relations
     * 🎯 Purpose: Eager load all common relations for performance
     * 🔍 Usage: Biography::withFullRelations()->find($id)
     */
    public function scopeWithFullRelations(Builder $query): Builder
    {
        return $query->with([
            'user:id,name,email',
            'chapters' => function ($query) {
                $query->orderBy('sort_order')->orderBy('date_from');
            },
            'media'
        ]);
    }

    /**
     * @Oracode Accessor: Is Chapter Based
     * 🎯 Purpose: Convenient check for biography structure type
     * 📤 Returns: boolean indicating if biography uses chapters
     */
    public function isChapterBased(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->type === 'chapters',
        );
    }

    /**
     * @Oracode Accessor: Content Preview
     * 🎯 Purpose: Generate preview text for both single and chapter types
     * 📤 Returns: Excerpt or first chapter content preview
     */
    public function contentPreview(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->excerpt) {
                    return $this->excerpt;
                }

                if ($this->type === 'single' && $this->content) {
                    return \Str::limit(strip_tags($this->content), 200);
                }

                $firstChapter = $this->chapters()->first();
                if ($firstChapter) {
                    return \Str::limit(strip_tags($firstChapter->content), 200);
                }

                return 'Biografia in preparazione...';
            }
        );
    }

    /**
     * @Oracode Method: Get Estimated Reading Time
     * 🎯 Purpose: Calculate reading time for biography content
     * 📊 Logic: Average 200 words per minute reading speed
     * 📤 Returns: Reading time in minutes
     */
    public function getEstimatedReadingTime(): int
    {
        $content = '';

        if ($this->type === 'single') {
            $content = $this->content ?? '';
        } else {
            $content = $this->chapters->pluck('content')->implode(' ');
        }

        $wordCount = str_word_count(strip_tags($content));
        return max(1, ceil($wordCount / 200)); // Minimum 1 minute
    }

    /**
     * @Oracode Method: Generate Auto Slug
     * 🎯 Purpose: Create URL-friendly slug from title
     * 🛡️ Security: Ensures unique slugs across biographies
     */
    public function generateSlug(): string
    {
        $baseSlug = \Str::slug($this->title);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * @Oracode Spatie: Media Collections Configuration
     * 🎯 Purpose: Define media collections for biography images
     * 🖼️ Collections: main_gallery for general images, featured for hero image
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main_gallery')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile(false);

        $this->addMediaCollection('featured_image')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile(true);
    }

    /**
     * @Oracode Spatie: Media Conversions for Performance
     * 🎯 Purpose: Auto-generate optimized image versions
     * ⚡ Performance: Thumbnail and web-optimized versions
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->optimize()
            ->nonQueued();

        $this->addMediaConversion('web')
            ->width(800)
            ->height(600)
            ->optimize()
            ->nonQueued();
    }

    /**
     * @Oracode Method: Boot Model Events
     * 🎯 Purpose: Auto-generate slug on creation and handle model events
     * 🔄 Automation: Seamless slug generation and settings initialization
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($biography) {
            if (empty($biography->slug)) {
                $biography->slug = $biography->generateSlug();
            }

            // Initialize default settings
            if (empty($biography->settings)) {
                $biography->settings = [
                    'theme' => 'default',
                    'show_timeline' => true,
                    'allow_comments' => false,
                ];
            }
        });

        static::updating(function ($biography) {
            if ($biography->isDirty('title') && empty($biography->getOriginal('slug'))) {
                $biography->slug = $biography->generateSlug();
            }
        });
    }
}
