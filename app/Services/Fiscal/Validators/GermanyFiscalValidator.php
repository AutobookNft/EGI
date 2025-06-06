<?php

namespace App\Services\Fiscal\Validators;

use App\Services\Fiscal\FiscalValidatorInterface;
use App\Services\Fiscal\ValidationResult;

/**
 * @Oracode Validator: Germany Fiscal Validation (OS1-Compliant)
 * 🎯 Purpose: Validate German Steuerliche Identifikationsnummer and USt-IdNr
 * 🌍 Scale: German market (MVP country)
 * 🧱 Core Logic: Steuer-IdNr (11 digits) + USt-IdNr (DE + 9 digits) validation
 * 🛡️ Privacy: Secure validation without external API calls
 * ⏰ MVP: Critical for German market compliance
 *
 * @package App\Services\Fiscal\Validators
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - German Market)
 * @deadline 2025-06-30
 */
class GermanyFiscalValidator implements FiscalValidatorInterface
{
    /**
     * Steuer-IdNr check digit weights
     * @var array<int, int>
     */
    private array $steuerIdWeights = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

    /**
     * @Oracode Method: Validate Tax/Fiscal Code for Germany (Steuerliche Identifikationsnummer)
     * 🎯 Purpose: Complete validation of German Steuer-IdNr with check digit algorithm
     * 📥 Input: Tax code string (Steuer-IdNr), optional business type
     * 📤 Output: ValidationResult with German-specific validation
     * 🌍 Scale: German market specific validation
     * 🛡️ Privacy: No external API calls, algorithmic validation only
     *
     * @param string $taxCode The German Steuer-IdNr to validate
     * @param string|null $businessType Optional business context (individual, business, non_profit)
     * @return ValidationResult Validation result with German-specific checks
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
                    'country' => 'DE'
                ]
            );
        }

        // German Steuer-IdNr must be exactly 11 digits
        if (strlen($taxCode) !== 11) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.tax_code_germany_length', ['required' => 11]),
                [
                    'field' => 'tax_code',
                    'required_length' => 11,
                    'actual_length' => strlen($taxCode),
                    'business_type' => $businessType,
                    'country' => 'DE'
                ]
            );
        }

        // Steuer-IdNr must be numeric only
        if (!preg_match('/^[0-9]{11}$/', $taxCode)) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.tax_code_germany_format'),
                [
                    'field' => 'tax_code',
                    'expected_format' => 'numeric_only',
                    'business_type' => $businessType,
                    'country' => 'DE'
                ]
            );
        }

        // Validate Steuer-IdNr business rules
        $businessValidation = $this->validateSteuerIdBusinessRules($taxCode);
        if (!$businessValidation['valid']) {
            return ValidationResult::invalid(
                null,
                $businessValidation['message'],
                array_merge($businessValidation['context'], [
                    'field' => 'tax_code',
                    'business_type' => $businessType,
                    'country' => 'DE'
                ])
            );
        }

        // Validate Steuer-IdNr checksum
        $checksumValidation = $this->validateSteuerIdChecksum($taxCode);
        if (!$checksumValidation) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.tax_code_germany_checksum'),
                [
                    'field' => 'tax_code',
                    'validation_type' => 'steuer_id_checksum',
                    'business_type' => $businessType,
                    'country' => 'DE'
                ]
            );
        }

        return ValidationResult::valid(
            $taxCode,
            [
                'country' => 'DE',
                'document_type' => 'Steuer-IdNr',
                'business_type' => $businessType,
                'validation_level' => 'full_checksum',
                'original_input' => $taxCode
            ]
        );
    }

    /**
     * @Oracode Method: Validate VAT/Business Registration Number for Germany (USt-IdNr)
     * 🎯 Purpose: Complete validation of German USt-IdNr with format validation
     * 📥 Input: VAT number string (USt-IdNr - DE + 9 digits)
     * 📤 Output: ValidationResult with German VAT validation
     * 🌍 Scale: German business market specific validation
     * 🛡️ Privacy: No external API calls, format validation only
     *
     * @param string $vatNumber The German USt-IdNr to validate
     * @return ValidationResult Validation result with German VAT checks
     */
    public function validateVatNumber(string $vatNumber): ValidationResult
    {
        $vatNumber = strtoupper(trim($vatNumber));

        // Basic empty check
        if (empty($vatNumber)) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.vat_number_required'),
                [
                    'field' => 'vat_number',
                    'country' => 'DE'
                ]
            );
        }

        // German USt-IdNr format: DE + 9 digits
        if (!preg_match('/^DE[0-9]{9}$/', $vatNumber)) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.vat_number_germany_format'),
                [
                    'field' => 'vat_number',
                    'expected_format' => 'DE + 9 digits',
                    'country' => 'DE'
                ]
            );
        }

        // Extract numeric part for validation
        $numericPart = substr($vatNumber, 2, 9);

        // Validate USt-IdNr checksum
        $checksumValidation = $this->validateUstIdChecksum($numericPart);
        if (!$checksumValidation) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.vat_number_germany_checksum'),
                [
                    'field' => 'vat_number',
                    'validation_type' => 'ust_id_checksum',
                    'numeric_part' => $numericPart,
                    'country' => 'DE'
                ]
            );
        }

        return ValidationResult::valid(
            $vatNumber,
            [
                'country' => 'DE',
                'document_type' => 'USt-IdNr',
                'numeric_part' => $numericPart,
                'validation_level' => 'full_checksum',
                'original_input' => $vatNumber
            ]
        );
    }

    /**
     * @Oracode Method: Validate Steuer-IdNr Business Rules
     * 🎯 Purpose: Check German Steuer-IdNr business logic constraints
     * 📥 Input: 11-digit Steuer-IdNr
     * 📤 Output: Array with validation result and context
     * 🧱 Core Logic: German-specific business rules for tax ID format
     *
     * @param string $steuerId The 11-digit Steuer-IdNr
     * @return array{valid: bool, message: string, context: array<string, mixed>}
     */
    private function validateSteuerIdBusinessRules(string $steuerId): array
    {
        // Rule 1: Cannot start with 0
        if ($steuerId[0] === '0') {
            return [
                'valid' => false,
                'message' => __('user_personal_data.validation.tax_code_germany_cannot_start_zero'),
                'context' => ['rule' => 'no_leading_zero']
            ];
        }

        // Rule 2: Cannot have more than one digit repeated more than twice
        $digitCounts = array_count_values(str_split($steuerId));
        $repeatedDigits = 0;

        foreach ($digitCounts as $count) {
            if ($count > 1) {
                $repeatedDigits++;
            }
            if ($count > 3) {
                return [
                    'valid' => false,
                    'message' => __('user_personal_data.validation.tax_code_germany_too_many_repeats'),
                    'context' => ['rule' => 'max_digit_repeats', 'digit_counts' => $digitCounts]
                ];
            }
        }

        // Rule 3: Cannot have more than two different digits repeated
        if ($repeatedDigits > 2) {
            return [
                'valid' => false,
                'message' => __('user_personal_data.validation.tax_code_germany_too_many_repeated_digits'),
                'context' => ['rule' => 'max_repeated_different_digits', 'repeated_count' => $repeatedDigits]
            ];
        }

        return [
            'valid' => true,
            'message' => '',
            'context' => []
        ];
    }

    /**
     * @Oracode Method: Validate Steuer-IdNr Checksum
     * 🎯 Purpose: Implement German Steuer-IdNr checksum algorithm
     * 📥 Input: 11-digit Steuer-IdNr
     * 📤 Output: Boolean indicating checksum validity
     * 🧱 Core Logic: German tax ID specific algorithm with modulo calculation
     *
     * @param string $steuerId The 11-digit Steuer-IdNr
     * @return bool True if checksum is valid, false otherwise
     */
    private function validateSteuerIdChecksum(string $steuerId): bool
    {
        $sum = 0;
        $product = 10;

        // Process first 10 digits
        for ($i = 0; $i < 10; $i++) {
            $digit = (int) $steuerId[$i];
            $sum = ($digit + $product) % 10;

            if ($sum === 0) {
                $sum = 10;
            }

            $product = ($sum * 2) % 11;
        }

        $checkDigit = (11 - $product) % 10;
        $actualCheckDigit = (int) $steuerId[10];

        return $checkDigit === $actualCheckDigit;
    }

    /**
     * @Oracode Method: Validate USt-IdNr Checksum
     * 🎯 Purpose: Implement German USt-IdNr checksum algorithm
     * 📥 Input: 9-digit numeric part of USt-IdNr
     * 📤 Output: Boolean indicating checksum validity
     * 🧱 Core Logic: German VAT ID specific algorithm
     *
     * @param string $numericPart The 9-digit numeric part
     * @return bool True if checksum is valid, false otherwise
     */
    private function validateUstIdChecksum(string $numericPart): bool
    {
        $sum = 0;

        // Weighted sum calculation for USt-IdNr
        for ($i = 0; $i < 8; $i++) {
            $sum += (int) $numericPart[$i] * ($i + 1);
        }

        $checkDigit = $sum % 11;

        // Special case: if checkDigit is 10, it's invalid
        if ($checkDigit === 10) {
            return false;
        }

        $actualCheckDigit = (int) $numericPart[8];

        return $checkDigit === $actualCheckDigit;
    }

    /**
     * @Oracode Method: Get Required Fiscal Fields for German Business Type
     * 🎯 Purpose: Return German-specific required fields for form rendering
     * 📥 Input: Business type (individual, business, non_profit, etc.)
     * 📤 Output: Array of required field names and German validation rules
     * 🌍 Scale: German market specific form fields
     * 🔧 Integration: Used by Blade components for form rendering
     *
     * @param string $businessType The type of business (individual, sole_proprietorship, corporation, etc.)
     * @return array<string, array{required: bool, rules: string[], label: string}> German field requirements
     */
    public function getRequiredFields(string $businessType): array
    {
        $baseFields = [
            'tax_code' => [
                'required' => true,
                'rules' => ['string', 'size:11', 'regex:/^[0-9]{11}$/'],
                'label' => __('user_personal_data.tax_code') . ' (Steuer-IdNr)'
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

        // Add business-specific fields for German entities
        if (in_array($businessType, ['business', 'corporation', 'partnership', 'non_profit'])) {
            $baseFields['vat_number'] = [
                'required' => true,
                'rules' => ['string', 'regex:/^DE[0-9]{9}$/'],
                'label' => __('user_personal_data.vat_number') . ' (USt-IdNr)'
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
     * @Oracode Method: Format Tax Code to German Standard
     * 🎯 Purpose: Normalize Steuer-IdNr format for storage and display
     * 📥 Input: Raw tax code string from user input
     * 📤 Output: Properly formatted Steuer-IdNr (numeric, 11 digits)
     * 🌍 Scale: German formatting standards
     * 🧱 Core Logic: Used before storage to ensure consistency
     *
     * @param string $taxCode Raw tax code string from user input
     * @return string Formatted Steuer-IdNr according to German standards
     */
    public function formatTaxCode(string $taxCode): string
    {
        // Remove any non-numeric characters and ensure 11 digits
        $formatted = preg_replace('/[^0-9]/', '', trim($taxCode));

        return $formatted;
    }

    /**
     * @Oracode Method: Get Country Code for German Validator
     * 🎯 Purpose: Return German country identifier
     * 📤 Output: German country code
     * 🔧 Integration: Used by factory for validator selection
     *
     * @return string German country code
     */
    public function getCountryCode(): string
    {
        return 'DE';
    }

    /**
     * @Oracode Method: Get Human-Readable Country Name
     * 🎯 Purpose: Return localized German country name for UI display
     * 📤 Output: Translated German country name using Laravel localization
     * 🌐 i18n: Supports multiple languages for global UX
     *
     * @return string Localized German country name
     */
    public function getCountryName(): string
    {
        return __('user_personal_data.countries.germany');
    }

    /**
     * @Oracode Method: Extract Numeric Part from USt-IdNr
     * 🎯 Purpose: Extract 9-digit numeric part from German VAT number
     * 📥 Input: Full USt-IdNr (DE + 9 digits)
     * 📤 Output: 9-digit numeric part
     * 🧱 Core Logic: German business logic for VAT processing
     *
     * @param string $ustId The full German USt-IdNr
     * @return string|null The 9-digit numeric part or null if invalid format
     */
    public function extractNumericPartFromUstId(string $ustId): ?string
    {
        if (!preg_match('/^DE[0-9]{9}$/', strtoupper($ustId))) {
            return null;
        }

        return substr($ustId, 2, 9);
    }

    /**
     * @Oracode Method: Format USt-IdNr with Country Prefix
     * 🎯 Purpose: Ensure proper DE prefix for German VAT numbers
     * 📥 Input: Raw VAT number (with or without DE prefix)
     * 📤 Output: Properly formatted USt-IdNr with DE prefix
     * 🧱 Core Logic: German business formatting standards
     *
     * @param string $vatNumber Raw VAT number input
     * @return string Formatted USt-IdNr with DE prefix
     */
    public function formatVatNumber(string $vatNumber): string
    {
        $vatNumber = strtoupper(trim($vatNumber));

        // If already has DE prefix, return as is
        if (str_starts_with($vatNumber, 'DE')) {
            return $vatNumber;
        }

        // If 9 digits, add DE prefix
        if (preg_match('/^[0-9]{9}$/', $vatNumber)) {
            return 'DE' . $vatNumber;
        }

        return $vatNumber; // Return as is if format is unclear
    }

    /**
     * @Oracode Method: Check if Steuer-IdNr Format is Valid
     * 🎯 Purpose: Quick format check without full validation
     * 📥 Input: Potential Steuer-IdNr string
     * 📤 Output: Boolean indicating basic format validity
     * 🧱 Core Logic: German tax ID format check for pre-validation
     *
     * @param string $steuerId The string to check
     * @return bool True if format matches Steuer-IdNr pattern, false otherwise
     */
    public function isValidSteuerIdFormat(string $steuerId): bool
    {
        return preg_match('/^[1-9][0-9]{10}$/', trim($steuerId)) === 1;
    }
}
