<?php

namespace App\Services\Fiscal\Validators;

use App\Services\Fiscal\FiscalValidatorInterface;
use App\Services\Fiscal\ValidationResult;

/**
 * @Oracode Validator: Spain Fiscal Validation (OS1-Compliant)
 * 🎯 Purpose: Validate Spanish NIE, DNI, and CIF numbers with check digits
 * 🌍 Scale: Spanish market (MVP country)
 * 🧱 Core Logic: DNI (individuals), NIE (foreigners), CIF (companies) validation
 * 🛡️ Privacy: Secure validation without external API calls
 * ⏰ MVP: Critical for Spanish market compliance
 *
 * @package App\Services\Fiscal\Validators
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Spanish Market)
 * @deadline 2025-06-30
 */
class SpainFiscalValidator implements FiscalValidatorInterface
{
    /**
     * DNI check letter table
     * @var array<int, string>
     */
    private array $dniLetters = [
        0 => 'T', 1 => 'R', 2 => 'W', 3 => 'A', 4 => 'G', 5 => 'M', 6 => 'Y', 7 => 'F',
        8 => 'P', 9 => 'D', 10 => 'X', 11 => 'B', 12 => 'N', 13 => 'J', 14 => 'Z',
        15 => 'S', 16 => 'Q', 17 => 'V', 18 => 'H', 19 => 'L', 20 => 'C', 21 => 'K', 22 => 'E'
    ];

    /**
     * NIE first letter conversion table
     * @var array<string, string>
     */
    private array $nieConversion = [
        'X' => '0',
        'Y' => '1',
        'Z' => '2'
    ];

    /**
     * CIF organization type letters
     * @var array<string, string>
     */
    private array $cifTypes = [
        'A' => 'Sociedad Anónima',
        'B' => 'Sociedad de Responsabilidad Limitada',
        'C' => 'Sociedad Colectiva',
        'D' => 'Sociedad Comanditaria',
        'E' => 'Comunidad de Bienes',
        'F' => 'Sociedad Cooperativa',
        'G' => 'Asociación',
        'H' => 'Comunidad de Propietarios',
        'J' => 'Sociedad Civil',
        'N' => 'Entidad Extranjera',
        'P' => 'Corporación Local',
        'Q' => 'Organismo Autónomo',
        'R' => 'Congregación o Institución Religiosa',
        'S' => 'Órgano de la Administración',
        'U' => 'Unión Temporal de Empresas',
        'V' => 'Fondo de Inversión',
        'W' => 'Establecimiento Permanente'
    ];

