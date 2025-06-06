<?php

namespace App\Services\Fiscal\Validators;

use App\Services\Fiscal\FiscalValidatorInterface;
use App\Services\Fiscal\ValidationResult;

/**
 * @Oracode Validator: Italy Fiscal Validation (OS1-Compliant)
 * 🎯 Purpose: Validate Italian Codice Fiscale and Partita IVA with checksum
 * 🌍 Scale: Italian market (primary FlorenceEGI market)
 * 🧱 Core Logic: Full Codice Fiscale algorithm + Partita IVA Luhn validation
 * 🛡️ Privacy: Secure validation without external API calls
 * ⏰ MVP: Critical for Italian market compliance
 *
 * @package App\Services\Fiscal\Validators
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Italian Market)
 * @deadline 2025-06-30
 */
class ItalyFiscalValidator implements FiscalValidatorInterface
{
    /**
     * Codice Fiscale check digit calculation table
     * @var array<string, int>
     */
    private array $codiceFiscaleOddTable = [
        '0' => 1, '1' => 0, '2' => 5, '3' => 7, '4' => 9, '5' => 13, '6' => 15, '7' => 17, '8' => 19, '9' => 21,
        'A' => 1, 'B' => 0, 'C' => 5, 'D' => 7, 'E' => 9, 'F' => 13, 'G' => 15, 'H' => 17, 'I' => 19, 'J' => 21,
        'K' => 2, 'L' => 4, 'M' => 18, 'N' => 20, 'O' => 11, 'P' => 3, 'Q' => 6, 'R' => 8, 'S' => 12, 'T' => 14,
        'U' => 16, 'V' => 10, 'W' => 22, 'X' => 25, 'Y' => 24, 'Z' => 23
    ];

    /**
     * Codice Fiscale even position values
     * @var array<string, int>
     */
    private array $codiceFiscaleEvenTable = [
        '0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9,
        'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4, 'F' => 5, 'G' => 6, 'H' => 7, 'I' => 8, 'J' => 9,
        'K' => 10, 'L' => 11, 'M' => 12, 'N' => 13, 'O' => 14, 'P' => 15, 'Q' => 16, 'R' => 17, 'S' => 18, 'T' => 19,
        'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23, 'Y' => 24, 'Z' => 25
    ];

    /**
     * Codice Fiscale check digit letters
     * @var array<int, string>
     */
    private array $codiceFiscaleCheckDigits = [
        0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G', 7 => 'H', 8 => 'I', 9 => 'J',
        10 => 'K', 11 => 'L', 12 => 'M', 13 => 'N', 14 => 'O', 15 => 'P', 16 => 'Q', 17 => 'R', 18 => 'S', 19 => 'T',
        20 => 'U', 21 => 'V', 22 => 'W', 23 => 'X', 24 => 'Y', 25 => 'Z'
    ];

    private ?string $errorCode = null;

