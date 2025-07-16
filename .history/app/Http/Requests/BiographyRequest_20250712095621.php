<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @Oracode Request: Biography Data Validation
 * 🎯 Purpose: Validates biography creation and update requests
 * 🛡️ Security: Input sanitization and business rule validation
 * 📡 API: Designed for RESTful API consumption
 *
 * @package App\Http\Requests
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - API-First Biography)
 * @date 2025-01-07
 */
class BiographyRequest extends FormRequest
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
        $rules = [
            'type' => ['required', 'string', Rule::in(['single', 'chapters'])],
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'is_public' => ['boolean'],
            'is_completed' => ['boolean'],
            'settings' => ['nullable', 'array'],
            'settings.theme' => ['nullable', 'string', 'max:50'],
            'settings.show_timeline' => ['nullable', 'boolean'],
            'settings.allow_comments' => ['nullable', 'boolean'],
        ];

        // Content is required for single type biographies
        if ($this->input('type') === 'single') {
            $rules['content'] = ['required', 'string'];
        } else {
            $rules['content'] = ['nullable', 'string'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'Il tipo di biografia è obbligatorio.',
            'type.in' => 'Il tipo di biografia deve essere "single" o "chapters".',
            'title.required' => 'Il titolo della biografia è obbligatorio.',
            'title.max' => 'Il titolo non può superare i 255 caratteri.',
            'content.required' => 'Il contenuto è obbligatorio per biografie di tipo "single".',
            'excerpt.max' => 'L\'estratto non può superare i 500 caratteri.',
            'is_public.boolean' => 'Il campo pubblico deve essere vero o falso.',
            'is_completed.boolean' => 'Il campo completato deve essere vero o falso.',
            'settings.array' => 'Le impostazioni devono essere un array.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'type' => 'tipo di biografia',
            'title' => 'titolo',
            'content' => 'contenuto',
            'excerpt' => 'estratto',
            'is_public' => 'pubblico',
            'is_completed' => 'completato',
            'settings' => 'impostazioni',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure boolean fields are properly cast
        $this->merge([
            'is_public' => $this->boolean('is_public'),
            'is_completed' => $this->boolean('is_completed'),
            'settings' => $this->input('settings', []),
        ]);
    }
}
