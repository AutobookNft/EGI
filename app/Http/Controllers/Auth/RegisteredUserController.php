<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Gdpr\GdprActivityCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserPersonalData;
use App\Models\UserOrganizationData;
use App\Models\UserDocument;
use App\Models\UserInvoicePreference;
use App\Services\Gdpr\ConsentService;
use App\Services\Gdpr\AuditLogService;
use App\Services\Gdpr\LegalContentService;
use App\Services\CollectionService;
use Ultra\EgiModule\Contracts\WalletServiceInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Ultra\EgiModule\Contracts\UserRoleServiceInterface;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * @Oracode Controller: Permission-Based Registration with Domain Separation
 * 🎯 Purpose: Handle complete user registration with conditional ecosystem setup
 * 🛡️ Privacy: Full GDPR compliance with domain-separated data initialization
 * 🧱 Core Logic: Permission-based ecosystem creation (not hardcoded user types)
 *
 * @package App\Http\Controllers\Auth
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 4.0.0 - CLEAN COMPLETE REWRITE
 * @date 2025-06-04
 * @solution Permission-based ecosystem setup + domain separation + Algorand integration
 */
class RegisteredUserController extends Controller {
    /**
     * Constructor with complete dependency injection
     */
    public function __construct(
        protected ErrorManagerInterface $errorManager,
        protected UltraLogManager $logger,
        protected ConsentService $consentService,
        protected AuditLogService $auditService,
        protected CollectionService $collectionService,
        protected WalletServiceInterface $walletService,
        protected UserRoleServiceInterface $userRoleService,
        protected LegalContentService $legalContentService,

    ) {
    }