    /**
     * @Oracode Method: Validate Tax/Fiscal Code for Italy (Codice Fiscale)
     * 🎯 Purpose: Complete validation of Italian Codice Fiscale with checksum
     * 📥 Input: Tax code string (Codice Fiscale), optional business type
     * 📤 Output: ValidationResult with checksum validation
     * 🌍 Scale: Italian market specific validation
     * 🛡️ Privacy: No external API calls, algorithmic validation only
     *
     * @param string $taxCode The Italian Codice Fiscale to validate
     * @param string|null $businessType Optional business context (individual, business, non_profit)
     * @return ValidationResult Validation result with Italian-specific checks
     */
    public function validateTaxCode(string $taxCode, ?string $businessType = null): ValidationResult
    {
        $taxCode = strtoupper(trim($taxCode));

        // Basic empty check
        if (empty($taxCode)) {
            return ValidationResult::invalid($this->errorCode,
                __('user_personal_data.validation.tax_code_required'),
                [
                    'field' => 'tax_code',
                    'business_type' => $businessType,
                    'country' => 'IT'
                ]
            );
        }

        // Italian Codice Fiscale must be exactly 16 characters
        if (strlen($taxCode) !== 16) {
            return ValidationResult::invalid($this->errorCode,
                __('user_personal_data.validation.tax_code_italy_length', ['required' => 16]),
                [
                    'field' => 'tax_code',
                    'required_length' => 16,
                    'actual_length' => strlen($taxCode),
                    'business_type' => $businessType,
                    'country' => 'IT'
                ]
            );
        }

        // Codice Fiscale format validation (15 alphanumeric + 1 letter check digit)
        if (!preg_match('/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/', $taxCode)) {
            return ValidationResult::invalid($this->errorCode,
                __('user_personal_data.validation.tax_code_italy_format'),
                [
                    'field' => 'tax_code',
                    'expected_format' => 'AAAAAANNANNNAANNA',
                    'business_type' => $businessType,
                    'country' => 'IT'
                ]
            );
        }

        // Validate checksum digit
        $checksumValidation = $this->validateCodiceFiscaleChecksum($taxCode);
        if (!$checksumValidation) {
            return ValidationResult::invalid($this->errorCode,
                __('user_personal_data.validation.tax_code_italy_checksum'),
                [
                    'field' => 'tax_code',
                    'validation_type' => 'checksum',
                    'business_type' => $businessType,
                    'country' => 'IT'
                ]
            );
        }

        // Additional business logic validation
        $businessValidation = $this->validateItalianBusinessLogic($taxCode, $businessType);
        if (!$businessValidation['valid']) {
            return ValidationResult::invalid($this->errorCode,
                $businessValidation['message'],
                array_merge($businessValidation['context'], [
                    'field' => 'tax_code',
                    'business_type' => $businessType,
                    'country' => 'IT'
                ])
            );
        }

        return ValidationResult::valid(
            $taxCode, // Already formatted (uppercase)
            [
                'country' => 'IT',
                'business_type' => $businessType,
                'validation_level' => 'full_checksum',
                'birth_date_encoded' => $this->extractBirthDateFromCodiceFiscale($taxCode),
                'gender_encoded' => $this->extractGenderFromCodiceFiscale($taxCode),
                'original_input' => $taxCode
            ]
        );
    }

    /**
     * @Oracode Method: Validate VAT/Business Registration Number for Italy (Partita IVA)
     * 🎯 Purpose: Complete validation of Italian Partita IVA with Luhn algorithm
     * 📥 Input: VAT number string (Partita IVA)
     * 📤 Output: ValidationResult with Luhn checksum validation
     * 🌍 Scale: Italian business market specific validation
     * 🛡️ Privacy: No external API calls, algorithmic validation only
     *
     * @param string $vatNumber The Italian Partita IVA to validate
     * @return ValidationResult Validation result with Italian VAT checks
     */
    public function validateVatNumber(string $vatNumber): ValidationResult
    {
        $vatNumber = trim($vatNumber);

        // Basic empty check
        if (empty($vatNumber)) {
            return ValidationResult::invalid($this->errorCode,
                __('user_personal_data.validation.vat_number_required'),
                [
                    'field' => 'vat_number',
                    'country' => 'IT'
                ]
            );
        }

        // Italian Partita IVA must be exactly 11 digits
        if (strlen($vatNumber) !== 11) {
            return ValidationResult::invalid($this->errorCode,
                __('user_personal_data.validation.vat_number_italy_length', ['required' => 11]),
                [
                    'field' => 'vat_number',
                    'required_length' => 11,
                    'actual_length' => strlen($vatNumber),
                    'country' => 'IT'
                ]
            );
        }

        // Partita IVA must be numeric only
        if (!preg_match('/^[0-9]{11}$/', $vatNumber)) {
            return ValidationResult::invalid($this->errorCode,
                __('user_personal_data.validation.vat_number_italy_format'),
                [
                    'field' => 'vat_number',
                    'expected_format' => 'numeric_only',
                    'country' => 'IT'
                ]
            );
        }

        // Validate Partita IVA checksum (Italian Luhn algorithm)
        $checksumValidation = $this->validatePartitaIvaChecksum($vatNumber);
        if (!$checksumValidation) {
            return ValidationResult::invalid($this->errorCode,
                __('user_personal_data.validation.vat_number_italy_checksum'),
                [
                    'field' => 'vat_number',
                    'validation_type' => 'luhn_checksum',
                    'country' => 'IT'
                ]
            );
        }

        return ValidationResult::valid(
            $vatNumber,
            [
                'country' => 'IT',
                'validation_level' => 'full_checksum',
                'original_input' => $vatNumber
            ]
        );
    }

