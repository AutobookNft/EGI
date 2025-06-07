<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Helpers\FegiAuth;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Carbon\Carbon;

/**
 * @Oracode Controller: Base User Domain GDPR-Compliant Management (OS1-Compliant)
 * 🎯 Purpose: Provide unified FegiAuth + UEM + GDPR foundation for all user domains
 * 🛡️ Privacy: Complete audit trail, identity verification, consent management
 * 🧱 Core Logic: FegiAuth verification, weak→strong upgrade detection, UEM integration
 * 🌍 Scale: Global-ready architecture with 6 MVP country-specific validation support
 * ⏰ MVP: Critical foundation for 30 June FlorenceEGI deadline
 *
 * @package App\Http\Controllers\User
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 2.0.0 (FlorenceEGI MVP - OS1 Native)
 * @deadline 2025-06-30
 */
abstract class BaseUserDomainController extends Controller
{
    /**
     * Error manager for robust error handling with UEM integration
     * @var ErrorManagerInterface
     */
    protected ErrorManagerInterface $errorManager;

    /**
     * Logger for audit trail and GDPR compliance logging
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Current authenticated user (weak or strong)
     * @var \App\Models\User|null
     */
    protected ?\App\Models\User $currentUser;

    /**
     * Current authentication type (weak, strong, guest)
     * @var string
     */
    protected string $authType;

    /**
     * MVP supported countries (6 nations only)
     * @var array<string, string>
     */
    protected array $mvpCountries = [
        'IT' => 'Italy',
        'PT' => 'Portugal',
        'FR' => 'France',
        'ES' => 'Spain',
        'EN' => 'England',
        'DE' => 'Germany'
    ];

    /**
     * @Oracode Constructor: Initialize Base Controller with Dependencies
     * 🎯 Purpose: Set up error handling, logging, and authentication context
     * 📥 Input: UEM error manager and ULM logger instances via DI
     * 🛡️ Privacy: Initialize audit logging for GDPR compliance
     * 🧱 Core Logic: Dependency injection with authentication context setup
     *
     * @param ErrorManagerInterface $errorManager UEM error manager for robust error handling
     * @param UltraLogManager $logger ULM logger for audit trail and compliance
     */
    public function __construct(
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger
    ) {
        $this->errorManager = $errorManager;
        $this->logger = $logger;

        // Initialize authentication context
        $this->currentUser = FegiAuth::user();
        $this->authType = FegiAuth::getAuthType();
    }

    /**
     * @Oracode Method: Check Weak Authentication Access Rights
     * 🎯 Purpose: Verify if weak auth user can access domain functionality
     * 📥 Input: No parameters (uses current authentication context)
     * 📤 Output: True if access allowed | RedirectResponse to upgrade
     * 🛡️ Privacy: Logs access attempts for audit trail
     * 🧱 Core Logic: Weak auth users can access some domains, others require upgrade
     *
     * @return bool|RedirectResponse True if access granted, redirect if upgrade needed
     */
    protected function checkWeakAuthAccess(): bool|RedirectResponse
    {
        // Guest users always need authentication
        if (!FegiAuth::check()) {
            $this->auditDataAccess('authentication_required', [
                'reason' => 'guest_user_domain_access',
                'requested_domain' => static::class
            ]);

            return $this->respondAuthRequired();
        }

        // Strong auth users always have access
        if (FegiAuth::isStrongAuth()) {
            return true;
        }

        // Weak auth access depends on domain - check permissions
        $domainPermission = $this->getRequiredDomainPermission();

        if ($domainPermission && !FegiAuth::can($domainPermission)) {
            $this->auditDataAccess('weak_auth_upgrade_required', [
                'user_id' => FegiAuth::id(),
                'required_permission' => $domainPermission,
                'requested_domain' => static::class
            ]);

            return $this->redirectToUpgrade();
        }

        return true;
    }

    /**
     * @Oracode Method: Redirect to Account Upgrade Flow
     * 🎯 Purpose: Redirect weak auth users to strong authentication upgrade
     * 📤 Output: RedirectResponse to upgrade flow with context
     * 🛡️ Privacy: Logs upgrade prompts for conversion tracking
     * 🧱 Core Logic: Context-aware redirect with return URL preservation
     *
     * @return RedirectResponse Redirect to upgrade flow with return context
     */
    protected function redirectToUpgrade(): RedirectResponse
    {
        $this->auditDataAccess('upgrade_flow_initiated', [
            'user_id' => FegiAuth::id(),
            'source_domain' => static::class,
            'return_url' => request()->fullUrl()
        ]);

        return redirect()
            ->route('account.upgrade')
            ->with('upgrade_reason', __('user_domains.upgrade_required_for_domain'))
            ->with('return_url', request()->fullUrl())
            ->with('info', __('user_domains.upgrade_access_more_features'));
    }

