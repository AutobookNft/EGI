<?php

namespace App\Services\Fiscal;

/**
 * @Oracode Interface: Global Fiscal Validation System
 * 🎯 Purpose: Enable worldwide tax compliance with country-specific fiscal rules
 * 🌍 Scale: Support global markets with country-specific validation logic
 * 🧱 Core Logic: Unified validation interface with country-specific implementations
 * 🛡️ Privacy: Secure handling of sensitive fiscal identification numbers
 * ⏰ MVP: Critical for global user onboarding in FlorenceEGI marketplace
 *
 * @package App\Services\Fiscal
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Global Fiscal Ready)
 * @deadline 2025-06-30
 */
interface FiscalValidatorInterface
{
    /**
     * @Oracode Method: Validate Tax/Fiscal Code for Country-Specific Rules
     * 🎯 Purpose: Validate personal/business tax identification numbers
     * 📥 Input: Tax code string, optional business type context
     * 📤 Output: ValidationResult with success/failure and details
     * 🌍 Scale: Country-specific algorithm validation (Luhn, checksum, format)
     * 🛡️ Privacy: No storage of validation data, sanitized error messages
     *
     * @param string $taxCode The tax/fiscal code to validate (e.g., Italian Codice Fiscale)
     * @param string|null $businessType Optional business context (individual, business, non_profit)
     * @return ValidationResult Validation result with success status and formatted value
     * @throws \InvalidArgumentException When tax code format is fundamentally invalid
     */
    public function validateTaxCode(string $taxCode, ?string $businessType = null): ValidationResult;

    /**
     * @Oracode Method: Validate VAT/Business Registration Number
     * 🎯 Purpose: Validate business VAT numbers for invoice/trading purposes
     * 📥 Input: VAT number string with country context
     * 📤 Output: ValidationResult with format and checksum validation
     * 🌍 Scale: EU VIES compatible, US EIN, global business number formats
     * 🛡️ Privacy: Secure validation without external API calls when possible
     *
     * @param string $vatNumber The VAT/business registration number to validate
     * @return ValidationResult Validation result with business number verification
     * @throws \InvalidArgumentException When VAT number format is fundamentally invalid
     */
    public function validateVatNumber(string $vatNumber): ValidationResult;

    /**
     * @Oracode Method: Get Required Fiscal Fields for Business Type
     * 🎯 Purpose: Return country-specific required fields for form rendering
     * 📥 Input: Business type (individual, business, non_profit, etc.)
     * 📤 Output: Array of required field names and validation rules
     * 🌍 Scale: Dynamic form generation based on country and business type
     * 🔧 Integration: Used by Blade components for form rendering
     *
     * @param string $businessType The type of business (individual, sole_proprietorship, corporation, etc.)
     * @return array<string, array> Associative array of field names with validation rules
     *                              Format: ['field_name' => ['required' => bool, 'rules' => string[], 'label' => string]]
     */
    public function getRequiredFields(string $businessType): array;

    /**
     * @Oracode Method: Format Tax Code to Country Standard
     * 🎯 Purpose: Normalize tax code format for storage and display
     * 📥 Input: Raw tax code string from user input
     * 📤 Output: Properly formatted tax code or original if no format rules
     * 🌍 Scale: Country-specific formatting (uppercase, spacing, checksum)
     * 🧱 Core Logic: Used before storage to ensure consistency
     *
     * @param string $taxCode Raw tax code string from user input
     * @return string Formatted tax code according to country standards
     */
    public function formatTaxCode(string $taxCode): string;

    /**
     * @Oracode Method: Get Country Code for Validator
     * 🎯 Purpose: Return ISO country code this validator handles
     * 📤 Output: ISO 3166-1 alpha-2 country code (IT, DE, FR, US, etc.)
     * 🔧 Integration: Used by factory for validator selection
     *
     * @return string ISO 3166-1 alpha-2 country code (e.g., 'IT', 'DE', 'FR')
     */
    public function getCountryCode(): string;

    /**
     * @Oracode Method: Get Human-Readable Country Name
     * 🎯 Purpose: Return localized country name for UI display
     * 📤 Output: Translated country name using Laravel localization
     * 🌐 i18n: Supports multiple languages for global UX
     *
     * @return string Localized country name (e.g., 'Italia', 'Germany', 'France')
     */
    public function getCountryName(): string;
}
