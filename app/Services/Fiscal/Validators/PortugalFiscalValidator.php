<?php

namespace App\Services\Fiscal\Validators;

use App\Services\Fiscal\FiscalValidatorInterface;
use App\Services\Fiscal\ValidationResult;

/**
 * @Oracode Validator: Portugal Fiscal Validation (OS1-Compliant)
 * 🎯 Purpose: Validate Portuguese NIF (Número de Identificação Fiscal) with checksum
 * 🌍 Scale: Portuguese market (MVP country)
 * 🧱 Core Logic: NIF algorithm + business type validation for Portugal
 * 🛡️ Privacy: Secure validation without external API calls
 * ⏰ MVP: Critical for Portuguese market compliance
 *
 * @package App\Services\Fiscal\Validators
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Portuguese Market)
 * @deadline 2025-06-30
 */
class PortugalFiscalValidator implements FiscalValidatorInterface
{
    /**
     * Error code for ValidationResult integration
     * @var int|null
     */
    private ?string $errorCode = null;

    /**
     * Valid NIF prefixes for different entity types
     * @var array<string, array<int>>
     */
    private array $nifPrefixes = [
        'individual' => [1, 2, 3], // Singular persons
        'business' => [5, 6, 7, 8, 9], // Collective persons/companies
        'public' => [4], // Public entities
        'non_profit' => [6], // Non-profit organizations
    ];

    /**
     * @Oracode Method: Validate Tax/Fiscal Code for Portugal (NIF)
     * 🎯 Purpose: Complete validation of Portuguese NIF with checksum
     * 📥 Input: Tax code string (NIF), optional business type
     * 📤 Output: ValidationResult with checksum validation
     * 🌍 Scale: Portuguese market specific validation
     * 🛡️ Privacy: No external API calls, algorithmic validation only
     *
     * @param string $taxCode The Portuguese NIF to validate
     * @param string|null $businessType Optional business context (individual, business, non_profit)
     * @return ValidationResult Validation result with Portuguese-specific checks
     */
    public function validateTaxCode(string $taxCode, ?string $businessType = null): ValidationResult
    {
        $taxCode = trim($taxCode);

        // Basic empty check
        if (empty($taxCode)) {
            return ValidationResult::invalid($this->errorCode,
                __('user_personal_data.validation.tax_code_required'),
                [
                    'field' => 'tax_code',
                    'business_type' => $businessType,
                    'country' => 'PT'
                ]
            );
        }

        // Portuguese NIF must be exactly 9 digits
        if (strlen($taxCode) !== 9) {
            return ValidationResult::invalid($this->errorCode,
                __('user_personal_data.validation.tax_code_portugal_length', ['required' => 9]),
                [
                    'field' => 'tax_code',
                    'required_length' => 9,
                    'actual_length' => strlen($taxCode),
                    'business_type' => $businessType,
                    'country' => 'PT'
                ]
            );
        }

        // NIF must be numeric only
        if (!preg_match('/^[0-9]{9}$/', $taxCode)) {
            return ValidationResult::invalid($this->errorCode,
                __('user_personal_data.validation.tax_code_portugal_format'),
                [
                    'field' => 'tax_code',
                    'expected_format' => 'numeric_only',
                    'business_type' => $businessType,
                    'country' => 'PT'
                ]
            );
        }

        // Validate NIF checksum
        $checksumValidation = $this->validateNifChecksum($taxCode);
        if (!$checksumValidation) {
            return ValidationResult::invalid($this->errorCode,
                __('user_personal_data.validation.tax_code_portugal_checksum'),
                [
                    'field' => 'tax_code',
                    'validation_type' => 'checksum',
                    'business_type' => $businessType,
                    'country' => 'PT'
                ]
            );
        }

        // Validate NIF prefix for business type
        $prefixValidation = $this->validateNifPrefix($taxCode, $businessType);
        if (!$prefixValidation['valid']) {
            return ValidationResult::invalid($this->errorCode,
                $prefixValidation['message'],
                array_merge($prefixValidation['context'], [
                    'field' => 'tax_code',
                    'business_type' => $businessType,
                    'country' => 'PT'
                ])
            );
        }

        return ValidationResult::valid(
            $taxCode,
            [
                'country' => 'PT',
                'business_type' => $businessType,
                'validation_level' => 'full_checksum',
                'entity_type' => $prefixValidation['entity_type'],
                'original_input' => $taxCode
            ]
        );
    }

