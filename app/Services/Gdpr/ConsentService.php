<?php

namespace App\Services\Gdpr;

use App\DataTransferObjects\Gdpr\ConsentTypeDto;
use App\Models\User;
use App\Models\UserConsent;
use App\Models\ConsentVersion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Carbon\Carbon;
use PSpell\Config;

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
     * @Oracode Method: Get All Consent Types as DTO Collection
     * 🎯 Purpose: Provide type-safe, localized consent type definitions
     * 📤 Output: Collection of ConsentTypeDto instances
     * 🧱 Core Logic: Runtime localization with DTO pattern
     */
    public function getConsentTypes(): Collection
    {
        return collect([
            new ConsentTypeDto(
                key: 'functional',
                category: 'necessary',
                legalBasis: 'legitimate_interest',
                required: true,
                defaultValue: true,
                canWithdraw: false
            ),
            new ConsentTypeDto(
                key: 'analytics',
                category: 'statistics',
                legalBasis: 'consent',
                required: false,
                defaultValue: false,
                canWithdraw: true
            ),
            new ConsentTypeDto(
                key: 'marketing',
                category: 'marketing',
                legalBasis: 'consent',
                required: false,
                defaultValue: false,
                canWithdraw: true
            ),
            new ConsentTypeDto(
                key: 'profiling',
                category: 'profiling',
                legalBasis: 'consent',
                required: false,
                defaultValue: false,
                canWithdraw: true
            ),
            new ConsentTypeDto(
                key: 'allow_personal_data_processing',
                category: 'data_processing',
                legalBasis: 'consent',
                required: false,
                defaultValue: false,
                canWithdraw: true
            ),
        ]);
    }

    /**
     * @Oracode Method: Get Single Consent Type
     * 🎯 Purpose: Retrieve specific consent type configuration
     * 📥 Input: Consent type key
     * 📤 Output: ConsentTypeDto or null
     */
    public function getConsentType(string $key): ?ConsentTypeDto
    {
        return $this->getConsentTypes()->firstWhere('key', $key);
    }

    /**
     * @Oracode Method: Get User's Current Consent Status
     * 🎯 Purpose: Retrieve complete consent status for user with UI-friendly format
     * 📥 Input: User instance
     * 📤 Output: Array with consent status, summary, and metadata
     * 🛡️ Privacy: Returns user's own consent status only
     * 🧱 Core Logic: DTO-based consent status with localized descriptions
     */
    public function getUserConsentStatus(User $user): array
    {
        try {
            $this->logger->info('Consent Service: Getting user consent status', [
                'user_id' => $user->id,
                'log_category' => 'CONSENT_SERVICE_OPERATION'
            ]);

            $currentConsents = $this->getCurrentUserConsents($user);

            $this->logger->info('Consent Service: consents_count', [
                'consents_count' => $currentConsents->count(),
            ]);

            $consentVersion = $this->getCurrentConsentVersion();
            $this->logger->info('Consent Service: consent_version', [
                'consent_version' => $consentVersion->version,
            ]);

            $consents = collect();

            foreach ($this->getConsentTypes() as $consentType) {
                $userConsent = $currentConsents->where('consent_type', $consentType->key)->first();

                // Create stdClass object for UI compatibility
                $consentItem = new \stdClass();
                $consentItem->id = $userConsent->id ?? null;
                $consentItem->purpose = $consentType->key;  // Consent type key as purpose for views
                $consentItem->granted = $userConsent ? $userConsent->granted : $consentType->defaultValue;
                $consentItem->status = $userConsent ? ($userConsent->granted ? 'active' : 'withdrawn') : 'not_given';
                $consentItem->timestamp = $userConsent?->created_at;
                $consentItem->given_at = $userConsent?->created_at;
                $consentItem->withdrawn_at = $userConsent?->withdrawn_at;
                $consentItem->consent_method = $userConsent?->consent_method ?? 'web';
                $consentItem->version = $userConsent?->consent_version_id ?? $consentVersion->id;
                $consentItem->consentVersion = $userConsent?->consentVersion ?? $consentVersion;
                $consentItem->required = $consentType->required;
                $consentItem->can_withdraw = $consentType->canWithdraw;
                $consentItem->legal_basis = $consentType->legalBasis;
                $consentItem->description = $consentType->getDescription(); // ✅ Localized!

                $consents->push($consentItem);
            }

            // Calculate consent summary data
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
     * @Oracode Method: Update User Consents with Full Audit Trail
     * 🎯 Purpose: Bulk update user consents with complete change tracking
     * 📥 Input: User, array of consent changes
     * 📤 Output: Array with previous/current state and changes
     * 🛡️ Privacy: GDPR-compliant consent updates with audit trail
     * 🧱 Core Logic: DTO-based validation and change detection
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
                    // Validate consent type using DTO
                    $consentConfig = $this->getConsentType($type);
                    if (!$consentConfig) {
                        throw new \InvalidArgumentException("Invalid consent type: {$type}");
                    }

                    $granted = (bool) $granted;

                    // Check if required consents are being denied
                    if ($consentConfig->required && !$granted) {
                        $this->logger->warning('Consent Service: Attempt to deny required consent', [
                            'user_id' => $user->id,
                            'consent_type' => $type,
                            'log_category' => 'CONSENT_SERVICE_WARNING'
                        ]);
                        $granted = true; // Force required consents to true
                    }

                    // Check for changes
                    $previousValue = $previousConsents['userConsents'][$type]['granted'] ?? $consentConfig->defaultValue;
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
                            'legal_basis' => $consentConfig->legalBasis,
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
                $this->updateUserConsentSummary($user);
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

            $userStatus = $this->getUserConsentStatus($user);

            return [
                'previous' => $previousConsents['userConsents'],
                'current' => $userStatus['userConsents'] ?? [],
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
                ->get();
                // ->map(function ($consent) {
                //     return [
                //         'id' => $consent->id,
                //         'consent_type' => $consent->consent_type,
                //         'granted' => $consent->granted,
                //         'timestamp' => $consent->created_at,
                //         'version' => $consent->consentVersion?->version,
                //         'legal_basis' => $consent->legal_basis,
                //         'ip_address' => $consent->ip_address,
                //         'withdrawal_method' => $consent->withdrawal_method,
                //         'source' => $consent->metadata['source'] ?? 'unknown'
                //     ];
                // });

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
     * @Oracode Method: Create Default Consents for New User
     * 🎯 Purpose: Initialize consent records for new user registration
     * 📥 Input: User, optional initial consent preferences
     * 📤 Output: Array of created consent records
     * 🛡️ Privacy: GDPR-compliant default consent setup
     * 🧱 Core Logic: Create consents using DTO configuration with proper defaults
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
                foreach ($this->getConsentTypes() as $consentType) {
                    $granted = $initialConsents[$consentType->key] ?? $consentType->defaultValue;

                    // Required consents must be granted
                    if ($consentType->required) {
                        $granted = true;
                    }

                    $this->logger->debug('Consent Service: Creating consent', [
                        'user_id' => $user->id,
                        'consent_type' => $consentType->key,
                        'granted' => $granted,
                        'version' => $consentVersion->version,
                        'log_category' => 'CONSENT_SERVICE_OPERATION'
                    ]);

                    $consent = UserConsent::create([
                        'user_id' => $user->id,
                        'consent_type' => $consentType->key,
                        'granted' => $granted,
                        'consent_version_id' => $consentVersion->id,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'legal_basis' => $consentType->legalBasis,
                        'metadata' => [
                            'source' => 'registration',
                            'is_default' => true,
                            'session_id' => session()->getId()
                        ]
                    ]);

                    $createdConsents[$consentType->key] = [
                        'granted' => $granted,
                        'timestamp' => $consent->created_at,
                        'version' => $consentVersion->version
                    ];
                }

                // Update user's consent summary
                $this->updateUserConsentSummary($user);
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
     * @Oracode Method: Grant Consent with UPSERT Logic
     * 🎯 Purpose: Grant or update existing consent using proper UPSERT pattern
     * 📥 Input: User, consent type, metadata
     * 📤 Output: Boolean success result
     * 🛡️ Privacy: GDPR-compliant consent management with audit trail
     * 🧱 Core Logic: Smart UPSERT - update metadata if same, create new if changed
     */
    public function grantConsent(User $user, string $consentType, array $metadata = []): bool
    {
        try {
            $this->logger->info('ConsentService: Granting/updating specific consent', [
                'user_id' => $user->id,
                'consent_type' => $consentType,
                'metadata_keys' => array_keys($metadata),
                'operation' => 'upsert_consent'
            ]);

            $consentConfig = $this->getConsentType($consentType);
            if (!$consentConfig) {
                throw new \InvalidArgumentException("Invalid consent type: {$consentType}");
            }

            $consentVersion = $this->getCurrentConsentVersion();

            $result = DB::transaction(function () use ($user, $consentType, $consentConfig, $consentVersion, $metadata) {

                // ✅ OS1.5 EXPLICITLY INTENTIONAL: Check if consent already exists
                $existingConsent = UserConsent::where('user_id', $user->id)
                    ->where('consent_type', $consentType)
                    ->latest()
                    ->first();

                $consentData = [
                    'user_id' => $user->id,
                    'consent_type' => $consentType,
                    'granted' => true,
                    'consent_version_id' => $consentVersion->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'legal_basis' => $consentConfig->legalBasis,
                    'withdrawal_method' => null,
                    'metadata' => array_merge([
                        'source' => 'consent_grant',
                        'grant_timestamp' => now()->toISOString(),
                        'session_id' => session()->getId()
                    ], $metadata)
                ];

                if ($existingConsent && $existingConsent->granted === true) {
                    // ✅ Consent already granted, just update metadata
                    $existingConsent->update([
                        'metadata' => $consentData['metadata'],
                        'updated_at' => now()
                    ]);

                    $this->logger->info('ConsentService: Updated existing granted consent metadata', [
                        'user_id' => $user->id,
                        'consent_type' => $consentType,
                        'consent_id' => $existingConsent->id,
                        'operation' => 'consent_metadata_update'
                    ]);

                    return $existingConsent;

                } else {
                    // ✅ Create new consent record (first time or re-grant after withdrawal)
                    $newConsent = UserConsent::create($consentData);

                    $this->logger->info('ConsentService: Created new consent record', [
                        'user_id' => $user->id,
                        'consent_type' => $consentType,
                        'consent_id' => $newConsent->id,
                        'previous_consent_id' => $existingConsent?->id,
                        'operation' => $existingConsent ? 'consent_re_grant' : 'consent_first_grant'
                    ]);

                    return $newConsent;
                }
            });

            // Update user's consent summary
            $this->updateUserConsentSummary($user);

            // Clear cache
            $this->clearUserConsentCache($user);

            return true;

        } catch (\Exception $e) {
            $this->logger->error('ConsentService: Failed to grant consent', [
                'user_id' => $user->id,
                'consent_type' => $consentType,
                'error' => $e->getMessage(),
                'operation' => 'consent_grant_failed'
            ]);

            throw $e;
        }
    }


    /**
     * @Oracode Method: Withdraw Consent with Audit Trail
     * 🎯 Purpose: Withdraw consent using proper audit trail pattern
     * 📥 Input: User, consent type, metadata array
     * 📤 Output: Boolean success result
     * 🛡️ Privacy: GDPR-compliant consent withdrawal with complete audit trail
     * 🧱 Core Logic: Always create withdrawal record for audit compliance
     */
    public function withdrawConsent(User $user, string $consentType, array $metadata = []): bool
    {
        try {
            $this->logger->info('ConsentService: Withdrawing specific consent', [
                'user_id' => $user->id,
                'consent_type' => $consentType,
                'withdrawal_method' => $metadata['withdrawal_method'] ?? 'manual',
                'operation' => 'consent_withdrawal'
            ]);

            $consentConfig = $this->getConsentType($consentType);
            if (!$consentConfig) {
                throw new \InvalidArgumentException("Invalid consent type: {$consentType}");
            }

            // Check if consent can be withdrawn
            if (!$consentConfig->canWithdraw) {
                $this->logger->warning('ConsentService: Attempt to withdraw non-withdrawable consent', [
                    'user_id' => $user->id,
                    'consent_type' => $consentType,
                    'operation' => 'withdrawal_denied'
                ]);
                return false;
            }

            $consentVersion = $this->getCurrentConsentVersion();
            $withdrawalMethod = $metadata['withdrawal_method'] ?? 'manual';

            DB::transaction(function () use ($user, $consentType, $withdrawalMethod, $consentConfig, $consentVersion, $metadata) {

                // ✅ OS1.5 EXPLICITLY INTENTIONAL: Always create withdrawal record for audit trail
                UserConsent::create([
                    'user_id' => $user->id,
                    'consent_type' => $consentType,
                    'granted' => false,
                    'consent_version_id' => $consentVersion->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'legal_basis' => $consentConfig->legalBasis,
                    'withdrawal_method' => $withdrawalMethod,
                    'metadata' => array_merge([
                        'source' => 'withdrawal',
                        'withdrawal_timestamp' => now()->toISOString(),
                        'session_id' => session()->getId()
                    ], $metadata)
                ]);
            });

            // Update user's consent summary
            $this->updateUserConsentSummary($user);

            // Clear cache
            $this->clearUserConsentCache($user);

            return true;

        } catch (\Exception $e) {
            $this->logger->error('ConsentService: Failed to withdraw consent', [
                'user_id' => $user->id,
                'consent_type' => $consentType,
                'error' => $e->getMessage(),
                'operation' => 'consent_withdrawal_failed'
            ]);

            throw $e;
        }
    }

    /**
     * @Oracode Method: Renew Consent with Audit Trail
     * 🎯 Purpose: Renew a previously withdrawn consent, creating a new grant record
     * 📥 Input: User, consent type string, metadata array
     * 📤 Output: Boolean success result
     * 🛡️ Privacy: GDPR-compliant consent renewal with complete audit trail
     * 🧱 Core Logic: Acts as a semantic alias for grantConsent to ensure clear intent
     */
    public function renewConsent(User $user, string $consentType, array $metadata = []): bool
    {
        $this->logger->info('ConsentService: Renewing specific consent', [
            'user_id' => $user->id,
            'consent_type' => $consentType,
            'operation' => 'consent_renewal'
        ]);

        // Rinnovare un consenso è funzionalmente identico a concederlo di nuovo.
        // Chiamiamo grantConsent per sfruttare la logica esistente che crea
        // un nuovo record "granted", mantenendo l'audit trail.
        return $this->grantConsent($user, $consentType, array_merge($metadata, ['source' => 'user_renewal']));
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
     * @Oracode Method: Create New Consent Version
     * 🎯 Purpose: Create new consent version for policy updates
     * 📥 Input: Version string, changes array
     * 📤 Output: ConsentVersion instance
     * 🛡️ Privacy: GDPR-compliant version tracking
     * 🧱 Core Logic: Store DTO-based consent types configuration
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
                'consent_types' => $this->getConsentTypes()->mapWithKeys(fn($dto) => [$dto->key => $dto->toArray()])->toArray()
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

    /**
     * @Oracode Method: Get Current User Consents from Database
     * 🎯 Purpose: Retrieve user's latest consent records for each type
     * 📥 Input: User instance
     * 📤 Output: Collection of UserConsent records
     * 🛡️ Privacy: Returns user's own consents only
     * 🧱 Core Logic: Query latest consent for each type using DTO keys
     */
    private function getCurrentUserConsents(User $user): Collection
    {
        return $user->consents()
            ->whereIn('consent_type', $this->getConsentTypes()->pluck('key')->toArray())
            ->latest('created_at')
            ->get()
            ->unique('consent_type');
    }

    /**
     * Get current consent version
     */
    private function getCurrentConsentVersion(): ConsentVersion
    {
        return ConsentVersion::latest('effective_date')->first()
            ?? ConsentVersion::create([
                'version' => '1.0',
                'effective_date' => now(),
                'consent_types' => $this->getConsentTypes()->mapWithKeys(fn($dto) => [$dto->key => $dto->toArray()])->toArray()
            ]);
    }

/**
     * Update user's consent summary based on current consents
     */
    private function updateUserConsentSummary(User $user): void
    {
        $summary = [];

        foreach ($this->getConsentTypes() as $consentType) {
            $latestConsent = UserConsent::where('user_id', $user->id)
                ->where('consent_type', $consentType->key)
                ->latest()
                ->first();

            $summary[$consentType->key] = $latestConsent ? $latestConsent->granted : $consentType->defaultValue;
        }

        $user->update([
            'consent_summary' => $summary,
            'consents_updated_at' => now()
        ]);
    }

    /**
     * Clear user consent cache
     */
    private function clearUserConsentCache(User $user): void
    {
        foreach ($this->getConsentTypes() as $consentType) {
            Cache::forget("user_consent_{$user->id}_{$consentType->key}");
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
     * @Oracode Method: Get Available Consent Types Configuration
     * 🎯 Purpose: Provide complete consent type metadata for UI generation
     * 📤 Output: Array of consent types with extended metadata
     * 🛡️ Privacy: GDPR-compliant consent type definitions
     * 🧱 Core Logic: DTO-based configuration with localized content
     */
    public function getAvailableConsentTypes(): array
    {
        try {
            $this->logger->info('Consent Service: Getting available consent types', [
                'log_category' => 'CONSENT_SERVICE_OPERATION'
            ]);

            // Return the consent types with additional metadata for UI
            $availableTypes = [];

            foreach ($this->getConsentTypes() as $consentType) {
                $availableTypes[$consentType->key] = [
                    'key' => $consentType->key,
                    'name' => $consentType->getName(),
                    'description' => $consentType->getDescription(),
                    'category' => $consentType->category,
                    'legal_basis' => $consentType->legalBasis,
                    'required' => $consentType->required,
                    'default_value' => $consentType->defaultValue,
                    'can_withdraw' => $consentType->canWithdraw,
                    'privacy_level' => $this->getPrivacyLevel($consentType->key),
                    'data_processing_purpose' => $this->getProcessingPurpose($consentType->key),
                    'retention_period' => $this->getRetentionPeriod($consentType->key),
                    'third_parties' => $this->getThirdParties($consentType->key),
                    'user_benefits' => $this->getUserBenefits($consentType->key),
                    'withdrawal_consequences' => $this->getWithdrawalConsequences($consentType->key)
                ];
            }

            return $availableTypes;

        } catch (\Exception $e) {
            $this->logger->error('Consent Service: Failed to get available consent types', [
                'error' => $e->getMessage(),
                'log_category' => 'CONSENT_SERVICE_ERROR'
            ]);

            // Return minimal safe fallback using DTO
            return $this->getConsentTypes()->mapWithKeys(fn($dto) => [$dto->key => $dto->toArray()])->toArray();
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
