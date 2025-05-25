<?php

namespace App\Http\Requests\Gdpr;

use App\Enums\Gdpr\ProcessingRestrictionReason;
use App\Enums\Gdpr\ProcessingRestrictionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * GDPR Processing Restriction Request Validation
 *
 * Validates processing restriction requests with proper rules.
 */
class ProcessingRestrictionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only authenticated users can restrict processing of their own data
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'restriction_type' => ['required', new Enum(ProcessingRestrictionType::class)],
            'restriction_reason' => ['required', new Enum(ProcessingRestrictionReason::class)],
            'data_categories' => 'sometimes|array',
            'data_categories.*' => 'string|distinct',
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'restriction_type.required' => __('gdpr.validation.restriction_type_required'),
            'restriction_reason.required' => __('gdpr.validation.restriction_reason_required'),
            'data_categories.array' => __('gdpr.validation.data_categories_format'),
            'data_categories.*.distinct' => __('gdpr.validation.data_categories_distinct'),
            'notes.max' => __('gdpr.validation.notes_max'),
        ];
    }
}
