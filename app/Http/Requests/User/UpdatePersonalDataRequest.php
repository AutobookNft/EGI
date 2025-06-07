<?php

namespace App\Http\Requests\User;

use App\Helpers\FegiAuth;
use App\Services\Fiscal\FiscalValidatorFactory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Request: Personal Data Update Form Request (GDPR-Compliant)
 * 🎯 Purpose: Validate personal data updates with country-specific fiscal validation
 * 🛡️ Privacy: GDPR-compliant validation with consent enforcement and audit trail
 * 🧱 Core Logic: FegiAuth integration + fiscal validation + data sensitivity awareness
 * 🌍 Scale: Multi-country support with fallback validation patterns
 * ⏰ MVP: Critical for personal data management in FlorenceEGI user domains
 *
 * @package App\Http\Requests\User
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - GDPR Native)
 * @deadline 2025-06-30
 *
 * @oracode-dimension communication (data validation)
 * @oracode-dimension governance (consent management)
 * @oracode-dimension impact (data quality assurance)
 * @value-flow Validates and sanitizes personal data updates
 * @community-impact Ensures data quality and legal compliance
 * @transparency-level High - clear validation rules and error messages
 * @narrative-coherence Supports user dignity and data control rights
 */
class UpdatePersonalDataRequest extends FormRequest
{
    /**
     * Error manager for robust validation error handling
     * @var ErrorManagerInterface
     */
    protected ErrorManagerInterface $errorManager;

    /**
     * User's detected or specified country for validation context
     * @var string
     */
    protected string $userCountry;

    /**
     * @Oracode Method: Determine Authorization for Personal Data Updates
     * 🎯 Purpose: Verify user can edit their own personal data
     * 📥 Input: No parameters (uses current authentication context)
     * 📤 Output: Boolean authorization result
     * 🛡️ Privacy: Ensures users can only modify their own sensitive data
     * 🧱 Core Logic: FegiAuth integration with permission-based access control
     *
     * @return bool True if user authorized to update personal data
     */
    public function authorize(): bool
    {
        // Must be authenticated (weak or strong)
        if (!FegiAuth::check()) {
            return false;
        }

        // Must have permission to edit own personal data
        if (!FegiAuth::can('edit_own_personal_data')) {
            return false;
        }

        // Additional GDPR consent check for sensitive data
        $user = FegiAuth::user();
        if ($user && !$this->hasValidDataProcessingConsent($user)) {
            $this->logGdprViolationAttempt('personal_data_update_without_consent');
            return false;
        }

        return true;
    }

