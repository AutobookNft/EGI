<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @Oracode Request: Biography Chapter Data Validation
 * �� Purpose: Validates biography chapter creation and update requests
 * 🛡️ Security: Input sanitization and business rule validation
 * 📡 API: Designed for RESTful API consumption
 *
 * @package App\Http\Requests
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - API-First Biography)
 * @date 2025-01-07
 */
class ChapterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'biography_id' => ['required', 'integer', 'exists:biographies,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'is_ongoing' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['boolean'],
            'formatting_data' => ['nullable', 'array'],
            'chapter_type' => ['nullable', 'string', 'max:50'],
            'slug' => ['nullable', 'string', 'max:255'],

            // Media uploads
            'media' => ['nullable', 'array'],
            'media.*' => ['file', 'mimes:jpeg,png,webp,gif,mp4,mov,avi', 'max:10240'], // 10MB max

            // Media metadata
            'media_metadata' => ['nullable', 'array'],
            'media_metadata.*.caption' => ['nullable', 'string', 'max:255'],
            'media_metadata.*.alt_text' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'biography_id.required' => __('chapter.validation.biography_id_required'),
            'biography_id.exists' => __('chapter.validation.biography_not_found'),
            'title.required' => __('chapter.validation.title_required'),
            'title.max' => __('chapter.validation.title_max'),
            'content.required' => __('chapter.validation.content_required'),
            'date_from.date' => __('chapter.validation.date_from_invalid'),
            'date_to.date' => __('chapter.validation.date_to_invalid'),
            'date_to.after_or_equal' => __('chapter.validation.date_to_after_from'),
            'sort_order.integer' => __('chapter.validation.sort_order_integer'),
            'sort_order.min' => __('chapter.validation.sort_order_min'),
            'media.*.file' => __('chapter.validation.media_file'),
            'media.*.mimes' => __('chapter.validation.media_mimes'),
            'media.*.max' => __('chapter.validation.media_max_size'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'biography_id' => __('chapter.fields.biography'),
            'title' => __('chapter.fields.title'),
            'content' => __('chapter.fields.content'),
            'date_from' => __('chapter.fields.date_from'),
            'date_to' => __('chapter.fields.date_to'),
            'is_ongoing' => __('chapter.fields.is_ongoing'),
            'sort_order' => __('chapter.fields.sort_order'),
            'is_published' => __('chapter.fields.is_published'),
            'chapter_type' => __('chapter.fields.chapter_type'),
            'slug' => __('chapter.fields.slug'),
            'media' => __('chapter.fields.media'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate slug if not provided
        if (!$this->has('slug') && $this->has('title')) {
            $this->merge([
                'slug' => $this->generateSlug($this->title)
            ]);
        }

        // Set default values
        $this->mergeIfMissing([
            'is_ongoing' => false,
            'is_published' => false,
            'formatting_data' => [],
        ]);

        // Handle date_to logic for ongoing chapters
        if ($this->boolean('is_ongoing')) {
            $this->merge(['date_to' => null]);
        }
    }

    /**
     * Generate a URL-friendly slug from the title
     */
    private function generateSlug(string $title): string
    {
        return str()->slug($title);
    }

    /**
     * Get the validated data with processed fields.
     */
    public function getProcessedData(): array
    {
        $data = $this->validated();

        // Process dates
        if (isset($data['date_from'])) {
            $data['date_from'] = $data['date_from'] ? date('Y-m-d', strtotime($data['date_from'])) : null;
        }

        if (isset($data['date_to'])) {
            $data['date_to'] = $data['date_to'] ? date('Y-m-d', strtotime($data['date_to'])) : null;
        }

        // Auto-generate sort_order if not provided
        if (!isset($data['sort_order']) && isset($data['biography_id'])) {
            $maxOrder = \App\Models\BiographyChapter::where('biography_id', $data['biography_id'])
                ->max('sort_order');
            $data['sort_order'] = ($maxOrder ?? 0) + 1;
        }

        return $data;
    }
}
