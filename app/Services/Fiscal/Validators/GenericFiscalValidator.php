<?php

namespace App\Services\Fiscal\Validators;

use App\Services\Fiscal\FiscalValidatorInterface;
use App\Services\Fiscal\ValidationResult;

/**
 * @Oracode Validator: Generic Fiscal Validation Simple (OS1-Compliant)
 * 🎯 Purpose: Provide fallback fiscal validation with translated messages
 * 🌍 Scale: Universal fallback with business logic only
 * 🧱 Core Logic: Simple validation patterns with i18n support
 * 🛡️ Privacy: Secure validation without external API calls
 * ⏰ MVP: Critical fallback for countries outside 6 MVP nations
 *
 * @package App\Services\Fiscal\Validators
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 3.0.0 (FlorenceEGI MVP - Simple + i18n)
 * @deadline 2025-06-30
 */
class GenericFiscalValidator implements FiscalValidatorInterface
{
    /**
     * @Oracode Method: Validate Tax/Fiscal Code for Generic Country
     * 🎯 Purpose: Provide basic validation with translated error messages
     * 📥 Input: Tax code string, optional business type context
     * 📤 Output: ValidationResult with translated messages
     * 🌍 Scale: Universal patterns with i18n support
     * 🛡️ Privacy: No external API calls, format-only validation
     *
     * @param string $taxCode The tax/fiscal code to validate
     * @param string|null $businessType Optional business context (individual, business, non_profit)
     * @return ValidationResult Validation result with translated messages
     */
    public function validateTaxCode(string $taxCode, ?string $businessType = null): ValidationResult
    {
        $taxCode = trim($taxCode);

        // Basic empty check
        if (empty($taxCode)) {
            return ValidationResult::invalid(null,
                __('user_personal_data.validation.tax_code_required'),
                [
                    'field' => 'tax_code',
                    'business_type' => $businessType,
                    'country' => 'GENERIC'
                ]
            );
        }

        // Basic length validation
        if (strlen($taxCode) < 6) {
            return ValidationResult::invalid(null,
                __('user_personal_data.validation.tax_code_min_length', ['min' => 6]),
                [
                    'field' => 'tax_code',
                    'min_length' => 6,
                    'actual_length' => strlen($taxCode),
                    'business_type' => $businessType,
                    'country' => 'GENERIC'
                ]
            );
        }

        if (strlen($taxCode) > 20) {
            return ValidationResult::invalid(null,
                __('user_personal_data.validation.tax_code_max_length', ['max' => 20]),
                [
                    'field' => 'tax_code',
                    'max_length' => 20,
                    'actual_length' => strlen($taxCode),
                    'business_type' => $businessType,
                    'country' => 'GENERIC'
                ]
            );
        }

        // Basic format validation (alphanumeric with common separators)
        if (!preg_match('/^[A-Z0-9\-\.\s]+$/i', $taxCode)) {
            return ValidationResult::invalid(null,
                __('user_personal_data.validation.tax_code_invalid_format'),
                [
                    'field' => 'tax_code',
                    'expected_format' => 'alphanumeric_with_separators',
                    'business_type' => $businessType,
                    'country' => 'GENERIC'
                ]
            );
        }

        // Format the tax code (uppercase, remove spaces and separators)
        $formattedTaxCode = $this->formatTaxCode($taxCode);

        return ValidationResult::valid(
            $formattedTaxCode,
            [
                'country' => 'GENERIC',
                'business_type' => $businessType,
                'validation_level' => 'basic_format',
                'original_input' => $taxCode
            ]
        );
    }

    /**
     * @Oracode Method: Validate VAT/Business Registration Number for Generic Country
     * 🎯 Purpose: Provide basic validation with translated error messages
     * 📥 Input: VAT number string
     * 📤 Output: ValidationResult with translated messages
     * 🌍 Scale: Universal patterns with i18n support
     * 🛡️ Privacy: No external API calls, format-only validation
     *
     * @param string $vatNumber The VAT/business registration number to validate
     * @return ValidationResult Validation result with translated messages
     */
    public function validateVatNumber(string $vatNumber): ValidationResult
    {
        $vatNumber = trim($vatNumber);

        // Basic empty check
        if (empty($vatNumber)) {
            return ValidationResult::invalid(null,
                __('user_personal_data.validation.vat_number_required'),

                [
                    'field' => 'vat_number',
                    'country' => 'GENERIC'
                ]
            );
        }

        // Basic length validation
        if (strlen($vatNumber) < 8) {
            return ValidationResult::invalid(null,
                __('user_personal_data.validation.vat_number_min_length', ['min' => 8]),
                [
                    'field' => 'vat_number',
                    'min_length' => 8,
                    'actual_length' => strlen($vatNumber),
                    'country' => 'GENERIC'
                ]
            );
        }

        if (strlen($vatNumber) > 15) {
            return ValidationResult::invalid(null,
                __('user_personal_data.validation.vat_number_max_length', ['max' => 15]),
                [
                    'field' => 'vat_number',
                    'max_length' => 15,
                    'actual_length' => strlen($vatNumber),
                    'country' => 'GENERIC'
                ]
            );
        }

        // Basic format validation (alphanumeric only)
        if (!preg_match('/^[A-Z0-9]+$/i', $vatNumber)) {
            return ValidationResult::invalid(null,
                __('user_personal_data.validation.vat_number_invalid_format'),
                [
                    'field' => 'vat_number',
                    'expected_format' => 'alphanumeric_only',
                    'country' => 'GENERIC'
                ]
            );
        }

        // Format the VAT number (uppercase)
        $formattedVatNumber = strtoupper($vatNumber);

        return ValidationResult::valid(
            $formattedVatNumber,
            [
                'country' => 'GENERIC',
                'validation_level' => 'basic_format',
                'original_input' => $vatNumber
            ]
        );
    }

