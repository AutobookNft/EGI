<?php

/**
 * @Oracode Model: Biography Chapter with Timeline and Media Support
 * 🎯 Purpose: Individual biography chapters with temporal organization and rich media
 * 🛡️ Privacy: Inherits privacy from parent biography with chapter-level control
 * 🧱 Core Logic: Date range support, ordering, and polymorphic media relations
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Carbon\Carbon;

class BiographyChapter extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    /**
     * @Oracode Property: Mass Assignment Protection
     * 🛡️ Security: Explicit fillable fields for data integrity
     */
    protected $fillable = [
        'biography_id',
        'title',
        'content',
        'date_from',
        'date_to',
        'is_ongoing',
        'sort_order',
        'is_published',
        'formatting_data',
        'chapter_type',
        'slug',
    ];

    /**
     * @Oracode Property: Attribute Casting for Type Safety
     * 🎯 Purpose: Ensure proper data types and JSON handling
     */
    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'is_ongoing' => 'boolean',
        'is_published' => 'boolean',
        'formatting_data' => 'array',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    /**
     * @Oracode Relationship: Parent Biography
     * 🔗 Purpose: Connect chapter to its parent biography
     * 🎯 Optimization: Eager loading ready for performance
     */
    public function biography(): BelongsTo
    {
        return $this->belongsTo(Biography::class);
    }

    /**
     * @Oracode Relationship: Biography Owner (Through Parent)
     * 🔗 Purpose: Access user owner through parent biography
     * 📊 Convenience: Direct access to owner without join complexity
     */
    public function user(): BelongsTo
    {
        return $this->biography->user();
    }

    /**
     * @Oracode Scope: Published Chapters Only
     * 🎯 Purpose: Filter only published chapters for public display
     * 🔍 Usage: BiographyChapter::published()->get()
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * @Oracode Scope: Ongoing Chapters Filter
     * 🎯 Purpose: Filter chapters that are marked as ongoing (current)
     * 🔍 Usage: BiographyChapter::ongoing()->get()
     */
    public function scopeOngoing(Builder $query): Builder
    {
        return $query->where('is_ongoing', true);
    }

    /**
     * @Oracode Scope: By Chapter Type
     * 🎯 Purpose: Filter chapters by type (standard, milestone, achievement)
     * 🔍 Usage: BiographyChapter::byType('milestone')->get()
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('chapter_type', $type);
    }

    /**
     * @Oracode Scope: Timeline Ordered
     * 🎯 Purpose: Order chapters by timeline logic (date_from, then sort_order)
     * 🔍 Usage: BiographyChapter::timelineOrdered()->get()
     */
    public function scopeTimelineOrdered(Builder $query): Builder
    {
        return $query->orderBy('date_from', 'asc')
                    ->orderBy('sort_order', 'asc');
    }

    /**
     * @Oracode Scope: Date Range Filter
     * 🎯 Purpose: Filter chapters within a specific date range
     * 🔍 Usage: BiographyChapter::dateRange('2020-01-01', '2023-12-31')->get()
     */
    public function scopeDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('date_from', [$startDate, $endDate])
              ->orWhereBetween('date_to', [$startDate, $endDate])
              ->orWhere(function ($q2) use ($startDate, $endDate) {
                  $q2->where('date_from', '<=', $startDate)
                     ->where('date_to', '>=', $endDate);
              });
        });
    }

    /**
     * @Oracode Accessor: Duration Formatted
     * 🎯 Purpose: Human-readable duration between dates
     * 📤 Returns: Formatted duration string (e.g., "2 anni, 3 mesi")
     */
    public function durationFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->date_from) {
                    return 'Periodo non specificato';
                }

                if ($this->is_ongoing) {
                    $duration = Carbon::parse($this->date_from)->diffForHumans(now(), true);
                    return "Da {$duration} (in corso)";
                }

                if (!$this->date_to) {
                    return Carbon::parse($this->date_from)->format('Y');
                }

                $from = Carbon::parse($this->date_from);
                $to = Carbon::parse($this->date_to);

                if ($from->year === $to->year) {
                    return $from->year;
                }

                $duration = $from->diffForHumans($to, true, false, 2);
                return "{$from->year} - {$to->year} ({$duration})";
            }
        );
    }

    /**
     * @Oracode Accessor: Date Range Display
     * 🎯 Purpose: Formatted date range for timeline display
     * 📤 Returns: User-friendly date range string
     */
    public function dateRangeDisplay(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->date_from) {
                    return 'Data non specificata';
                }

                $from = Carbon::parse($this->date_from)->format('M Y');

                if ($this->is_ongoing) {
                    return "{$from} - Presente";
                }

                if (!$this->date_to) {
                    return $from;
                }

                $to = Carbon::parse($this->date_to)->format('M Y');
                return "{$from} - {$to}";
            }
        );
    }

    /**
     * @Oracode Accessor: Content Preview
     * 🎯 Purpose: Short preview of chapter content for cards/listings
     * 📤 Returns: Truncated content with HTML tags stripped
     */
    public function contentPreview(): Attribute
    {
        return Attribute::make(
            get: fn () => \Str::limit(strip_tags($this->content), 150)
        );
    }

    /**
     * @Oracode Accessor: Chapter Icon
     * 🎯 Purpose: Return appropriate icon based on chapter type
     * 📤 Returns: Icon class/name for UI rendering
     */
    public function chapterIcon(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match($this->chapter_type) {
                    'milestone' => 'star',
                    'achievement' => 'trophy',
                    'standard' => 'book-open',
                    default => 'book-open'
                };
            }
        );
    }

    /**
     * @Oracode Method: Get Reading Time
     * 🎯 Purpose: Calculate estimated reading time for chapter
     * 📊 Logic: Average 200 words per minute reading speed
     * 📤 Returns: Reading time in minutes
     */
    public function getReadingTime(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / 200));
    }

    /**
     * @Oracode Method: Generate Chapter Slug
     * 🎯 Purpose: Create URL-friendly slug for chapter within biography
     * 🛡️ Security: Ensures unique slugs within parent biography
     */
    public function generateSlug(): string
    {
        $baseSlug = \Str::slug($this->title);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('biography_id', $this->biography_id)
                     ->where('slug', $slug)
                     ->where('id', '!=', $this->id)
                     ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * @Oracode Method: Is Current Period
     * 🎯 Purpose: Check if chapter represents current/ongoing period
     * 📤 Returns: Boolean indicating if chapter is current
     */
    public function isCurrentPeriod(): bool
    {
        if ($this->is_ongoing) {
            return true;
        }

        if (!$this->date_to) {
            return false;
        }

        $now = now();
        return $now->between($this->date_from, $this->date_to);
    }

    /**
     * @Oracode Spatie: Media Collections Configuration
     * 🎯 Purpose: Define media collections for chapter images
     * 🖼️ Collections: chapter_images for general content, featured for chapter hero
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('chapter_images')
              ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
              ->singleFile(false);

        $this->addMediaCollection('chapter_featured')
              ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
              ->singleFile(true);
    }

    /**
     * @Oracode Spatie: Media Conversions for Performance
     * 🎯 Purpose: Auto-generate optimized image versions
     * ⚡ Performance: Thumbnail and web-optimized versions
     */
    use InteractsWithMedia;

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
             ->width(300)
             ->height(300)
             ->sharpen(10);
    }

    /**
     * @Oracode Method: Boot Model Events
     * 🎯 Purpose: Auto-generate slug and handle model events
     * 🔄 Automation: Seamless slug generation and ordering
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($chapter) {
            if (empty($chapter->slug)) {
                $chapter->slug = $chapter->generateSlug();
            }

            // Auto-set sort_order if not provided
            if (is_null($chapter->sort_order)) {
                $maxOrder = static::where('biography_id', $chapter->biography_id)
                                 ->max('sort_order') ?? 0;
                $chapter->sort_order = $maxOrder + 1;
            }

            // Initialize default formatting_data
            if (empty($chapter->formatting_data)) {
                $chapter->formatting_data = [
                    'text_align' => 'left',
                    'highlight_color' => null,
                    'custom_css' => null,
                ];
            }
        });

        static::updating(function ($chapter) {
            if ($chapter->isDirty('title') && empty($chapter->getOriginal('slug'))) {
                $chapter->slug = $chapter->generateSlug();
            }
        });
    }
}
