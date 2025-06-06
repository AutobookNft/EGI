<?php

namespace App\Services\Fiscal\Validators;

use App\Services\Fiscal\FiscalValidatorInterface;
use App\Services\Fiscal\ValidationResult;

/**
 * @Oracode Validator: France Fiscal Validation (OS1-Compliant)
 * 🎯 Purpose: Validate French SIREN and SIRET numbers with Luhn algorithm
 * 🌍 Scale: French market (MVP country)
 * 🧱 Core Logic: SIREN (9 digits) + SIRET (14 digits) validation
 * 🛡️ Privacy: Secure validation without external API calls
 * ⏰ MVP: Critical for French market compliance
 *
 * @package App\Services\Fiscal\Validators
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - French Market)
 * @deadline 2025-06-30
 */
class FranceFiscalValidator implements FiscalValidatorInterface
{
    /**
     * @Oracode Method: Validate Tax/Fiscal Code for France (SIREN)
     * 🎯 Purpose: Complete validation of French SIREN with Luhn algorithm
     * 📥 Input: Tax code string (SIREN), optional business type
     * 📤 Output: ValidationResult with Luhn validation
     * 🌍 Scale: French market specific validation
     * 🛡️ Privacy: No external API calls, algorithmic validation only
     *
     * @param string $taxCode The French SIREN to validate
     * @param string|null $businessType Optional business context (individual, business, non_profit)
     * @return ValidationResult Validation result with French-specific checks
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
                    'country' => 'FR'
                ]
            );
        }

        // French SIREN must be exactly 9 digits
        if (strlen($taxCode) !== 9) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.tax_code_france_length', ['required' => 9]),
                [
                    'field' => 'tax_code',
                    'required_length' => 9,
                    'actual_length' => strlen($taxCode),
                    'business_type' => $businessType,
                    'country' => 'FR'
                ]
            );
        }

        // SIREN must be numeric only
        if (!preg_match('/^[0-9]{9}$/', $taxCode)) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.tax_code_france_format'),
                [
                    'field' => 'tax_code',
                    'expected_format' => 'numeric_only',
                    'business_type' => $businessType,
                    'country' => 'FR'
                ]
            );
        }

        // Validate SIREN checksum (Luhn algorithm)
        $checksumValidation = $this->validateSirenChecksum($taxCode);
        if (!$checksumValidation) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.tax_code_france_checksum'),
                [
                    'field' => 'tax_code',
                    'validation_type' => 'luhn_checksum',
                    'business_type' => $businessType,
                    'country' => 'FR'
                ]
            );
        }

        return ValidationResult::valid(
            $taxCode,
            [
                'country' => 'FR',
                'business_type' => $businessType,
                'validation_level' => 'full_checksum',
                'number_type' => 'SIREN',
                'original_input' => $taxCode
            ]
        );
    }

    /**
     * @Oracode Method: Validate VAT/Business Registration Number for France (SIRET)
     * 🎯 Purpose: Complete validation of French SIRET with Luhn algorithm
     * 📥 Input: VAT number string (SIRET - 14 digits)
     * 📤 Output: ValidationResult with SIRET-specific validation
     * 🌍 Scale: French business market specific validation
     * 🛡️ Privacy: No external API calls, algorithmic validation only
     *
     * @param string $vatNumber The French SIRET to validate
     * @return ValidationResult Validation result with French SIRET checks
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
                    'country' => 'FR'
                ]
            );
        }

        // French SIRET must be exactly 14 digits
        if (strlen($vatNumber) !== 14) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.vat_number_france_length', ['required' => 14]),
                [
                    'field' => 'vat_number',
                    'required_length' => 14,
                    'actual_length' => strlen($vatNumber),
                    'country' => 'FR'
                ]
            );
        }

        // SIRET must be numeric only
        if (!preg_match('/^[0-9]{14}$/', $vatNumber)) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.vat_number_france_format'),
                [
                    'field' => 'vat_number',
                    'expected_format' => 'numeric_only',
                    'country' => 'FR'
                ]
            );
        }

        // Extract SIREN from SIRET (first 9 digits)
        $siren = substr($vatNumber, 0, 9);
        $nic = substr($vatNumber, 9, 5); // NIC (Numéro Interne de Classement)

        // Validate SIREN part
        $sirenValidation = $this->validateSirenChecksum($siren);
        if (!$sirenValidation) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.vat_number_france_siren_invalid'),
                [
                    'field' => 'vat_number',
                    'validation_type' => 'siren_checksum',
                    'siren_part' => $siren,
                    'country' => 'FR'
                ]
            );
        }

        // Validate SIRET checksum (Luhn algorithm on full 14 digits)
        $siretValidation = $this->validateSiretChecksum($vatNumber);
        if (!$siretValidation) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.vat_number_france_checksum'),
                [
                    'field' => 'vat_number',
                    'validation_type' => 'siret_checksum',
                    'country' => 'FR'
                ]
            );
        }

        return ValidationResult::valid(
            $vatNumber,
            [
                'country' => 'FR',
                'validation_level' => 'full_checksum',
                'number_type' => 'SIRET',
                'siren_part' => $siren,
                'nic_part' => $nic,
                'original_input' => $vatNumber
            ]
        );
    }

    /**
     * @Oracode Method: Validate SIREN Checksum
     * 🎯 Purpose: Implement Luhn algorithm for French SIREN validation
     * 📥 Input: 9-digit SIREN
     * 📤 Output: Boolean indicating checksum validity
     * 🧱 Core Logic: Standard Luhn algorithm implementation
     *
     * @param string $siren The 9-digit SIREN
     * @return bool True if checksum is valid, false otherwise
     */
    private function validateSirenChecksum(string $siren): bool
    {
        return $this->validateLuhnChecksum($siren);
    }

