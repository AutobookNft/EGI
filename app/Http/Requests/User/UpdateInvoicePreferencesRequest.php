<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Helpers\FegiAuth;

/**
 * @Oracode Request: Invoice Preferences Update Validation
 * 🎯 Purpose: Validate user invoice and billing preferences
 * 🛡️ Privacy: VAT and billing data validation for Italian market
 * 🧱 Core Logic: Conditional validation based on billing type
 */
class UpdateInvoicePreferencesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     */
    public function authorize(): bool
    {
        // Only strong auth users can configure invoice preferences
        return FegiAuth::isStrongAuth() && FegiAuth::can('configure_invoice_preferences');
    }

    /**
     * Get the validation rules that apply to the request
     */
    public function rules(): array
    {
        $rules = [
            'billing_type' => [
                'required',
                'string',
                'in:individual,business,professional,non_profit'
            ],
            'billing_address_line_1' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\.\,\-\/]+$/'
            ],
            'billing_address_line_2' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\.\,\-\/]+$/'
            ],
            'billing_city' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZÀ-ÿ\s\.\-\']+$/'
            ],
            'billing_state' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-zA-ZÀ-ÿ\s\.\-\']+$/'
            ],
            'billing_postal_code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9A-Z\-\s]+$/'
            ],
            'billing_country' => [
                'required',
                'string',
                'size:2',
                'regex:/^[A-Z]{2}$/'
            ],
            'send_invoices_via_email' => [
                'boolean'
            ],
            'invoice_email' => [
                'nullable',
                'email:rfc,dns',
                'max:255'
            ],
            'invoice_language' => [
                'required',
                'string',
                'in:it,en,fr,de,es'
            ],
            'preferred_payment_method' => [
                'required',
                'string',
                'in:bank_transfer,credit_card,paypal,crypto'
            ],
            'payment_terms_days' => [
                'required',
                'integer',
                'min:0',
                'max:180'
            ],
            'special_instructions' => [
                'nullable',
                'string',
                'max:1000'
            ],
        ];

        // Conditional rules based on billing type
        if (in_array($this->billing_type, ['business', 'professional', 'non_profit'])) {
            $rules['company_name'] = [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZÀ-ÿ0-9\s\.\,\-&\']+$/'
            ];

            $rules['vat_number'] = [
                'nullable',
                'string',
                'max:20',
                'regex:/^[A-Z]{2}[0-9A-Z]+$/' // EU VAT format
            ];

            $rules['tax_code'] = [
                'nullable',
                'string',
                'max:20'
            ];

            $rules['sdi_code'] = [
                'nullable',
                'string',
                'size:7',
                'regex:/^[A-Z0-9]{7}$/' // Italian SDI code format
            ];

            $rules['certified_email'] = [
                'nullable',
                'email:rfc,dns',
                'max:255'
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
            'billing_type.required' => __('validation.user_invoice.billing_type_required'),
            'billing_type.in' => __('validation.user_invoice.billing_type_invalid'),

            'company_name.required' => __('validation.user_invoice.company_name_required'),
            'company_name.regex' => __('validation.user_invoice.company_name_invalid'),

            'vat_number.regex' => __('validation.user_invoice.vat_number_format'),
            'tax_code.max' => __('validation.user_invoice.tax_code_too_long'),

            'sdi_code.size' => __('validation.user_invoice.sdi_code_length'),
            'sdi_code.regex' => __('validation.user_invoice.sdi_code_format'),

            'billing_address_line_1.required' => __('validation.user_invoice.address_required'),
            'billing_address_line_1.regex' => __('validation.user_invoice.address_invalid_chars'),
            'billing_address_line_2.regex' => __('validation.user_invoice.address_invalid_chars'),

            'billing_city.required' => __('validation.user_invoice.city_required'),
            'billing_city.regex' => __('validation.user_invoice.city_invalid_chars'),

            'billing_postal_code.required' => __('validation.user_invoice.postal_code_required'),
            'billing_postal_code.regex' => __('validation.user_invoice.postal_code_format'),

            'billing_country.required' => __('validation.user_invoice.country_required'),
            'billing_country.size' => __('validation.user_invoice.country_format'),
            'billing_country.regex' => __('validation.user_invoice.country_format'),

            'invoice_email.email' => __('validation.user_invoice.email_invalid'),
            'certified_email.email' => __('validation.user_invoice.certified_email_invalid'),

            'invoice_language.required' => __('validation.user_invoice.language_required'),
            'invoice_language.in' => __('validation.user_invoice.language_invalid'),

            'preferred_payment_method.required' => __('validation.user_invoice.payment_method_required'),
            'preferred_payment_method.in' => __('validation.user_invoice.payment_method_invalid'),

            'payment_terms_days.required' => __('validation.user_invoice.payment_terms_required'),
            'payment_terms_days.min' => __('validation.user_invoice.payment_terms_min'),
            'payment_terms_days.max' => __('validation.user_invoice.payment_terms_max'),

            'special_instructions.max' => __('validation.user_invoice.instructions_too_long'),
        ];
    }

    /**
     * Get custom attributes for validator errors
     */
    public function attributes(): array
    {
        return [
            'billing_type' => __('validation.attributes.billing_type'),
            'company_name' => __('validation.attributes.company_name'),
            'vat_number' => __('validation.attributes.vat_number'),
            'tax_code' => __('validation.attributes.tax_code'),
            'sdi_code' => __('validation.attributes.sdi_code'),
            'certified_email' => __('validation.attributes.certified_email'),
            'billing_address_line_1' => __('validation.attributes.billing_address'),
            'billing_city' => __('validation.attributes.billing_city'),
            'billing_postal_code' => __('validation.attributes.postal_code'),
            'billing_country' => __('validation.attributes.country'),
            'invoice_email' => __('validation.attributes.invoice_email'),
            'invoice_language' => __('validation.attributes.invoice_language'),
            'preferred_payment_method' => __('validation.attributes.payment_method'),
            'payment_terms_days' => __('validation.attributes.payment_terms'),
            'special_instructions' => __('validation.attributes.special_instructions'),
        ];
    }

    /**
     * Configure the validator instance
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate Italian VAT number format if provided
            if ($this->vat_number && $this->billing_country === 'IT') {
                if (!$this->isValidItalianVAT($this->vat_number)) {
                    $validator->errors()->add(
                        'vat_number',
                        __('validation.user_invoice.italian_vat_invalid')
                    );
                }
            }

            // Validate Italian tax code format if provided
            if ($this->tax_code && $this->billing_country === 'IT') {
                if (!$this->isValidItalianTaxCode($this->tax_code)) {
                    $validator->errors()->add(
                        'tax_code',
                        __('validation.user_invoice.italian_tax_code_invalid')
                    );
                }
            }

            // For business billing, require either VAT number or tax code
            if (in_array($this->billing_type, ['business', 'professional'])) {
                if (empty($this->vat_number) && empty($this->tax_code)) {
                    $validator->errors()->add(
                        'vat_number',
                        __('validation.user_invoice.business_requires_vat_or_tax')
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
        // Normalize country code to uppercase
        if ($this->has('billing_country')) {
            $this->merge([
                'billing_country' => strtoupper($this->billing_country)
            ]);
        }

        // Normalize VAT number
        if ($this->has('vat_number')) {
            $this->merge([
                'vat_number' => strtoupper(str_replace([' ', '-'], '', $this->vat_number))
            ]);
        }

        // Normalize tax code
        if ($this->has('tax_code')) {
            $this->merge([
                'tax_code' => strtoupper(str_replace([' ', '-'], '', $this->tax_code))
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

        // Basic checksum validation for Italian VAT
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
     * Validate Italian tax code (basic format check)
     */
    private function isValidItalianTaxCode(string $taxCode): bool
    {
        // Italian tax code format: 16 characters (persons) or 11 digits (companies)
        return preg_match('/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/', $taxCode) ||
               preg_match('/^[0-9]{11}$/', $taxCode);
    }
}