    /**
     * @Oracode Method: Get Validation Rules for Personal Data
     * 🎯 Purpose: Define comprehensive validation rules with country-specific logic
     * 📥 Input: No parameters (uses request data and user context)
     * 📤 Output: Array of Laravel validation rules
     * 🌍 Scale: Multi-country fiscal validation with intelligent fallbacks
     * 🛡️ Privacy: Validates sensitive data with appropriate constraints
     *
     * @return array<string, mixed> Laravel validation rules array
     */
    public function rules(): array
    {
        $this->initializeValidationContext();

        return [
            // Personal Identity Information
            'birth_date' => [
                'nullable',
                'date',
                'before:' . now()->subYears(13)->toDateString(),
                'after:' . now()->subYears(120)->toDateString()
            ],
            'birth_place' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[\p{L}\s\-\.\,\']+$/u'
            ],
            'gender' => [
                'nullable',
                Rule::in(['male', 'female', 'other', 'prefer_not_say'])
            ],

            // Address Information
            'street' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[\p{L}\p{N}\s\-\.\,\/\#]+$/u'
            ],
            'city' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[\p{L}\s\-\.\']+$/u'
            ],
            'region' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[\p{L}\s\-\.\']+$/u'
            ],
            'state' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[\p{L}\s\-\.\']+$/u'
            ],
            'province' => [
                'nullable',
                'string',
                'max:10',
                'regex:/^[A-Z]{2,3}$/i'
            ],
            'zip' => [
                'nullable',
                'string',
                'max:20',
                $this->getZipValidationRule()
            ],
            'country' => [
                'nullable',
                'string',
                'size:2',
                'regex:/^[A-Z]{2}$/',
                Rule::in($this->getSupportedCountryCodes())
            ],

            // Contact Information
            'home_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[\d\s\-\(\)\.]+$/'
            ],
            'cell_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[\d\s\-\(\)\.]+$/'
            ],
            'work_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[\d\s\-\(\)\.]+$/'
            ],
            'emergency_contact' => [
                'nullable',
                'string',
                'max:500',
                'regex:/^[\p{L}\p{N}\s\-\.\,\+\(\)\/]+$/u'
            ],

            // Fiscal Information (Country-Specific)
            'fiscal_code' => $this->getFiscalCodeValidationRules(),
            'tax_id_number' => $this->getTaxIdValidationRules(),

            // GDPR Consent Management
            'allow_personal_data_processing' => [
                'required',
                'boolean'
            ],
            'processing_purposes' => [
                'nullable',
                'array',
                'max:10'
            ],
            'processing_purposes.*' => [
                'string',
                Rule::in([
                    'account_management',
                    'service_delivery',
                    'legal_compliance',
                    'marketing',
                    'analytics',
                    'customer_support'
                ])
            ]
        ];
    }

    /**
     * @Oracode Method: Get Custom Validation Error Messages
     * 🎯 Purpose: Provide localized, user-friendly validation error messages
     * 📤 Output: Array of custom validation messages using translation keys
     * 🌐 i18n: Full localization support with fallback handling
     * 🛡️ Privacy: GDPR-compliant error messages without data exposure
     *
     * @return array<string, string> Custom validation messages
     */
    public function messages(): array
    {
        return [
            // Personal Identity
            'birth_date.before' => __('user_personal_data.validation.birth_date_age'),
            'birth_date.after' => __('user_personal_data.validation.birth_date_valid'),
            'birth_place.regex' => __('user_personal_data.validation.birth_place_format'),
            'gender.in' => __('user_personal_data.validation.gender_invalid'),

            // Address
            'street.regex' => __('user_personal_data.validation.street_format'),
            'city.regex' => __('user_personal_data.validation.city_format'),
            'province.regex' => __('user_personal_data.validation.province_format'),
            'zip.regex' => __('user_personal_data.validation.postal_code_invalid'),
            'country.in' => __('user_personal_data.validation.country_required'),

            // Contact
            'home_phone.regex' => __('user_personal_data.validation.phone_invalid'),
            'cell_phone.regex' => __('user_personal_data.validation.phone_invalid'),
            'work_phone.regex' => __('user_personal_data.validation.phone_invalid'),
            'emergency_contact.regex' => __('user_personal_data.validation.emergency_contact_format'),

            // Fiscal
            'fiscal_code.regex' => __('user_personal_data.validation.tax_code_format'),
            'tax_id_number.regex' => __('user_personal_data.validation.tax_code_invalid'),

            // GDPR
            'allow_personal_data_processing.required' => __('user_personal_data.validation.consent_required'),
            'processing_purposes.max' => __('user_personal_data.validation.processing_purposes_limit'),
            'processing_purposes.*.in' => __('user_personal_data.validation.processing_purpose_invalid')
        ];
    }

    /**
     * @Oracode Method: Get Custom Attribute Names for Validation
     * 🎯 Purpose: Provide human-readable field names for validation messages
     * 📤 Output: Array mapping field names to localized labels
     * 🌐 i18n: Supports multilingual form validation feedback
     *
     * @return array<string, string> Field name to label mapping
     */
    public function attributes(): array
    {
        return [
            'birth_date' => __('user_personal_data.birth_date'),
            'birth_place' => __('user_personal_data.birth_place'),
            'gender' => __('user_personal_data.gender'),
            'street' => __('user_personal_data.street_address'),
            'city' => __('user_personal_data.city'),
            'region' => __('user_personal_data.region'),
            'state' => __('user_personal_data.state'),
            'province' => __('user_personal_data.province'),
            'zip' => __('user_personal_data.postal_code'),
            'country' => __('user_personal_data.country'),
            'home_phone' => __('user_personal_data.phone'),
            'cell_phone' => __('user_personal_data.mobile'),
            'work_phone' => __('user_personal_data.work_phone'),
            'emergency_contact' => __('user_personal_data.emergency_contact'),
            'fiscal_code' => __('user_personal_data.tax_code'),
            'tax_id_number' => __('user_personal_data.id_card_number'),
            'allow_personal_data_processing' => __('user_personal_data.consent_required'),
            'processing_purposes' => __('user_personal_data.consent_description')
        ];
    }

    /**
     * @Oracode Method: Initialize Validation Context
     * 🎯 Purpose: Set up country-specific validation context and dependencies
     * 📥 Input: No parameters (uses request data and user context)
     * 📤 Output: Void (initializes internal state)
     * 🧱 Core Logic: Dependency injection setup and country detection
     *
     * @return void
     */
    protected function initializeValidationContext(): void
    {
        $this->errorManager = app(ErrorManagerInterface::class);
        $this->userCountry = $this->determineUserCountry();
    }

    /**
     * @Oracode Method: Determine User Country for Validation
     * 🎯 Purpose: Detect user's country from request, profile, or fallback
     * 📤 Output: ISO 3166-1 alpha-2 country code
     * 🌍 Scale: Intelligent country detection with multiple fallback methods
     *
     * @return string ISO country code for validation context
     */
    protected function determineUserCountry(): string
    {
        // Try from form data first
        if ($this->filled('country')) {
            return strtoupper($this->input('country'));
        }

        // Try from user profile
        $user = FegiAuth::user();
        if ($user && $user->country) {
            return strtoupper($user->country);
        }

        // Try to detect from Accept-Language header
        $acceptLanguage = $this->header('Accept-Language', '');
        $languageMap = [
            'it' => 'IT', 'de' => 'DE', 'fr' => 'FR',
            'en-US' => 'US', 'en-GB' => 'GB', 'es' => 'ES'
        ];

        foreach ($languageMap as $lang => $country) {
            if (str_contains($acceptLanguage, $lang)) {
                return $country;
            }
        }

        // Default to Italy for MVP
        return 'IT';
    }

    /**
     * @Oracode Method: Get Country-Specific Fiscal Code Validation Rules
     * 🎯 Purpose: Return validation rules for fiscal/tax codes based on country
     * 📤 Output: Array of validation rules for fiscal code field
     * 🌍 Scale: Country-specific validation using FiscalValidatorFactory
     *
     * @return array<string> Validation rules for fiscal code
     */
    protected function getFiscalCodeValidationRules(): array
    {

        $this->initializeValidationContext();

        try {
            $validator = FiscalValidatorFactory::create($this->userCountry);

            $rules = ['nullable', 'string'];

            // Add country-specific format validation
            if ($this->userCountry === 'IT') {
                $rules[] = 'size:16';
                $rules[] = 'regex:/^[A-Z]{6}[0-9]{2}[ABCDEHLMPRST][0-9]{2}[A-Z][0-9]{3}[A-Z]$/i';
            } elseif ($this->userCountry === 'DE') {
                $rules[] = 'size:11';
                $rules[] = 'regex:/^[0-9]{11}$/';
            } elseif ($this->userCountry === 'FR') {
                $rules[] = 'size:13';
                $rules[] = 'regex:/^[0-9]{13}$/';
            } else {
                // Generic validation for other countries
                $rules[] = 'max:20';
                $rules[] = 'regex:/^[A-Z0-9\-]+$/i';
            }

            return $rules;

        } catch (\Exception $e) {
            $this->errorManager->handle('FISCAL_VALIDATION_SETUP_ERROR', [
                'country' => $this->userCountry,
                'user_id' => FegiAuth::id(),
                'error' => $e->getMessage()
            ], $e);

            return ['nullable', 'string', 'max:20'];
        }
    }

    /**
     * @Oracode Method: Get Tax ID Validation Rules
     * 🎯 Purpose: Return validation rules for secondary tax identification
     * 📤 Output: Array of validation rules for tax ID field
     * 🧱 Core Logic: Fallback validation for additional tax identifiers
     *
     * @return array<string> Validation rules for tax ID number
     */
    protected function getTaxIdValidationRules(): array
    {
        return [
            'nullable',
            'string',
            'max:30',
            'regex:/^[A-Z0-9\-\s]+$/i'
        ];
    }

    /**
     * @Oracode Method: Get ZIP Code Validation Rule
     * 🎯 Purpose: Return country-specific postal code validation
     * 📤 Output: Validation rule string for ZIP/postal codes
     * 🌍 Scale: Supports major country postal code formats
     *
     * @return string Regex validation rule for postal codes
     */
    protected function getZipValidationRule(): string
    {
        return match ($this->userCountry) {
            'IT' => 'regex:/^[0-9]{5}$/',           // Italian CAP
            'DE' => 'regex:/^[0-9]{5}$/',           // German PLZ
            'FR' => 'regex:/^[0-9]{5}$/',           // French Code Postal
            'US' => 'regex:/^[0-9]{5}(-[0-9]{4})?$/', // US ZIP+4
            'GB' => 'regex:/^[A-Z]{1,2}[0-9R][0-9A-Z]?\s?[0-9][A-Z]{2}$/i', // UK Postcode
            default => 'regex:/^[A-Z0-9\s\-]{3,10}$/i' // Generic
        };
    }

    /**
     * @Oracode Method: Get Supported Country Codes
     * 🎯 Purpose: Return list of countries supported by the platform
     * 📤 Output: Array of ISO 3166-1 alpha-2 country codes
     * 🌍 Scale: Configurable country support for global expansion
     *
     * @return array<string> Supported country codes
     */
    protected function getSupportedCountryCodes(): array
    {
        return [
            'IT', 'DE', 'FR', 'US', 'GB', 'ES', 'PT', 'NL', 'BE', 'CH',
            'AT', 'SE', 'NO', 'DK', 'FI', 'IE', 'PL', 'CZ', 'HU', 'GR'
        ];
    }

    /**
     * @Oracode Method: Check Valid Data Processing Consent
     * 🎯 Purpose: Verify user has valid GDPR consent for data processing
     * 📥 Input: User model instance
     * 📤 Output: Boolean indicating valid consent status
     * 🛡️ Privacy: GDPR compliance verification for sensitive operations
     *
     * @param \App\Models\User $user User to check consent for
     * @return bool True if user has valid data processing consent
     */
    protected function hasValidDataProcessingConsent(\App\Models\User $user): bool
    {
        // Check if user has given explicit consent for personal data processing
        if ($user->personalData && $user->personalData->allow_personal_data_processing) {
            return true;
        }

        // Check for consent via UserConsent model if available
        if (method_exists($user, 'hasConsentFor')) {
            return $user->hasConsentFor('personal_data_processing');
        }

        // Default to false for GDPR compliance
        return false;
    }

    /**
     * @Oracode Method: Log GDPR Violation Attempt
     * 🎯 Purpose: Log attempted GDPR violations for compliance audit
     * 📥 Input: Violation type string
     * 📤 Output: Void (logs violation attempt)
     * 🛡️ Privacy: Security logging for GDPR compliance monitoring
     *
     * @param string $violationType Type of GDPR violation attempted
     * @return void
     */
    protected function logGdprViolationAttempt(string $violationType): void
    {

        $this->initializeValidationContext();

        $this->errorManager->handle('GDPR_VIOLATION_ATTEMPT', [
            'violation_type' => $violationType,
            'user_id' => FegiAuth::id(),
            'ip_address' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'timestamp' => now()->toISOString()
        ]);
    }
}
