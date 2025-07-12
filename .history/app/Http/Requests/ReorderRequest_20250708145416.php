<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @Oracode Request: Chapter Reordering Validation
 * 🎯 Purpose: Validates chapter reordering requests
 * 🛡️ Security: Input sanitization and order validation
 * 📡 API: Designed for RESTful API consumption
 *
 * @package App\Http\Requests
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - API-First Biography)
 * @date 2025-01-07
 */
class ReorderRequest extends FormRequest
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
            'chapters' => ['required', 'array', 'min:1'],
            'chapters.*.id' => ['required', 'integer', 'exists:biography_chapters,id'],
            'chapters.*.sort_order' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'chapters.required' => 'L\'array dei capitoli è obbligatorio.',
            'chapters.array' => 'I capitoli devono essere un array.',
            'chapters.min' => 'Deve essere fornito almeno un capitolo.',
            'chapters.*.id.required' => 'L\'ID del capitolo è obbligatorio.',
            'chapters.*.id.integer' => 'L\'ID del capitolo deve essere un numero intero.',
            'chapters.*.id.exists' => 'Il capitolo specificato non esiste.',
            'chapters.*.sort_order.required' => 'L\'ordine del capitolo è obbligatorio.',
            'chapters.*.sort_order.integer' => 'L\'ordine deve essere un numero intero.',
            'chapters.*.sort_order.min' => 'L\'ordine non può essere negativo.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'chapters' => 'capitoli',
            'chapters.*.id' => 'ID del capitolo',
            'chapters.*.sort_order' => 'ordine del capitolo',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure sort_order is properly cast to integer
        $chapters = $this->input('chapters', []);

        foreach ($chapters as &$chapter) {
            if (isset($chapter['sort_order'])) {
                $chapter['sort_order'] = (int) $chapter['sort_order'];
            }
        }

        $this->merge(['chapters' => $chapters]);
    }
}