    /**
     * @Oracode Method: Audit Data Access for GDPR Compliance
     * 🎯 Purpose: Log all data access attempts for GDPR audit trail
     * 📥 Input: Action description and context data
     * 📤 Output: Void (logs to audit system)
     * 🛡️ Privacy: GDPR-compliant audit logging with data minimization
     * 🧱 Core Logic: Structured logging for compliance and security monitoring
     *
     * @param string $action Description of the action being audited
     * @param array<string, mixed> $context Additional context data for audit
     * @return void
     */
    protected function auditDataAccess(string $action, array $context = []): void
    {
        $auditData = [
            'action' => $action,
            'user_id' => FegiAuth::id(),
            'auth_type' => $this->authType,
            'domain_controller' => static::class,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => Carbon::now()->toISOString(),
            'context' => $context
        ];

        // Log with ULM for structured audit trail
        $this->logger->info('[GDPR AUDIT] Data domain access', $auditData);

        // Store in database for long-term compliance if needed
        if (config('gdpr.store_audit_database', true)) {

            // Prepare data using MODEL FILLABLE fields
            $recordData = [
                'user_id' => FegiAuth::id(),
                'action_type' => $action, // Usa action_type invece di action
                'category' => 'user_data_access',
                'description' => "Access to {$action} via " . static::class,
                'legal_basis' => 'user_request',
                'data_subject_id' => FegiAuth::id(),
                'data_controller' => 'FlorenceEGI Platform',
                'purpose_of_processing' => 'User data management',
                'context_data' => $context, // Usa context_data invece di details
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'retention_period' => '7 years', // Usa retention_period invece di retention_until
            ];

            // Create checksum for existing model
            $checksum = hash('sha256', json_encode($recordData));
            $recordData['checksum'] = $checksum;

            \App\Models\GdprAuditLog::create($recordData);
        }
    }

    /**
     * @Oracode Method: Require Identity Verification for Sensitive Operations
     * 🎯 Purpose: Enforce identity re-verification for sensitive data changes
     * 📥 Input: No parameters (checks session verification status)
     * 📤 Output: True if verified | RedirectResponse to verification
     * 🛡️ Privacy: Enhanced security for sensitive personal data operations
     * 🧱 Core Logic: Time-based verification with session management
     *
     * @return bool|RedirectResponse True if identity verified, redirect if verification needed
     */
    protected function requireIdentityVerification(): bool|RedirectResponse
    {
        // Strong auth users may need re-verification for sensitive data
        if (!FegiAuth::isStrongAuth()) {
            return true; // Weak auth users have limited access anyway
        }

        $lastVerification = session('identity_verified_at');
        $verificationWindow = config('user_domains.identity_verification_window', 30); // minutes

        // Check if verification is still valid
        if ($lastVerification &&
            Carbon::parse($lastVerification)->addMinutes($verificationWindow)->isFuture()) {
            return true;
        }

        $this->auditDataAccess('identity_verification_required', [
            'user_id' => FegiAuth::id(),
            'last_verification' => $lastVerification,
            'verification_window' => $verificationWindow
        ]);

        return redirect()
            ->route('user.identity-verification')
            ->with('return_url', request()->fullUrl())
            ->with('verification_reason', __('user_domains.identity_verification_sensitive_data'));
    }

    /**
     * @Oracode Method: Handle Authentication Required Response
     * 🎯 Purpose: Provide consistent response for unauthenticated access attempts
     * 📤 Output: JsonResponse for API | RedirectResponse for web
     * 🧱 Core Logic: Content negotiation for API vs web responses
     *
     * @return JsonResponse|RedirectResponse Appropriate response based on request type
     */
    protected function respondAuthRequired(): JsonResponse|RedirectResponse
    {
        if (request()->expectsJson()) {
            return response()->json([
                'error' => 'AUTHENTICATION_REQUIRED',
                'message' => __('user_domains.authentication_required'),
                'auth_options' => [
                    'fegi_connect' => route('wallet.connect'),
                    'register' => route('register'),
                    'login' => route('login')
                ]
            ], 401);
        }

        return redirect()
            ->route('login')
            ->with('info', __('user_domains.login_to_access_domain'))
            ->with('return_url', request()->fullUrl());
    }

