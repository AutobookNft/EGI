<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Helpers\FegiAuth;

/**
 * @Oracode Request: Organization Data Update Validation
 * 🎯 Purpose: Validate organization/business data updates
 * 🛡️ Privacy: Company and legal data validation with role restrictions
 * 🧱 Core Logic: Complex validation for different organization types
 */
class UpdateOrganizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     */
    public function authorize(): bool
    {
        // Only strong auth users with appropriate roles can edit organization data
        $user = FegiAuth::user();

        return FegiAuth::isStrongAuth() &&
               FegiAuth::can('edit_own_organization_data') &&
               $user && $user->hasAnyRole(['creator', 'enterprise', 'epp_entity']);
    }

    /**
     * Get the validation rules that apply to the request
     */
    public function rules(): array
    {
        $rules = [
            'organization_type' => [
                'required',
                'string',
                'in:sole_proprietorship,srl,spa,snc,sas,cooperative,association,foundation,ngo,public_entity,other'
            ],
            'company_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZÀ-ÿ0-9\s\.\,\-&\'\"]+$/'
            ],
            'business_sector' => [
                'required',
                'string',
                'in:art_culture,craftsmanship,design,fashion,food_beverage,technology,sustainability,education,consulting,retail,manufacturing,services,tourism,agriculture,other'
            ],
            'company_size' => [
                'required',
                'string',
                'in:micro,small,medium,large,individual'
            ],
            'headquarters_address_line_1' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZÀ-ÿ0-9\s\.\,\-\/]+$/'
            ],
            'headquarters_address_line_2' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-ZÀ-ÿ0-9\s\.\,\-\/]+$/'
            ],
            'headquarters_city' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZÀ-ÿ\s\.\-\']+$/'
            ],
            'headquarters_state' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-zA-ZÀ-ÿ\s\.\-\']+$/'
            ],
            'headquarters_postal_code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9A-Z\-\s]+$/'
            ],
            'headquarters_country' => [
                'required',
                'string',
                'size:2',
                'regex:/^[A-Z]{2}$/'
            ],
            'vat_number' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[A-Z]{2}[0-9A-Z]+$/'
            ],
            'tax_code' => [
                'nullable',
                'string',
                'max:20'
            ],
            'chamber_of_commerce_number' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-\/]+$/'
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/'
            ],
            'website' => [
                'nullable',
                'url',
                'max:255'
            ],
            'description' => [
                'nullable',
                'string',
                'max:2000'
            ],
            'certifications' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'sustainability_goals' => [
                'nullable',
                'string',
                'max:2000'
            ],
            'epp_commitment_level' => [
                'nullable',
                'string',
                'in:basic,intermediate,advanced,expert'
            ],
            'foundation_year' => [
                'nullable',
                'integer',
                'min:1800',
                'max:' . date('Y')
            ],
        ];

        // Conditional validation based on organization type
        $organizationTypesRequiringLegalRep = ['srl', 'spa', 'snc', 'sas', 'cooperative'];

        if (in_array($this->organization_type, $organizationTypesRequiringLegalRep)) {
            $rules['legal_representative_name'] = [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZÀ-ÿ\s\.\-\']+$/'
            ];

            $rules['legal_representative_surname'] = [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZÀ-ÿ\s\.\-\']+$/'
            ];

            $rules['legal_representative_tax_code'] = [
                'nullable',
                'string',
                'max:20'
            ];
        }

        // Business types requiring VAT number
        $businessTypes = ['srl', 'spa', 'snc', 'sas', 'sole_proprietorship'];

        if (in_array($this->organization_type, $businessTypes)) {
            $rules['vat_number'] = [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z]{2}[0-9A-Z]+$/'
            ];
        }

        return $rules;
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'organization_type.required' => __('validation.user_organization.type_required'),
            'organization_type.in' => __('validation.user_organization.type_invalid'),

            'company_name.required' => __('validation.user_organization.company_name_required'),
            'company_name.regex' => __('validation.user_organization.company_name_invalid'),

            'vat_number.required' => __('validation.user_organization.vat_number_required'),
            'vat_number.regex' => __('validation.user_organization.vat_number_format'),

            'tax_code.max' => __('validation.user_organization.tax_code_too_long'),

            'chamber_of_commerce_number.regex' => __('validation.user_organization.commerce_number_format'),

            'legal_representative_name.required' => __('validation.user_organization.legal_rep_name_required'),
            'legal_representative_name.regex' => __('validation.user_organization.legal_rep_name_invalid'),

            'legal_representative_surname.required' => __('validation.user_organization.legal_rep_surname_required'),
            'legal_representative_surname.regex' => __('validation.user_organization.legal_rep_surname_invalid'),

            'business_sector.required' => __('validation.user_organization.sector_required'),
            'business_sector.in' => __('validation.user_organization.sector_invalid'),

            'company_size.required' => __('validation.user_organization.size_required'),
            'company_size.in' => __('validation.user_organization.size_invalid'),

            'headquarters_address_line_1.required' => __('validation.user_organization.address_required'),
            'headquarters_address_line_1.regex' => __('validation.user_organization.address_invalid'),
            'headquarters_address_line_2.regex' => __('validation.user_organization.address_invalid'),

            'headquarters_city.required' => __('validation.user_organization.city_required'),
            'headquarters_city.regex' => __('validation.user_organization.city_invalid'),

            'headquarters_postal_code.required' => __('validation.user_organization.postal_code_required'),
            'headquarters_postal_code.regex' => __('validation.user_organization.postal_code_format'),

            'headquarters_country.required' => __('validation.user_organization.country_required'),
            'headquarters_country.size' => __('validation.user_organization.country_format'),

            'phone.regex' => __('validation.user_organization.phone_format'),
            'website.url' => __('validation.user_organization.website_format'),

            'foundation_year.min' => __('validation.user_organization.foundation_year_min'),
            'foundation_year.max' => __('validation.user_organization.foundation_year_max'),

            'epp_commitment_level.in' => __('validation.user_organization.epp_level_invalid'),

            'description.max' => __('validation.user_organization.description_too_long'),
            'certifications.max' => __('validation.user_organization.certifications_too_long'),
            'sustainability_goals.max' => __('validation.user_organization.sustainability_goals_too_long'),
        ];
    }

    /**
     * Get custom attributes for validator errors
     */
    public function attributes(): array
    {
        return [
            'organization_type' => __('validation.attributes.organization_type'),
            'company_name' => __('validation.attributes.company_name'),
            'vat_number' => __('validation.attributes.vat_number'),
            'tax_code' => __('validation.attributes.tax_code'),
            'chamber_of_commerce_number' => __('validation.attributes.chamber_number'),
            'legal_representative_name' => __('validation.attributes.legal_rep_name'),
            'legal_representative_surname' => __('validation.attributes.legal_rep_surname'),
            'legal_representative_tax_code' => __('validation.attributes.legal_rep_tax_code'),
            'business_sector' => __('validation.attributes.business_sector'),
            'company_size' => __('validation.attributes.company_size'),
            'headquarters_address_line_1' => __('validation.attributes.headquarters_address'),
            'headquarters_city' => __('validation.attributes.headquarters_city'),
            'headquarters_postal_code' => __('validation.attributes.headquarters_postal_code'),
            'headquarters_country' => __('validation.attributes.headquarters_country'),
            'phone' => __('validation.attributes.phone'),
            'website' => __('validation.attributes.website'),
            'foundation_year' => __('validation.attributes.foundation_year'),
            'description' => __('validation.attributes.description'),
            'certifications' => __('validation.attributes.certifications'),
            'sustainability_goals' => __('validation.attributes.sustainability_goals'),
            'epp_commitment_level' => __('validation.attributes.epp_commitment_level'),
        ];
    }

    /**
     * Configure the validator instance
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate Italian VAT number if provided and country is IT
            if ($this->vat_number && $this->headquarters_country === 'IT') {
                if (!$this->isValidItalianVAT($this->vat_number)) {
                    $validator->errors()->add(
                        'vat_number',
                        __('validation.user_organization.italian_vat_invalid')
                    );
                }
            }

            // Validate Italian tax code if provided and country is IT
            if ($this->tax_code && $this->headquarters_country === 'IT') {
                if (!$this->isValidItalianTaxCode($this->tax_code)) {
                    $validator->errors()->add(
                        'tax_code',
                        __('validation.user_organization.italian_tax_code_invalid')
                    );
                }
            }

            // Check if company name is unique for this user (prevent duplicates)
            $user = FegiAuth::user();
            if ($user && $this->company_name) {
                $existingOrg = $user->organizationData()
                    ->where('company_name', $this->company_name)
                    ->where('id', '!=', $user->organizationData?->id)
                    ->exists();

                if ($existingOrg) {
                    $validator->errors()->add(
                        'company_name',
                        __('validation.user_organization.company_name_duplicate')
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
        // Normalize country code
        if ($this->has('headquarters_country')) {
            $this->merge([
                'headquarters_country' => strtoupper($this->headquarters_country)
            ]);
        }

        // Normalize VAT number
        if ($this->has('vat_number')) {
            $this->merge([
                'vat_number' => strtoupper(str_replace([' ', '-'], '', $this->vat_number))
            ]);
        }

        // Normalize tax codes
        if ($this->has('tax_code')) {
            $this->merge([
                'tax_code' => strtoupper(str_replace([' ', '-'], '', $this->tax_code))
            ]);
        }

        if ($this->has('legal_representative_tax_code')) {
            $this->merge([
                'legal_representative_tax_code' => strtoupper(str_replace([' ', '-'], '', $this->legal_representative_tax_code))
            ]);
        }
    }

    /**
     * Validate Italian VAT number (basic check)
     */
    private function isValidItalianVAT(string $vat): bool
    {
        // Remove IT prefix if present
        $vat = preg_replace('/^IT/', '', $vat);

        // Must be 11 digits
        if (!preg_match('/^[0-9]{11}$/', $vat)) {
            return false;
        }

        // Basic checksum validation
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $digit = (int)$vat[$i];
            if ($i % 2 === 1) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit = $digit - 9;
                }
            }
            $sum += $digit;
        }

        $checkDigit = (10 - ($sum % 10)) % 10;
        return $checkDigit === (int)$vat[10];
    }

    /**
     * Validate Italian tax code
     */
    private function isValidItalianTaxCode(string $taxCode): bool
    {
        return preg_match('/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/', $taxCode) ||
               preg_match('/^[0-9]{11}$/', $taxCode);
    }
}
