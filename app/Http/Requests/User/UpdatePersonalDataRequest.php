<?php

namespace App\Http\Requests\User;

use App\Helpers\FegiAuth;
use App\Services\Gdpr\ConsentService;
use App\Services\Fiscal\FiscalValidatorFactory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Request: Personal Data Update Form Request (OS1.5 Compliant)
 * 🎯 Purpose: Validate personal data updates with GDPR compliance and fiscal validation
 * 🛡️ Privacy: OS1.5 security-first validation with complete audit trail
 * 🧱 Core Logic: Clean validation, UEM/ULM integration, ConsentService integration
 * 🌍 Scale: Multi-country support with graceful fallback validation
 * ⏰ MVP: Production-ready for FlorenceEGI personal data management
 *
 * @package App\Http\Requests\User
 * @author Padmin D. Curtis (AI Partner OS1.5 Compliant)
 * @version 2.0.0 (Clean OS1.5 Implementation)
 * @date 2025-06-15
 *
 * @oracode-dimension communication (data validation)
 * @oracode-dimension governance (consent management)
 * @oracode-dimension impact (data quality assurance)
 */
class UpdatePersonalDataRequest extends FormRequest
{
    /**
     * ULM Logger for audit trail
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * UEM Error manager for robust validation error handling
     * @var ErrorManagerInterface
     */
    protected ErrorManagerInterface $errorManager;

    /**
     * ConsentService for GDPR validation
     * @var ConsentService
     */
    protected ConsentService $consentService;

    /**
     * User's country for validation context
     * @var string
     */
    protected string $userCountry;

    /**
     * @Oracode Method: Boot Request Dependencies
     * 🎯 Purpose: Initialize OS1.5 compliant dependencies for validation
     * 🛡️ Privacy: Set up secure validation context with audit capability
     * 🧱 Core Logic: DI-first initialization with graceful fallbacks
     */
    protected function initializeDependencies(): void
    {
        try {
            $this->logger = app(UltraLogManager::class);
            $this->errorManager = app(ErrorManagerInterface::class);
            $this->consentService = app(ConsentService::class);
            $this->userCountry = $this->determineUserCountry();

            $this->logger->info('UpdatePersonalDataRequest initialized', [
                'component' => 'UpdatePersonalDataRequest',
                'user_id' => FegiAuth::id(),
                'user_country' => $this->userCountry,
                'request_ip' => $this->ip(),
                'operation' => 'request_initialization'
            ]);

        } catch (\Throwable $e) {
            // ✅ OS1.5 PROACTIVE SECURITY: Graceful degradation if dependencies fail
            if (app()->bound(UltraLogManager::class)) {
                app(UltraLogManager::class)->warning('Failed to initialize UpdatePersonalDataRequest dependencies', [
                    'error' => $e->getMessage(),
                    'operation' => 'dependency_initialization_failure'
                ]);
            }

            // Set safe defaults
            $this->userCountry = 'IT';
        }
    }

    /**
     * @Oracode Method: Authorization Check
     * 🎯 Purpose: OS1.5 compliant authorization with clear audit trail
     * 📥 Input: No parameters (uses authentication context)
     * 📤 Output: Boolean authorization result
     * 🛡️ Privacy: Secure authorization with GDPR consent verification
     * 🧱 Core Logic: Clean authorization flow with comprehensive logging
     *
     * @return bool True if user authorized to update personal data
     */
    public function authorize(): bool
    {
        $this->initializeDependencies();

        $this->logger->info('Personal data update authorization started', [
            'component' => 'UpdatePersonalDataRequest',
            'operation' => 'authorization_check',
            'user_id' => FegiAuth::id(),
            'auth_type' => FegiAuth::getAuthType(),
            'ip_address' => $this->ip()
        ]);

        // ✅ OS1.5 EXPLICITLY INTENTIONAL: Clear authorization steps

        // STEP 1: Authentication Check
        if (!FegiAuth::check()) {
            $this->logger->warning('Personal data update denied - user not authenticated', [
                'component' => 'UpdatePersonalDataRequest',
                'operation' => 'authorization_denied',
                'reason' => 'not_authenticated',
                'ip_address' => $this->ip()
            ]);
            return false;
        }

        $user = FegiAuth::user();

        // STEP 2: Permission Check
        if (!FegiAuth::can('edit_own_personal_data')) {
            $this->logger->warning('Personal data update denied - insufficient permissions', [
                'component' => 'UpdatePersonalDataRequest',
                'operation' => 'authorization_denied',
                'reason' => 'insufficient_permissions',
                'user_id' => $user->id,
                'ip_address' => $this->ip()
            ]);
            return false;
        }

        // STEP 3: GDPR Consent Check (Only for existing users with data)
        if (!$this->hasRequiredGdprConsent($user)) {
            $this->logger->warning('Personal data update denied - missing GDPR consent', [
                'component' => 'UpdatePersonalDataRequest',
                'operation' => 'authorization_denied',
                'reason' => 'missing_gdpr_consent',
                'user_id' => $user->id,
                'ip_address' => $this->ip()
            ]);
            return false;
        }

        // ✅ SUCCESS: Authorization granted
        $this->logger->info('Personal data update authorization granted', [
            'component' => 'UpdatePersonalDataRequest',
            'operation' => 'authorization_granted',
            'user_id' => $user->id,
            'auth_type' => FegiAuth::getAuthType(),
            'ip_address' => $this->ip()
        ]);

        return true;
    }

