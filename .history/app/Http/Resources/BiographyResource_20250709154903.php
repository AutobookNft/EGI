<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @Oracode Resource: Biography API Response Formatting
 * 🎯 Purpose: Formats biography data for API responses
 * 🧱 Core Logic: Includes chapters, media URLs, and computed properties
 * 📡 API: Designed for RESTful API consumption with consistent structure
 *
 * @package App\Http\Resources
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - API-First Biography)
 * @date 2025-01-07
 */
class BiographyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'is_public' => $this->is_public,
            'is_completed' => $this->is_completed,
            'settings' => $this->settings,

            // Computed properties
            'content_preview' => $this->content_preview,
            'estimated_reading_time' => $this->getEstimatedReadingTime(),
            'is_chapter_based' => $this->is_chapter_based,

            // Media URLs
            'media' => [
                'featured_image' => $this->getFirstMediaUrl('featured_image'),
                'featured_image_thumb' => $this->getFirstMediaUrl('featured_image', 'thumb'),
                'gallery' => $this->getMedia('main_gallery')->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'url' => $media->getUrl(),
                        'thumb_url' => $media->getUrl('thumb'),
                        'filename' => $media->file_name,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                    ];
                }),
            ],

            // Chapters (only for chapter-based biographies)
            'chapters' => $this->when($this->type === 'chapters', function () {
                return BiographyChapterResource::collection($this->chapters);
            }),

            // User info (minimal for privacy)
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],

            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Meta information
            'meta' => [
                'total_chapters' => $this->when($this->type === 'chapters', $this->chapters->count()),
                'published_chapters' => $this->when($this->type === 'chapters', $this->publishedChapters->count()),
                'total_media' => $this->getMedia()->count(),
                'has_featured_image' => $this->hasMedia('featured_image'),
            ],
        ];
    }
}
