<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Gdpr\ConsentService;
use App\Services\Gdpr\AuditLogService;
use App\Services\CollectionService;
use Ultra\EgiModule\Contracts\WalletServiceInterface;
use Ultra\EgiModule\Contracts\UserRoleServiceInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Controller: Enhanced User Registration USANDO IL SISTEMA GDPR ESISTENTE
 * 🎯 Purpose: Handle user registration integrandosi con l'ecosistema GDPR già implementato
 * 🛡️ Privacy: USA ConsentService e AuditLogService già esistenti
 * 🧱 Core Logic: Estende Jetstream SENZA duplicare funzionalità GDPR
 *
 * @package App\Http\Controllers\Auth
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 2.1.0 - CORRECTED per evitare duplicazioni
 * @date 2025-05-25
 * @integration-note USA SOLO servizi GDPR esistenti, no duplicazioni
 */
class RegisteredUserController extends Controller
{
    /**
     * Error manager (ESISTENTE)
     * @var ErrorManagerInterface
     */
    protected ErrorManagerInterface $errorManager;

    /**
     * Ultra log manager (ESISTENTE)
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * GDPR consent service (GIÀ IMPLEMENTATO nel sistema GDPR)
     * @var ConsentService
     */
    protected ConsentService $consentService;

    /**
     * GDPR audit service (GIÀ IMPLEMENTATO nel sistema GDPR)
     * @var AuditLogService
     */
    protected AuditLogService $auditService;

    /**
     * Collection service (ESISTENTE per EGI)
     * @var CollectionService
     */
    protected CollectionService $collectionService;

    /**
     * Wallet service (ESISTENTE per EGI)
     * @var WalletServiceInterface
     */
    protected WalletServiceInterface $walletService;

    /**
     * User role service (ESISTENTE per EGI)
     * @var UserRoleServiceInterface
     */
    protected UserRoleServiceInterface $userRoleService;

    /**
     * Constructor - USA SOLO servizi esistenti
     *
     * @param ErrorManagerInterface $errorManager
     * @param UltraLogManager $logger
     * @param ConsentService $consentService [ESISTENTE - sistema GDPR]
     * @param AuditLogService $auditService [ESISTENTE - sistema GDPR]
     * @param CollectionService $collectionService [ESISTENTE - sistema EGI]
     * @param WalletServiceInterface $walletService [ESISTENTE - sistema EGI]
     * @param UserRoleServiceInterface $userRoleService [ESISTENTE - sistema EGI]
     *
     * @integration-safety Tutti i servizi sono già implementati e testati
     */
    public function __construct(
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger,
        ConsentService $consentService,
        AuditLogService $auditService,
        CollectionService $collectionService,
        WalletServiceInterface $walletService,
        UserRoleServiceInterface $userRoleService
    ) {
        $this->errorManager = $errorManager;
        $this->logger = $logger;
        $this->consentService = $consentService;
        $this->auditService = $auditService;
        $this->collectionService = $collectionService;
        $this->walletService = $walletService;
        $this->userRoleService = $userRoleService;
    }