    /**
     * @Oracode Method: Validation Rules
     * 🎯 Purpose: OS1.5 compliant validation rules with country-specific logic
     * 📥 Input: No parameters (uses request data and user context)
     * 📤 Output: Array of Laravel validation rules
     * 🌍 Scale: Multi-country fiscal validation with intelligent fallbacks
     * 🛡️ Privacy: Validates sensitive data with appropriate constraints
     *
     * @return array<string, mixed> Laravel validation rules array
     */
    public function rules(): array
    {
        $this->initializeDependencies();

        $this->logger->info('Generating validation rules for personal data update', [
            'component' => 'UpdatePersonalDataRequest',
            'operation' => 'validation_rules_generation',
            'user_id' => FegiAuth::id(),
            'user_country' => $this->userCountry
        ]);

        // ✅ OS1.5 MODULAR SEMANTICS: Organized rule groups
        $rules = array_merge(
            $this->getPersonalIdentityRules(),
            $this->getAddressRules(),
            $this->getContactRules(),
            $this->getFiscalRules(),
            $this->getConsentRules()
        );

        $this->logger->debug('Validation rules generated', [
            'component' => 'UpdatePersonalDataRequest',
            'operation' => 'validation_rules_complete',
            'rule_count' => count($rules),
            'rule_groups' => ['identity', 'address', 'contact', 'fiscal', 'consent']
        ]);

        return $rules;
    }

    /**
     * @Oracode Method: Custom Validation Messages
     * 🎯 Purpose: User-friendly, localized validation error messages
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

            // GDPR Consent
            'consents.allow_personal_data_processing.required' => __('user_personal_data.validation.consent_required'),
            'consent_metadata.processing_purposes.max' => __('user_personal_data.validation.processing_purposes_limit'),
            'consent_metadata.processing_purposes.*.in' => __('user_personal_data.validation.processing_purpose_invalid')
        ];
    }

    /**
     * @Oracode Method: Custom Attribute Names
     * 🎯 Purpose: Human-readable field names for validation messages
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
            'consents.allow_personal_data_processing' => __('user_personal_data.consent_required'),
            'consent_metadata.processing_purposes' => __('user_personal_data.consent_description')
        ];
    }

    /**
     * @Oracode Method: Input Preparation and Sanitization
     * 🎯 Purpose: Clean and prepare input data before validation
     * 📥 Input: No parameters (modifies request data)
     * 📤 Output: Void (side effect: sanitized request data)
     * 🛡️ Privacy: Input sanitization for security
     * 🧱 Core Logic: OS1.5 proactive security through data cleaning
     */
    protected function prepareForValidation(): void
    {
        $this->initializeDependencies();

        $this->logger->debug('Preparing personal data for validation', [
            'component' => 'UpdatePersonalDataRequest',
            'operation' => 'input_preparation',
            'user_id' => FegiAuth::id(),
            'input_fields' => array_keys($this->all())
        ]);

        // ✅ OS1.5 PROACTIVE SECURITY: Sanitize input data
        $sanitizedData = [];

        // Sanitize personal data fields
        if ($this->filled('birth_place')) {
            $sanitizedData['birth_place'] = $this->sanitizeTextInput($this->input('birth_place'));
        }

        if ($this->filled('street')) {
            $sanitizedData['street'] = $this->sanitizeTextInput($this->input('street'));
        }

        if ($this->filled('city')) {
            $sanitizedData['city'] = $this->sanitizeTextInput($this->input('city'));
        }

        if ($this->filled('province')) {
            $sanitizedData['province'] = strtoupper(trim($this->input('province')));
        }

        if ($this->filled('country')) {
            $sanitizedData['country'] = strtoupper(trim($this->input('country')));
        }

        if ($this->filled('zip')) {
            $sanitizedData['zip'] = preg_replace('/[^A-Z0-9\-\s]/', '', strtoupper(trim($this->input('zip'))));
        }

        if ($this->filled('fiscal_code')) {
            $sanitizedData['fiscal_code'] = strtoupper(preg_replace('/[^A-Z0-9]/', '', $this->input('fiscal_code')));
        }

        // Sanitize consent data
        if ($this->filled('consents')) {
            $sanitizedData['consents'] = $this->sanitizeConsentData($this->input('consents', []));
        }

        if ($this->filled('consent_metadata')) {
            $sanitizedData['consent_metadata'] = $this->sanitizeConsentMetadata($this->input('consent_metadata', []));
        }

        // Apply sanitized data
        $this->merge($sanitizedData);

        $this->logger->debug('Input preparation completed', [
            'component' => 'UpdatePersonalDataRequest',
            'operation' => 'input_preparation_complete',
            'sanitized_fields' => array_keys($sanitizedData)
        ]);
    }