    /**
     * @Oracode Method: Validate Tax/Fiscal Code for Spain (DNI/NIE)
     * 🎯 Purpose: Complete validation of Spanish DNI or NIE with check letter
     * 📥 Input: Tax code string (DNI/NIE), optional business type
     * 📤 Output: ValidationResult with Spanish-specific validation
     * 🌍 Scale: Spanish market specific validation
     * 🛡️ Privacy: No external API calls, algorithmic validation only
     *
     * @param string $taxCode The Spanish DNI or NIE to validate
     * @param string|null $businessType Optional business context (individual, business, non_profit)
     * @return ValidationResult Validation result with Spanish-specific checks
     */
    public function validateTaxCode(string $taxCode, ?string $businessType = null): ValidationResult
    {
        $taxCode = strtoupper(trim($taxCode));

        // Basic empty check
        if (empty($taxCode)) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.tax_code_required'),
                [
                    'field' => 'tax_code',
                    'business_type' => $businessType,
                    'country' => 'ES'
                ]
            );
        }

        // Determine document type
        $documentType = $this->detectSpanishDocumentType($taxCode);

        if ($documentType === 'unknown') {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.tax_code_spain_format'),
                [
                    'field' => 'tax_code',
                    'business_type' => $businessType,
                    'country' => 'ES'
                ]
            );
        }

        // Validate based on document type
        switch ($documentType) {
            case 'DNI':
                return $this->validateDni($taxCode, $businessType);
            case 'NIE':
                return $this->validateNie($taxCode, $businessType);
            case 'CIF':
                return $this->validateCif($taxCode, $businessType);
            default:
                return ValidationResult::invalid(
                    null,
                    __('user_personal_data.validation.tax_code_spain_unknown_type'),
                    [
                        'field' => 'tax_code',
                        'business_type' => $businessType,
                        'country' => 'ES'
                    ]
                );
        }
    }

    /**
     * @Oracode Method: Validate VAT/Business Registration Number for Spain (CIF)
     * 🎯 Purpose: Complete validation of Spanish CIF with check digit
     * 📥 Input: VAT number string (CIF)
     * 📤 Output: ValidationResult with CIF-specific validation
     * 🌍 Scale: Spanish business market specific validation
     * 🛡️ Privacy: No external API calls, algorithmic validation only
     *
     * @param string $vatNumber The Spanish CIF to validate
     * @return ValidationResult Validation result with Spanish CIF checks
     */
    public function validateVatNumber(string $vatNumber): ValidationResult
    {
        return $this->validateCif(strtoupper(trim($vatNumber)), 'business');
    }

    /**
     * @Oracode Method: Detect Spanish Document Type
     * 🎯 Purpose: Identify if document is DNI, NIE, or CIF
     * 📥 Input: Spanish tax document
     * 📤 Output: Document type string
     * 🧱 Core Logic: Pattern recognition for Spanish documents
     *
     * @param string $document The document to analyze
     * @return string Document type (DNI, NIE, CIF, unknown)
     */
    private function detectSpanishDocumentType(string $document): string
    {
        // DNI: 8 digits + 1 letter
        if (preg_match('/^[0-9]{8}[A-Z]$/', $document)) {
            return 'DNI';
        }

        // NIE: X/Y/Z + 7 digits + 1 letter
        if (preg_match('/^[XYZ][0-9]{7}[A-Z]$/', $document)) {
            return 'NIE';
        }

        // CIF: 1 letter + 7 digits + 1 letter/digit
        if (preg_match('/^[ABCDEFGHJNPQRSUVW][0-9]{7}[0-9A-J]$/', $document)) {
            return 'CIF';
        }

        return 'unknown';
    }

    /**
     * @Oracode Method: Validate Spanish DNI
     * 🎯 Purpose: Validate DNI format and check letter
     * 📥 Input: DNI string and business type
     * 📤 Output: ValidationResult with DNI validation
     * 🧱 Core Logic: DNI modulo 23 algorithm
     *
     * @param string $dni The DNI to validate
     * @param string|null $businessType Business type context
     * @return ValidationResult Validation result
     */
    private function validateDni(string $dni, ?string $businessType): ValidationResult
    {
        $numbers = substr($dni, 0, 8);
        $letter = substr($dni, 8, 1);

        $expectedLetter = $this->dniLetters[(int) $numbers % 23];

        if ($letter !== $expectedLetter) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.tax_code_spain_dni_checksum'),
                [
                    'field' => 'tax_code',
                    'document_type' => 'DNI',
                    'expected_letter' => $expectedLetter,
                    'provided_letter' => $letter,
                    'business_type' => $businessType,
                    'country' => 'ES'
                ]
            );
        }

        return ValidationResult::valid(
            $dni,
            [
                'country' => 'ES',
                'document_type' => 'DNI',
                'business_type' => $businessType,
                'validation_level' => 'full_checksum',
                'original_input' => $dni
            ]
        );
    }

    /**
     * @Oracode Method: Validate Spanish NIE
     * 🎯 Purpose: Validate NIE format and check letter
     * 📥 Input: NIE string and business type
     * 📤 Output: ValidationResult with NIE validation
     * 🧱 Core Logic: NIE to DNI conversion + modulo 23 algorithm
     *
     * @param string $nie The NIE to validate
     * @param string|null $businessType Business type context
     * @return ValidationResult Validation result
     */
    private function validateNie(string $nie, ?string $businessType): ValidationResult
    {
        $firstLetter = substr($nie, 0, 1);
        $numbers = substr($nie, 1, 7);
        $letter = substr($nie, 8, 1);

        // Convert NIE to DNI equivalent
        $convertedNumber = $this->nieConversion[$firstLetter] . $numbers;
        $expectedLetter = $this->dniLetters[(int) $convertedNumber % 23];

        if ($letter !== $expectedLetter) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.tax_code_spain_nie_checksum'),
                [
                    'field' => 'tax_code',
                    'document_type' => 'NIE',
                    'expected_letter' => $expectedLetter,
                    'provided_letter' => $letter,
                    'business_type' => $businessType,
                    'country' => 'ES'
                ]
            );
        }

        return ValidationResult::valid(
            $nie,
            [
                'country' => 'ES',
                'document_type' => 'NIE',
                'business_type' => $businessType,
                'validation_level' => 'full_checksum',
                'original_input' => $nie
            ]
        );
    }

    /**
     * @Oracode Method: Validate Spanish CIF
     * 🎯 Purpose: Validate CIF format and check digit/letter
     * 📥 Input: CIF string and business type
     * 📤 Output: ValidationResult with CIF validation
     * 🧱 Core Logic: CIF sum algorithm with organization type validation
     *
     * @param string $cif The CIF to validate
     * @param string|null $businessType Business type context
     * @return ValidationResult Validation result
     */
    private function validateCif(string $cif, ?string $businessType): ValidationResult
    {
        $organizationLetter = substr($cif, 0, 1);
        $numbers = substr($cif, 1, 7);
        $checkCharacter = substr($cif, 8, 1);

        // Validate organization type
        if (!isset($this->cifTypes[$organizationLetter])) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.tax_code_spain_cif_invalid_org'),
                [
                    'field' => 'tax_code',
                    'document_type' => 'CIF',
                    'invalid_org_letter' => $organizationLetter,
                    'business_type' => $businessType,
                    'country' => 'ES'
                ]
            );
        }

        // Calculate check digit/letter
        $sum = 0;
        for ($i = 0; $i < 7; $i++) {
            $digit = (int) $numbers[$i];

            if ($i % 2 === 1) { // Even positions (2nd, 4th, 6th)
                $sum += $digit;
            } else { // Odd positions (1st, 3rd, 5th, 7th)
                $doubled = $digit * 2;
                $sum += ($doubled > 9) ? (floor($doubled / 10) + ($doubled % 10)) : $doubled;
            }
        }

        $checkDigit = (10 - ($sum % 10)) % 10;
        $checkLetter = substr('JABCDEFGHI', $checkDigit, 1);

        // Some CIF types use letter, others use digit
        $validCheck = ($checkCharacter === (string) $checkDigit) || ($checkCharacter === $checkLetter);

        if (!$validCheck) {
            return ValidationResult::invalid(
                null,
                __('user_personal_data.validation.tax_code_spain_cif_checksum'),
                [
                    'field' => 'tax_code',
                    'document_type' => 'CIF',
                    'expected_digit' => $checkDigit,
                    'expected_letter' => $checkLetter,
                    'provided_check' => $checkCharacter,
                    'business_type' => $businessType,
                    'country' => 'ES'
                ]
            );
        }

        return ValidationResult::valid(
            $cif,
            [
                'country' => 'ES',
                'document_type' => 'CIF',
                'organization_type' => $this->cifTypes[$organizationLetter],
                'business_type' => $businessType,
                'validation_level' => 'full_checksum',
                'original_input' => $cif
            ]
        );
    }

    /**
     * @Oracode Method: Get Required Fiscal Fields for Spanish Business Type
     * 🎯 Purpose: Return Spanish-specific required fields for form rendering
     * 📥 Input: Business type (individual, business, non_profit, etc.)
     * 📤 Output: Array of required field names and Spanish validation rules
     * 🌍 Scale: Spanish market specific form fields
     * 🔧 Integration: Used by Blade components for form rendering
     *
     * @param string $businessType The type of business (individual, sole_proprietorship, corporation, etc.)
     * @return array<string, array{required: bool, rules: string[], label: string}> Spanish field requirements
     */
    public function getRequiredFields(string $businessType): array
    {
        $baseFields = [
            'tax_code' => [
                'required' => true,
                'rules' => ['string', 'regex:/^([0-9]{8}[A-Z]|[XYZ][0-9]{7}[A-Z]|[ABCDEFGHJNPQRSUVW][0-9]{7}[0-9A-J])$/'],
                'label' => __('user_personal_data.tax_code') . ' (DNI/NIE/CIF)'
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

        // Add business-specific fields for Spanish entities
        if (in_array($businessType, ['business', 'corporation', 'partnership', 'non_profit'])) {
            // For businesses, require CIF as VAT number
            $baseFields['vat_number'] = [
                'required' => true,
                'rules' => ['string', 'regex:/^[ABCDEFGHJNPQRSUVW][0-9]{7}[0-9A-J]$/'],
                'label' => __('user_personal_data.vat_number') . ' (CIF)'
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
     * @Oracode Method: Format Tax Code to Spanish Standard
     * 🎯 Purpose: Normalize Spanish tax document format for storage and display
     * 📥 Input: Raw tax code string from user input
     * 📤 Output: Properly formatted Spanish document (uppercase, clean)
     * 🌍 Scale: Spanish formatting standards
     * 🧱 Core Logic: Used before storage to ensure consistency
     *
     * @param string $taxCode Raw tax code string from user input
     * @return string Formatted Spanish document according to standards
     */
    public function formatTaxCode(string $taxCode): string
    {
        // Remove any spaces/separators and convert to uppercase
        $formatted = strtoupper(trim(str_replace([' ', '-', '.'], '', $taxCode)));

        return $formatted;
    }

    /**
     * @Oracode Method: Get Country Code for Spanish Validator
     * 🎯 Purpose: Return Spanish country identifier
     * 📤 Output: Spanish country code
     * 🔧 Integration: Used by factory for validator selection
     *
     * @return string Spanish country code
     */
    public function getCountryCode(): string
    {
        return 'ES';
    }

    /**
     * @Oracode Method: Get Human-Readable Country Name
     * 🎯 Purpose: Return localized Spanish country name for UI display
     * 📤 Output: Translated Spanish country name using Laravel localization
     * 🌐 i18n: Supports multiple languages for global UX
     *
     * @return string Localized Spanish country name
     */
    public function getCountryName(): string
    {
        return __('user_personal_data.countries.spain');
    }
}
