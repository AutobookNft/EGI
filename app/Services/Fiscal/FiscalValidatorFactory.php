<?php

namespace App\Services\Fiscal;

use App\Services\Fiscal\Validators\ItalyFiscalValidator;
use App\Services\Fiscal\Validators\PortugalFiscalValidator;
use App\Services\Fiscal\Validators\FranceFiscalValidator;
use App\Services\Fiscal\Validators\SpainFiscalValidator;
use App\Services\Fiscal\Validators\EnglandFiscalValidator;
use App\Services\Fiscal\Validators\GermanyFiscalValidator;
use App\Services\Fiscal\Validators\GenericFiscalValidator;
use InvalidArgumentException;

/**
 * @Oracode Factory: Country-Specific Fiscal Validator Factory (OS1-Compliant)
 * 🎯 Purpose: Create appropriate fiscal validator based on MVP country codes
 * 🌍 Scale: Supports ONLY 6 MVP markets (IT,PT,FR,ES,EN,DE) with fallback
 * 🧱 Core Logic: Factory pattern with validator caching for performance
 * 🔧 Integration: Used by controllers and form requests for validation
 * ⏰ MVP: Supports FlorenceEGI launch markets exclusively
 *
 * @package App\Services\Fiscal
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 2.0.0 (FlorenceEGI MVP - 6 Nations Only)
 * @deadline 2025-06-30
 */
class FiscalValidatorFactory
{
    /**
     * Cached validator instances for performance optimization
     * @var array<string, FiscalValidatorInterface>
     */
    private static array $validators = [];

    /**
     * MVP Country code to validator class mapping - ONLY 6 NATIONS
     * @var array<string, class-string<FiscalValidatorInterface>>
     */
    public static array $validatorMap = [
        'IT' => ItalyFiscalValidator::class,        // Italy - Primary market
        'PT' => PortugalFiscalValidator::class,     // Portugal
        'FR' => FranceFiscalValidator::class,       // France
        'ES' => SpainFiscalValidator::class,        // Spain
        'EN' => EnglandFiscalValidator::class,      // England
        'DE' => GermanyFiscalValidator::class,      // Germany
    ];

    /**
     * @Oracode Method: Get Supported Countries Translated
     * 🎯 Purpose: Act as the Single Source of Truth for the list of supported countries,
     * returning a localized, dropdown-ready associative array.
     * 📤 Output: An associative array [country_code => translated_name].
     * 🧱 Core Logic: Iterates over its own validator map and uses Laravel's
     * localization system to retrieve the country names.
     *
     * @return array
     */
    public static function getSupportedCountriesTranslated(): array
    {
        $supportedCodes = array_keys(self::$validatorMap);
        $translatedList = [];

        foreach ($supportedCodes as $code) {
            // Genera la chiave di traduzione in modo programmatico. Es: 'countries.it'
            $translationKey = 'countries.' . strtolower($code);
            $translatedList[$code] = __($translationKey);
        }

        return $translatedList;
    }

    /**
     * @Oracode Method: Create Country-Specific Fiscal Validator
     * 🎯 Purpose: Return appropriate validator for MVP country tax rules
     * 📥 Input: ISO 3166-1 alpha-2 country code (IT,PT,FR,ES,EN,DE ONLY)
     * 📤 Output: FiscalValidatorInterface implementation for country
     * 🌍 Scale: Supports ONLY 6 MVP markets with generic fallback
     * 🔧 Integration: Cached instances for performance optimization
     *
     * @param string $countryCode ISO 3166-1 alpha-2 country code from MVP list
     * @return FiscalValidatorInterface Country-specific or generic validator instance
     * @throws InvalidArgumentException When country code format is invalid (not 2-char ISO)
     */
    public static function create(string $countryCode): FiscalValidatorInterface
    {
        $countryCode = strtoupper(trim($countryCode));

        // Input validation for ISO format
        if (empty($countryCode) || strlen($countryCode) !== 2) {
            throw new InvalidArgumentException(
                "Invalid country code format. Expected ISO 3166-1 alpha-2 from MVP list (IT,PT,FR,ES,EN,DE)."
            );
        }

        // Return cached validator if available
        if (isset(self::$validators[$countryCode])) {
            return self::$validators[$countryCode];
        }

        // Create MVP country-specific validator or fallback to generic
        $validatorClass = self::$validatorMap[$countryCode] ?? GenericFiscalValidator::class;

        /** @var FiscalValidatorInterface $validator */
        $validator = new $validatorClass();

        // Cache for future use
        self::$validators[$countryCode] = $validator;

        return $validator;
    }

