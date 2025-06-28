<?php

namespace App\Http\Requests\Gdpr;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidLegalContent;

/**
 * @package App\Http\Requests\Gdpr
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI MVP - Legal Domain Validation)
 * @date 2025-06-24
 *
 * @Oracode FormRequest: SaveLegalContentRequest
 * 🎯 Purpose: Centralizes authorization and validation for saving legal documents.
 * 🛡️ Security: Authorization via permissions, validation via custom rules.
 * 🧱 Core Logic: Implements rules for content, metadata, and publishing options.
 */
class SaveLegalContentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // The permission middleware on the controller can now be removed for this method,
        // as this Form Request handles authorization.
        return auth()->check() && auth()->user()->can('legal.terms.create_version');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'content' => ['required', 'string', 'min:100', new ValidLegalContent()],
            'change_summary' => ['required', 'string', 'min:10', 'max:1000'],
            'auto_publish' => ['nullable', 'boolean'],
            'effective_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'content' => 'contenuto del documento',
            'change_summary' => 'sommario delle modifiche',
            'auto_publish' => 'pubblicazione automatica',
            'effective_date' => 'data di entrata in vigore',
        ];
    }
}
