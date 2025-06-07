<?php

namespace App\Http\Controllers;

use App\Enums\Gdpr\ProcessingRestrictionReason;
use App\Enums\Gdpr\ProcessingRestrictionType;
use App\Http\Requests\Gdpr\ProcessingRestrictionRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\GdprService;
use App\Services\Gdpr\ConsentService;
use App\Services\Gdpr\DataExportService;
use App\Services\Gdpr\AuditLogService;
use App\Models\User;
use App\Models\GdprRequest;
use App\Models\BreachReport;
use App\Models\ProcessingRestriction;
use App\Services\Gdpr\ProcessingRestrictionService;

/**
 * @Oracode Controller: GDPR Compliance Management
 * 🎯 Purpose: Complete GDPR rights implementation for FlorenceEGI users
 * 🛡️ Privacy: Handles all GDPR data subject rights with full audit trail
 * 🧱 Core Logic: Manages consent, data portability, rectification, erasure, limitation
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 2.0.0
 * @date 2025-05-22
 * @context gdpr
 */
class GdprController extends Controller
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
     * GDPR business logic service
     * @var GdprService
     */
    protected GdprService $gdprService;

    /**
     * Consent management service
     * @var ConsentService
     */
    protected ConsentService $consentService;

    /**
     * Data export service
     * @var DataExportService
     */
    protected DataExportService $exportService;

    /**
     * Audit logging service
     * @var AuditLogService
     */
    protected AuditLogService $auditService;

    protected ProcessingRestrictionService $processingRestrictionService;


    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param GdprService $gdprService
     * @param ConsentService $consentService
     * @param DataExportService $exportService
     * @param AuditLogService $auditService
     * @param ProcessingRestrictionService $processingRestrictionService
     * @privacy-safe All injected services handle GDPR compliance
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        GdprService $gdprService,
        ConsentService $consentService,
        DataExportService $exportService,
        AuditLogService $auditService,
        ProcessingRestrictionService $processingRestrictionService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->gdprService = $gdprService;
        $this->consentService = $consentService;
        $this->exportService = $exportService;
        $this->auditService = $auditService;
        $this->processingRestrictionService = $processingRestrictionService;

    }

    /**
     * Show GDPR-compliant profile management (replaces Jetstream profile)
     *
     * @return \Illuminate\View\View
     * @seo-purpose Provide comprehensive GDPR profile management interface
     * @accessibility-trait Full ARIA tablist navigation and landmark structure
     */
    public function showProfile()
    {
        try {
            $user = auth()->user();

            // Get user consent status for privacy settings tab
            $consentStatus = null;
            if ($this->consentService) {
                try {
                    $consentStatus = $this->consentService->getUserConsentStatus($user);
                } catch (\Exception $e) {
                    // Log but don't fail - privacy tab will show fallback message
                    if ($this->logger) {
                        $this->logger->warning('[GDPR Profile] Failed to load consent status', [
                            'user_id' => $user->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            return view('gdpr.profile', [
                'user' => $user,
                'consentStatus' => $consentStatus,
                'pageTitle' => __('gdpr.profile_management_title'),
                'brandColors' => [
                    'oro_fiorentino' => '#D4A574',
                    'verde_rinascita' => '#2D5016',
                    'blu_algoritmo' => '#1B365D',
                    'grigio_pietra' => '#6B6B6B',
                    'rosso_urgenza' => '#C13120'
                ],
                // Feature flags for conditional rendering
                'features' => [
                    'password_updates' => \Laravel\Fortify\Features::enabled(\Laravel\Fortify\Features::updatePasswords()),
                    'two_factor_auth' => \Laravel\Fortify\Features::canManageTwoFactorAuthentication(),
                    'browser_sessions' => true,
                    'account_deletion' => \Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures(),
                ]
            ]);

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_PROFILE_PAGE_LOAD_ERROR', [
                'user_id' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'error' => $e->getMessage()
            ], $e);
        }
    }


    /**
     * Store a new processing limitation request.
     *
     * @param ProcessingRestrictionRequest $request Validated restriction request
     * @return \Illuminate\Http\RedirectResponse Redirect with status message
     *
     * @oracode-dimension governance
     * @value-flow Creates new data processing limitation
     * @community-impact Enables user control over their data
     * @transparency-level High - clear feedback on restriction status
     * @narrative-coherence Supports user autonomy and data dignity
     */
    public function limitProcessingStore(ProcessingRestrictionRequest $request)
    {
        try {
            $user = Auth::user();
            $validated = $request->validated();

            $restriction = $this->processingRestrictionService->createRestriction(
                $user,
                ProcessingRestrictionType::from($validated['restriction_type']),
                ProcessingRestrictionReason::from($validated['restriction_reason']),
                $validated['notes'] ?? null,
                $validated['data_categories'] ?? []
            );

            if ($restriction) {
                return redirect()->route('gdpr.limit-processing')
                    ->with('success', __('gdpr.processing_restriction_success'));
            }

            return redirect()->route('gdpr.limit-processing')
                ->with('error', __('gdpr.processing_restriction_failed'));

        } catch (\Throwable $e) {
            $this->errorManager->handle('GDPR_PROCESSING_RESTRICTION_CREATE_ERROR', [
                'user_id' => Auth::id(),
                'request_data' => $request->safe()->except(['notes']),
                'error' => $e->getMessage()
            ], $e);

            return redirect()->route('gdpr.limit-processing')
                ->with('error', __('gdpr.processing_restriction_system_error'));
        }
    }

    /**
     * Remove an existing processing limitation.
     *
     * @param Request $request The HTTP request
     * @param ProcessingRestriction $restriction The restriction to remove
     * @return \Illuminate\Http\RedirectResponse Redirect with status message
     *
     * @oracode-dimension governance
     * @value-flow Removes data processing limitation
     * @community-impact Maintains user control with removal option
     * @transparency-level High - clear feedback on restriction removal
     * @narrative-coherence Completes the control cycle with removal rights
     */
    public function removeProcessingRestriction(Request $request, ProcessingRestriction $restriction)
    {
        try {
            $user = Auth::user();

            // Security check - only allow users to remove their own restrictions
            if ($restriction->user_id !== $user->id) {
                return redirect()->route('gdpr.limit-processing')
                    ->with('error', __('gdpr.unauthorized_action'));
            }

            $success = $this->processingRestrictionService->removeRestriction($restriction);

            if ($success) {
                return redirect()->route('gdpr.limit-processing')
                    ->with('success', __('gdpr.processing_restriction_removed'));
            }

            return redirect()->route('gdpr.limit-processing')
                ->with('error', __('gdpr.processing_restriction_removal_failed'));

        } catch (\Throwable $e) {
            $this->errorManager->handle('GDPR_PROCESSING_RESTRICTION_REMOVE_ERROR', [
                'user_id' => Auth::id(),
                'restriction_id' => $restriction->id,
                'error' => $e->getMessage()
            ], $e);

            return redirect()->route('gdpr.limit-processing')
                ->with('error', __('gdpr.processing_restriction_system_error'));
        }
    }

    // ===================================================================
    // CONSENT MANAGEMENT (ConsentMenu)
    // ===================================================================

    /**
     * Display consent management page
     *
     * @return View
     * @privacy-safe Shows user's own consent status only
     */
    public function consent(): View
    {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing consent management page', [
                'user_id' => $user->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            $consentData = $this->consentService->getUserConsentStatus($user);
            $consentHistory = $this->consentService->getConsentHistory($user);

            $this->auditService->logUserAction($user, 'consent_page_viewed');

            return view('gdpr.consent', [
                'user' => $user,
                'consentStatus' => $consentData['userConsents'],
                'consentHistory' => $consentHistory,
                'lastUpdate' => $consentHistory->first()?->created_at,
                'userConsents' => $consentData['userConsents'],
                'consentSummary' => $consentData['consentSummary']
            ]);

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_CONSENT_PAGE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Display consent preferences management page
     *
     * @return View
     * @privacy-safe Shows user's consent preferences management interface
     */
    public function consentPreferences(): View
    {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing consent preferences page', [
                'user_id' => $user->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            // Ottieni i consensi attuali e le configurazioni disponibili
            $consentData = $this->consentService->getUserConsentStatus($user);
            $consentTypes = $this->consentService->getAvailableConsentTypes();

            $this->auditService->logUserAction($user, 'consent_preferences_page_viewed');

            // Use the main consent view with preferences mode
            return view('gdpr.consent.preferences', [
                'user' => $user,
                'userConsents' => $consentData['userConsents'],
                'consentTypes' => $consentTypes,
                'consentSummary' => $consentData['consentSummary'],
                'lastUpdate' => $consentData['last_updated'],
                'mode' => 'preferences', // Flag to show preferences interface
                'pageTitle' => __('gdpr.consent.preferences_title'),
                'pageSubtitle' => __('gdpr.consent.preferences_subtitle')
            ]);

        } catch (\Exception $e) {
            $this->errorManager->handle('GDPR_CONSENT_PREFERENCES_PAGE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);

            // Return fallback view instead of null
            return view('gdpr.consent', [
                'user' => Auth::user(),
                'userConsents' => collect([]),
                'consentTypes' => [],
                'consentSummary' => [
                    'active_consents' => 0,
                    'total_consents' => 0,
                    'compliance_score' => 0
                ],
                'lastUpdate' => null,
                'mode' => 'error',
                'error' => true,
                'pageTitle' => __('gdpr.consent.preferences_title'),
                'pageSubtitle' => __('gdpr.consent.error_subtitle')
            ]);
        }
    }

    /**
     * Update user consent preferences
     *
     * @param Request $request
     * @return RedirectResponse
     * @privacy-safe Updates only authenticated user's consents
     */
    public function updateConsent(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'consents' => 'required|array',
                'consents.functional' => 'boolean',
                'consents.analytics' => 'boolean',
                'consents.marketing' => 'boolean',
                'consents.profiling' => 'boolean',
            ]);

            $user = Auth::user();

            $this->logger->info('GDPR: Updating user consents', [
                'user_id' => $user->id,
                'consents' => $validated['consents'],
                'log_category' => 'GDPR_CONSENT_UPDATE'
            ]);

            $result = $this->consentService->updateUserConsents($user, $validated['consents']);

            $this->auditService->logUserAction($user, 'consents_updated', [
                'previous_consents' => $result['previous'],
                'new_consents' => $result['current']
            ]);

            return redirect()->route('gdpr.consent')
                ->with('success', __('gdpr.consents_updated_successfully'));

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_CONSENT_UPDATE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Get consent history for user
     *
     * @return RedirectResponse
     * @privacy-safe Shows only authenticated user's consent history
     */
    public function consentHistory(): RedirectResponse
    {
        try {
            $user = Auth::user();
            $history = $this->consentService->getDetailedConsentHistory($user);

            $this->auditService->logUserAction($user, 'consent_history_viewed');

            // Redirect to main consent page with history data in session
            return redirect()->route('gdpr.consent')
                ->with('show_history', true)
                ->with('consent_history', $history)
                ->with('success', __('gdpr.consent.history_loaded'));

        } catch (\Exception $e) {
            $this->errorManager->handle('GDPR_CONSENT_HISTORY_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);

            // Redirect back to consent page with error
            return redirect()->route('gdpr.consent')
                ->with('error', __('gdpr.consent.history_load_failed'));
        }
    }

    // ===================================================================
    // DATA EXPORT & PORTABILITY (ExportDataMenu)
    // ===================================================================

    /**
     * Display data export page
     *
     * @return View
     * @privacy-safe Shows export options and history for authenticated user
     */
    public function exportData(): View
    {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing data export page', [
                'user_id' => $user->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            $exportHistory = $this->exportService->getUserExportHistory($user);
            $dataCategories = $this->exportService->getAvailableDataCategories();

            $this->auditService->logUserAction($user, 'export_page_viewed');

            return view('gdpr.export-data', [
                'user' => $user,
                'exportHistory' => $exportHistory,
                'dataCategories' => $dataCategories
            ]);

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_EXPORT_PAGE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Generate data export for user
     *
     * @param Request $request
     * @return RedirectResponse
     * @privacy-safe Generates export only for authenticated user's data
     */
    public function generateExport(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'format' => 'required|in:json,csv,pdf',
                'categories' => 'required|array',
                'categories.*' => 'string|in:profile,activities,collections,wallet,consents,audit'
            ]);

            $user = Auth::user();

            $this->logger->info('GDPR: Generating data export', [
                'user_id' => $user->id,
                'format' => $validated['format'],
                'categories' => $validated['categories'],
                'log_category' => 'GDPR_EXPORT_GENERATE'
            ]);

            $exportToken = $this->exportService->generateUserDataExport(
                $user,
                $validated['format'],
                $validated['categories']
            );

            $this->auditService->logUserAction($user, 'export_requested', [
                'format' => $validated['format'],
                'categories' => $validated['categories'],
                'export_token' => $exportToken
            ]);

            return redirect()->route('gdpr.export-data')
                ->with('success', __('gdpr.export_generation_started'))
                ->with('export_token', $exportToken);

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_EXPORT_GENERATION_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Download generated data export
     *
     * @param string $token
     * @return StreamedResponse
     * @privacy-safe Downloads only if token belongs to authenticated user
     */
    public function downloadExport(string $token): StreamedResponse
    {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Downloading data export', [
                'user_id' => $user->id,
                'export_token' => $token,
                'log_category' => 'GDPR_EXPORT_DOWNLOAD'
            ]);

            $export = $this->exportService->getExportByToken($token, $user);

            if (!$export || $export->user_id !== $user->id) {
                return $this->errorManager->handle('GDPR_EXPORT_NOT_FOUND', [
                    'user_id' => $user->id,
                    'token' => $token
                ]);
            }

            $this->auditService->logUserAction($user, 'export_downloaded', [
                'export_token' => $token,
                'file_size' => $export->file_size
            ]);

            return $this->exportService->streamExportFile($export);

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_EXPORT_DOWNLOAD_FAILED', [
                'user_id' => Auth::id(),
                'token' => $token,
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    // ===================================================================
    // PERSONAL DATA MANAGEMENT (EditPersonalDataMenu)
    // ===================================================================

    /**
     * Show the form for editing personal data
     *
     * @return \Illuminate\View\View
     * @seo-purpose Personal data editing form for authenticated users
     * @accessibility-trait Form validation, field descriptions, error handling
     */
    public function editPersonalData()
    {
        try {
            $user = auth()->user();

            // Get countries list for the country dropdown
            $countries = $this->getCountriesList();

            // Get editable fields based on user permissions
            $editableFields = $this->getEditableFieldsForUser($user);

            // Get on-chain data if user has wallet integrations
            $onChainData = $this->getOnChainData($user);

            $this->logger->info('[GDPR] Personal data edit form accessed', [
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'has_wallet' => !empty($user->wallet),
                'editable_fields_count' => count($editableFields)
            ]);

            return view('gdpr.edit-personal-data', [
                'user' => $user,
                'countries' => $countries,
                'editableFields' => $editableFields,
                'onChainData' => $onChainData,
                'pageTitle' => __('profile.edit_personal_data'),
                'pageDescription' => __('profile.edit_personal_data_description'),
            ]);

        } catch (\Exception $e) {
            $this->logger->error('[GDPR] Failed to load personal data edit form', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorManager->handle('GDPR_EDIT_DATA_PAGE_FAILED', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
    * Update personal data
    *
    * @param \App\Http\Requests\UpdatePersonalDataRequest $request
    * @return \Illuminate\Http\RedirectResponse
    */
    public function updatePersonalData(\App\Http\Requests\UpdatePersonalDataRequest $request)
    {
        try {
            $user = auth()->user();
            $validated = $request->validated();

            // Log the update attempt
            $this->logger->info('[GDPR] Personal data update initiated', [
                'user_id' => $user->id,
                'fields_to_update' => array_keys($validated),
                'ip_address' => $request->ip()
            ]);

            // Filter out non-editable fields for this user
            $editableFields = $this->getEditableFieldsForUser($user);
            $filteredData = array_intersect_key($validated, array_flip($editableFields));

            // Update user data
            $user->update($filteredData);

            // Log the successful update via GDPR audit service
            if ($this->auditService) {
                $this->auditService->logUserAction(
                    $user,
                    'personal_data_updated',
                    [
                        'updated_fields' => array_keys($filteredData),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ],
                    'data_modification'
                );
            }

            $this->logger->info('[GDPR] Personal data updated successfully', [
                'user_id' => $user->id,
                'updated_fields' => array_keys($filteredData)
            ]);

            return redirect()
                ->route('profile.show')
                ->with('success', __('profile.personal_data_updated_successfully'));

        } catch (\Exception $e) {
            $this->logger->error('[GDPR] Failed to update personal data', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'request_data' => $request->safe()->except(['password', 'password_confirmation'])
            ]);

            return $this->errorManager->handle('GDPR_UPDATE_PERSONAL_DATA_FAILED', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Get list of countries for dropdown
     *
     * @return array
     */
    private function getCountriesList(): array
    {
        // You can replace this with a more comprehensive list or use a package
        return [
            'IT' => 'Italia',
            'US' => 'United States',
            'GB' => 'United Kingdom',
            'FR' => 'France',
            'DE' => 'Germany',
            'ES' => 'Spain',
            'PT' => 'Portugal',
            'NL' => 'Netherlands',
            'BE' => 'Belgium',
            'CH' => 'Switzerland',
            'AT' => 'Austria',
            'SE' => 'Sweden',
            'NO' => 'Norway',
            'DK' => 'Denmark',
            'FI' => 'Finland',
            'IE' => 'Ireland',
            'PL' => 'Poland',
            'CZ' => 'Czech Republic',
            'HU' => 'Hungary',
            'GR' => 'Greece',
            'HR' => 'Croatia',
            'SI' => 'Slovenia',
            'SK' => 'Slovakia',
            'EE' => 'Estonia',
            'LV' => 'Latvia',
            'LT' => 'Lithuania',
            'MT' => 'Malta',
            'CY' => 'Cyprus',
            'LU' => 'Luxembourg',
            'BG' => 'Bulgaria',
            'RO' => 'Romania',
            // Add more countries as needed
        ];
    }

    /**
     * Get editable fields based on user type and permissions
     *
     * @param \App\Models\User $user
     * @return array
     */
    private function getEditableFieldsForUser(\App\Models\User $user): array
    {
        $baseFields = [
            'name',
            'email',
            'phone',
            'date_of_birth',
            'address',
            'city',
            'state',
            'postal_code',
            'country',
            'bio'
        ];

        // Add fields based on user type
        switch ($user->user_type) {
            case 'creator':
            case 'patron':
                $baseFields = array_merge($baseFields, [
                    'bio_title',
                    'bio_story',
                    'site_url',
                    'instagram',
                    'facebook',
                    'linkedin'
                ]);
                break;

            case 'enterprise':
                $baseFields = array_merge($baseFields, [
                    'org_name',
                    'org_email',
                    'org_street',
                    'org_city',
                    'org_region',
                    'org_state',
                    'org_zip',
                    'org_site_url',
                    'org_phone_1',
                    'rea',
                    'org_fiscal_code',
                    'org_vat_number'
                ]);
                break;

            case 'epp_entity':
                $baseFields = array_merge($baseFields, [
                    'org_name',
                    'org_email',
                    'org_street',
                    'org_city',
                    'org_region',
                    'org_state',
                    'org_zip',
                    'org_site_url',
                    'org_phone_1',
                    'rea',
                    'org_fiscal_code',
                    'org_vat_number'
                ]);
                break;
            case 'collector':
                $baseFields = array_merge($baseFields, [
                    'site_url',
                    'instagram',
                    'facebook',
                    'linkedin'
                ]);
                break;
            case 'trader_pro':
                $baseFields = array_merge($baseFields, [
                    'site_url',
                    'instagram',
                    'facebook',
                    'linkedin',
                    'org_name',
                    'org_email',
                    'org_street',
                    'org_city',
                    'org_region',
                    'org_state',
                    'org_zip',
                    'org_site_url',
                    'org_phone_1',
                    'rea',
                    'org_fiscal_code',
                    'org_vat_number'
                ]);
                break;

        }

        return $baseFields;
    }

    /**
     * Get on-chain data if available
     *
     * @param \App\Models\User $user
     * @return array|null
     */
    private function getOnChainData(\App\Models\User $user): ?array
    {
        if (empty($user->wallet)) {
            return null;
        }

        try {
            // If you have wallet integration, fetch on-chain data here
            return [
                'wallet_address' => $user->wallet,
                'wallet_balance' => $user->wallet_balance,
                // Add more on-chain data as needed
            ];
        } catch (\Exception $e) {
            $this->logger->warning('[GDPR] Failed to fetch on-chain data', [
                'user_id' => $user->id,
                'wallet' => $user->wallet,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }


    /**
     * Request data rectification for incorrect information
     *
     * @param Request $request
     * @return RedirectResponse
     * @privacy-safe Creates rectification request for authenticated user
     */
    public function requestRectification(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'field_name' => 'required|string|max:255',
                'current_value' => 'required|string|max:1000',
                'requested_value' => 'required|string|max:1000',
                'reason' => 'required|string|max:2000'
            ]);

            $user = Auth::user();

            $this->logger->info('GDPR: Rectification request submitted', [
                'user_id' => $user->id,
                'field_name' => $validated['field_name'],
                'log_category' => 'GDPR_RECTIFICATION_REQUEST'
            ]);

            $request = $this->gdprService->createRectificationRequest($user, $validated);

            $this->auditService->logUserAction($user, 'rectification_requested', [
                'request_id' => $request->id,
                'field_name' => $validated['field_name']
            ]);

            return redirect()->route('gdpr.edit-personal-data')
                ->with('success', __('gdpr.rectification_request_submitted'));

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_RECTIFICATION_REQUEST_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    // ===================================================================
    // PROCESSING LIMITATION (LimitProcessingMenu)
    // ===================================================================

    /**
     * Show the processing limitation request form.
     *
     * @param Request $request The HTTP request
     * @return View The processing limitation form view
     *
     * @oracode-dimension governance
     * @value-flow Enables users to control their data processing
     * @community-impact Empowers users with data control rights
     * @transparency-level High - all current restrictions displayed
     * @narrative-coherence Aligns with user dignity and control values
     */
    public function limitProcessing(Request $request): View
    {
        try {
            $user = Auth::user();
            $activeRestrictions = $this->processingRestrictionService->getUserActiveRestrictions($user);

            return view('gdpr.limit-processing', [
                'user' => $user,
                'activeRestrictions' => $activeRestrictions,
                'restrictionTypes' => ProcessingRestrictionType::cases(),
                'restrictionReasons' => ProcessingRestrictionReason::cases(),
            ]);
        } catch (\Throwable $e) {
            $this->errorManager->handle('GDPR_PROCESSING_LIMIT_VIEW_ERROR', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ], $e);

            return view('gdpr.limit-processing', [
                'user' => Auth::user(),
                'activeRestrictions' => [],
                'restrictionTypes' => ProcessingRestrictionType::cases(),
                'restrictionReasons' => ProcessingRestrictionReason::cases(),
                'error' => true
            ]);
        }
    }

    /**
     * Update processing limitations
     *
     * @param Request $request
     * @return RedirectResponse
     * @privacy-safe Updates limitations for authenticated user only
     */
    public function updateProcessingLimits(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'limitations' => 'required|array',
                'limitations.marketing' => 'boolean',
                'limitations.profiling' => 'boolean',
                'limitations.analytics' => 'boolean',
                'limitations.automated_decisions' => 'boolean',
            ]);

            $user = Auth::user();

            $this->logger->info('GDPR: Updating processing limitations', [
                'user_id' => $user->id,
                'limitations' => $validated['limitations'],
                'log_category' => 'GDPR_PROCESSING_LIMITS_UPDATE'
            ]);

            $result = $this->gdprService->updateProcessingLimitations($user, $validated['limitations']);

            $this->auditService->logUserAction($user, 'processing_limits_updated', [
                'previous_limits' => $result['previous'],
                'new_limits' => $result['current']
            ]);

            return redirect()->route('gdpr.limit-processing')
                ->with('success', __('gdpr.processing_limits_updated_successfully'));

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_PROCESSING_LIMITS_UPDATE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    // ===================================================================
    // ACCOUNT DELETION (DeleteAccountMenu)
    // ===================================================================

    /**
     * Display account deletion page
     *
     * @return View
     * @privacy-safe Shows deletion options for authenticated user
     */
    public function deleteAccount(): View
    {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing account deletion page', [
                'user_id' => $user->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            $deletionInfo = $this->gdprService->getDeletionInfo($user);
            $onChainDataSummary = $this->gdprService->getOnChainDataSummary($user);

            $this->auditService->logUserAction($user, 'delete_account_page_viewed');

            return view('gdpr.delete-account', [
                'user' => $user,
                'deletionInfo' => $deletionInfo,
                'onChainDataSummary' => $onChainDataSummary
            ]);

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_DELETE_ACCOUNT_PAGE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Request account deletion
     *
     * @param Request $request
     * @return RedirectResponse
     * @privacy-safe Creates deletion request for authenticated user
     */
    public function requestAccountDeletion(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'reason' => 'sometimes|string|max:1000',
                'acknowledge_onchain' => 'required|accepted'
            ]);

            $user = Auth::user();

            $this->logger->warning('GDPR: Account deletion requested', [
                'user_id' => $user->id,
                'reason' => $validated['reason'] ?? 'No reason provided',
                'log_category' => 'GDPR_DELETION_REQUEST'
            ]);

            $deletionRequest = $this->gdprService->createDeletionRequest($user, $validated);

            $this->auditService->logUserAction($user, 'deletion_requested', [
                'request_id' => $deletionRequest->id,
                'reason' => $validated['reason'] ?? null
            ]);

            return redirect()->route('gdpr.delete-account')
                ->with('warning', __('gdpr.deletion_request_submitted'));

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_DELETION_REQUEST_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Confirm and execute account deletion
     *
     * @param Request $request
     * @return RedirectResponse
     * @privacy-safe Deletes only authenticated user's account
     */
    public function confirmAccountDeletion(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'password' => 'required|current_password',
                'confirmation' => 'required|in:DELETE',
                'final_confirmation' => 'required|accepted'
            ]);

            $user = Auth::user();
            $userId = $user->id;
            $userEmail = $user->email;

            $this->logger->critical('GDPR: Account deletion confirmed and executing', [
                'user_id' => $userId,
                'user_email' => $userEmail,
                'log_category' => 'GDPR_DELETION_CONFIRMED'
            ]);

            // Execute deletion process
            $deletionResult = $this->gdprService->executeAccountDeletion($user);

            $this->auditService->logUserAction($user, 'account_deleted', [
                'deletion_result' => $deletionResult,
                'final_action' => true
            ]);

            // Logout and invalidate session
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')
                ->with('account_deleted', true)
                ->with('deletion_summary', $deletionResult);

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_ACCOUNT_DELETION_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    // ===================================================================
    // ACTIVITY LOG & AUDIT (ActivityLogMenu)
    // ===================================================================

    /**
     * Display user activity log
     *
     * @return View
     * @privacy-safe Shows only authenticated user's activity log
     */
    public function activityLog(): View
    {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing activity log page', [
                'user_id' => $user->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            $activities = $this->auditService->getUserActivityLog($user, 50);
            $activityStats = $this->auditService->getUserActivityStats($user);

            $this->auditService->logUserAction($user, 'activity_log_viewed');

            return view('gdpr.activity-log', [
                'user' => $user,
                'activities' => $activities,
                'activityStats' => $activityStats
            ]);

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_ACTIVITY_LOG_PAGE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Export user activity log
     *
     * @param Request $request
     * @return StreamedResponse
     * @privacy-safe Exports only authenticated user's activity log
     */
    public function exportActivityLog(Request $request): StreamedResponse
    {
        try {
            $validated = $request->validate([
                'format' => 'sometimes|in:csv,json',
                'date_from' => 'sometimes|date',
                'date_to' => 'sometimes|date|after_or_equal:date_from'
            ]);

            $user = Auth::user();

            $this->logger->info('GDPR: Exporting activity log', [
                'user_id' => $user->id,
                'format' => $validated['format'] ?? 'csv',
                'log_category' => 'GDPR_ACTIVITY_EXPORT'
            ]);

            $this->auditService->logUserAction($user, 'activity_log_exported', [
                'format' => $validated['format'] ?? 'csv',
                'date_range' => [
                    'from' => $validated['date_from'] ?? null,
                    'to' => $validated['date_to'] ?? null
                ]
            ]);

            return $this->auditService->exportUserActivityLog($user, $validated);

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_ACTIVITY_LOG_EXPORT_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    // ===================================================================
    // BREACH REPORTING (BreachReportMenu)
    // ===================================================================

    /**
     * Display breach reporting page
     *
     * @return View
     * @privacy-safe Shows breach reporting form and user's reports
     */
    public function breachReport(): View
    {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing breach report page', [
                'user_id' => $user->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            $userReports = $this->gdprService->getUserBreachReports($user);
            $reportCategories = $this->gdprService->getBreachReportCategories();

            $this->auditService->logUserAction($user, 'breach_report_page_viewed');

            return view('gdpr.breach-report', [
                'user' => $user,
                'userReports' => $userReports,
                'reportCategories' => $reportCategories
            ]);

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_BREACH_REPORT_PAGE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Submit breach report
     *
     * @param Request $request
     * @return RedirectResponse
     * @privacy-safe Creates breach report for authenticated user
     */
    public function submitBreachReport(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'category' => 'required|string|in:data_leak,unauthorized_access,system_breach,phishing,other',
                'description' => 'required|string|min:10|max:2000',
                'incident_date' => 'sometimes|date|before_or_equal:today',
                'affected_data' => 'sometimes|array',
                'severity' => 'required|in:low,medium,high,critical'
            ]);

            $user = Auth::user();

            $this->logger->warning('GDPR: Breach report submitted', [
                'user_id' => $user->id,
                'category' => $validated['category'],
                'severity' => $validated['severity'],
                'log_category' => 'GDPR_BREACH_REPORT'
            ]);

            $report = $this->gdprService->createBreachReport($user, $validated);

            $this->auditService->logUserAction($user, 'breach_report_submitted', [
                'report_id' => $report->id,
                'category' => $validated['category'],
                'severity' => $validated['severity']
            ]);

            return redirect()->route('gdpr.breach-report')
                ->with('success', __('gdpr.breach_report_submitted_successfully'));

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_BREACH_REPORT_SUBMISSION_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * View breach report status
     *
     * @param BreachReport $report
     * @return View
     * @privacy-safe Shows report status only if user owns the report
     */
    public function breachReportStatus(BreachReport $report): View
    {
        try {
            $user = Auth::user();

            if ($report->user_id !== $user->id) {
                return $this->errorManager->handle('GDPR_BREACH_REPORT_ACCESS_DENIED', [
                    'user_id' => $user->id,
                    'report_id' => $report->id
                ]);
            }

            $this->logger->info('GDPR: Viewing breach report status', [
                'user_id' => $user->id,
                'report_id' => $report->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            $this->auditService->logUserAction($user, 'breach_report_status_viewed', [
                'report_id' => $report->id
            ]);

            return view('gdpr.breach-report-status', [
                'user' => $user,
                'report' => $report
            ]);

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_BREACH_REPORT_STATUS_FAILED', [
                'user_id' => Auth::id(),
                'report_id' => $report->id ?? null,
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    // ===================================================================
    // PRIVACY POLICY & TRANSPARENCY (PrivacyPolicyMenu)
    // ===================================================================

    /**
     * Display privacy policy page
     *
     * @return View
     * @privacy-safe Public information display
     */
    public function privacyPolicy(): View
    {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing privacy policy page', [
                'user_id' => $user?->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            $policyData = $this->gdprService->getCurrentPrivacyPolicy();
            $userConsents = $user ? $this->consentService->getUserConsentStatus($user) : null;

            if ($user) {
                $this->auditService->logUserAction($user, 'privacy_policy_viewed');
            }

            return view('gdpr.privacy-policy', [
                'user' => $user,
                'policyData' => $policyData,
                'userConsents' => $userConsents
            ]);

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_PRIVACY_POLICY_PAGE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Display privacy policy changelog
     *
     * @return View
     * @privacy-safe Shows public policy version history
     */
    public function privacyPolicyChangelog(): View
    {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing privacy policy changelog', [
                'user_id' => $user?->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            $policyVersions = $this->gdprService->getPrivacyPolicyVersions();

            if ($user) {
                $this->auditService->logUserAction($user, 'privacy_policy_changelog_viewed');
            }

            return view('gdpr.privacy-policy-changelog', [
                'user' => $user,
                'policyVersions' => $policyVersions
            ]);

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_PRIVACY_POLICY_CHANGELOG_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Display data processing information
     *
     * @return View
     * @privacy-safe Shows transparency information about data processing
     */
    public function dataProcessingInfo(): View
    {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing data processing info page', [
                'user_id' => $user?->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            $processingInfo = $this->gdprService->getDataProcessingInformation();
            $thirdPartyServices = $this->gdprService->getThirdPartyServices();

            if ($user) {
                $this->auditService->logUserAction($user, 'data_processing_info_viewed');
            }

            return view('gdpr.data-processing-info', [
                'user' => $user,
                'processingInfo' => $processingInfo,
                'thirdPartyServices' => $thirdPartyServices
            ]);

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_DATA_PROCESSING_INFO_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    // ===================================================================
    // DPO CONTACT & SUPPORT
    // ===================================================================

    /**
     * Display DPO contact page
     *
     * @return View
     * @privacy-safe Shows DPO contact information and form
     */
    public function contactDpo(): View
    {
        try {
            $user = Auth::user();

            $this->logger->info('GDPR: Accessing DPO contact page', [
                'user_id' => $user->id,
                'log_category' => 'GDPR_ACCESS'
            ]);

            $dpoInfo = $this->gdprService->getDpoContactInformation();
            $userMessages = $this->gdprService->getUserDpoMessages($user);

            $this->auditService->logUserAction($user, 'dpo_contact_page_viewed');

            return view('gdpr.contact-dpo', [
                'user' => $user,
                'dpoInfo' => $dpoInfo,
                'userMessages' => $userMessages
            ]);

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_DPO_CONTACT_PAGE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Send message to DPO
     *
     * @param Request $request
     * @return RedirectResponse
     * @privacy-safe Creates message from authenticated user to DPO
     */
    public function sendDpoMessage(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'subject' => 'required|string|max:255',
                'message' => 'required|string|min:10|max:2000',
                'priority' => 'required|in:low,normal,high,urgent',
                'request_type' => 'required|in:information,complaint,access_request,other'
            ]);

            $user = Auth::user();

            $this->logger->info('GDPR: Sending message to DPO', [
                'user_id' => $user->id,
                'subject' => $validated['subject'],
                'priority' => $validated['priority'],
                'log_category' => 'GDPR_DPO_MESSAGE'
            ]);

            $message = $this->gdprService->sendMessageToDpo($user, $validated);

            $this->auditService->logUserAction($user, 'dpo_message_sent', [
                'message_id' => $message->id,
                'subject' => $validated['subject'],
                'priority' => $validated['priority']
            ]);

            return redirect()->route('gdpr.contact-dpo')
                ->with('success', __('gdpr.dpo_message_sent_successfully'));

        } catch (\Exception $e) {
            return $this->errorManager->handle('GDPR_DPO_MESSAGE_FAILED', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage()
            ], $e);
        }
    }

    // ===================================================================
    // API METHODS (for dynamic frontend)
    // ===================================================================

    /**
    * Get current consent status (API)
    *
    * @return JsonResponse
    * @privacy-safe Returns authenticated user's consent status
    */
   public function getConsentStatus(): JsonResponse
   {
       try {
           $user = Auth::user();
           $consentStatus = $this->consentService->getUserConsentStatus($user);

           $this->logger->debug('GDPR API: Consent status requested', [
               'user_id' => $user->id,
               'log_category' => 'GDPR_API_ACCESS'
           ]);

           return response()->json([
               'success' => true,
               'data' => $consentStatus,
               'last_updated' => $consentStatus['last_updated'] ?? null
           ]);

       } catch (\Exception $e) {
           return $this->errorManager->handle('GDPR_API_CONSENT_STATUS_FAILED', [
               'user_id' => Auth::id(),
               'error_message' => $e->getMessage()
           ], $e);
       }
   }

   /**
    * Get current processing limitations (API)
    *
    * @return JsonResponse
    * @privacy-safe Returns authenticated user's processing limits
    */
   public function getProcessingLimits(): JsonResponse
   {
       try {
           $user = Auth::user();
           $processingLimits = $this->gdprService->getUserProcessingLimitations($user);

           $this->logger->debug('GDPR API: Processing limits requested', [
               'user_id' => $user->id,
               'log_category' => 'GDPR_API_ACCESS'
           ]);

           return response()->json([
               'success' => true,
               'data' => $processingLimits,
               'effective_date' => $processingLimits['effective_date'] ?? null
           ]);

       } catch (\Exception $e) {
           return $this->errorManager->handle('GDPR_API_PROCESSING_LIMITS_FAILED', [
               'user_id' => Auth::id(),
               'error_message' => $e->getMessage()
           ], $e);
       }
   }

   /**
    * Get export status by token (API)
    *
    * @param string $token
    * @return JsonResponse
    * @privacy-safe Returns export status only if token belongs to authenticated user
    */
   public function getExportStatus(string $token): JsonResponse
   {
       try {
           $user = Auth::user();
           $export = $this->exportService->getExportByToken($token, $user);

           if (!$export || $export->user_id !== $user->id) {
               return response()->json([
                   'success' => false,
                   'error' => 'Export not found or access denied'
               ], 404);
           }

           $this->logger->debug('GDPR API: Export status requested', [
               'user_id' => $user->id,
               'export_token' => $token,
               'log_category' => 'GDPR_API_ACCESS'
           ]);

           return response()->json([
               'success' => true,
               'data' => [
                   'status' => $export->status,
                   'progress' => $export->progress,
                   'created_at' => $export->created_at,
                   'completed_at' => $export->completed_at,
                   'download_url' => $export->status === 'completed' ?
                       route('gdpr.export-data.download', $token) : null,
                   'file_size' => $export->file_size,
                   'format' => $export->format
               ]
           ]);

       } catch (\Exception $e) {
           return $this->errorManager->handle('GDPR_API_EXPORT_STATUS_FAILED', [
               'user_id' => Auth::id(),
               'token' => $token,
               'error_message' => $e->getMessage()
           ], $e);
       }
   }

   // ===================================================================
   // LEGACY METHODS (for backward compatibility)
   // ===================================================================

   /**
    * Legacy consent display method
    *
    * @return View
    * @deprecated Use consent() method instead
    * @privacy-safe Shows user's own consent status only
    */
   public function consents(): View
   {
       $this->logger->warning('GDPR: Legacy consents() method called', [
           'user_id' => Auth::id(),
           'log_category' => 'GDPR_LEGACY_ACCESS'
       ]);

       return $this->consent();
   }

   /**
    * Legacy consent update method
    *
    * @param Request $request
    * @return RedirectResponse
    * @deprecated Use updateConsent() method instead
    * @privacy-safe Updates only authenticated user's consents
    */
   public function updateConsents(Request $request): RedirectResponse
   {
       $this->logger->warning('GDPR: Legacy updateConsents() method called', [
           'user_id' => Auth::id(),
           'log_category' => 'GDPR_LEGACY_ACCESS'
       ]);

       return $this->updateConsent($request);
   }

   /**
    * Legacy data download method
    *
    * @param Request $request
    * @return StreamedResponse
    * @deprecated Use generateExport() and downloadExport() methods instead
    * @privacy-safe Downloads only authenticated user's data
    */
   public function downloadData(Request $request): StreamedResponse
   {
       try {
           $user = Auth::user();

           $this->logger->warning('GDPR: Legacy downloadData() method called', [
               'user_id' => $user->id,
               'log_category' => 'GDPR_LEGACY_ACCESS'
           ]);

           // Generate immediate export for backward compatibility
           $exportToken = $this->exportService->generateUserDataExport(
               $user,
               'json',
               ['profile', 'activities', 'consents']
           );

           $export = $this->exportService->getExportByToken($exportToken, $user);

           $this->auditService->logUserAction($user, 'legacy_data_downloaded', [
               'export_token' => $exportToken
           ]);

           return $this->exportService->streamExportFile($export);

       } catch (\Exception $e) {
           return $this->errorManager->handle('GDPR_LEGACY_DATA_DOWNLOAD_FAILED', [
               'user_id' => Auth::id(),
               'error_message' => $e->getMessage()
           ], $e);
       }
   }

   /**
    * Legacy account destruction method
    *
    * @param Request $request
    * @return RedirectResponse
    * @deprecated Use confirmAccountDeletion() method instead
    * @privacy-safe Deletes only authenticated user's account
    */
   public function destroyAccount(Request $request): RedirectResponse
   {
       $this->logger->warning('GDPR: Legacy destroyAccount() method called', [
           'user_id' => Auth::id(),
           'log_category' => 'GDPR_LEGACY_ACCESS'
       ]);

       return $this->confirmAccountDeletion($request);
   }
}
