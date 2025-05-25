<?php

namespace App\Services\Gdpr;

use App\Models\User;
use App\Models\UserConsent;
use App\Models\ConsentVersion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Carbon\Carbon;

/**
 * @Oracode Service: Consent Management System
 * 🎯 Purpose: Manages user consents with versioning and audit trail
 * 🛡️ Privacy: Handles GDPR consent requirements with full compliance
 * 🧱 Core Logic: Tracks consent changes, versions, and legal basis
 *
 * @package App\Services
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-22
 */
class ConsentService
{
    /**
     * Logger instance for audit trail
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Error manager for robust error handling
     * @var UltraErrorManager
     */
    protected ErrorManagerInterface $errorManager;

    /**
     * Available consent types with their configurations
     * @var array
     */
    protected array $consentTypes = [
        'functional' => [
            'name' => 'Essential Cookies',
            'description' => 'Required for platform operation and security',
            'category' => 'necessary',
            'legal_basis' => 'legitimate_interest',
            'required' => true,
            'default_value' => true,
            'can_withdraw' => false
        ],
        'analytics' => [
            'name' => 'Analytics Cookies',
            'description' => 'Help us understand how you use our platform',
            'category' => 'statistics',
            'legal_basis' => 'consent',
            'required' => false,
            'default_value' => false,
            'can_withdraw' => true
        ],
        'marketing' => [
            'name' => 'Marketing Cookies',
            'description' => 'Used to deliver personalized advertisements',
            'category' => 'marketing',
            'legal_basis' => 'consent',
            'required' => false,
            'default_value' => false,
            'can_withdraw' => true
        ],
        'profiling' => [
            'name' => 'Profiling and Personalization',
            'description' => 'Create personalized experiences and recommendations',
            'category' => 'profiling',
            'legal_basis' => 'consent',
            'required' => false,
            'default_value' => false,
            'can_withdraw' => true
        ]
    ];

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @privacy-safe All injected dependencies handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
    }

    /**
     * Get user's current consent status
     *
     * @param User $user
     * @return array
     * @privacy-safe Returns user's own consent status only
     */
   public function getUserConsentStatus(User $user): array
    {
        try {
            $this->logger->info('Consent Service: Getting user consent status', [
                'user_id' => $user->id,
                'log_category' => 'CONSENT_SERVICE_OPERATION'
            ]);

            $currentConsents = $this->getCurrentUserConsents($user);
            $consentVersion = $this->getCurrentConsentVersion();

            $consents = collect();
            foreach ($this->consentTypes as $type => $config) {
                $userConsent = $currentConsents->where('consent_type', $type)->first();

                // Creo un oggetto stdClass invece di un array
                $consentItem = new \stdClass();
                $consentItem->id = $userConsent->id ?? null;
                $consentItem->purpose = $type;  // Il tipo di consenso è il purpose nella vista
                $consentItem->granted = $userConsent ? $userConsent->granted : $config['default_value'];
                $consentItem->status = $userConsent ? ($userConsent->granted ? 'active' : 'withdrawn') : 'not_given';
                $consentItem->timestamp = $userConsent?->created_at;
                $consentItem->given_at = $userConsent?->created_at;
                $consentItem->withdrawn_at = $userConsent?->withdrawn_at;
                $consentItem->consent_method = $userConsent?->consent_method ?? 'web';
                $consentItem->version = $userConsent?->consent_version_id ?? $consentVersion->id;
                $consentItem->consentVersion = $userConsent?->consentVersion ?? $consentVersion;
                $consentItem->required = $config['required'];
                $consentItem->can_withdraw = $config['can_withdraw'];
                $consentItem->legal_basis = $config['legal_basis'];
                $consentItem->description = $config['description'];

                $consents->push($consentItem);
            }

            // Calcolare anche i dati per il consent summary
            $consentSummary = [
                'active_consents' => $consents->where('status', 'active')->count(),
                'total_consents' => $consents->count(),
                'compliance_score' => $consents->count() > 0
                    ? round(($consents->where('granted', true)->count() / $consents->count()) * 100)
                    : 0
            ];

            return [
                'userConsents' => $consents,
                'consentSummary' => $consentSummary,
                'last_updated' => $currentConsents->max('created_at'),
                'consent_version' => $consentVersion->version,
                'user_id' => $user->id
            ];

        } catch (\Exception $e) {
            $this->logger->error('Consent Service: Failed to get user consent status', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'log_category' => 'CONSENT_SERVICE_ERROR'
            ]);

            throw $e;
        }
    }

    /**
     * Update user consents with full audit trail
     *
     * @param User $user
     * @param array $consents
     * @return array
     * @privacy-safe Updates consents for authenticated user only
     */
    public function updateUserConsents(User $user, array $consents): array
    {
        try {
            $this->logger->info('Consent Service: Updating user consents', [
                'user_id' => $user->id,
                'consent_changes' => $consents,
                'log_category' => 'CONSENT_SERVICE_OPERATION'
            ]);

            $previousConsents = $this->getUserConsentStatus($user);
            $consentVersion = $this->getCurrentConsentVersion();
            $changes = [];

            DB::transaction(function () use ($user, $consents, $consentVersion, &$changes, $previousConsents) {
                foreach ($consents as $type => $granted) {
                    // Validate consent type
                    if (!isset($this->consentTypes[$type])) {
                        throw new \InvalidArgumentException("Invalid consent type: {$type}");
                    }

                    $config = $this->consentTypes[$type];
                    $granted = (bool) $granted;

                    // Check if required consents are being denied
                    if ($config['required'] && !$granted) {
                        $this->logger->warning('Consent Service: Attempt to deny required consent', [
                            'user_id' => $user->id,
                            'consent_type' => $type,
                            'log_category' => 'CONSENT_SERVICE_WARNING'
                        ]);
                        $granted = true; // Force required consents to true
                    }

                    // Check for changes
                    $previousValue = $previousConsents['consents'][$type]['granted'] ?? $config['default_value'];
                    if ($previousValue !== $granted) {
                        $changes[$type] = [
                            'from' => $previousValue,
                            'to' => $granted,
                            'timestamp' => now()
                        ];

                        // Store new consent record
                        UserConsent::create([
                            'user_id' => $user->id,
                            'consent_type' => $type,
                            'granted' => $granted,
                            'consent_version_id' => $consentVersion->id,
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                            'legal_basis' => $config['legal_basis'],
                            'withdrawal_method' => !$granted ? 'manual' : null,
                            'metadata' => [
                                'source' => 'user_preferences',
                                'previous_value' => $previousValue,
                                'session_id' => session()->getId()
                            ]
                        ]);
                    }
                }

                // Update user's consent summary for quick access
                $this->updateUserConsentSummary($user, $consents);
            });

            // Clear user consent cache
            $this->clearUserConsentCache($user);

            // Log significant changes
            if (!empty($changes)) {
                $this->logger->info('Consent Service: Consent changes recorded', [
                    'user_id' => $user->id,
                    'changes' => $changes,
                    'consent_version' => $consentVersion->version,
                    'log_category' => 'CONSENT_SERVICE_CHANGE'
                ]);
            }

            return [
                'previous' => $previousConsents['consents'],
                'current' => $this->getUserConsentStatus($user)['consents'],
                'changes' => $changes,
                'consent_version' => $consentVersion->version
            ];

        } catch (\Exception $e) {
            $this->logger->error('Consent Service: Failed to update user consents', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'log_category' => 'CONSENT_SERVICE_ERROR'
            ]);

            throw $e;
        }
    }

    /**
     * Get user's consent history with full audit trail
     *
     * @param User $user
     * @param int $limit
     * @return Collection
     * @privacy-safe Returns user's own consent history only
     */
    public function getConsentHistory(User $user, int $limit = 50): Collection
    {
        try {
            $this->logger->info('Consent Service: Getting consent history', [
                'user_id' => $user->id,
                'limit' => $limit,
                'log_category' => 'CONSENT_SERVICE_OPERATION'
            ]);

            return $user->consents()
                ->with('consentVersion')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($consent) {
                    return [
                        'id' => $consent->id,
                        'consent_type' => $consent->consent_type,
                        'granted' => $consent->granted,
                        'timestamp' => $consent->created_at,
                        'version' => $consent->consentVersion?->version,
                        'legal_basis' => $consent->legal_basis,
                        'ip_address' => $consent->ip_address,
                        'withdrawal_method' => $consent->withdrawal_method,
                        'source' => $consent->metadata['source'] ?? 'unknown'
                    ];
                });

        } catch (\Exception $e) {
            $this->logger->error('Consent Service: Failed to get consent history', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'log_category' => 'CONSENT_SERVICE_ERROR'
            ]);

            throw $e;
        }
    }

    /**
     * Get detailed consent history with version changes
     *
     * @param User $user
     * @return Collection
     * @privacy-safe Returns user's detailed consent history
     */
    public function getDetailedConsentHistory(User $user): Collection
    {
        try {
            $this->logger->info('Consent Service: Getting detailed consent history', [
                'user_id' => $user->id,
                'log_category' => 'CONSENT_SERVICE_OPERATION'
            ]);

            return $user->consents()
                ->with(['consentVersion'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('consent_type')
                ->map(function ($consents, $type) {
                    return [
                        'consent_type' => $type,
                        'config' => $this->consentTypes[$type] ?? [],
                        'current_status' => $consents->first()->granted,
                        'total_changes' => $consents->count(),
                        'history' => $consents->map(function ($consent) {
                            return [
                                'granted' => $consent->granted,
                                'timestamp' => $consent->created_at,
                                'version' => $consent->consentVersion?->version,
                                'legal_basis' => $consent->legal_basis,
                                'ip_address' => $this->maskIpAddress($consent->ip_address),
                                'withdrawal_method' => $consent->withdrawal_method,
                                'metadata' => $consent->metadata
                            ];
                        })->values()
                    ];
                })->values();

        } catch (\Exception $e) {
            $this->logger->error('Consent Service: Failed to get detailed consent history', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'log_category' => 'CONSENT_SERVICE_ERROR'
            ]);

            throw $e;
        }
    }

    /**
     * Create default consents for new user
     *
     * @param User $user
     * @param array $initialConsents
     * @return array
     * @privacy-safe Creates consents for specified user only
     */
    public function createDefaultConsents(User $user, array $initialConsents = []): array
    {
        try {
            $this->logger->info('Consent Service: Creating default consents for new user', [
                'user_id' => $user->id,
                'initial_consents' => $initialConsents,
                'log_category' => 'CONSENT_SERVICE_OPERATION'
            ]);

            $consentVersion = $this->getCurrentConsentVersion();
            $createdConsents = [];

            DB::transaction(function () use ($user, $initialConsents, $consentVersion, &$createdConsents) {
                foreach ($this->consentTypes as $type => $config) {
                    $granted = $initialConsents[$type] ?? $config['default_value'];

                    // Required consents must be granted
                    if ($config['required']) {
                        $granted = true;
                    }

                    $consent = UserConsent::create([
                        'user_id' => $user->id,
                        'consent_type' => $type,
                        'granted' => $granted,
                        'consent_version_id' => $consentVersion->id,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'legal_basis' => $config['legal_basis'],
                        'metadata' => [
                            'source' => 'registration',
                            'is_default' => true,
                            'session_id' => session()->getId()
                        ]
                    ]);

                    $createdConsents[$type] = [
                        'granted' => $granted,
                        'timestamp' => $consent->created_at,
                        'version' => $consentVersion->version
                    ];
                }

                // Update user's consent summary
                $this->updateUserConsentSummary($user, $initialConsents);
            });

            return $createdConsents;

        } catch (\Exception $e) {
            $this->logger->error('Consent Service: Failed to create default consents', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'log_category' => 'CONSENT_SERVICE_ERROR'
            ]);

            throw $e;
        }
    }

    /**
     * Withdraw specific consent type
     *
     * @param User $user
     * @param string $consentType
     * @param string $withdrawalMethod
     * @return bool
     * @privacy-safe Withdraws consent for authenticated user only
     */
    public function withdrawConsent(User $user, string $consentType, string $withdrawalMethod = 'manual'): bool
    {
        try {
            $this->logger->info('Consent Service: Withdrawing specific consent', [
                'user_id' => $user->id,
                'consent_type' => $consentType,
                'withdrawal_method' => $withdrawalMethod,
                'log_category' => 'CONSENT_SERVICE_OPERATION'
            ]);

            if (!isset($this->consentTypes[$consentType])) {
                throw new \InvalidArgumentException("Invalid consent type: {$consentType}");
            }

            $config = $this->consentTypes[$consentType];

            // Check if consent can be withdrawn
            if (!$config['can_withdraw']) {
                $this->logger->warning('Consent Service: Attempt to withdraw non-withdrawable consent', [
                    'user_id' => $user->id,
                    'consent_type' => $consentType,
                    'log_category' => 'CONSENT_SERVICE_WARNING'
                ]);
                return false;
            }

            $consentVersion = $this->getCurrentConsentVersion();

            DB::transaction(function () use ($user, $consentType, $withdrawalMethod, $config, $consentVersion) {
                UserConsent::create([
                    'user_id' => $user->id,
                    'consent_type' => $consentType,
                    'granted' => false,
                    'consent_version_id' => $consentVersion->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'legal_basis' => $config['legal_basis'],
                    'withdrawal_method' => $withdrawalMethod,
                    'metadata' => [
                        'source' => 'withdrawal',
                        'withdrawal_timestamp' => now()->toISOString(),
                        'session_id' => session()->getId()
                    ]
                ]);

                // Update user's consent summary
                $currentConsents = $this->getUserConsentStatus($user)['consents'];
                $currentConsents[$consentType]['granted'] = false;
                $this->updateUserConsentSummary($user, $currentConsents);
            });

            // Clear cache
            $this->clearUserConsentCache($user);

            return true;

        } catch (\Exception $e) {
            $this->logger->error('Consent Service: Failed to withdraw consent', [
                'user_id' => $user->id,
                'consent_type' => $consentType,
                'error' => $e->getMessage(),
                'log_category' => 'CONSENT_SERVICE_ERROR'
            ]);

            throw $e;
        }
    }

    /**
     * Check if user has granted specific consent
     *
     * @param User $user
     * @param string $consentType
     * @return bool
     * @privacy-safe Checks consent for authenticated user only
     */
    public function hasConsent(User $user, string $consentType): bool
    {
        try {
            $cacheKey = "user_consent_{$user->id}_{$consentType}";

            return Cache::remember($cacheKey, 300, function () use ($user, $consentType) {
                $consent = $user->consents()
                    ->where('consent_type', $consentType)
                    ->latest()
                    ->first();

                if (!$consent) {
                    // Return default value if no explicit consent found
                    return $this->consentTypes[$consentType]['default_value'] ?? false;
                }

                return $consent->granted;
            });

        } catch (\Exception $e) {
            $this->logger->error('Consent Service: Failed to check consent', [
                'user_id' => $user->id,
                'consent_type' => $consentType,
                'error' => $e->getMessage(),
                'log_category' => 'CONSENT_SERVICE_ERROR'
            ]);

            // Return safe default
            return false;
        }
    }

    /**
     * Get consent types configuration
     *
     * @return array
     * @privacy-safe Returns public consent type definitions
     */
    public function getConsentTypes(): array
    {
        return $this->consentTypes;
    }

    /**
     * Create new consent version (for policy updates)
     *
     * @param string $version
     * @param array $changes
     * @return ConsentVersion
     * @privacy-safe Creates new consent version
     */
    public function createConsentVersion(string $version, array $changes = []): ConsentVersion
    {
        try {
            $this->logger->info('Consent Service: Creating new consent version', [
                'version' => $version,
                'changes' => $changes,
                'log_category' => 'CONSENT_SERVICE_VERSION'
            ]);

            return ConsentVersion::create([
                'version' => $version,
                'changes' => $changes,
                'effective_date' => now(),
                'created_by' => auth()->id(),
                'consent_types' => $this->consentTypes
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Consent Service: Failed to create consent version', [
                'version' => $version,
                'error' => $e->getMessage(),
                'log_category' => 'CONSENT_SERVICE_ERROR'
            ]);

            throw $e;
        }
    }

    // ===================================================================
    // PRIVATE HELPER METHODS
    // ===================================================================

    /**
     * Get current user consents from database
     *
     * @param User $user
     * @return Collection
     * @privacy-safe Returns user's own consents only
     */
    private function getCurrentUserConsents(User $user): Collection
    {
        return $user->consents()
            ->whereIn('consent_type', array_keys($this->consentTypes))
            ->latest('created_at')
            ->get()
            ->unique('consent_type');
    }

    /**
     * Get current consent version
     *
     * @return ConsentVersion
     * @privacy-safe Returns current version information
     */
    private function getCurrentConsentVersion(): ConsentVersion
    {
        return ConsentVersion::latest('effective_date')->first()
            ?? ConsentVersion::create([
                'version' => '1.0',
                'effective_date' => now(),
                'consent_types' => $this->consentTypes
            ]);
    }

    /**
     * Update user's consent summary for quick access
     *
     * @param User $user
     * @param array $consents
     * @return void
     * @privacy-safe Updates summary for specified user only
     */
    private function updateUserConsentSummary(User $user, array $consents): void
    {
        $summary = [];
        foreach ($this->consentTypes as $type => $config) {
            $summary[$type] = $consents[$type] ?? $config['default_value'];
        }

        $user->update([
            'consent_summary' => $summary,
            'consents_updated_at' => now()
        ]);
    }

    /**
     * Clear user consent cache
     *
     * @param User $user
     * @return void
     * @privacy-safe Clears cache for specified user only
     */
    private function clearUserConsentCache(User $user): void
    {
        foreach (array_keys($this->consentTypes) as $type) {
            Cache::forget("user_consent_{$user->id}_{$type}");
        }
        Cache::forget("user_consent_status_{$user->id}");
    }

    /**
     * Mask IP address for privacy
     *
     * @param string|null $ipAddress
     * @return string|null
     * @privacy-safe Masks IP address for privacy compliance
     */
    private function maskIpAddress(?string $ipAddress): ?string
    {
        if (!$ipAddress) {
            return null;
        }

        // Mask last octet of IPv4 or last segment of IPv6
        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ipAddress);
            $parts[3] = 'xxx';
            return implode('.', $parts);
        }

        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $ipAddress);
            $parts[count($parts) - 1] = 'xxxx';
            return implode(':', $parts);
        }

        return 'masked';
    }

    /**
     * Get available consent types configuration
     *
     * @return array
     * @privacy-safe Returns public consent type definitions
     * @oracode-dimension technical
     * @value-flow Provides consent type metadata for UI generation
     * @community-impact Enables informed consent decisions
     * @transparency-level High - complete visibility of consent options
     * @narrative-coherence Supports user autonomy and GDPR compliance
     */
    public function getAvailableConsentTypes(): array
    {
        try {
            $this->logger->info('Consent Service: Getting available consent types', [
                'log_category' => 'CONSENT_SERVICE_OPERATION'
            ]);

            // Return the consent types with additional metadata for UI
            $availableTypes = [];

            foreach ($this->consentTypes as $type => $config) {
                $availableTypes[$type] = [
                    'key' => $type,
                    'name' => $config['name'],
                    'description' => $config['description'],
                    'category' => $config['category'],
                    'legal_basis' => $config['legal_basis'],
                    'required' => $config['required'],
                    'default_value' => $config['default_value'],
                    'can_withdraw' => $config['can_withdraw'],
                    'privacy_level' => $this->getPrivacyLevel($type),
                    'data_processing_purpose' => $this->getProcessingPurpose($type),
                    'retention_period' => $this->getRetentionPeriod($type),
                    'third_parties' => $this->getThirdParties($type),
                    'user_benefits' => $this->getUserBenefits($type),
                    'withdrawal_consequences' => $this->getWithdrawalConsequences($type)
                ];
            }

            return $availableTypes;

        } catch (\Exception $e) {
            $this->logger->error('Consent Service: Failed to get available consent types', [
                'error' => $e->getMessage(),
                'log_category' => 'CONSENT_SERVICE_ERROR'
            ]);

            // Return minimal safe fallback
            return $this->consentTypes;
        }
    }

    /**
     * Get privacy level for consent type
     *
     * @param string $type
     * @return string
     * @privacy-safe Returns privacy impact classification
     */
    private function getPrivacyLevel(string $type): string
    {
        $privacyLevels = [
            'functional' => 'essential',
            'analytics' => 'standard',
            'marketing' => 'enhanced',
            'profiling' => 'advanced'
        ];

        return $privacyLevels[$type] ?? 'standard';
    }

    /**
     * Get detailed processing purpose for consent type
     *
     * @param string $type
     * @return string
     * @privacy-safe Returns localized detailed purpose description
     */
    private function getProcessingPurpose(string $type): string
    {
        return __("gdpr.consent.processing_purposes.{$type}");
    }

   /**
     * Get retention period for consent type
     *
     * @param string $type
     * @return string
     * @privacy-safe Returns localized data retention information
     */
    private function getRetentionPeriod(string $type): string
    {
        return __("gdpr.consent.retention_periods.{$type}");
    }

    /**
     * Get third parties involved for consent type
     *
     * @param string $type
     * @return array
     * @privacy-safe Returns localized third party data sharing information
     */
    private function getThirdParties(string $type): array
    {
        $translationKey = "gdpr.consent.third_parties.{$type}";
        $translated = __($translationKey);

        // If translation exists and is an array, return it; otherwise return empty array
        return is_array($translated) ? $translated : [];
    }

    /**
     * Get user benefits for consent type
     *
     * @param string $type
     * @return array
     * @privacy-safe Returns localized user benefit information
     */
    private function getUserBenefits(string $type): array
    {
        $translationKey = "gdpr.consent.user_benefits.{$type}";
        $translated = __($translationKey);

        // If translation exists and is an array, return it; otherwise return empty array
        return is_array($translated) ? $translated : [];
    }

    /**
     * Get withdrawal consequences for consent type
     *
     * @param string $type
     * @return array
     * @privacy-safe Returns localized consequence information for informed decisions
     */
    private function getWithdrawalConsequences(string $type): array
    {
        $translationKey = "gdpr.consent.withdrawal_consequences.{$type}";
        $translated = __($translationKey);

        // If translation exists and is an array, return it; otherwise return empty array
        return is_array($translated) ? $translated : [];
    }
}
