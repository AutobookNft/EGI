<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @Oracode Request: Biography Chapter Data Validation
 * 🎯 Purpose: Validates chapter creation and update requests
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
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'is_ongoing' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['boolean'],
            'chapter_type' => ['nullable', 'string', Rule::in(['standard', 'milestone', 'achievement'])],
            'formatting_data' => ['nullable', 'array'],
            'formatting_data.text_align' => ['nullable', 'string', Rule::in(['left', 'center', 'right', 'justify'])],
            'formatting_data.highlight_color' => ['nullable', 'string', 'max:7'], // Hex color
            'formatting_data.custom_css' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Il titolo del capitolo è obbligatorio.',
            'title.max' => 'Il titolo non può superare i 255 caratteri.',
            'content.required' => 'Il contenuto del capitolo è obbligatorio.',
            'date_from.date' => 'La data di inizio deve essere una data valida.',
            'date_to.date' => 'La data di fine deve essere una data valida.',
            'date_to.after_or_equal' => 'La data di fine deve essere successiva o uguale alla data di inizio.',
            'is_ongoing.boolean' => 'Il campo in corso deve essere vero o falso.',
            'sort_order.integer' => 'L\'ordine deve essere un numero intero.',
            'sort_order.min' => 'L\'ordine non può essere negativo.',
            'is_published.boolean' => 'Il campo pubblicato deve essere vero o falso.',
            'chapter_type.in' => 'Il tipo di capitolo deve essere "standard", "milestone" o "achievement".',
            'formatting_data.array' => 'I dati di formattazione devono essere un array.',
            'formatting_data.text_align.in' => 'L\'allineamento del testo deve essere "left", "center", "right" o "justify".',
            'formatting_data.highlight_color.max' => 'Il colore di evidenziazione non può superare i 7 caratteri.',
            'formatting_data.custom_css.max' => 'Il CSS personalizzato non può superare i 1000 caratteri.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'titolo del capitolo',
            'content' => 'contenuto del capitolo',
            'date_from' => 'data di inizio',
            'date_to' => 'data di fine',
            'is_ongoing' => 'in corso',
            'sort_order' => 'ordine',
            'is_published' => 'pubblicato',
            'chapter_type' => 'tipo di capitolo',
            'formatting_data' => 'dati di formattazione',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure boolean fields are properly cast
        $this->merge([
            'is_ongoing' => $this->boolean('is_ongoing'),
            'is_published' => $this->boolean('is_published'),
            'formatting_data' => $this->input('formatting_data', []),
        ]);

        // Handle ongoing logic: if ongoing, clear date_to
        if ($this->boolean('is_ongoing')) {
            $this->merge(['date_to' => null]);
        }
    }
}
