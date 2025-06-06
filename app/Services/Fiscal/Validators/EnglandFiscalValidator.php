<?php

namespace App\Services\Fiscal\Validators;

use App\Services\Fiscal\FiscalValidatorInterface;
use App\Services\Fiscal\ValidationResult;

/**
 * @Oracode Validator: England Fiscal Validation (OS1-Compliant)
 * 🎯 Purpose: Validate English UTR and VAT numbers with check digits
 * 🌍 Scale: English market (MVP country)
 * 🧱 Core Logic: UTR (Unique Taxpayer Reference) + VAT number validation
 * 🛡️ Privacy: Secure validation without external API calls
 * ⏰ MVP: Critical for English market compliance
 *
 * @package App\Services\Fiscal\Validators
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - English Market)
 * @deadline 2025-06-30
 */
class EnglandFiscalValidator implements FiscalValidatorInterface
{
    /**
     * UTR check digit weights
     * @var array<int, int>
     */
    private array $utrWeights = [6, 7, 8, 9, 10, 5, 4, 3, 2];

    /**
     * Valid VAT number suffixes for different business types
     * @var array<string, array<string>>
     */
    private array $vatSuffixes = [
        'standard' => [''], // No suffix for standard businesses
        'group' => [''], // VAT groups can have same format
        'division' => ['00', '01', '02', '03', '04', '05'], // Divisional registration
    ];

