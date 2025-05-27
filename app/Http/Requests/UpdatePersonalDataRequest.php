<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @Oracode Request: Personal Data Update Validation
 * 🎯 Purpose: Validate personal data updates with GDPR compliance
 * 🛡️ Privacy: Ensure data integrity and user consent
 * 🧱 Core Logic: Field validation based on user type and permissions
 *
 * @package App\Http\Requests
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0 - Initial implementation
 * @date 2025-05-25
 */
class UpdatePersonalDataRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = auth()->user();

        $rules = [
            // Basic personal information
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],

            // Address information
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'size:2'], // ISO country code

            // Profile information
            'bio' => ['nullable', 'string', 'max:500'],
        ];

        // Add user-type specific validation rules
        $rules = array_merge($rules, $this->getUserTypeSpecificRules($user));

        return $rules;
    }

    /**
     * Get validation rules specific to user type
     *
     * @param \App\Models\User $user
     * @return array
     */
    private function getUserTypeSpecificRules($user): array
    {
        switch ($user->user_type) {
            case 'creator':
            case 'mecenate':
                return [
                    'bio_title' => ['nullable', 'string', 'max:100'],
                    'bio_story' => ['nullable', 'string', 'max:1000'],
                    'site_url' => ['nullable', 'url', 'max:255'],
                    'instagram' => ['nullable', 'string', 'max:100'],
                    'facebook' => ['nullable', 'string', 'max:100'],
                    'linkedin' => ['nullable', 'string', 'max:100'],
                ];

            case 'azienda':
                return [
                    'org_name' => ['required', 'string', 'max:255'],
                    'org_email' => ['nullable', 'email', 'max:255'],
                    'org_street' => ['nullable', 'string', 'max:255'],
                    'org_city' => ['nullable', 'string', 'max:100'],
                    'org_region' => ['nullable', 'string', 'max:100'],
                    'org_state' => ['nullable', 'string', 'max:100'],
                    'org_zip' => ['nullable', 'string', 'max:20'],
                    'org_site_url' => ['nullable', 'url', 'max:255'],
                    'org_phone_1' => ['nullable', 'string', 'max:20'],
                    'rea' => ['nullable', 'string', 'max:20'],
                    'org_fiscal_code' => ['nullable', 'string', 'max:20'],
                    'org_vat_number' => ['nullable', 'string', 'max:20'],
                ];

            case 'epp_entity':
                return [
                    'org_name' => ['required', 'string', 'max:255'],
                    'org_email' => ['nullable', 'email', 'max:255'],
                    'org_street' => ['nullable', 'string', 'max:255'],
                    'org_city' => ['nullable', 'string', 'max:100'],
                    'org_region' => ['nullable', 'string', 'max:100'],
                    'org_state' => ['nullable', 'string', 'max:100'],
                    'org_zip' => ['nullable', 'string', 'max:20'],
                    'org_site_url' => ['nullable', 'url', 'max:255'],
                    'org_phone_1' => ['nullable', 'string', 'max:20'],
                ];

            default:
                return [];
        }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => __('profile.validation.name_required'),
            'name.max' => __('profile.validation.name_max'),
            'email.required' => __('profile.validation.email_required'),
            'email.email' => __('profile.validation.email_invalid'),
            'email.unique' => __('profile.validation.email_taken'),
            'phone.max' => __('profile.validation.phone_max'),
            'date_of_birth.date' => __('profile.validation.date_of_birth_invalid'),
            'date_of_birth.before' => __('profile.validation.date_of_birth_future'),
            'bio.max' => __('profile.validation.bio_max'),
            'country.size' => __('profile.validation.country_invalid'),
            'site_url.url' => __('profile.validation.url_invalid'),
            'org_email.email' => __('profile.validation.org_email_invalid'),
            'org_site_url.url' => __('profile.validation.org_url_invalid'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('profile.name'),
            'email' => __('profile.email'),
            'phone' => __('profile.phone'),
            'date_of_birth' => __('profile.date_of_birth'),
            'address' => __('profile.street_address'),
            'city' => __('profile.city'),
            'state' => __('profile.state'),
            'postal_code' => __('profile.postal_code'),
            'country' => __('profile.country'),
            'bio' => __('profile.bio'),
            'bio_title' => __('profile.bio_title'),
            'bio_story' => __('profile.bio_story'),
            'site_url' => __('profile.website'),
            'org_name' => __('profile.organization_name'),
            'org_email' => __('profile.organization_email'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean and format data before validation
        $data = $this->all();

        // Trim whitespace from string fields
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);

                // Convert empty strings to null for optional fields
                if ($data[$key] === '' && $this->isOptionalField($key)) {
                    $data[$key] = null;
                }
            }
        }

        // Format phone numbers (remove non-numeric characters)
        if (isset($data['phone'])) {
            $data['phone'] = preg_replace('/[^+\d\s()-]/', '', $data['phone']);
        }

        // Format URLs (ensure they have protocol)
        foreach (['site_url', 'org_site_url'] as $urlField) {
            if (isset($data[$urlField]) && $data[$urlField] && !str_starts_with($data[$urlField], 'http')) {
                $data[$urlField] = 'https://' . $data[$urlField];
            }
        }

        $this->replace($data);
    }

    /**
     * Check if a field is optional
     *
     * @param string $field
     * @return bool
     */
    private function isOptionalField(string $field): bool
    {
        $requiredFields = ['name', 'email'];

        $user = auth()->user();
        if (in_array($user->user_type, ['azienda', 'epp_entity'])) {
            $requiredFields[] = 'org_name';
        }

        return !in_array($field, $requiredFields);
    }
}
