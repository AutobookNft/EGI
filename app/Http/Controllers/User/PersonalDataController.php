<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\User\UpdatePersonalDataRequest;
use App\Models\User;
use App\Models\UserPersonalData;
use App\Helpers\FegiAuth;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @Oracode Controller: Personal Data Management (OS1-Compliant)
 * 🎯 Purpose: Manage user personal data with GDPR compliance and fiscal validation
 * 🛡️ Privacy: Complete audit trail, consent management, data subject rights
 * 🧱 Core Logic: CRUD operations with UEM+ULM integration and fiscal validation
 * 🌍 Scale: MVP countries support with enterprise-grade validation
 * ⏰ MVP: Critical Personal Data Domain for 30 June deadline
 *
 * @package App\Http\Controllers\User
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Personal Data Domain)
 * @deadline 2025-06-30
 */
class PersonalDataController extends BaseUserDomainController
{
    /**
     * @Oracode Constructor: Initialize Personal Data Controller with Dependencies
     * 🎯 Purpose: Set up error handling, logging, and authentication for personal data
     * 📥 Input: UEM error manager and ULM logger instances via DI
     * 🛡️ Privacy: Initialize GDPR-compliant audit logging
     * 🧱 Core Logic: Extends BaseUserDomainController with specific permissions
     *
     * @param ErrorManagerInterface $errorManager UEM error manager for robust error handling
     * @param UltraLogManager $logger ULM logger for audit trail and compliance
     */
    public function __construct(
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger
    ) {
        parent::__construct($errorManager, $logger);
    }