    /**
     * @Oracode Method: Validate Tax/Fiscal Code for England (UTR)
     * 🎯 Purpose: Complete validation of English UTR with check digit
     * 📥 Input: Tax code string (UTR), optional business type
     * 📤 Output: ValidationResult with UTR validation
     * 🌍 Scale: English market specific validation
     * 🛡️ Privacy: No external API calls, algorithmic validation only
     *
     * @param string $taxCode The English UTR to validate
     * @param string|null $businessType Optional business context (individual, business, non_profit)
     * @return ValidationResult Validation result with English-specific checks
     */
    public function validateTaxCode(string $taxCode, ?string $businessType = null): ValidationResult
    {
        $taxCode = trim($taxCode);

        // Basic empty check
        if (empty($taxCode)) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.tax_code_required'),
                [
                    'field' => 'tax_code',
                    'business_type' => $businessType,
                    'country' => 'EN'
                ]
            );
        }

        // English UTR must be exactly 10 digits
        if (strlen($taxCode) !== 10) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.tax_code_england_length', ['required' => 10]),
                [
                    'field' => 'tax_code',
                    'required_length' => 10,
                    'actual_length' => strlen($taxCode),
                    'business_type' => $businessType,
                    'country' => 'EN'
                ]
            );
        }

        // UTR must be numeric only
        if (!preg_match('/^[0-9]{10}$/', $taxCode)) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.tax_code_england_format'),
                [
                    'field' => 'tax_code',
                    'expected_format' => 'numeric_only',
                    'business_type' => $businessType,
                    'country' => 'EN'
                ]
            );
        }

        // Validate UTR checksum
        $checksumValidation = $this->validateUtrChecksum($taxCode);
        if (!$checksumValidation) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.tax_code_england_checksum'),
                [
                    'field' => 'tax_code',
                    'validation_type' => 'utr_checksum',
                    'business_type' => $businessType,
                    'country' => 'EN'
                ]
            );
        }

        return ValidationResult::valid(
            $taxCode,
            [
                'country' => 'EN',
                'document_type' => 'UTR',
                'business_type' => $businessType,
                'validation_level' => 'full_checksum',
                'original_input' => $taxCode
            ]
        );
    }

    /**
     * @Oracode Method: Validate VAT/Business Registration Number for England
     * 🎯 Purpose: Complete validation of English VAT number with check digit
     * 📥 Input: VAT number string (9 digits + optional suffix)
     * 📤 Output: ValidationResult with English VAT validation
     * 🌍 Scale: English business market specific validation
     * 🛡️ Privacy: No external API calls, algorithmic validation only
     *
     * @param string $vatNumber The English VAT number to validate
     * @return ValidationResult Validation result with English VAT checks
     */
    public function validateVatNumber(string $vatNumber): ValidationResult
    {
        $vatNumber = trim($vatNumber);

        // Basic empty check
        if (empty($vatNumber)) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.vat_number_required'),
                [
                    'field' => 'vat_number',
                    'country' => 'EN'
                ]
            );
        }

        // English VAT must be 9 digits, optionally with 2-digit suffix
        if (!preg_match('/^[0-9]{9}([0-9]{2})?$/', $vatNumber)) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.vat_number_england_format'),
                [
                    'field' => 'vat_number',
                    'expected_format' => '9_digits_optional_suffix',
                    'country' => 'EN'
                ]
            );
        }

        // Extract core VAT number and suffix
        $coreVatNumber = substr($vatNumber, 0, 9);
        $suffix = (strlen($vatNumber) > 9) ? substr($vatNumber, 9, 2) : '';

        // Validate VAT checksum on core 9 digits
        $checksumValidation = $this->validateVatChecksum($coreVatNumber);
        if (!$checksumValidation) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.vat_number_england_checksum'),
                [
                    'field' => 'vat_number',
                    'validation_type' => 'vat_checksum',
                    'core_number' => $coreVatNumber,
                    'country' => 'EN'
                ]
            );
        }

        return ValidationResult::valid(
            $vatNumber,
            [
                'country' => 'EN',
                'document_type' => 'VAT',
                'core_number' => $coreVatNumber,
                'suffix' => $suffix,
                'validation_level' => 'full_checksum',
                'original_input' => $vatNumber
            ]
        );
    }

    /**
     * @Oracode Method: Validate UTR Checksum
     * 🎯 Purpose: Implement English UTR checksum algorithm
     * 📥 Input: 10-digit UTR
     * 📤 Output: Boolean indicating checksum validity
     * 🧱 Core Logic: Weighted sum algorithm specific to UTR
     *
     * @param string $utr The 10-digit UTR
     * @return bool True if checksum is valid, false otherwise
     */
    private function validateUtrChecksum(string $utr): bool
    {
        $sum = 0;

        // Calculate weighted sum for first 9 digits
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $utr[$i] * $this->utrWeights[$i];
        }

        $remainder = $sum % 23;

        // Special cases for UTR remainder calculation
        if ($remainder === 0) {
            $expectedCheckDigit = 0;
        } elseif ($remainder === 1) {
            $expectedCheckDigit = 0;
        } else {
            $expectedCheckDigit = 23 - $remainder;
        }

        $actualCheckDigit = (int) $utr[9];

        return $expectedCheckDigit === $actualCheckDigit;
    }

    /**
     * @Oracode Method: Validate VAT Checksum
     * 🎯 Purpose: Implement English VAT checksum algorithm
     * 📥 Input: 9-digit VAT number
     * 📤 Output: Boolean indicating checksum validity
     * 🧱 Core Logic: Weighted sum algorithm specific to English VAT
     *
     * @param string $vatNumber The 9-digit VAT number
     * @return bool True if checksum is valid, false otherwise
     */
    private function validateVatChecksum(string $vatNumber): bool
    {
        $weights = [8, 7, 6, 5, 4, 3, 2];
        $sum = 0;

        // Calculate weighted sum for first 7 digits
        for ($i = 0; $i < 7; $i++) {
            $sum += (int) $vatNumber[$i] * $weights[$i];
        }

        // Add 8th digit
        $sum += (int) $vatNumber[7];

        $remainder = $sum % 97;
        $expectedCheckDigits = 97 - $remainder;

        // If remainder is 0 or 1, special case
        if ($remainder <= 1) {
            $expectedCheckDigits = $remainder;
        }

        $actualCheckDigits = (int) substr($vatNumber, 7, 2);

        return $expectedCheckDigits === $actualCheckDigits;
    }

    /**
     * @Oracode Method: Get Required Fiscal Fields for English Business Type
     * 🎯 Purpose: Return English-specific required fields for form rendering
     * 📥 Input: Business type (individual, business, non_profit, etc.)
     * 📤 Output: Array of required field names and English validation rules
     * 🌍 Scale: English market specific form fields
     * 🔧 Integration: Used by Blade components for form rendering
     *
     * @param string $businessType The type of business (individual, sole_proprietorship, corporation, etc.)
     * @return array<string, array{required: bool, rules: string[], label: string}> English field requirements
     */
    public function getRequiredFields(string $businessType): array
    {
        $baseFields = [
            'tax_code' => [
                'required' => true,
                'rules' => ['string', 'size:10', 'regex:/^[0-9]{10}$/'],
                'label' => __('user_personal_data.tax_code') . ' (UTR)'
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

        // Add business-specific fields for English entities
        if (in_array($businessType, ['business', 'corporation', 'partnership', 'non_profit'])) {
            $baseFields['vat_number'] = [
                'required' => true,
                'rules' => ['string', 'regex:/^[0-9]{9}([0-9]{2})?$/'],
                'label' => __('user_personal_data.vat_number') . ' (VAT Number)'
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
     * @Oracode Method: Format Tax Code to English Standard
     * 🎯 Purpose: Normalize UTR format for storage and display
     * 📥 Input: Raw tax code string from user input
     * 📤 Output: Properly formatted UTR (numeric, 10 digits)
     * 🌍 Scale: English formatting standards
     * 🧱 Core Logic: Used before storage to ensure consistency
     *
     * @param string $taxCode Raw tax code string from user input
     * @return string Formatted UTR according to English standards
     */
    public function formatTaxCode(string $taxCode): string
    {
        // Remove any non-numeric characters and ensure 10 digits
        $formatted = preg_replace('/[^0-9]/', '', trim($taxCode));

        return $formatted;
    }

    /**
     * @Oracode Method: Get Country Code for English Validator
     * 🎯 Purpose: Return English country identifier
     * 📤 Output: English country code
     * 🔧 Integration: Used by factory for validator selection
     *
     * @return string English country code
     */
    public function getCountryCode(): string
    {
        return 'EN';
    }

    /**
     * @Oracode Method: Get Human-Readable Country Name
     * 🎯 Purpose: Return localized English country name for UI display
     * 📤 Output: Translated English country name using Laravel localization
     * 🌐 i18n: Supports multiple languages for global UX
     *
     * @return string Localized English country name
     */
    public function getCountryName(): string
    {
        return __('user_personal_data.countries.england');
    }

    /**
     * @Oracode Method: Extract VAT Core Number
     * 🎯 Purpose: Extract 9-digit core from VAT number with suffix
     * 📥 Input: English VAT number (9 or 11 digits)
     * 📤 Output: 9-digit core number
     * 🧱 Core Logic: English business logic for VAT processing
     *
     * @param string $vatNumber The English VAT number
     * @return string The 9-digit core VAT number
     */
    public function extractVatCoreNumber(string $vatNumber): string
    {
        return substr($vatNumber, 0, 9);
    }

    /**
     * @Oracode Method: Extract VAT Suffix
     * 🎯 Purpose: Extract suffix from VAT number if present
     * 📥 Input: English VAT number (9 or 11 digits)
     * 📤 Output: 2-digit suffix or empty string
     * 🧱 Core Logic: English business logic for VAT group/division identification
     *
     * @param string $vatNumber The English VAT number
     * @return string The 2-digit suffix or empty string
     */
    public function extractVatSuffix(string $vatNumber): string
    {
        return (strlen($vatNumber) > 9) ? substr($vatNumber, 9, 2) : '';
    }

    /**
     * @Oracode Method: Check if VAT has Group/Division Suffix
     * 🎯 Purpose: Determine if VAT number indicates group or divisional registration
     * 📥 Input: English VAT number
     * 📤 Output: Boolean indicating suffix presence
     * 🧱 Core Logic: English business structure identification
     *
     * @param string $vatNumber The English VAT number
     * @return bool True if VAT has group/division suffix, false otherwise
     */
    public function hasVatSuffix(string $vatNumber): bool
    {
        return strlen($vatNumber) > 9;
    }
}
