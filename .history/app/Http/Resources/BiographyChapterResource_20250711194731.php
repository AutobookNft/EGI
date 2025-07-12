<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @Oracode API Resource: Biography Chapter Data Transformation
 * 🎯 Purpose: Transform biography chapter data for API responses
 * 📡 API: Consistent data format for frontend consumption
 * 🔄 Relations: Optimized loading of biography and media
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
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'biography_id' => $this->biography_id,
            'title' => $this->title,
            'content' => $this->content,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'is_ongoing' => $this->is_ongoing,
            'sort_order' => $this->sort_order,
            'is_published' => $this->is_published,
            'formatting_data' => $this->formatting_data,
            'chapter_type' => $this->chapter_type,
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Computed fields
            'content_preview' => $this->content_preview,
            'estimated_reading_time' => $this->getEstimatedReadingTime(),
            'date_display' => $this->getDateDisplay(),
            'period_display' => $this->getPeriodDisplay(),

            // Relations
            'biography' => $this->whenLoaded('biography', function () {
                return [
                    'id' => $this->biography->id,
                    'title' => $this->biography->title,
                    'slug' => $this->biography->slug,
                    'type' => $this->biography->type,
                    'user_id' => $this->biography->user_id,
                ];
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

            // URLs
            'urls' => [
                'public' => $this->is_published && $this->biography->is_public
                    ? route('biographies.chapters.public.show', [$this->biography->slug, $this->slug])
                    : null,
                'edit' => route('biography.manage') . '?chapter=' . $this->id,
                'api' => route('api.biographies.chapters.fetch', [$this->biography_id, $this->id]),
            ],
        ];
    }
}