    // ================================================================
    // PRIVATE HELPER METHODS - OS1.5 MODULAR SEMANTICS
    // ================================================================

    /**
     * @Oracode Method: Check Required GDPR Consent
     * 🎯 Purpose: Verify user has required GDPR consent for data processing
     * 📥 Input: User model instance
     * 📤 Output: Boolean indicating valid consent status
     * 🛡️ Privacy: GDPR compliance verification using ConsentService
     * 🧱 Core Logic: Smart consent checking with first-time user support
     *
     * @param \App\Models\User $user User to check consent for
     * @return bool True if user has valid data processing consent
     */
    private function hasRequiredGdprConsent(\App\Models\User $user): bool
    {
        try {
            // ✅ OS1.5 SIMPLICITY EMPOWERMENT: Check if user is setting up consent for first time
            if ($this->isFirstTimeConsentSetup()) {
                $this->logger->info('First-time consent setup detected - allowing through', [
                    'component' => 'UpdatePersonalDataRequest',
                    'operation' => 'first_time_consent_setup',
                    'user_id' => $user->id
                ]);
                return true;
            }

            // ✅ Use ConsentService for accurate consent checking
            $hasConsent = $this->consentService->hasConsent($user, 'allow-personal-data-processing');

            $this->logger->debug('GDPR consent verification completed', [
                'component' => 'UpdatePersonalDataRequest',
                'operation' => 'gdpr_consent_check',
                'user_id' => $user->id,
                'has_consent' => $hasConsent,
                'method' => 'ConsentService'
            ]);

            return $hasConsent;

        } catch (\Throwable $e) {
            $this->logger->warning('GDPR consent check failed - using fallback', [
                'component' => 'UpdatePersonalDataRequest',
                'operation' => 'gdpr_consent_check_fallback',
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            // ✅ OS1.5 RESILIENCE: Fallback to direct database check
            return $this->fallbackConsentCheck($user);
        }
    }

    /**
     * @Oracode Method: Check First-Time Consent Setup
     * 🎯 Purpose: Detect if user is providing consent for the first time
     * 📤 Output: Boolean indicating first-time setup
     * 🧱 Core Logic: Allow first-time consent without existing consent requirement
     *
     * @return bool True if this is first-time consent setup
     */
    private function isFirstTimeConsentSetup(): bool
    {
        $consentData = $this->input('consents', []);

        // User is providing main consent for first time
        return isset($consentData['allow-personal-data-processing'])
               && $consentData['allow-personal-data-processing'] === '1';
    }

    /**
     * @Oracode Method: Fallback Consent Check
     * 🎯 Purpose: Direct database consent verification when ConsentService fails
     * 📥 Input: User instance
     * 📤 Output: Boolean consent status
     * 🛡️ Privacy: Read-only database check for consent
     *
     * @param \App\Models\User $user User to check
     * @return bool True if user has consent in database
     */
    private function fallbackConsentCheck(\App\Models\User $user): bool
    {
        try {
            $hasConsent = \App\Models\UserConsent::where('user_id', $user->id)
                ->where('consent_type', 'allow-personal-data-processing')
                ->where('granted', true)
                ->exists();

            $this->logger->info('Fallback consent check completed', [
                'component' => 'UpdatePersonalDataRequest',
                'operation' => 'fallback_consent_check',
                'user_id' => $user->id,
                'has_consent' => $hasConsent
            ]);

            return $hasConsent;

        } catch (\Throwable $e) {
            $this->logger->error('Fallback consent check failed', [
                'component' => 'UpdatePersonalDataRequest',
                'operation' => 'fallback_consent_check_failed',
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            // ✅ OS1.5 PROACTIVE SECURITY: Fail secure for unknown consent status
            return false;
        }
    }

    /**
     * @Oracode Method: Get Personal Identity Validation Rules
     * 🎯 Purpose: Return validation rules for personal identity fields
     * 📤 Output: Array of validation rules for identity data
     *
     * @return array<string, mixed> Identity validation rules
     */
    private function getPersonalIdentityRules(): array
    {
        return [
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
        ];
    }

    /**
     * @Oracode Method: Get Address Validation Rules
     * 🎯 Purpose: Return validation rules for address fields
     * 📤 Output: Array of validation rules for address data
     *
     * @return array<string, mixed> Address validation rules
     */
    private function getAddressRules(): array
    {
        return [
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
        ];
    }

    /**
     * @Oracode Method: Get Contact Validation Rules
     * 🎯 Purpose: Return validation rules for contact fields
     * 📤 Output: Array of validation rules for contact data
     *
     * @return array<string, mixed> Contact validation rules
     */
    private function getContactRules(): array
    {
        return [
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
        ];
    }

    /**
     * @Oracode Method: Get Fiscal Validation Rules
     * 🎯 Purpose: Return country-specific fiscal validation rules for MVP countries
     * 📤 Output: Array of validation rules for fiscal data
     *
     * @return array<string, mixed> Fiscal validation rules
     */
    private function getFiscalRules(): array
    {
        try {
            $validator = FiscalValidatorFactory::create($this->userCountry);

            $rules = ['nullable', 'string'];

            // Add country-specific format validation for MVP countries
            switch ($this->userCountry) {
                case 'IT': // Italia
                    $rules[] = 'size:16';
                    $rules[] = 'regex:/^[A-Z]{6}[0-9]{2}[ABCDEHLMPRST][0-9]{2}[A-Z][0-9]{3}[A-Z]$/i';
                    break;
                case 'FR': // Francia
                    $rules[] = 'size:13';
                    $rules[] = 'regex:/^[0-9]{13}$/';
                    break;
                case 'SP': // Spagna
                    $rules[] = 'size:9';
                    $rules[] = 'regex:/^[0-9]{8}[A-Z]$/i';
                    break;
                case 'PT': // Portogallo
                    $rules[] = 'size:9';
                    $rules[] = 'regex:/^[0-9]{9}$/';
                    break;
                case 'EN': // Inghilterra
                    $rules[] = 'size:9';
                    $rules[] = 'regex:/^[A-Z]{2}[0-9]{6}[A-Z]$/i';
                    break;
                case 'DE': // Germania
                    $rules[] = 'size:11';
                    $rules[] = 'regex:/^[0-9]{11}$/';
                    break;
                default:
                    $rules[] = 'max:20';
                    $rules[] = 'regex:/^[A-Z0-9\-]+$/i';
            }

            return [
                'fiscal_code' => $rules,
                'tax_id_number' => [
                    'nullable',
                    'string',
                    'max:30',
                    'regex:/^[A-Z0-9\-\s]+$/i'
                ]
            ];

        } catch (\Exception $e) {
            $this->logger->warning('Fiscal validation setup failed - using generic rules', [
                'component' => 'UpdatePersonalDataRequest',
                'operation' => 'fiscal_validation_fallback',
                'country' => $this->userCountry,
                'error' => $e->getMessage()
            ]);

            return [
                'fiscal_code' => ['nullable', 'string', 'max:20'],
                'tax_id_number' => ['nullable', 'string', 'max:30']
            ];
        }
    }

    /**
     * @Oracode Method: Get Consent Validation Rules
     * 🎯 Purpose: Return validation rules for GDPR consent fields
     * 📤 Output: Array of validation rules for consent data
     *
     * @return array<string, mixed> Consent validation rules
     */
    private function getConsentRules(): array
    {
        return [
            'consents' => [
                'sometimes',
                'array'
            ],
            'consents.allow_personal_data_processing' => [
                'sometimes',
                'boolean'
            ],
            'consents.marketing' => [
                'sometimes',
                'boolean'
            ],
            'consents.analytics' => [
                'sometimes',
                'boolean'
            ],
            'consent_metadata' => [
                'sometimes',
                'array'
            ],
            'consent_metadata.processing_purposes' => [
                'sometimes',
                'array',
                'max:10'
            ],
            'consent_metadata.processing_purposes.*' => [
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
     * @Oracode Method: Determine User Country
     * 🎯 Purpose: Detect user's country for validation context (MVP countries)
     * 📤 Output: ISO 3166-1 alpha-2 country code
     *
     * @return string ISO country code for validation context
     */
    private function determineUserCountry(): string
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

        // Try to detect from Accept-Language header (MVP countries)
        $acceptLanguage = $this->header('Accept-Language', '');
        $languageMap = [
            'it' => 'IT', 'fr' => 'FR', 'es' => 'SP',
            'pt' => 'PT', 'en-GB' => 'EN', 'de' => 'DE'
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
     * @Oracode Method: Get ZIP Code Validation Rule
     * 🎯 Purpose: Return country-specific postal code validation
     * 📤 Output: Validation rule string for ZIP/postal codes
     *
     * @return string Regex validation rule for postal codes
     */
    private function getZipValidationRule(): string
    {
        return match ($this->userCountry) {
            'IT' => 'regex:/^[0-9]{5}$/',
            'DE' => 'regex:/^[0-9]{5}$/',
            'FR' => 'regex:/^[0-9]{5}$/',
            'US' => 'regex:/^[0-9]{5}(-[0-9]{4})?$/',
            'GB' => 'regex:/^[A-Z]{1,2}[0-9R][0-9A-Z]?\s?[0-9][A-Z]{2}$/i',
            default => 'regex:/^[A-Z0-9\s\-]{3,10}$/i'
        };
    }

    /**
     * @Oracode Method: Get Supported Country Codes
     * 🎯 Purpose: Return list of countries supported by the platform (MVP + extras)
     * 📤 Output: Array of ISO 3166-1 alpha-2 country codes
     *
     * @return array<string> Supported country codes
     */
    private function getSupportedCountryCodes(): array
    {
        return [
            // MVP Countries (Primary Support)
            'IT', 'FR', 'SP', 'PT', 'EN', 'DE',

            // Additional Countries (Extended Support)
            'US', 'NL', 'BE', 'CH', 'AT', 'SE', 'NO', 'DK',
            'FI', 'IE', 'PL', 'CZ', 'HU', 'GR'
        ];
    }

    /**
     * @Oracode Method: Sanitize Text Input
     * 🎯 Purpose: Clean text input for security
     * 📥 Input: Raw text string
     * 📤 Output: Sanitized text string
     *
     * @param string $input Raw text input
     * @return string Sanitized text
     */
    private function sanitizeTextInput(string $input): string
    {
        return trim(strip_tags($input));
    }

    /**
     * @Oracode Method: Sanitize Consent Data
     * 🎯 Purpose: Clean consent array data
     * 📥 Input: Raw consent array
     * 📤 Output: Sanitized consent array
     *
     * @param array $consents Raw consent data
     * @return array Sanitized consent data
     */
    private function sanitizeConsentData(array $consents): array
    {
        $sanitized = [];
        $allowedConsents = ['allow-personal-data-processing', 'marketing', 'analytics'];

        foreach ($consents as $key => $value) {
            if (in_array($key, $allowedConsents)) {
                $sanitized[$key] = $value === '1' || $value === 1 || $value === true ? '1' : '0';
            }
        }

        return $sanitized;
    }

    /**
     * @Oracode Method: Sanitize Consent Metadata
     * 🎯 Purpose: Clean consent metadata
     * 📥 Input: Raw metadata array
     * 📤 Output: Sanitized metadata array
     *
     * @param array $metadata Raw metadata
     * @return array Sanitized metadata
     */
    private function sanitizeConsentMetadata(array $metadata): array
    {
        $sanitized = [];

        if (isset($metadata['processing_purposes']) && is_array($metadata['processing_purposes'])) {
            $allowedPurposes = [
                'account_management', 'service_delivery', 'legal_compliance',
                'marketing', 'analytics', 'customer_support'
            ];

            $sanitized['processing_purposes'] = array_filter(
                $metadata['processing_purposes'],
                fn($purpose) => in_array($purpose, $allowedPurposes)
            );
        }

        return $sanitized;
    }
}
