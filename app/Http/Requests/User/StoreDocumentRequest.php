<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;
use App\Helpers\FegiAuth;

/**
 * @Oracode Request: Document Upload Validation
 * 🎯 Purpose: Validate user document uploads with security and file constraints
 * 🛡️ Privacy: File type and size validation for identity documents
 * 🧱 Core Logic: Secure file upload validation with FegiAuth integration
 */
class StoreDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     */
    public function authorize(): bool
    {
        // Only strong auth users can upload documents
        return FegiAuth::isStrongAuth() && FegiAuth::can('upload_identity_documents');
    }

    /**
     * Get the validation rules that apply to the request
     */
    public function rules(): array
    {
        return [
            'document_type' => [
                'required',
                'string',
                'in:identity_card,passport,driving_license,tax_code,vat_certificate,business_registration,other'
            ],
            'document' => [
                'required',
                'file',
                File::types(['pdf', 'jpg', 'jpeg', 'png'])
                    ->max(10 * 1024) // 10MB max
                    ->min(10), // 10KB min
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
                'regex:/^[a-zA-Z0-9\s\.\,\-\(\)\/]+$/', // Allow basic characters only
            ],
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'document_type.required' => __('validation.user_documents.type_required'),
            'document_type.in' => __('validation.user_documents.type_invalid'),

            'document.required' => __('validation.user_documents.file_required'),
            'document.file' => __('validation.user_documents.file_invalid'),
            'document.mimes' => __('validation.user_documents.file_type_invalid'),
            'document.max' => __('validation.user_documents.file_too_large'),
            'document.min' => __('validation.user_documents.file_too_small'),

            'description.max' => __('validation.user_documents.description_too_long'),
            'description.regex' => __('validation.user_documents.description_invalid_chars'),
        ];
    }

    /**
     * Get custom attributes for validator errors
     */
    public function attributes(): array
    {
        return [
            'document_type' => __('validation.attributes.document_type'),
            'document' => __('validation.attributes.document_file'),
            'description' => __('validation.attributes.document_description'),
        ];
    }

    /**
     * Configure the validator instance
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Check if user already has this document type (except 'other')
            if ($this->document_type && $this->document_type !== 'other') {
                $user = FegiAuth::user();
                $existingDocument = $user->documents()
                    ->where('document_type', $this->document_type)
                    ->where('verification_status', '!=', 'rejected')
                    ->exists();

                if ($existingDocument) {
                    $validator->errors()->add(
                        'document_type',
                        __('validation.user_documents.type_already_uploaded')
                    );
                }
            }

            // Additional file content validation
            if ($this->hasFile('document')) {
                $file = $this->file('document');

                // Check file is not corrupted (basic check)
                if ($file->getError() !== UPLOAD_ERR_OK) {
                    $validator->errors()->add(
                        'document',
                        __('validation.user_documents.file_upload_error')
                    );
                }

                // Check file has content
                if ($file->getSize() === 0) {
                    $validator->errors()->add(
                        'document',
                        __('validation.user_documents.file_empty')
                    );
                }
            }
        });
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        // Sanitize description if provided
        if ($this->has('description')) {
            $this->merge([
                'description' => trim($this->description)
            ]);
        }
    }
}