    /**
     * @Oracode Method: Validate Codice Fiscale Checksum
     * 🎯 Purpose: Implement official Italian Codice Fiscale checksum algorithm
     * 📥 Input: 16-character Codice Fiscale
     * 📤 Output: Boolean indicating checksum validity
     * 🧱 Core Logic: Official algorithm with odd/even position calculation
     *
     * @param string $codiceFiscale The 16-character Codice Fiscale
     * @return bool True if checksum is valid, false otherwise
     */
    private function validateCodiceFiscaleChecksum(string $codiceFiscale): bool
    {
        $sum = 0;

        // Calculate checksum for first 15 characters
        for ($i = 0; $i < 15; $i++) {
            $char = $codiceFiscale[$i];

            if ($i % 2 === 0) { // Odd position (1st, 3rd, 5th, etc. - 0-indexed)
                $sum += $this->codiceFiscaleOddTable[$char] ?? 0;
            } else { // Even position (2nd, 4th, 6th, etc. - 0-indexed)
                $sum += $this->codiceFiscaleEvenTable[$char] ?? 0;
            }
        }

        $expectedCheckDigit = $this->codiceFiscaleCheckDigits[$sum % 26];
        $actualCheckDigit = $codiceFiscale[15];

        return $expectedCheckDigit === $actualCheckDigit;
    }

    /**
     * @Oracode Method: Validate Partita IVA Checksum
     * 🎯 Purpose: Implement official Italian Partita IVA checksum algorithm
     * 📥 Input: 11-digit Partita IVA
     * 📤 Output: Boolean indicating checksum validity
     * 🧱 Core Logic: Modified Luhn algorithm specific to Italian VAT
     *
     * @param string $partitaIva The 11-digit Partita IVA
     * @return bool True if checksum is valid, false otherwise
     */
    private function validatePartitaIvaChecksum(string $partitaIva): bool
    {
        $sum = 0;

        // Calculate weighted sum for first 10 digits
        for ($i = 0; $i < 10; $i++) {
            $digit = (int) $partitaIva[$i];

            if ($i % 2 === 0) { // Odd position (1st, 3rd, 5th, etc.)
                $sum += $digit;
            } else { // Even position (2nd, 4th, 6th, etc.)
                $doubled = $digit * 2;
                $sum += ($doubled > 9) ? ($doubled - 9) : $doubled;
            }
        }

        $checkDigit = (10 - ($sum % 10)) % 10;
        $actualCheckDigit = (int) $partitaIva[10];

        return $checkDigit === $actualCheckDigit;
    }

    /**
     * @Oracode Method: Validate Italian Business Logic
     * 🎯 Purpose: Additional business rules for Italian fiscal codes
     * 📥 Input: Codice Fiscale and business type
     * 📤 Output: Array with validation result and context
     * 🧱 Core Logic: Italian-specific business validation rules
     *
     * @param string $codiceFiscale The Codice Fiscale to validate
     * @param string|null $businessType The business type context
     * @return array{valid: bool, message: string, context: array<string, mixed>}
     */
    private function validateItalianBusinessLogic(string $codiceFiscale, ?string $businessType): array
    {
        // Extract birth date to validate age and temporal logic
        $birthDate = $this->extractBirthDateFromCodiceFiscale($codiceFiscale);

        if ($birthDate && $birthDate > now()) {
            return [
                'valid' => false,
                'message' => __('user_personal_data.validation.tax_code_italy_future_birth'),
                'context' => ['extracted_birth_date' => $birthDate->format('Y-m-d')]
            ];
        }

        if ($birthDate && $birthDate < now()->subYears(150)) {
            return [
                'valid' => false,
                'message' => __('user_personal_data.validation.tax_code_italy_too_old'),
                'context' => ['extracted_birth_date' => $birthDate->format('Y-m-d')]
            ];
        }

        return [
            'valid' => true,
            'message' => '',
            'context' => []
        ];
    }

