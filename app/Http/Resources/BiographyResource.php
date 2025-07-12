<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @Oracode API Resource: Biography Data Transformation
 * 🎯 Purpose: Transform biography data for API responses
 * 📡 API: Consistent data format for frontend consumption
 * 🔄 Relations: Optimized loading of user, chapters, and media
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
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'title' => $this->title,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'is_public' => $this->is_public,
            'is_completed' => $this->is_completed,
            'slug' => $this->slug,
            'settings' => $this->settings,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Computed fields
            'content_preview' => $this->content_preview,
            'estimated_reading_time' => $this->getEstimatedReadingTime(),
            'is_chapter_based' => $this->is_chapter_based,

            // Relations
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'slug' => $this->user->slug,
                    'profile_photo_url' => $this->user->profile_photo_url,
                ];
            }),

            'chapters' => $this->whenLoaded('chapters', function () {
                return BiographyChapterResource::collection($this->chapters);
            }),

            'published_chapters' => $this->whenLoaded('publishedChapters', function () {
                return BiographyChapterResource::collection($this->publishedChapters);
            }),

            'media' => $this->whenLoaded('media', function () {
                return $this->media->map(function ($media) {
                    return [
                        'id' => $media->id,
                        'name' => $media->name,
                        'file_name' => $media->file_name,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                        'collection_name' => $media->collection_name,
                        'url' => $media->getUrl(),
                        'thumb_url' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : null,
                        'web_url' => $media->hasGeneratedConversion('web') ? $media->getUrl('web') : null,
                        'custom_properties' => $media->custom_properties,
                    ];
                });
            }),

            // Statistics
            'stats' => $this->when($this->type === 'chapters', function () {
                return [
                    'total_chapters' => $this->chapters->count() ?? 0,
                    'published_chapters' => $this->publishedChapters->count() ?? 0,
                    'total_media' => $this->media->count() ?? 0,
                ];
            }),

            // URLs
            'urls' => [
                'public' => $this->is_public ? route('biography.user.show', $this->user) : null,
                'edit' => route('biography.manage'),
                'api' => route('api.biographies.fetch', $this->id),
            ],
        ];
    }
}
