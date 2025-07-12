<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @Oracode Resource: Biography Chapter API Response Formatting
 * 🎯 Purpose: Formats biography chapter data for API responses
 * 🧱 Core Logic: Includes media URLs, timeline data, and computed properties
 * 📡 API: Designed for RESTful API consumption with consistent structure
 *
 * @package App\Http\Resources
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - API-First Biography)
 * @date 2025-01-07
 */
class BiographyChapterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'biography_id' => $this->biography_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'chapter_type' => $this->chapter_type,
            'sort_order' => $this->sort_order,
            'is_published' => $this->is_published,
            'is_ongoing' => $this->is_ongoing,
            'formatting_data' => $this->formatting_data,

            // Timeline data
            'date_from' => $this->date_from?->toDateString(),
            'date_to' => $this->date_to?->toDateString(),
            'date_range_display' => $this->date_range_display,
            'duration_formatted' => $this->duration_formatted,
            'is_current_period' => $this->isCurrentPeriod(),

            // Computed properties
            'content_preview' => $this->content_preview,
            'reading_time' => $this->getReadingTime(),
            'chapter_icon' => $this->chapter_icon,

            // Media URLs
            'media' => [
                'featured_image' => $this->getFirstMediaUrl('chapter_featured'),
                'featured_image_thumb' => $this->getFirstMediaUrl('chapter_featured', 'thumb'),
                'images' => $this->getMedia('chapter_images')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                        'thumb_url' => $media->getUrl('thumb'),
                        'card_url' => $media->getUrl('card'),
                        'full_url' => $media->getUrl('full'),
                        'filename' => $media->file_name,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                    ];
                }),
            ],

            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Meta information
            'meta' => [
                'total_images' => $this->getMedia('chapter_images')->count(),
                'has_featured_image' => $this->hasMedia('chapter_featured'),
                'word_count' => str_word_count(strip_tags($this->content)),
            ],
        ];
    }
}