    /**
     * Display registration view with GDPR context
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create() {
        try {
            $consentTypes = $this->consentService->getConsentTypes();
            $privacyPolicyVersion = config('gdpr.current_policy_version', '1.0.0');

            $this->logger->info('[Registration] Registration page loaded', [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toISOString()
            ]);

            return view('auth.register', [
                'consentTypes' => $consentTypes,
                'privacyPolicyVersion' => $privacyPolicyVersion,
                'brandColors' => [
                    'oro_fiorentino' => '#D4A574',
                    'verde_rinascita' => '#2D5016',
                    'blu_algoritmo' => '#1B365D',
                    'rosso_committente' => '#A12C2F' // <-- Colore aggiunto per il Commissioner
                ]
            ]);
        } catch (\Exception $e) {
            return $this->errorManager->handle('REGISTRATION_PAGE_LOAD_ERROR', [
                'error' => $e->getMessage(),
                'ip_address' => request()->ip()
            ], $e);
        }
    }

    /**
     * Handle complete permission-based registration with ecosystem setup
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(RegistrationRequest $request): RedirectResponse {
        $userId = null;
        $collectionId = null;

        $logContext = [
            'operation' => 'permission_based_user_registration',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString()
        ];

        try {
            // ═══ VALIDATION ═══
            $validated = $this->validateRegistration($request);
            $logContext['user_type'] = $validated['user_type'];

            $this->logger->info('[Registration] Starting permission-based registration', $logContext);

            // ═══ MAIN TRANSACTION ═══
            $result = DB::transaction(function () use ($validated, $logContext, &$userId, &$collectionId) {

                // 1. CREATE USER WITH ALGORAND WALLET
                $user = $this->createUserWithAlgorandWallet($validated);
                $userId = $user->id;
                $logContext['user_id'] = $userId;
                $logContext['algorand_wallet'] = $user->wallet;

                $this->logger->info('[Registration] User created with Algorand wallet', $logContext);

                // 2. ASSIGN ROLE AND CHECK ECOSYSTEM PERMISSIONS
                $canCreateEcosystem = $this->assignRoleAndCheckPermissions($user, $validated['user_type']);
                $logContext['can_create_ecosystem'] = $canCreateEcosystem;

                // 3. CONDITIONAL ECOSYSTEM SETUP
                $collection = null;
                if ($canCreateEcosystem) {
                    $collection = $this->createFullEcosystem($user, $validated, $logContext);
                    $collectionId = $collection->id;
                    $logContext['collection_id'] = $collectionId;
                    $logContext['ecosystem_created'] = true;
                } else {
                    $logContext['ecosystem_created'] = false;
                    $this->logger->info('[Registration] User type does not require ecosystem setup', $logContext);
                }

                // 4. INITIALIZE USER DOMAINS (always)
                $this->initializeUserDomains($user, $validated, $logContext);

                // 5. PROCESS GDPR CONSENTS
                $this->processGdprConsents($user, $validated, $logContext);

                // 6. CREATE AUDIT RECORD
                $this->createRegistrationAuditRecord($user, $collection, $validated, $logContext);

                return [
                    'user' => $user,
                    'collection' => $collection,
                    'ecosystem_created' => $canCreateEcosystem,
                ];
            });

            // ═══ SUCCESS FLOW ═══
            event(new Registered($result['user']));
            Auth::login($result['user']);

            $successMessage = $result['ecosystem_created']
                ? __('Welcome to your Digital Renaissance! Your creative ecosystem is ready.')
                : __('Welcome to FlorenceEGI! Complete your profile to get started.');

            $this->logger->info('[Registration] Registration completed successfully', [
                ...$logContext,
                'success' => true,
                'ecosystem_created' => $result['ecosystem_created']
            ]);

            return redirect()->route('dashboard')
                ->with('success', $successMessage)
                ->with('user_type', $validated['user_type'])
                ->with('ecosystem_created', $result['ecosystem_created'])
                ->with('algorand_wallet', $result['user']->wallet);
        } catch (\Exception $e) {
            $errorContext = [
                ...$logContext,
                'user_id' => $userId,
                'collection_id' => $collectionId,
                'error' => $e->getMessage()
            ];

            // Determine specific error code based on exception message
            $errorCode = $this->determineErrorCode($e->getMessage());

            // Log the error using errorManager
            $this->errorManager->handle($errorCode, $errorContext, $e);

            // Always return a RedirectResponse for registration errors
            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', __('Registration failed. Please try again or contact support if the problem persists.'));
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // PRIVATE METHODS
    // ═══════════════════════════════════════════════════════════════

    /**
     * Validate registration form with GDPR consents
     * @oracode-pillar: Interrogabilità Totale
     */
    protected function validateRegistration(Request $request): array {
        // 🎯 Dynamic user types from config with fallback
        $allowedUserTypes = config('app.fegi_user_type', []);

        // 🛡️ Fallback to default user types if config is empty or missing
        if (empty($allowedUserTypes)) {
            $allowedUserTypes = [
                'commissioner',
                'collector',
                'creator',
                'patron',
                'epp',
                'company',
                'trader_pro'
            ];
        }

        $userTypeRule = ['required', 'in:' . implode(',', $allowedUserTypes)];

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'user_type' => $userTypeRule,

            // ═══ GDPR REQUIRED ═══
            'privacy_policy_accepted' => ['required', 'accepted'],
            'terms_accepted' => ['required', 'accepted'],
            'age_confirmation' => ['required', 'accepted'],

            // ═══ GDPR OPTIONAL ═══
            'consents' => ['sometimes', 'array'],
            'consents.analytics' => ['sometimes', 'string', 'in:1,0'],
            'consents.marketing' => ['sometimes', 'string', 'in:1,0'],
            'consents.profiling' => ['sometimes', 'string', 'in:1,0'],
        ]);
    }

    /**
     * Create user with valid Algorand wallet address
     * @oracode-pillar: Esplicitamente Intenzionale
     */
    protected function createUserWithAlgorandWallet(array $validated): User {
        try {
            $algorandAddress = $this->generateValidAlgorandAddress();

            return User::create([
                // ═══ CORE FIELDS ═══
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'usertype' => $validated['user_type'],

                // ═══ ALGORAND INTEGRATION ═══
                'wallet' => $algorandAddress,
                'wallet_balance' => 0.0000,

                // ═══ SYSTEM FIELDS ═══
                'language' => app()->getLocale(),
                'email_verified_at' => null,
                'terms' => $validated['terms_accepted'] ? 1 : 0,

                // ═══ GDPR COMPLIANCE ═══
                'gdpr_consents_given_at' => now(),
                'gdpr_compliant' => true,
                'consent_summary' => $this->buildConsentSummary($validated),

                'created_via' => 'web_form_permission_based',
            ]);
        } catch (\Exception $e) {
            $this->logger->error('[Registration] Failed to create user with Algorand wallet', [
                'error' => $e->getMessage(),
                'user_type' => $validated['user_type'],
                'email' => $validated['email']
            ]);

            throw new \Exception('Failed to create user with Algorand wallet: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Generate valid Algorand address format (58 chars, Base32 [A-Z2-7])
     * @oracode-pillar: Semplicità Potenziante
     */
    private function generateValidAlgorandAddress(): string {
        try {
            $maxAttempts = 10; // Limite per evitare loop infiniti
            $attempt = 0;

            do {
                $attempt++;

                // Algorand addresses: 58 chars, Base32 alphabet [A-Z2-7]
                $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
                $address = '';

                for ($i = 0; $i < 58; $i++) {
                    $address .= $base32Chars[\random_int(0, 31)];
                }

                // Validate our generated address
                if (!\preg_match('/^[A-Z2-7]{58}$/', $address)) {
                    throw new \Exception('Generated address does not match Algorand format validation');
                }

                // Check if this address already exists in users or wallets tables
                $existsInUsers = User::where('wallet', $address)->exists();
                $existsInWallets = \App\Models\Wallet::where('wallet', $address)->exists();

                if (!$existsInUsers && !$existsInWallets) {
                    // Address is unique, return it
                    $this->logger->info('[Registration] Unique Algorand address generated', [
                        'address_length' => strlen($address),
                        'attempts_needed' => $attempt,
                        'format_valid' => true
                    ]);

                    return $address;
                }

                // Log collision for monitoring
                $this->logger->warning('[Registration] Algorand address collision detected', [
                    'attempt' => $attempt,
                    'exists_in_users' => $existsInUsers,
                    'exists_in_wallets' => $existsInWallets,
                    'address_preview' => substr($address, 0, 10) . '...'
                ]);
            } while ($attempt < $maxAttempts);

            // Se arriviamo qui, abbiamo esaurito i tentativi
            throw new \Exception("Failed to generate unique Algorand address after {$maxAttempts} attempts");
        } catch (\Exception $e) {
            $this->logger->error('[Registration] Failed to generate Algorand address', [
                'error' => $e->getMessage(),
                'validation_pattern' => '^[A-Z2-7]{58}$'
            ]);

            throw new \Exception('Failed to generate valid Algorand address: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Build consent summary for user record
     * @oracode-pillar: Coerenza Semantica
     */
    private function buildConsentSummary(array $validated): string {
        return json_encode([
            'privacy_policy' => $validated['privacy_policy_accepted'] ?? false,
            'terms' => $validated['terms_accepted'] ?? false,
            'age_confirmed' => $validated['age_confirmation'] ?? false,
            'registration_ip' => request()->ip(),
            'registration_user_agent' => request()->userAgent(),
            'registration_method' => 'web_form_permission_based',
            'consent_timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Assign role and check ecosystem creation permissions
     * @oracode-pillar: Coerenza Semantica
     */
    protected function assignRoleAndCheckPermissions(User $user, string $userType): bool {
        try {
            // Map user type to Spatie role
            $roleMapping = config('app.role_mapping');

            $roleName = $roleMapping[$userType] ?? 'guest';
            $user->assignRole($roleName);

            // Refresh user to load role permissions
            $user->refresh();

            // Check if user can create ecosystem (permission-based, not hardcoded)
            $canCreateEcosystem = $user->can('create_collection');

            $this->logger->info('[Registration] Role assigned and permissions checked', [
                'user_id' => $user->id,
                'user_type' => $userType,
                'assigned_role' => $roleName,
                'can_create_ecosystem' => $canCreateEcosystem
            ]);

            return $canCreateEcosystem;
        } catch (\Exception $e) {
            $this->logger->error('[Registration] Failed to assign role and check permissions', [
                'user_id' => $user->id,
                'user_type' => $userType,
                'error' => $e->getMessage()
            ]);

            throw new \Exception('Failed to assign role and check permissions: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Create complete ecosystem: collection + wallets + relationships
     * @oracode-pillar: Circolarità Virtuosa
     */
    protected function createFullEcosystem(User $user, array $validated, array $logContext): \App\Models\Collection {
        try {
            // 1. Create Collection using existing CollectionService
            $collectionName = $this->getCollectionNameForUserType($validated['user_type'], $validated['name']);

            $this->logger->info('[Registration] Creating ecosystem collection', [
                ...$logContext,
                'collection_name' => $collectionName
            ]);

            $collection = $this->collectionService->findOrCreateUserCollection($user, [
                ...$logContext,
                'custom_name' => $collectionName,
                'created_via' => 'user_registration',
                'user_type_context' => $validated['user_type'],
            ]);

            if ($collection instanceof \Illuminate\Http\JsonResponse) {
                throw new \Exception('CollectionService returned error response instead of Collection model');
            }

            // 2. Determine the correct collection role based on user type
            $collectionRole = $this->determineCollectionRole($validated['user_type']);

            // 3. Link User to Collection with correct role (SAFE - usa syncWithoutDetaching)
            // CollectionService potrebbe aver già fatto il link, quindi usiamo sync invece di attach
            $collection->users()->syncWithoutDetaching([
                $user->id => [
                    'role' => $collectionRole,
                    'is_owner' => $collectionRole === 'creator',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);

            $this->logger->info('[Registration] User linked to collection with correct role', [
                ...$logContext,
                'collection_id' => $collection->id,
                'user_type' => $validated['user_type'],
                'collection_role' => $collectionRole,
                'is_owner' => $collectionRole === 'creator'
            ]);

            // 4. Setup Wallets for Collection using WalletService
            $this->walletService->attachDefaultWalletsToCollection($collection, $user);

            $this->logger->info('[Registration] Wallets attached to collection', [
                ...$logContext,
                'collection_id' => $collection->id
            ]);

            // 5. Set as Current Collection
            $user->update(['current_collection_id' => $collection->id]);

            $this->logger->info('[Registration] Full ecosystem created successfully', [
                ...$logContext,
                'collection_id' => $collection->id,
                'collection_name' => $collection->collection_name,
                'user_role_in_collection' => $collectionRole
            ]);

            return $collection;
        } catch (\Exception $e) {
            $this->logger->error('[Registration] Failed to create ecosystem', [
                'user_id' => $user->id,
                'user_type' => $validated['user_type'],
                'step_failed' => $this->determineEcosystemFailureStep($e),
                'error' => $e->getMessage()
            ]);

            throw new \Exception('Failed to create ecosystem: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Generate collection name based on user type and name
     * @oracode-pillar: Semplicità Potenziante
     */
    private function getCollectionNameForUserType(string $userType, string $userName): string {
        $firstName = explode(' ', trim($userName), 2)[0];

        $typeNames = [
            'creator' => "{$firstName}'s Arte",
            'enterprise' => "{$firstName} Corporate Gallery",
            'patron' => "Patronato di {$firstName}",
        ];

        return $typeNames[$userType] ?? "{$firstName}'s Collection";
    }

    /**
     * Determine the correct collection role based on user type
     * @oracode-pillar: Coerenza Semantica
     */
    private function determineCollectionRole(string $userType): string {
        // Map user types to their collection roles
        $collectionRoleMapping = [
            'commissioner' => 'commissioner',
            'creator' => 'creator',
            'enterprise' => 'creator', // Enterprise users are creators of their collections
            'patron' => 'patron',
            'collector' => 'collector',
            'trader_pro' => 'trader',
            'epp_entity' => 'creator',
        ];

        return $collectionRoleMapping[$userType] ?? 'viewer';
    }

    /**
     * Initialize all user domain tables
     * @oracode-pillar: Evoluzione Ricorsiva
     */
    protected function initializeUserDomains(User $user, array $validated, array $logContext): void {
        try {
            // User Profile (always)
            UserProfile::create(['user_id' => $user->id]);

            // Personal Data (always, GDPR-sensitive)
            UserPersonalData::create([
                'user_id' => $user->id,
                'allow_personal_data_processing' => true,
                'processing_purposes' => json_encode(['platform_operation']),
                'consent_updated_at' => now(),
            ]);

            // Organization Data (only for enterprise)
            if ($validated['user_type'] === 'enterprise') {
                UserOrganizationData::create([
                    'user_id' => $user->id,
                    'business_type' => 'enterprise',
                    'is_seller_verified' => false,
                    'can_issue_invoices' => true,
                ]);
            }

            // Documents (always)
            UserDocument::create([
                'user_id' => $user->id,
                'verification_status' => 'pending',
            ]);

            // Invoice Preferences (always)
            UserInvoicePreference::create([
                'user_id' => $user->id,
                'can_issue_invoices' => true,
            ]);

            $this->logger->info('[Registration] User domains initialized successfully', [
                ...$logContext,
                'domains_created' => ['profiles', 'personal_data', 'documents', 'invoice_preferences'],
                'enterprise_domain' => $validated['user_type'] === 'enterprise' ? 'created' : 'skipped'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('[Registration] Failed to initialize user domains', [
                'user_id' => $user->id,
                'user_type' => $validated['user_type'],
                'error' => $e->getMessage()
            ]);

            throw new \Exception('Failed to initialize user domains: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Process GDPR consents dynamically and record Terms of Service acceptance.
     * The logic is now driven by the consent types defined in the database,
     * making it robust and automatically adaptable to future changes.
     * @oracode-pillar: Dignità Preservata, Coerenza Semantica
     */
    protected function processGdprConsents(User $user, array $validated, array $logContext): void {
        try {
            // 1. Otteniamo la versione corrente dei ToS, necessaria per la registrazione specifica.
            $currentTermsVersion = $this->legalContentService->getCurrentVersionString();

            // 2. Recuperiamo TUTTI i tipi di consenso disponibili dal nostro servizio.
            // Questa è la nostra Single Source of Truth.
            $availableConsentTypes = $this->consentService->getConsentTypes();

            // ✅ NUOVO BLOCCO DINAMICO: Costruiamo l'array dei consensi iniziali
            // iterando su quelli disponibili, invece di hardcodarli.
            $initialConsents = [];
            foreach ($availableConsentTypes as $consentType) {
                // I consensi obbligatori sono sempre 'true'.
                // Nota: 'terms-of-service' verrà gestito a parte per registrare la versione.
                if ($consentType->required && $consentType->key !== 'terms-of-service') {
                    $initialConsents[$consentType->key] = true;
                } else {
                    // Per i consensi opzionali, controlliamo il valore inviato dal form.
                    // Il cast a (bool) gestisce '1', '0', o l'assenza del campo.
                    $initialConsents[$consentType->key] = (bool)($validated['consents'][$consentType->key] ?? false);
                }
            }

            // Validiamo i consensi obbligatori che devono essere stati accettati dal form
            if (!($validated['privacy_policy_accepted'] ?? false) || !($validated['terms_accepted'] ?? false) || !($validated['age_confirmation'] ?? false)) {
                throw new \Exception("Required consent (privacy, terms, or age) not accepted in submission.");
            }

            $this->logger->info('[Registration] Processing dynamically built GDPR consents', [
                ...$logContext,
                'initial_consents_built' => $initialConsents,
                'terms_version_to_be_accepted' => $currentTermsVersion
            ]);

            // 3. Creiamo i consensi di default (esclusi i ToS che registriamo dopo)
            // Il metodo createDefaultConsents gestirà i consensi opzionali e tecnici.
            $createdConsents = $this->consentService->createDefaultConsents($user, $initialConsents);

            if (empty($createdConsents)) {
                throw new \Exception('ConsentService createDefaultConsents returned empty result');
            }

            // 4. Registriamo in modo ESPLICITO l'accettazione dei Termini di Servizio con la versione.
            $this->consentService->recordTermsConsent(
                $user,
                $currentTermsVersion,
                ['source' => 'user_registration']
            );

            $this->logger->info('[Registration] GDPR consents processed successfully', [
                ...$logContext,
                'consents_created' => array_keys($createdConsents),
                'terms_of_service_accepted_version' => $currentTermsVersion
            ]);
        } catch (\Exception $e) {
            $this->logger->error('[Registration] Failed to process GDPR consents', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            throw new \Exception('Failed to process GDPR consents: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Create comprehensive audit record for registration
     * @oracode-pillar: Trasparenza Operativa
     */
    protected function createRegistrationAuditRecord(User $user, ?\App\Models\Collection $collection, array $validated, array $logContext): void {
        try {
            $this->auditService->logUserAction(
                $user,
                'user_registration_completed_with_domains',
                [
                    'registration_method' => 'web_form_permission_based',
                    'user_type' => $validated['user_type'],
                    'ecosystem_created' => !is_null($collection),
                    'collection_id' => $collection?->id,
                    'collection_name' => $collection?->collection_name,
                    'algorand_wallet' => $user->wallet,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'privacy_consents' => [
                        'privacy_policy' => $validated['privacy_policy_accepted'] ?? false,
                        'terms' => $validated['terms_accepted'] ?? false,
                        'age_confirmed' => $validated['age_confirmation'] ?? false,
                    ],
                    'optional_consents' => $validated['consents'] ?? [],
                    'domains_initialized' => true,
                ],
                GdprActivityCategory::REGISTRATION,

            );
        } catch (\Exception $e) {
            $this->logger->warning('[Registration] Failed to create audit record (non-blocking)', [
                ...$logContext,
                'audit_error' => $e->getMessage()
            ]);
            // Don't fail registration for audit errors - it's non-blocking
        }
    }

    /**
     * Determine which step of ecosystem setup failed
     */
    private function determineEcosystemFailureStep(\Exception $e): string {
        $message = $e->getMessage();

        if (str_contains($message, 'Collection')) return 'collection_creation';
        if (str_contains($message, 'attach') || str_contains($message, 'wallet')) return 'wallet_attachment';
        if (str_contains($message, 'link') || str_contains($message, 'users')) return 'user_collection_linking';
        if (str_contains($message, 'current_collection_id')) return 'current_collection_assignment';

        return 'unknown_ecosystem_step';
    }

    /**
     * Determine appropriate UEM error code based on exception message
     */
    private function determineErrorCode(string $errorMessage): string {
        // Check for specific error patterns to map to appropriate UEM codes
        if (str_contains($errorMessage, 'Algorand')) {
            return 'ALGORAND_WALLET_GENERATION_FAILED';
        }

        if (str_contains($errorMessage, 'role') || str_contains($errorMessage, 'permission')) {
            return 'ROLE_ASSIGNMENT_FAILED';
        }

        if (str_contains($errorMessage, 'ecosystem') || str_contains($errorMessage, 'collection') || str_contains($errorMessage, 'wallet')) {
            return 'ECOSYSTEM_SETUP_FAILED';
        }

        if (str_contains($errorMessage, 'domain') || str_contains($errorMessage, 'UserProfile') || str_contains($errorMessage, 'UserPersonalData')) {
            return 'USER_DOMAIN_INITIALIZATION_FAILED';
        }

        if (str_contains($errorMessage, 'consent') || str_contains($errorMessage, 'GDPR')) {
            return 'GDPR_CONSENT_PROCESSING_FAILED';
        }

        // Default fallback
        return 'PERMISSION_BASED_REGISTRATION_FAILED';
    }
}