    /**
     * @Oracode Method: Get Required Fiscal Fields for Generic Business Type
     * 🎯 Purpose: Return generic required fields for form rendering
     * 📥 Input: Business type (individual, business, non_profit, etc.)
     * 📤 Output: Array of required field names and validation rules
     * 🌍 Scale: Universal form fields for any country
     * 🔧 Integration: Used by Blade components for form rendering
     *
     * @param string $businessType The type of business (individual, sole_proprietorship, corporation, etc.)
     * @return array<string, array{required: bool, rules: string[], label: string}> Generic field requirements
     */
    public function getRequiredFields(string $businessType): array
    {
        $baseFields = [
            'tax_code' => [
                'required' => true,
                'rules' => ['string', 'min:6', 'max:20', 'regex:/^[A-Z0-9\-\.\s]+$/i'],
                'label' => __('user_personal_data.tax_code')
            ],
            'first_name' => [
                'required' => true,
                'rules' => ['string', 'min:2', 'max:50'],
                'label' => __('user_personal_data.first_name')
            ],
            'last_name' => [
                'required' => true,
                'rules' => ['string', 'min:2', 'max:50'],
                'label' => __('user_personal_data.last_name')
            ]
        ];

        // Add business-specific fields
        if (in_array($businessType, ['business', 'corporation', 'partnership', 'non_profit'])) {
            $baseFields['vat_number'] = [
                'required' => true,
                'rules' => ['string', 'min:8', 'max:15', 'regex:/^[A-Z0-9]+$/i'],
                'label' => __('user_personal_data.vat_number')
            ];
            $baseFields['company_name'] = [
                'required' => true,
                'rules' => ['string', 'min:2', 'max:100'],
                'label' => __('user_personal_data.company_name')
            ];
        }

        return $baseFields;
    }

    /**
     * @Oracode Method: Format Tax Code to Generic Standard
     * 🎯 Purpose: Normalize tax code format for storage and display
     * 📥 Input: Raw tax code string from user input
     * 📤 Output: Properly formatted tax code (uppercase, clean)
     * 🌍 Scale: Universal formatting for any country
     * 🧱 Core Logic: Used before storage to ensure consistency
     *
     * @param string $taxCode Raw tax code string from user input
     * @return string Formatted tax code according to generic standards
     */
    public function formatTaxCode(string $taxCode): string
    {
        // Remove spaces, dots, dashes and convert to uppercase
        $formatted = strtoupper(trim($taxCode));
        $formatted = preg_replace('/[\s\.\-]/', '', $formatted);

        return $formatted;
    }

    /**
     * @Oracode Method: Get Country Code for Generic Validator
     * 🎯 Purpose: Return generic country identifier
     * 📤 Output: Generic country identifier
     * 🔧 Integration: Used by factory for validator selection
     *
     * @return string Generic country identifier
     */
    public function getCountryCode(): string
    {
        return 'GENERIC';
    }

    /**
     * @Oracode Method: Get Human-Readable Country Name
     * 🎯 Purpose: Return localized generic country name for UI display
     * 📤 Output: Translated generic country name using Laravel localization
     * 🌐 i18n: Supports multiple languages for global UX
     *
     * @return string Localized generic country name
     */
    public function getCountryName(): string
    {
        return __('user_personal_data.countries.generic');
    }

    /**
     * @Oracode Method: Check if VAT Number is Required for Business Type
     * 🎯 Purpose: Determine if VAT number is mandatory for specific business type
     * 📥 Input: Business type string
     * 📤 Output: Boolean indicating VAT requirement
     * 🧱 Core Logic: Used for conditional form validation
     *
     * @param string $businessType The type of business to check
     * @return bool True if VAT number is required, false otherwise
     */
    public function isVatNumberRequired(string $businessType): bool
    {
        $businessTypesRequiringVat = [
            'business',
            'corporation',
            'partnership',
            'non_profit',
            'company'
        ];

        return in_array($businessType, $businessTypesRequiringVat, true);
    }

    /**
     * @Oracode Method: Get Validation Level Information
     * 🎯 Purpose: Return information about validation capabilities
     * 📤 Output: Array describing validation level and capabilities
     * 🔧 Integration: Used for UI feedback and logging
     *
     * @return array<string, mixed> Validation level information
     */
    public function getValidationLevel(): array
    {
        return [
            'level' => 'basic_format',
            'capabilities' => [
                'format_validation' => true,
                'length_validation' => true,
                'checksum_validation' => false,
                'external_api_validation' => false,
                'business_registry_validation' => false
            ],
            'supported_business_types' => [
                'individual',
                'sole_proprietorship',
                'business',
                'corporation',
                'partnership',
                'non_profit'
            ]
        ];
    }
}