    /**
     * @Oracode Method: Handle Insufficient Permissions Response
     * 🎯 Purpose: Provide consistent response for permission denied scenarios
     * 📥 Input: Optional specific permission that was denied
     * 📤 Output: JsonResponse for API | RedirectResponse for web
     * 🧱 Core Logic: Context-aware error messages with permission details
     *
     * @param string|null $permission Specific permission that was denied
     * @return JsonResponse|RedirectResponse Appropriate response with permission context
     */
    protected function respondPermissionDenied(?string $permission = null): JsonResponse|RedirectResponse
    {
        $this->auditDataAccess('permission_denied', [
            'user_id' => FegiAuth::id(),
            'denied_permission' => $permission,
            'auth_type' => $this->authType
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'error' => 'INSUFFICIENT_PERMISSIONS',
                'message' => __('user_domains.insufficient_permissions'),
                'required_permission' => $permission,
                'upgrade_available' => FegiAuth::isWeakAuth()
            ], 403);
        }

        $redirectRoute = FegiAuth::isWeakAuth() ? 'account.upgrade' : 'dashboard';
        $message = FegiAuth::isWeakAuth()
            ? __('user_domains.upgrade_for_access')
            : __('user_domains.insufficient_permissions');

        return redirect()
            ->route($redirectRoute)
            ->with('error', $message);
    }

    /**
     * @Oracode Method: Handle Successful Operation Response
     * 🎯 Purpose: Provide consistent success response format
     * 📥 Input: Success message and optional data
     * 📤 Output: JsonResponse for API | RedirectResponse for web
     * 🧱 Core Logic: Content negotiation with consistent success format
     *
     * @param string $message Success message key or text
     * @param array<string, mixed> $data Optional additional data for response
     * @param string|null $redirectRoute Optional route for web redirect
     * @return JsonResponse|RedirectResponse Success response in appropriate format
     */
    protected function respondSuccess(
        string $message,
        array $data = [],
        ?string $redirectRoute = null
    ): JsonResponse|RedirectResponse {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data
            ]);
        }

        $redirect = $redirectRoute ? redirect()->route($redirectRoute) : redirect()->back();

        return $redirect->with('success', $message);
    }

    /**
     * @Oracode Method: Handle Error Response with UEM Integration
     * 🎯 Purpose: Process errors through UEM and provide user-friendly responses
     * 📥 Input: Error code, exception, and optional context
     * 📤 Output: JsonResponse for API | RedirectResponse for web
     * 🧱 Core Logic: UEM integration with automatic error handling and logging
     *
     * @param string $errorCode UEM error code for consistent error handling
     * @param \Throwable $exception Original exception for debugging context
     * @param array<string, mixed> $context Additional context for error analysis
     * @return JsonResponse|RedirectResponse Error response in appropriate format
     */
    protected function respondError(
        string $errorCode,
        \Throwable $exception,
        array $context = []
    ): JsonResponse|RedirectResponse {
        // Add domain context to error
        $context['domain_controller'] = static::class;
        $context['user_id'] = FegiAuth::id();
        $context['auth_type'] = $this->authType;

        // Use UEM for consistent error handling
        return $this->errorManager->handle($errorCode, $context, $exception);
    }

    /**
     * @Oracode Method: Get Required Permission for Current Domain
     * 🎯 Purpose: Return domain-specific permission required for access
     * 📤 Output: Permission string or null if no specific permission required
     * 🧱 Core Logic: Override in child controllers for domain-specific permissions
     *
     * @return string|null Permission required for this domain
     */
    protected function getRequiredDomainPermission(): ?string
    {
        // Override in child controllers to specify domain permissions
        // Examples:
        // - PersonalDataController: 'edit_own_personal_data'
        // - DocumentsController: 'manage_own_documents'
        // - InvoiceController: 'manage_own_invoice_preferences'
        // - OrganizationController: 'edit_own_organization_data'
        return null;
    }

    /**
     * @Oracode Method: Check if Current User Can Access Organization Data
     * 🎯 Purpose: Verify user type supports organization data management
     * 📤 Output: Boolean indicating organization data access rights
     * 🧱 Core Logic: Only creator/enterprise/epp_entity have organization data
     *
     * @return bool True if user can access organization data
     */
    protected function canAccessOrganizationData(): bool
    {
        if (!FegiAuth::check()) {
            return false;
        }

        $user = FegiAuth::user();
        $organizationUserTypes = ['creator', 'enterprise', 'epp_entity'];

        return in_array($user->usertype, $organizationUserTypes, true);
    }

    /**
     * @Oracode Method: Get User Country for Localized Validation
     * 🎯 Purpose: Return user's country for MVP country-specific validations
     * 📤 Output: ISO country code string (from 6 MVP nations)
     * 🧱 Core Logic: Fallback chain from user data to request to IT default
     *
     * @return string ISO 3166-1 alpha-2 country code (IT,PT,FR,ES,EN,DE)
     */
    protected function getUserCountry(): string
    {
        $user = FegiAuth::user();

        // Try user's country from profile
        if ($user && $user->country && isset($this->mvpCountries[strtoupper($user->country)])) {
            return strtoupper($user->country);
        }

        // Try to detect from request (MVP countries only)
        $detectedCountry = $this->detectCountryFromRequest();
        if ($detectedCountry && isset($this->mvpCountries[$detectedCountry])) {
            return $detectedCountry;
        }

        // Default to Italy for MVP (primary market)
        return 'IT';
    }

    /**
     * @Oracode Method: Detect Country from Request Context (MVP Only)
     * 🎯 Purpose: Attempt to detect user country from request headers (MVP nations)
     * 📤 Output: ISO country code string or null if detection failed
     * 🧱 Core Logic: Language-based detection restricted to 6 MVP countries
     *
     * @return string|null Detected MVP country code or null
     */
    private function detectCountryFromRequest(): ?string
    {
        // Try to detect from Accept-Language header (MVP countries only)
        $acceptLanguage = request()->header('Accept-Language');
        if ($acceptLanguage) {
            $languageCountryMap = [
                'it' => 'IT',    // Italian → Italy
                'pt' => 'PT',    // Portuguese → Portugal
                'fr' => 'FR',    // French → France
                'es' => 'ES',    // Spanish → Spain
                'en' => 'EN',    // English → England (MVP market)
                'de' => 'DE',    // German → Germany
            ];

            foreach ($languageCountryMap as $lang => $country) {
                if (str_contains(strtolower($acceptLanguage), $lang)) {
                    return $country;
                }
            }
        }

        // Could add IP-based detection here if needed for MVP
        // For now, we keep it simple and language-based only

        return null;
    }

    /**
     * @Oracode Method: Get Formatted User Display Name
     * 🎯 Purpose: Return appropriate user display name based on auth type
     * 📤 Output: Formatted name string for UI display
     * 🧱 Core Logic: Different name formats for weak vs strong auth users
     *
     * @return string User display name appropriate for current auth context
     */
    protected function getUserDisplayName(): string
    {
        $user = FegiAuth::user();

        if (!$user) {
            return __('user_domains.guest_user');
        }

        if (FegiAuth::isWeakAuth()) {
            // Weak auth users might only have wallet address
            return $user->name ?: __('user_domains.fegi_user_short', [
                'wallet' => substr(FegiAuth::getWallet(), 0, 8) . '...'
            ]);
        }

        // Strong auth users have full name
        return $user->name ?: __('user_domains.registered_user');
    }

    /**
     * @Oracode Method: Get All MVP Countries for Form Options
     * 🎯 Purpose: Return array of MVP countries for select options
     * 📤 Output: Array of country codes and names
     * 🧱 Core Logic: Used in form generation for country selection
     *
     * @return array<string, string> Array of MVP country codes and names
     */
    protected function getMvpCountries(): array
    {
        return $this->mvpCountries;
    }

    /**
     * @Oracode Method: Validate MVP Country Code
     * 🎯 Purpose: Check if provided country code is in MVP list
     * 📥 Input: Country code to validate
     * 📤 Output: Boolean indicating if country is MVP supported
     * 🧱 Core Logic: Used for form validation and data integrity
     *
     * @param string $countryCode ISO 3166-1 alpha-2 country code
     * @return bool True if country is in MVP list, false otherwise
     */
    protected function isValidMvpCountry(string $countryCode): bool
    {
        return isset($this->mvpCountries[strtoupper($countryCode)]);
    }
}