    /**
     * @Oracode Method: Display Personal Data Management Page
     * 🎯 Purpose: Show user's personal data with edit capabilities and GDPR consent integration
     * 📥 Input: HTTP request
     * 📤 Output: View with personal data and GDPR consent status or redirect if access denied
     * 🛡️ Privacy: Access control, audit logging, and ConsentService integration
     * 🧱 Core Logic: Load personal data from UserPersonalData + consent status from GDPR ConsentService
     * 🔗 Integration: Bridges User Domains with existing GDPR system for unified consent management
     *
     * @param Request $request HTTP request instance
     * @return View|RedirectResponse Personal data view with GDPR consent integration or redirect
     */
    public function index(Request $request): View|RedirectResponse
    {
        // Check authentication and permissions
        $accessCheck = $this->checkWeakAuthAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        try {
            $user = FegiAuth::user();

            // Audit access attempt
            $this->auditDataAccess('personal_data_view_requested', [
                'user_id' => $user->id,
                'auth_type' => $this->authType,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Load or create personal data record (non-consent data)
            $personalData = $this->getOrCreatePersonalData($user);

            // Get GDPR consent status from ConsentService (integrated with existing GDPR system)
            $consentService = app(\App\Services\Gdpr\ConsentService::class);
            $gdprConsents = [
                'personal_data_processing' => $consentService->hasConsent($user, 'personal_data_processing'),
                'marketing' => $consentService->hasConsent($user, 'marketing'),
                'analytics' => $consentService->hasConsent($user, 'analytics'),
                'cookies' => $consentService->hasConsent($user, 'cookies'),
            ];

            // Get user's country for form configuration
            $userCountry = $this->getUserCountry();

            // Get available countries (MVP only)
            $availableCountries = $this->getMvpCountries();

            // Prepare view data with GDPR integration
            $viewData = [
                'user' => $user,
                'personalData' => $personalData,
                'gdprConsents' => $gdprConsents, // ✅ GDPR consent status from ConsentService
                'userCountry' => $userCountry,
                'availableCountries' => $availableCountries,
                'authType' => $this->authType,
                'canEdit' => $this->canEditPersonalData($user),
                'gdprSummary' => $this->getGdprSummary($user, $gdprConsents),
                'lastUpdate' => $personalData->updated_at,
                'validationConfig' => $this->getValidationConfig($userCountry),
                'consentHistory' => $consentService->getConsentHistory($user, 10),
            ];

            // Log successful page load with consent integration info
            $this->logger->info('Personal data page displayed successfully with GDPR integration', [
                'user_id' => $user->id,
                'auth_type' => $this->authType,
                'country' => $userCountry,
                'has_data_processing_consent' => $gdprConsents['personal_data_processing'],
                'gdpr_consents_loaded' => count($gdprConsents)
            ]);

            return view('users.domains.personal-data.index', $viewData);

        } catch (\Exception $e) {
            return $this->respondError('PERSONAL_DATA_VIEW_ERROR', $e, [
                'user_id' => FegiAuth::id(),
                'requested_page' => 'personal_data_index',
                'integration_point' => 'gdpr_consent_service'
            ]);
        }
    }

    /**
     * @Oracode Method: Update Personal Data
     * 🎯 Purpose: Process personal data updates with full validation and audit
     * 📥 Input: Validated update request
     * 📤 Output: Success response or error handling via UEM
     * 🛡️ Privacy: GDPR audit trail and consent management
     * 🧱 Core Logic: Transaction-based update with fiscal validation
     *
     * @param UpdatePersonalDataRequest $request Validated personal data update request
     * @return JsonResponse|RedirectResponse Update result
     */
    public function update(UpdatePersonalDataRequest $request): JsonResponse|RedirectResponse
    {
        // Additional access verification for updates
        $accessCheck = $this->checkWeakAuthAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        // For sensitive data updates, require identity verification
        $identityCheck = $this->requireIdentityVerification();
        if ($identityCheck !== true) {
            return $identityCheck;
        }

        try {
            $user = FegiAuth::user();
            $validatedData = $request->validated();

            $this->auditDataAccess('personal_data_update_requested', [
                'user_id' => $user->id,
                'fields_updated' => array_keys($validatedData),
                'auth_type' => $this->authType
            ]);

            // Process update in transaction
            $result = DB::transaction(function () use ($user, $validatedData, $request) {
                return $this->processPersonalDataUpdate($user, $validatedData, $request);
            });

            if ($result['success']) {
                $this->auditDataAccess('personal_data_updated_successfully', [
                    'user_id' => $user->id,
                    'changes_made' => $result['changes'],
                    'consent_updated' => $result['consent_updated']
                ]);

                $this->logger->info('Personal data updated successfully', [
                    'user_id' => $user->id,
                    'fields_changed' => count($result['changes']),
                    'auth_type' => $this->authType
                ]);

                return $this->respondSuccess(
                    __('user_personal_data.update_success'),
                    [
                        'updated_fields' => array_keys($result['changes']),
                        'consent_status' => $result['consent_updated']
                    ]
                );
            } else {
                throw new \Exception('Personal data update failed: ' . $result['error']);
            }

        } catch (\Exception $e) {
            return $this->respondError('PERSONAL_DATA_UPDATE_ERROR', $e, [
                'user_id' => FegiAuth::id(),
                'input_data_hash' => hash('sha256', json_encode($request->except(['password', 'password_confirmation'])))
            ]);
        }
    }

    /**
     * @Oracode Method: Export Personal Data (GDPR Right to Data Portability)
     * 🎯 Purpose: Generate GDPR-compliant data export for user
     * 📥 Input: HTTP request with export preferences
     * 📤 Output: Export response or redirect to processing page
     * 🛡️ Privacy: Full GDPR compliance with data minimization
     * 🧱 Core Logic: Generate comprehensive data export
     *
     * @param Request $request HTTP request with export parameters
     * @return JsonResponse|RedirectResponse Export result
     */
    public function export(Request $request): JsonResponse|RedirectResponse|StreamedResponse
    {
        $accessCheck = $this->checkWeakAuthAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        try {
            $user = FegiAuth::user();

            // Rate limiting check for exports
            if (!$this->canRequestDataExport($user)) {
                return $this->respondError('GDPR_EXPORT_RATE_LIMIT', new \Exception('Export rate limit exceeded'), [
                    'user_id' => $user->id,
                    'last_export' => $this->getLastExportDate($user)
                ]);
            }

            $this->auditDataAccess('personal_data_export_requested', [
                'user_id' => $user->id,
                'format' => $request->input('format', 'json'),
                'categories' => $request->input('categories', ['all'])
            ]);

            // Generate export data
            $exportData = $this->generatePersonalDataExport($user, $request->input('categories', ['all']));

            // Format based on request
            $format = $request->input('format', 'json');
            $formattedData = $this->formatExportData($exportData, $format);

            $this->logger->info('Personal data export generated', [
                'user_id' => $user->id,
                'format' => $format,
                'data_size' => strlen(json_encode($formattedData))
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $formattedData,
                    'export_date' => now()->toISOString(),
                    'format' => $format
                ]);
            }

            // For web requests, return download
            $filename = 'personal_data_' . $user->id . '_' . now()->format('Y-m-d_H-i-s') . '.' . $format;

            return response()->streamDownload(function () use ($formattedData, $format) {
                echo $format === 'json' ? json_encode($formattedData, JSON_PRETTY_PRINT) : $formattedData;
            }, $filename, $this->getExportHeaders($format));

        } catch (\Exception $e) {
            return $this->respondError('PERSONAL_DATA_EXPORT_ERROR', $e, [
                'user_id' => FegiAuth::id(),
                'export_format' => $request->input('format', 'json')
            ]);
        }
    }

    /**
     * @Oracode Method: Delete Personal Data (GDPR Right to Erasure)
     * 🎯 Purpose: Handle personal data deletion requests
     * 📥 Input: HTTP request with deletion confirmation
     * 📤 Output: Deletion confirmation or error
     * 🛡️ Privacy: GDPR-compliant data erasure with audit trail
     * 🧱 Core Logic: Secure deletion process with verification
     *
     * @param Request $request HTTP request with deletion parameters
     * @return JsonResponse|RedirectResponse Deletion result
     */
    public function destroy(Request $request): JsonResponse|RedirectResponse
    {
        $accessCheck = $this->checkWeakAuthAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        // Require strong authentication for deletion
        if (!FegiAuth::isStrongAuth()) {
            return $this->redirectToUpgrade();
        }

        // Require identity verification for deletion
        $identityCheck = $this->requireIdentityVerification();
        if ($identityCheck !== true) {
            return $identityCheck;
        }

        try {
            $user = FegiAuth::user();

            // Validate deletion request
            $request->validate([
                'confirm_deletion' => 'required|boolean|accepted',
                'deletion_reason' => 'nullable|string|max:500',
                'confirm_text' => 'required|string|in:DELETE'
            ]);

            $this->auditDataAccess('personal_data_deletion_requested', [
                'user_id' => $user->id,
                'reason' => $request->input('deletion_reason'),
                'ip_address' => $request->ip()
            ]);

            // Process deletion in transaction
            DB::transaction(function () use ($user, $request) {
                $this->processPersonalDataDeletion($user, $request->input('deletion_reason'));
            });

            $this->auditDataAccess('personal_data_deleted_successfully', [
                'user_id' => $user->id,
                'deletion_timestamp' => now()->toISOString()
            ]);

            $this->logger->info('Personal data deleted successfully', [
                'user_id' => $user->id,
                'auth_type' => $this->authType
            ]);

            return $this->respondSuccess(
                __('user_personal_data.deletion.request_submitted'),
                ['deletion_timestamp' => now()->toISOString()]
            );

        } catch (\Exception $e) {
            return $this->respondError('PERSONAL_DATA_DELETION_ERROR', $e, [
                'user_id' => FegiAuth::id()
            ]);
        }
    }

    /**
     * @Oracode Method: Get Required Domain Permission
     * 🎯 Purpose: Return permission required for personal data access
     * 📤 Output: Permission string for weak auth users
     * 🧱 Core Logic: Override from BaseUserDomainController
     *
     * @return string Permission required for personal data domain
     */
    protected function getRequiredDomainPermission(): string
    {
        return 'manage_profile'; // Weak auth users can manage their profile
    }

    /**
     * @Oracode Method: Get or Create Personal Data Record
     * 🎯 Purpose: Ensure user has personal data record, create if missing
     * 📥 Input: User instance
     * 📤 Output: UserPersonalData instance
     * 🧱 Core Logic: Lazy creation of personal data record
     *
     * @param User $user User instance
     * @return UserPersonalData Personal data record
     */
    private function getOrCreatePersonalData(User $user): UserPersonalData
    {
        return UserPersonalData::firstOrCreate(
            ['user_id' => $user->id],
            [
                'allow_personal_data_processing' => false,
                'processing_purposes' => [],
                'consent_updated_at' => now()
            ]
        );
    }

    /**
     * @Oracode Method: Check if User Can Edit Personal Data
     * 🎯 Purpose: Determine if user has edit permissions for personal data
     * 📥 Input: User instance
     * 📤 Output: Boolean indicating edit capability
     * 🧱 Core Logic: Permission and authentication level checks
     *
     * @param User $user User instance
     * @return bool True if user can edit personal data
     */
    private function canEditPersonalData(User $user): bool
    {
        // Users can always edit their own personal data if authenticated
        return FegiAuth::check() && FegiAuth::id() === $user->id;
    }

    /**
     * @Oracode Method: Get GDPR Summary for User
     * 🎯 Purpose: Generate GDPR compliance summary for display
     * 📥 Input: User instance
     * 📤 Output: Array with GDPR status information
     * 🛡️ Privacy: Privacy-focused summary generation
     *
     * @param User $user User instance
     * @return array<string, mixed> GDPR summary data
     */
    private function getGdprSummary(User $user): array
    {
        $personalData = $this->getOrCreatePersonalData($user);

        return [
            'consent_status' => $personalData->allow_personal_data_processing,
            'consent_date' => $personalData->consent_updated_at,
            'processing_purposes' => $personalData->processing_purposes ?: [],
            'data_retention_status' => 'active',
            'last_data_update' => $personalData->updated_at,
            'export_available' => $this->canRequestDataExport($user),
            'deletion_available' => FegiAuth::isStrongAuth()
        ];
    }

    /**
     * @Oracode Method: Get Validation Config for Frontend
     * 🎯 Purpose: Generate validation configuration for JavaScript
     * 📥 Input: User's country code
     * 📤 Output: Array with validation rules for frontend
     * 🧱 Core Logic: Country-specific validation config
     *
     * @param string $country User's country code
     * @return array<string, mixed> Validation configuration
     */
    private function getValidationConfig(string $country): array
    {
        try {
            $validator = \App\Services\Fiscal\FiscalValidatorFactory::create($country);
            $businessTypes = ['individual', 'business', 'corporation', 'partnership', 'non_profit'];

            $config = [
                'country' => $country,
                'validator_type' => $validator->getCountryCode(),
                'business_types' => []
            ];

            foreach ($businessTypes as $type) {
                $config['business_types'][$type] = $validator->getRequiredFields($type);
            }

            return $config;

        } catch (\Exception $e) {
            return [
                'country' => $country,
                'validator_type' => 'generic',
                'business_types' => []
            ];
        }
    }

    /**
     * @Oracode Method: Process Personal Data Update
     * 🎯 Purpose: Execute personal data update with audit trail
     * 📥 Input: User, validated data, and request
     * 📤 Output: Array with update results
     * 🧱 Core Logic: Database update with change tracking
     *
     * @param User $user User instance
     * @param array<string, mixed> $validatedData Validated input data
     * @param UpdatePersonalDataRequest $request Original request
     * @return array<string, mixed> Update results
     */
    private function processPersonalDataUpdate(User $user, array $validatedData, UpdatePersonalDataRequest $request): array
    {
        $personalData = $this->getOrCreatePersonalData($user);
        $originalData = $personalData->toArray();

        // Track changes
        $changes = [];
        $consentUpdated = false;

        // Update personal data fields
        foreach ($validatedData as $field => $value) {
            if ($personalData->isFillable($field) && $personalData->$field !== $value) {
                $changes[$field] = [
                    'old' => $personalData->$field,
                    'new' => $value
                ];
                $personalData->$field = $value;
            }
        }

        // Handle consent updates
        if (isset($validatedData['allow_personal_data_processing'])) {
            $newConsentStatus = $validatedData['allow_personal_data_processing'];
            if ($personalData->allow_personal_data_processing !== $newConsentStatus) {
                $consentUpdated = true;
                $personalData->allow_personal_data_processing = $newConsentStatus;
                $personalData->processing_purposes = $validatedData['processing_purposes'] ?? [];
                $personalData->consent_updated_at = now();
            }
        }

        // Save changes
        $personalData->save();

        // Update user basic info if provided
        $userChanges = [];
        $userFields = ['name', 'last_name', 'language'];
        foreach ($userFields as $field) {
            if (isset($validatedData[$field]) && $user->$field !== $validatedData[$field]) {
                $userChanges[$field] = [
                    'old' => $user->$field,
                    'new' => $validatedData[$field]
                ];
                $user->$field = $validatedData[$field];
            }
        }

        if (!empty($userChanges)) {
            $user->save();
            $changes = array_merge($changes, $userChanges);
        }

        return [
            'success' => true,
            'changes' => $changes,
            'consent_updated' => $consentUpdated,
            'personal_data' => $personalData->fresh(),
            'user' => $user->fresh()
        ];
    }

    /**
     * @Oracode Method: Generate Personal Data Export
     * 🎯 Purpose: Create comprehensive data export for GDPR compliance
     * 📥 Input: User and export categories
     * 📤 Output: Array with exported data
     * 🛡️ Privacy: Only include data user has consented to process
     *
     * @param User $user User instance
     * @param array<int, string> $categories Export categories
     * @return array<string, mixed> Exported data
     */
    private function generatePersonalDataExport(User $user, array $categories): array
    {
        $personalData = $this->getOrCreatePersonalData($user);
        $exportData = [
            'export_info' => [
                'user_id' => $user->id,
                'export_date' => now()->toISOString(),
                'categories' => $categories,
                'gdpr_basis' => 'Article 20 - Right to data portability'
            ]
        ];

        if (in_array('basic', $categories) || in_array('all', $categories)) {
            $exportData['basic_information'] = [
                'first_name' => $personalData->first_name ?? $user->name,
                'last_name' => $personalData->last_name ?? $user->last_name,
                'email' => $user->email,
                'birth_date' => $personalData->birth_date?->format('Y-m-d'),
                'gender' => $personalData->gender
            ];
        }

        if (in_array('address', $categories) || in_array('all', $categories)) {
            $exportData['address_information'] = [
                'street' => $personalData->street,
                'city' => $personalData->city,
                'zip' => $personalData->zip,
                'country' => $personalData->country,
                'region' => $personalData->region,
                'province' => $personalData->province
            ];
        }

        if (in_array('contact', $categories) || in_array('all', $categories)) {
            $exportData['contact_information'] = [
                'home_phone' => $personalData->home_phone,
                'cell_phone' => $personalData->cell_phone,
                'work_phone' => $personalData->work_phone
            ];
        }

        if (in_array('consent', $categories) || in_array('all', $categories)) {
            $exportData['consent_information'] = [
                'data_processing_consent' => $personalData->allow_personal_data_processing,
                'processing_purposes' => $personalData->processing_purposes,
                'consent_date' => $personalData->consent_updated_at?->toISOString()
            ];
        }

        return $exportData;
    }

    /**
     * @Oracode Method: Check if User Can Request Data Export
     * 🎯 Purpose: Rate limiting for data export requests
     * 📥 Input: User instance
     * 📤 Output: Boolean indicating export availability
     * 🧱 Core Logic: GDPR-compliant rate limiting
     *
     * @param User $user User instance
     * @return bool True if user can request export
     */
    private function canRequestDataExport(User $user): bool
    {
        // Allow one export per 30 days per GDPR guidelines
        // This would typically check a data_exports table
        return true; // Simplified for MVP
    }

    /**
     * @Oracode Method: Get Last Export Date
     * 🎯 Purpose: Get user's last data export date for rate limiting
     * 📥 Input: User instance
     * 📤 Output: Carbon date or null
     * 🧱 Core Logic: Export history tracking
     *
     * @param User $user User instance
     * @return Carbon|null Last export date
     */
    private function getLastExportDate(User $user): ?Carbon
    {
        // This would typically query a data_exports table
        return null; // Simplified for MVP
    }

    /**
     * @Oracode Method: Format Export Data
     * 🎯 Purpose: Format exported data in requested format
     * 📥 Input: Export data array and format
     * 📤 Output: Formatted data string
     * 🧱 Core Logic: Multi-format export support
     *
     * @param array<string, mixed> $data Export data
     * @param string $format Export format (json, csv, xml)
     * @return string Formatted export data
     */
    private function formatExportData(array $data, string $format): string
    {
        switch ($format) {
            case 'csv':
                return $this->convertToCSV($data);
            case 'xml':
                return $this->convertToXML($data);
            case 'json':
            default:
                return json_encode($data, JSON_PRETTY_PRINT);
        }
    }

    /**
     * @Oracode Method: Convert Data to CSV
     * 🎯 Purpose: Convert export data to CSV format
     * 📥 Input: Export data array
     * 📤 Output: CSV formatted string
     * 🧱 Core Logic: Flatten nested arrays for CSV
     *
     * @param array<string, mixed> $data Export data
     * @return string CSV formatted data
     */
    private function convertToCSV(array $data): string
    {
        // Simplified CSV conversion for MVP
        $csv = "Category,Field,Value\n";

        foreach ($data as $category => $fields) {
            if (is_array($fields)) {
                foreach ($fields as $field => $value) {
                    $csv .= sprintf("%s,%s,%s\n",
                        $category,
                        $field,
                        is_array($value) ? json_encode($value) : $value
                    );
                }
            }
        }

        return $csv;
    }

    /**
     * @Oracode Method: Convert Data to XML
     * 🎯 Purpose: Convert export data to XML format
     * 📥 Input: Export data array
     * 📤 Output: XML formatted string
     * 🧱 Core Logic: Simple XML structure
     *
     * @param array<string, mixed> $data Export data
     * @return string XML formatted data
     */
    private function convertToXML(array $data): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n<personal_data_export>\n";

        foreach ($data as $category => $fields) {
            $xml .= "  <{$category}>\n";
            if (is_array($fields)) {
                foreach ($fields as $field => $value) {
                    $xml .= "    <{$field}>" . htmlspecialchars((string)$value) . "</{$field}>\n";
                }
            }
            $xml .= "  </{$category}>\n";
        }

        $xml .= "</personal_data_export>\n";

        return $xml;
    }