    /**
     * @Oracode Method: Validate SIRET Checksum
     * 🎯 Purpose: Implement Luhn algorithm for French SIRET validation
     * 📥 Input: 14-digit SIRET
     * 📤 Output: Boolean indicating checksum validity
     * 🧱 Core Logic: Standard Luhn algorithm implementation
     *
     * @param string $siret The 14-digit SIRET
     * @return bool True if checksum is valid, false otherwise
     */
    private function validateSiretChecksum(string $siret): bool
    {
        return $this->validateLuhnChecksum($siret);
    }

    /**
     * @Oracode Method: Validate Luhn Checksum
     * 🎯 Purpose: Standard Luhn algorithm implementation
     * 📥 Input: Numeric string
     * 📤 Output: Boolean indicating Luhn validity
     * 🧱 Core Logic: Luhn algorithm with alternating digit doubling
     *
     * @param string $number The numeric string to validate
     * @return bool True if Luhn checksum is valid, false otherwise
     */
    private function validateLuhnChecksum(string $number): bool
    {
        $sum = 0;
        $alternate = false;

        // Process digits from right to left
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $digit = (int) $number[$i];

            if ($alternate) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit = ($digit % 10) + 1;
                }
            }

            $sum += $digit;
            $alternate = !$alternate;
        }

        return ($sum % 10) === 0;
    }

    /**
     * @Oracode Method: Get Required Fiscal Fields for French Business Type
     * 🎯 Purpose: Return French-specific required fields for form rendering
     * 📥 Input: Business type (individual, business, non_profit, etc.)
     * 📤 Output: Array of required field names and French validation rules
     * 🌍 Scale: French market specific form fields
     * 🔧 Integration: Used by Blade components for form rendering
     *
     * @param string $businessType The type of business (individual, sole_proprietorship, corporation, etc.)
     * @return array<string, array{required: bool, rules: string[], label: string}> French field requirements
     */
    public function getRequiredFields(string $businessType): array
    {
        $baseFields = [
            'tax_code' => [
                'required' => true,
                'rules' => ['string', 'size:9', 'regex:/^[0-9]{9}$/'],
                'label' => __('user_personal_data.tax_code') . ' (SIREN)'
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

        // Add business-specific fields for French entities
        if (in_array($businessType, ['business', 'corporation', 'partnership', 'non_profit'])) {
            $baseFields['vat_number'] = [
                'required' => true,
                'rules' => ['string', 'size:14', 'regex:/^[0-9]{14}$/'],
                'label' => __('user_personal_data.vat_number') . ' (SIRET)'
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
     * @Oracode Method: Format Tax Code to French Standard
     * 🎯 Purpose: Normalize SIREN format for storage and display
     * 📥 Input: Raw tax code string from user input
     * 📤 Output: Properly formatted SIREN (numeric, 9 digits)
     * 🌍 Scale: French formatting standards
     * 🧱 Core Logic: Used before storage to ensure consistency
     *
     * @param string $taxCode Raw tax code string from user input
     * @return string Formatted SIREN according to French standards
     */
    public function formatTaxCode(string $taxCode): string
    {
        // Remove any non-numeric characters and ensure 9 digits
        $formatted = preg_replace('/[^0-9]/', '', trim($taxCode));

        return $formatted;
    }

    /**
     * @Oracode Method: Get Country Code for French Validator
     * 🎯 Purpose: Return French country identifier
     * 📤 Output: French country code
     * 🔧 Integration: Used by factory for validator selection
     *
     * @return string French country code
     */
    public function getCountryCode(): string
    {
        return 'FR';
    }

    /**
     * @Oracode Method: Get Human-Readable Country Name
     * 🎯 Purpose: Return localized French country name for UI display
     * 📤 Output: Translated French country name using Laravel localization
     * 🌐 i18n: Supports multiple languages for global UX
     *
     * @return string Localized French country name
     */
    public function getCountryName(): string
    {
        return __('user_personal_data.countries.france');
    }

    /**
     * @Oracode Method: Extract SIREN from SIRET
     * 🎯 Purpose: Extract company identifier from establishment identifier
     * 📥 Input: 14-digit SIRET
     * 📤 Output: 9-digit SIREN or null if invalid
     * 🧱 Core Logic: French business logic - SIREN is first 9 digits of SIRET
     *
     * @param string $siret The 14-digit SIRET
     * @return string|null The 9-digit SIREN or null if invalid
     */
    public function extractSirenFromSiret(string $siret): ?string
    {
        if (strlen($siret) !== 14) {
            return null;
        }

        return substr($siret, 0, 9);
    }

    /**
     * @Oracode Method: Extract NIC from SIRET
     * 🎯 Purpose: Extract establishment identifier from SIRET
     * 📥 Input: 14-digit SIRET
     * 📤 Output: 5-digit NIC or null if invalid
     * 🧱 Core Logic: French business logic - NIC is digits 10-14 of SIRET
     *
     * @param string $siret The 14-digit SIRET
     * @return string|null The 5-digit NIC or null if invalid
     */
    public function extractNicFromSiret(string $siret): ?string
    {
        if (strlen($siret) !== 14) {
            return null;
        }

        return substr($siret, 9, 5);
    }
}