    /**
     * @Oracode Method: Validate VAT/Business Registration Number for Portugal
     * 🎯 Purpose: Portuguese NIF serves as both tax code and VAT number
     * 📥 Input: VAT number string (same as NIF)
     * 📤 Output: ValidationResult using NIF validation
     * 🌍 Scale: Portuguese business market specific validation
     * 🛡️ Privacy: No external API calls, reuses NIF validation
     *
     * @param string $vatNumber The Portuguese VAT number (NIF) to validate
     * @return ValidationResult Validation result with Portuguese VAT checks
     */
    public function validateVatNumber(string $vatNumber): ValidationResult
    {
        // In Portugal, NIF serves as both tax code and VAT number
        // Validate as business NIF
        return $this->validateTaxCode($vatNumber, 'business');
    }

    /**
     * @Oracode Method: Validate NIF Checksum
     * 🎯 Purpose: Implement official Portuguese NIF checksum algorithm
     * 📥 Input: 9-digit NIF
     * 📤 Output: Boolean indicating checksum validity
     * 🧱 Core Logic: Portuguese NIF algorithm with weighted sum
     *
     * @param string $nif The 9-digit NIF
     * @return bool True if checksum is valid, false otherwise
     */
    private function validateNifChecksum(string $nif): bool
    {
        $weights = [9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;

        // Calculate weighted sum for first 8 digits
        for ($i = 0; $i < 8; $i++) {
            $sum += (int) $nif[$i] * $weights[$i];
        }

        $remainder = $sum % 11;

        // Calculate expected check digit
        if ($remainder < 2) {
            $expectedCheckDigit = 0;
        } else {
            $expectedCheckDigit = 11 - $remainder;
        }

        $actualCheckDigit = (int) $nif[8];

        return $expectedCheckDigit === $actualCheckDigit;
    }

    /**
     * @Oracode Method: Validate NIF Prefix for Business Type
     * 🎯 Purpose: Check if NIF prefix matches declared business type
     * 📥 Input: NIF and business type
     * 📤 Output: Array with validation result and context
     * 🧱 Core Logic: Portuguese-specific business type mapping
     *
     * @param string $nif The NIF to validate
     * @param string|null $businessType The business type context
     * @return array{valid: bool, message: string, context: array<string, mixed>, entity_type: string}
     */
    private function validateNifPrefix(string $nif, ?string $businessType): array
    {
        $firstDigit = (int) $nif[0];

        // Determine entity type from first digit
        $entityType = 'unknown';
        if (in_array($firstDigit, $this->nifPrefixes['individual'])) {
            $entityType = 'individual';
        } elseif (in_array($firstDigit, $this->nifPrefixes['business'])) {
            $entityType = 'business';
        } elseif (in_array($firstDigit, $this->nifPrefixes['public'])) {
            $entityType = 'public';
        }

        // If no business type specified, accept any valid prefix
        if (!$businessType) {
            return [
                'valid' => $entityType !== 'unknown',
                'message' => $entityType === 'unknown'
                    ? __('user_personal_data.validation.tax_code_portugal_invalid_prefix')
                    : '',
                'context' => ['detected_entity_type' => $entityType, 'first_digit' => $firstDigit],
                'entity_type' => $entityType
            ];
        }

        // Validate specific business type mapping
        $expectedPrefixes = $this->nifPrefixes[$businessType] ?? [];

        if (empty($expectedPrefixes)) {
            return [
                'valid' => true, // Unknown business type, accept if valid NIF
                'message' => '',
                'context' => ['detected_entity_type' => $entityType, 'first_digit' => $firstDigit],
                'entity_type' => $entityType
            ];
        }

        if (!in_array($firstDigit, $expectedPrefixes)) {
            return [
                'valid' => false,
                'message' => __('user_personal_data.validation.tax_code_portugal_business_type_mismatch', [
                    'business_type' => $businessType,
                    'detected_type' => $entityType
                ]),
                'context' => [
                    'detected_entity_type' => $entityType,
                    'expected_business_type' => $businessType,
                    'first_digit' => $firstDigit,
                    'expected_prefixes' => $expectedPrefixes
                ],
                'entity_type' => $entityType
            ];
        }

        return [
            'valid' => true,
            'message' => '',
            'context' => ['detected_entity_type' => $entityType, 'first_digit' => $firstDigit],
            'entity_type' => $entityType
        ];
    }

    /**
     * @Oracode Method: Get Required Fiscal Fields for Portuguese Business Type
     * 🎯 Purpose: Return Portuguese-specific required fields for form rendering
     * 📥 Input: Business type (individual, business, non_profit, etc.)
     * 📤 Output: Array of required field names and Portuguese validation rules
     * 🌍 Scale: Portuguese market specific form fields
     * 🔧 Integration: Used by Blade components for form rendering
     *
     * @param string $businessType The type of business (individual, sole_proprietorship, corporation, etc.)
     * @return array<string, array{required: bool, rules: string[], label: string}> Portuguese field requirements
     */
    public function getRequiredFields(string $businessType): array
    {
        $baseFields = [
            'tax_code' => [
                'required' => true,
                'rules' => ['string', 'size:9', 'regex:/^[0-9]{9}$/'],
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

        // Add business-specific fields for Portuguese entities
        if (in_array($businessType, ['business', 'corporation', 'partnership', 'non_profit'])) {
            // In Portugal, NIF serves as both tax code and VAT number
            $baseFields['company_name'] = [
                'required' => true,
                'rules' => ['string', 'min:2', 'max:100'],
                'label' => __('user_personal_data.company_name')
            ];
        }

        return $baseFields;
    }

    /**
     * @Oracode Method: Format Tax Code to Portuguese Standard
     * 🎯 Purpose: Normalize NIF format for storage and display
     * 📥 Input: Raw tax code string from user input
     * 📤 Output: Properly formatted NIF (numeric, 9 digits)
     * 🌍 Scale: Portuguese formatting standards
     * 🧱 Core Logic: Used before storage to ensure consistency
     *
     * @param string $taxCode Raw tax code string from user input
     * @return string Formatted NIF according to Portuguese standards
     */
    public function formatTaxCode(string $taxCode): string
    {
        // Remove any non-numeric characters and ensure 9 digits
        $formatted = preg_replace('/[^0-9]/', '', trim($taxCode));

        return $formatted;
    }

    /**
     * @Oracode Method: Get Country Code for Portuguese Validator
     * 🎯 Purpose: Return Portuguese country identifier
     * 📤 Output: Portuguese country code
     * 🔧 Integration: Used by factory for validator selection
     *
     * @return string Portuguese country code
     */
    public function getCountryCode(): string
    {
        return 'PT';
    }

    /**
     * @Oracode Method: Get Human-Readable Country Name
     * 🎯 Purpose: Return localized Portuguese country name for UI display
     * 📤 Output: Translated Portuguese country name using Laravel localization
     * 🌐 i18n: Supports multiple languages for global UX
     *
     * @return string Localized Portuguese country name
     */
    public function getCountryName(): string
    {
        return __('user_personal_data.countries.portugal');
    }

    /**
     * @Oracode Method: Get Entity Type from NIF
     * 🎯 Purpose: Determine entity type from NIF first digit
     * 📥 Input: Valid NIF
     * 📤 Output: Entity type string
     * 🧱 Core Logic: Portuguese business logic mapping
     *
     * @param string $nif The NIF to analyze
     * @return string Entity type (individual, business, public, unknown)
     */
    public function getEntityTypeFromNif(string $nif): string
    {
        if (strlen($nif) !== 9) {
            return 'unknown';
        }

        $firstDigit = (int) $nif[0];

        if (in_array($firstDigit, $this->nifPrefixes['individual'])) {
            return 'individual';
        } elseif (in_array($firstDigit, $this->nifPrefixes['business'])) {
            return 'business';
        } elseif (in_array($firstDigit, $this->nifPrefixes['public'])) {
            return 'public';
        }

        return 'unknown';
    }
}