    /**
     * @Oracode Method: Get All Supported MVP Country Codes
     * 🎯 Purpose: Return list of MVP countries with specific validator implementations
     * 📤 Output: Array of supported ISO country codes (6 MVP nations only)
     * 🔧 Integration: Used for configuration and form generation
     *
     * @return array<int, string> Array of ISO 3166-1 alpha-2 country codes with specific validators
     */
    public static function getSupportedCountries(): array
    {
        return array_keys(self::$validatorMap);
    }

    /**
     * @Oracode Method: Check if Country Has Specific Validator
     * 🎯 Purpose: Determine if MVP country has custom validation rules
     * 📥 Input: ISO 3166-1 alpha-2 country code
     * 📤 Output: Boolean indicating specific validator availability
     * 🔧 Integration: Used for conditional form field rendering
     *
     * @param string $countryCode ISO 3166-1 alpha-2 country code to check
     * @return bool True if MVP country has specific validator, false if uses generic
     */
    public static function hasSpecificValidator(string $countryCode): bool
    {
        return isset(self::$validatorMap[strtoupper($countryCode)]);
    }

    /**
     * @Oracode Method: Clear Validator Cache
     * 🎯 Purpose: Clear cached validators for testing or configuration changes
     * 🧪 Testing: Used in test teardown to ensure clean state
     *
     * @return void
     */
    public static function clearCache(): void
    {
        self::$validators = [];
    }

    /**
     * @Oracode Method: Register Custom Validator for MVP Country
     * 🎯 Purpose: Allow runtime registration of new MVP country validators
     * 📥 Input: MVP country code and validator class name
     * 🔧 Integration: Used for plugin/extension system
     *
     * @param string $countryCode ISO 3166-1 alpha-2 country code (MVP list only)
     * @param class-string<FiscalValidatorInterface> $validatorClass FQCN of validator implementing interface
     * @return void
     * @throws InvalidArgumentException When validator class doesn't implement interface
     */
    public static function registerValidator(string $countryCode, string $validatorClass): void
    {
        $countryCode = strtoupper(trim($countryCode));

        // Validate validator class implements interface
        if (!is_subclass_of($validatorClass, FiscalValidatorInterface::class)) {
            throw new InvalidArgumentException(
                "Validator class must implement FiscalValidatorInterface"
            );
        }

        self::$validatorMap[$countryCode] = $validatorClass;

        // Clear cached instance if exists
        unset(self::$validators[$countryCode]);
    }

    /**
     * @Oracode Method: Get All Available Countries (MVP + Generic)
     * 🎯 Purpose: Return comprehensive list for form dropdowns
     * 📤 Output: Array of all countries (MVP specific + generic fallback indicator)
     * 🔧 Integration: Used for country selection forms
     *
     * @return array<string, array{name: string, hasSpecific: bool}> Country data with validator availability
     */
    public static function getAllCountries(): array
    {
        $supportedCountries = self::getSupportedCountries();
        $allCountries = config('countries.list', []); // Assumes country list config

        $result = [];
        foreach ($allCountries as $code => $name) {
            $result[$code] = [
                'name' => $name,
                'hasSpecific' => in_array($code, $supportedCountries, true)
            ];
        }

        return $result;
    }
}