    /**
     * @Oracode Method: Extract Birth Date from Codice Fiscale
     * 🎯 Purpose: Decode birth date information from Codice Fiscale
     * 📥 Input: Valid Codice Fiscale
     * 📤 Output: Carbon date instance or null if extraction fails
     * 🧱 Core Logic: Decode year, month, day from specific positions
     *
     * @param string $codiceFiscale The Codice Fiscale to decode
     * @return \Carbon\Carbon|null Extracted birth date or null
     */
    private function extractBirthDateFromCodiceFiscale(string $codiceFiscale): ?\Carbon\Carbon
    {
        try {
            $year = (int) substr($codiceFiscale, 6, 2);
            $monthCode = substr($codiceFiscale, 8, 1);
            $dayGender = (int) substr($codiceFiscale, 9, 2);

            // Determine century (assume current century if year > current year % 100, else previous century)
            $currentYear = (int) date('Y');
            $currentYearShort = $currentYear % 100;
            $fullYear = ($year > $currentYearShort) ? (1900 + $year) : (2000 + $year);

            // Month mapping
            $monthMap = [
                'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'H' => 6,
                'L' => 7, 'M' => 8, 'P' => 9, 'R' => 10, 'S' => 11, 'T' => 12
            ];

            $month = $monthMap[$monthCode] ?? null;
            if (!$month) {
                return null;
            }

            // Day extraction (subtract 40 for females)
            $day = ($dayGender > 40) ? ($dayGender - 40) : $dayGender;

            return \Carbon\Carbon::createFromDate($fullYear, $month, $day);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @Oracode Method: Extract Gender from Codice Fiscale
     * 🎯 Purpose: Decode gender information from Codice Fiscale
     * 📥 Input: Valid Codice Fiscale
     * 📤 Output: Gender string (M/F) or null if extraction fails
     * 🧱 Core Logic: Day number indicates gender (>40 = female)
     *
     * @param string $codiceFiscale The Codice Fiscale to decode
     * @return string|null Extracted gender ('M'/'F') or null
     */
    private function extractGenderFromCodiceFiscale(string $codiceFiscale): ?string
    {
        try {
            $dayGender = (int) substr($codiceFiscale, 9, 2);
            return ($dayGender > 40) ? 'F' : 'M';
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @Oracode Method: Get Required Fiscal Fields for Italian Business Type
     * 🎯 Purpose: Return Italian-specific required fields for form rendering
     * 📥 Input: Business type (individual, business, non_profit, etc.)
     * 📤 Output: Array of required field names and Italian validation rules
     * 🌍 Scale: Italian market specific form fields
     * 🔧 Integration: Used by Blade components for form rendering
     *
     * @param string $businessType The type of business (individual, sole_proprietorship, corporation, etc.)
     * @return array<string, array{required: bool, rules: string[], label: string}> Italian field requirements
     */
    public function getRequiredFields(string $businessType): array
    {
        $baseFields = [
            'tax_code' => [
                'required' => true,
                'rules' => ['string', 'size:16', 'regex:/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/'],
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
            ],
            'birth_date' => [
                'required' => true,
                'rules' => ['date', 'before:today', 'after:' . now()->subYears(150)->format('Y-m-d')],
                'label' => __('user_personal_data.birth_date')
            ]
        ];

        // Add business-specific fields for Italian entities
        if (in_array($businessType, ['business', 'corporation', 'partnership', 'non_profit'])) {
            $baseFields['vat_number'] = [
                'required' => true,
                'rules' => ['string', 'size:11', 'regex:/^[0-9]{11}$/'],
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
     * @Oracode Method: Format Tax Code to Italian Standard
     * 🎯 Purpose: Normalize Codice Fiscale format for storage and display
     * 📥 Input: Raw tax code string from user input
     * 📤 Output: Properly formatted Codice Fiscale (uppercase, 16 chars)
     * 🌍 Scale: Italian formatting standards
     * 🧱 Core Logic: Used before storage to ensure consistency
     *
     * @param string $taxCode Raw tax code string from user input
     * @return string Formatted Codice Fiscale according to Italian standards
     */
    public function formatTaxCode(string $taxCode): string
    {
        // Remove any spaces and convert to uppercase
        $formatted = strtoupper(trim(str_replace(' ', '', $taxCode)));

        return $formatted;
    }

    /**
     * @Oracode Method: Get Country Code for Italian Validator
     * 🎯 Purpose: Return Italian country identifier
     * 📤 Output: Italian country code
     * 🔧 Integration: Used by factory for validator selection
     *
     * @return string Italian country code
     */
    public function getCountryCode(): string
    {
        return 'IT';
    }

    /**
     * @Oracode Method: Get Human-Readable Country Name
     * 🎯 Purpose: Return localized Italian country name for UI display
     * 📤 Output: Translated Italian country name using Laravel localization
     * 🌐 i18n: Supports multiple languages for global UX
     *
     * @return string Localized Italian country name
     */
    public function getCountryName(): string
    {
        return __('user_personal_data.countries.italy');
    }
}