    /**
     * Display registration view - USA configurazione GDPR esistente
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        try {
            // USA il ConsentService ESISTENTE per ottenere i tipi
            $consentTypes = $this->consentService->getConsentTypes();

            // USA configurazione GDPR esistente (non creare config/gdpr.php duplicato!)
            $privacyPolicyVersion = config('gdpr.current_policy_version', '1.0.0');

            $this->logger->info('[Registration] Registration page loaded', [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toISOString()
            ]);
            // Render registration view with existing services

            return view('auth.register', [
                'consentTypes' => $consentTypes,
                'privacyPolicyVersion' => $privacyPolicyVersion,
                'brandColors' => [
                    'oro_fiorentino' => '#D4A574',
                    'verde_rinascita' => '#2D5016',
                    'blu_algoritmo' => '#1B365D'
                ]
            ]);

        } catch (\Exception $e) {
            // USA error manager esistente con codici UEM esistenti
            return $this->errorManager->handle('REGISTRATION_PAGE_LOAD_ERROR', [
                'error' => $e->getMessage(),
                'ip_address' => request()->ip()
            ], $e);
        }
    }

    /**
     * Handle registration USANDO solo servizi esistenti
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $userId = null;
        $collectionId = null;

        $logContext = [
            'operation' => 'enhanced_user_registration_with_existing_services',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString()
        ];

        try {
            // Validation con regole standard (non duplicare quelle GDPR)
            $validated = $this->validateRegistration($request);
            $logContext['user_type'] = $validated['user_type'];

            $this->logger->info('[Registration] Starting registration using EXISTING GDPR services', $logContext);

            // Transaction per ecosystem setup
            $result = DB::transaction(function () use ($validated, $logContext, &$userId, &$collectionId) {

                // 1. CREATE USER (standard)
                $user = $this->createUser($validated, $logContext);
                $userId = $user->id;
                $logContext['user_id'] = $userId;

                $this->logger->info('[Registration] User created successfully', [
                    ...$logContext,
                    'user_email' => $user->email,
                    'user_type' => $user->user_type
                ]);

                // 2. USA ConsentService ESISTENTE (non duplicare!)
                $this->processGdprConsentsWithExistingService($user, $validated['consents'] ?? [], $logContext);

                // 3. USA CollectionService ESISTENTE
                $collection = $this->createCollectionWithExistingService($user, $validated, $logContext);
                $collectionId = $collection->id;
                $logContext['collection_id'] = $collectionId;

                // 4. USA WalletService ESISTENTE
                $this->setupWalletsWithExistingService($collection, $user, $logContext);

                // 5. USA UserRoleService ESISTENTE
                $this->assignRolesWithExistingService($user, $validated['user_type'], $logContext);

                // 6. Finalize
                $this->finalizeUserSetup($user, $collection, $validated, $logContext);

                return [
                    'user' => $user,
                    'collection' => $collection,
                    'redirect_route' => $this->determinePostRegistrationRoute($validated)
                ];
            });

            // 7. USA AuditLogService ESISTENTE per logging
            $this->logRegistrationWithExistingService($result, $logContext);

            // Fire Jetstream event
            event(new Registered($result['user']));
            Auth::login($result['user']);

            return redirect()->route($result['redirect_route'])
                ->with('success', __('Benvenuto nel tuo Rinascimento Digitale!'))
                ->with('collection_created', $result['collection']->collection_name);

        } catch (\Exception $e) {
            $errorContext = [
                ...$logContext,
                'user_id' => $userId,
                'collection_id' => $collectionId,
                'error' => $e->getMessage()
            ];

            return $this->errorManager->handle('ENHANCED_REGISTRATION_FAILED', $errorContext, $e);
        }
    }

    /**
     * USA ConsentService ESISTENTE - no duplicazioni
     */
    protected function processGdprConsentsWithExistingService(User $user, array $consents, array $logContext): void
    {
        try {
            // Log what we received for debugging
            $this->logger->info('[Registration] Processing GDPR consents - Raw input', [
                ...$logContext,
                'consents_received' => $consents,
                'consents_empty' => empty($consents),
                'consents_count' => count($consents)
            ]);

            // Build processed consents with explicit defaults
            // This ensures we always have a complete structure
            $processedConsents = [
                'functional' => true, // Always required for platform operation
                'analytics' => isset($consents['analytics']) && $consents['analytics'] === '1',
                'marketing' => isset($consents['marketing']) && $consents['marketing'] === '1',
                'profiling' => isset($consents['profiling']) && $consents['profiling'] === '1',
            ];

            $this->logger->info('[Registration] Processed consents for ConsentService', [
                ...$logContext,
                'processed_consents' => $processedConsents,
                'functional_consent' => $processedConsents['functional'],
                'optional_consents_given' => array_filter(array_slice($processedConsents, 1))
            ]);

            // Call ConsentService with processed structure
            $result = $this->consentService->updateUserConsents($user, $processedConsents);

            if (!$result) {
                throw new \Exception('ConsentService returned false - update failed');
            }

            $this->logger->info('[Registration] GDPR consents successfully processed via ConsentService', [
                ...$logContext,
                'result' => $result
            ]);

        } catch (\Exception $e) {
            $this->logger->error('[Registration] CRITICAL: Failed to process consents via ConsentService', [
                ...$logContext,
                'consents_received' => $consents,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            throw new \Exception('Failed to process GDPR consents: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * USA CollectionService ESISTENTE - no duplicazioni
     */
    protected function createCollectionWithExistingService(User $user, array $validated, array $logContext): \App\Models\Collection
    {
        try {
            // USA il metodo ESISTENTE findOrCreateUserCollection
            $collection = $this->collectionService->findOrCreateUserCollection($user, $logContext);

            if ($collection instanceof \Illuminate\Http\JsonResponse) {
                throw new \Exception('CollectionService returned error response');
            }

            // Enhance con dati registration-specific
            $this->enhanceCollectionForRegistration($collection, $validated, $logContext);

            return $collection;

        } catch (\Exception $e) {
            throw new \Exception('Failed to create collection via existing CollectionService: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * USA WalletService ESISTENTE - no duplicazioni
     */
    protected function setupWalletsWithExistingService(\App\Models\Collection $collection, User $user, array $logContext): void
    {
        try {
            // USA il metodo ESISTENTE attachDefaultWalletsToCollection
            $this->walletService->attachDefaultWalletsToCollection($collection, $user);

            $this->logger->info('[Registration] Wallets setup via EXISTING WalletService', $logContext);

        } catch (\Exception $e) {
            throw new \Exception('Failed to setup wallets via existing WalletService: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * USA UserRoleService ESISTENTE - no duplicazioni
     */
    protected function assignRolesWithExistingService(User $user, string $userType, array $logContext): void
    {
        try {
            // USA il metodo ESISTENTE assignCreatorRole
            $this->userRoleService->assignCreatorRole($user->id);

            // Assign additional roles if needed
            $this->assignTypeSpecificRoles($user, $userType, $logContext);

            $this->logger->info('[Registration] Roles assigned via EXISTING UserRoleService', $logContext);

        } catch (\Exception $e) {
            throw new \Exception('Failed to assign roles via existing UserRoleService: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * USA AuditLogService ESISTENTE - no duplicazioni
     */
    protected function logRegistrationWithExistingService(array $result, array $logContext): void
    {
        try {
            $user = $result['user'];
            $collection = $result['collection'];

            // USA il metodo ESISTENTE logUserAction
            $this->auditService->logUserAction(
                $user,
                'enhanced_user_registration_completed',
                [
                    'registration_method' => 'web_form_with_existing_gdpr_services',
                    'user_type' => $user->user_type,
                    'collection_id' => $collection->id,
                    'collection_name' => $collection->collection_name,
                    'ecosystem_setup' => 'complete',
                    'ip_address' => request()->ip()
                ],
                'authentication'
            );

        } catch (\Exception $e) {
            $this->logger->warning('[Registration] Audit logging failed', [
                ...$logContext,
                'audit_error' => $e->getMessage()
            ]);
        }
    }

    // ===== HELPER METHODS (unchanged) =====

    protected function createUser(array $validated, array $logContext): User
    {
        return User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'usertype' => $validated['user_type'],
            'email_verified_at' => null,
            'gdpr_consents_given_at' => now(),
            'registration_ip' => request()->ip(),
            'registration_user_agent' => request()->userAgent(),
            'created_via' => 'web_form_gdpr_integrated',
            'ecosystem_setup_completed' => false,
        ]);
    }

    protected function enhanceCollectionForRegistration(\App\Models\Collection $collection, array $validated, array $logContext): void
    {
        // Basic enhancement without duplicating Collection logic
        $typeSpecificName = $this->getTypeSpecificCollectionName($validated['user_type'], $validated['name']);

        $collection->update([
            'collection_name' => $typeSpecificName,
            'created_via' => 'registration_enhanced',
            'user_type_context' => $validated['user_type']
        ]);
    }

    protected function assignTypeSpecificRoles(User $user, string $userType, array $logContext): void
    {
        // Implementation depends on existing UserRoleService capabilities
        // Only add if methods exist in the existing service
    }

    protected function finalizeUserSetup(User $user, \App\Models\Collection $collection, array $validated, array $logContext): void
    {
        $user->update([
            'current_collection_id' => $collection->id,
            'ecosystem_setup_completed' => true,
            'onboarding_step' => 'collection_created'
        ]);
    }

    protected function getTypeSpecificCollectionName(string $userType, string $userName): string
    {
        $firstName = explode(' ', trim($userName), 2)[0];

        $typeNames = [
            'creator' => "{$firstName}'s Arte",
            'patron' => "{$firstName}'s Collection",
            'enterprise' => "{$firstName} Corporate Gallery",
        ];

        return $typeNames[$userType] ?? "{$firstName}'s Collection";
    }

    protected function validateRegistration(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'user_type' => ['required', 'in:creator,patron,collector,enterprise,trader_pro,epp_entity'],
            'privacy_policy_accepted' => ['required', 'accepted'],
            'terms_accepted' => ['required', 'accepted'],
            'consents' => ['sometimes', 'array'],
            'consents.functional' => ['sometimes', 'boolean'],
            'consents.analytics' => ['sometimes', 'boolean'],
            'consents.marketing' => ['sometimes', 'boolean'],
            'consents.profiling' => ['sometimes', 'boolean'],
            'age_confirmation' => ['required', 'accepted'],
        ]);
    }

    protected function determinePostRegistrationRoute(array $validated): string
    {
        // USA le route esistenti del sistema
        switch ($validated['user_type']) {
            case 'creator':
                return 'dashboard'; // Route esistente, gestita da DashboardController
            case 'patron':
                return 'dashboard';
            case 'collector':
                return 'marketplace.index'; // Se esiste
            case 'enterprise':
                return 'dashboard';
            case 'trader_pro':
                return 'dashboard';
            case 'epp_entity':
                return 'dashboard'; // Route esistente, gestita da DashboardController
            default:
                return 'dashboard';
        }
    }
}