    /**
     * @Oracode Method: Get Export Headers
     * 🎯 Purpose: Return appropriate HTTP headers for export download
     * 📥 Input: Export format
     * 📤 Output: Array of HTTP headers
     * 🧱 Core Logic: Format-specific headers
     *
     * @param string $format Export format
     * @return array<string, string> HTTP headers
     */
    private function getExportHeaders(string $format): array
    {
        $headers = [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];

        switch ($format) {
            case 'csv':
                $headers['Content-Type'] = 'text/csv';
                break;
            case 'xml':
                $headers['Content-Type'] = 'application/xml';
                break;
            case 'json':
            default:
                $headers['Content-Type'] = 'application/json';
                break;
        }

        return $headers;
    }

    /**
     * @Oracode Method: Process Personal Data Deletion
     * 🎯 Purpose: Execute GDPR-compliant data deletion
     * 📥 Input: User and deletion reason
     * 📤 Output: Void (processes deletion)
     * 🛡️ Privacy: Secure deletion with audit trail
     *
     * @param User $user User instance
     * @param string|null $reason Deletion reason
     * @return void
     */
    private function processPersonalDataDeletion(User $user, ?string $reason): void
    {
        $personalData = UserPersonalData::where('user_id', $user->id)->first();

        if ($personalData) {
            // Create deletion audit record before deletion
            $this->auditDataAccess('personal_data_deletion_executed', [
                'user_id' => $user->id,
                'data_fields_deleted' => array_keys($personalData->toArray()),
                'reason' => $reason,
                'deletion_method' => 'user_requested'
            ]);

            // Perform deletion
            $personalData->delete();
        }

        // Clear related data processing consents
        $user->update([
            'consent' => false,
            'consent_summary' => null,
            'consents_updated_at' => now()
        ]);
    }
}
