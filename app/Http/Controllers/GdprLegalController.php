<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Services\Gdpr\LegalContentService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use App\Services\Gdpr\ConsentService;
use App\Services\Gdpr\AuditLogService;

/**
 * @Oracode Controller: Legal Document Management
 * 🎯 Purpose: Dedicated controller for legal terms and document versioning
 * 🛡️ Security: Permission-based access with content validation and audit trail
 * 🧱 Core Logic: File-based content management with database consent integration
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0 - OS2.0 Implementation
 * @date 2025-06-22
 */
class GdprLegalController extends Controller
{
    /**
     * Logger instance for audit trail
     * @var UltraLogManager
     */
    protected UltraLogManager $logger;

    /**
     * Error manager for robust error handling
     * @var ErrorManagerInterface
     */
    protected ErrorManagerInterface $errorManager;

    /**
     * Consent service for integration with existing GDPR system
     * @var ConsentService
     */
    protected ConsentService $consentService;

    /**
     * Audit logging service
     * @var AuditLogService
     */
    protected AuditLogService $auditService;

    /**
     * Legal content service for file-based legal terms management
     * @var LegalContentService
     */
    protected LegalContentService $legalContentService;

    /**
     * Valid user types for legal documents
     */
    public const VALID_USER_TYPES = [
        'collector', 'creator', 'patron', 'epp', 'company', 'trader_pro'
    ];

    /**
     * Valid locales for legal documents
     */
    public const VALID_LOCALES = [
        'it', 'en', 'es', 'pt', 'fr', 'de'
    ];

    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger
     * @param ErrorManagerInterface $errorManager
     * @param ConsentService $consentService
     * @param AuditLogService $auditService
     * @param LegalContentService $legalContentService
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        ConsentService $consentService,
        AuditLogService $auditService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->consentService = $consentService;
        $this->auditService = $auditService;
    }

    // ═══════════════════════════════════════════════════════════════════════════════
    // LEGAL EDITOR INTERFACE
    // ═══════════════════════════════════════════════════════════════════════════════

    /**
     * Show legal terms editor for specific user type and locale
     *
     * @param string $userType
     * @param string $locale
     * @return View
     */
    public function editTerms(string $userType, string $locale = 'it'): View | RedirectResponse
    {

        $this->middleware(['auth', 'permission:legal.terms.edit']);

        try {
            $this->logger->info('Legal Editor: Accessing terms editor', [
                'user_id' => auth()->id(),
                'user_type' => $userType,
                'locale' => $locale,
                'log_category' => 'LEGAL_EDITOR_ACCESS'
            ]);

            // Validate parameters
            $this->validateUserType($userType);
            $this->validateLocale($locale);

            // Load current content and metadata
            $currentContent = $this->loadTermsContent($userType, $locale);
            $currentVersion = $this->getCurrentVersion();
            $versions = $this->getVersionHistory($userType, $locale);

            // Editor configuration
            $editorConfig = [
                'userTypes' => self::VALID_USER_TYPES,
                'locales' => self::VALID_LOCALES,
                'currentUserType' => $userType,
                'currentLocale' => $locale,
                'canCreateVersion' => auth()->user()->can('legal.terms.create_version'),
                'canApproveVersion' => auth()->user()->can('legal.terms.approve_version'),
            ];

            // Log access for audit
            $this->auditService->logUserAction(auth()->user(), 'legal_editor_accessed', [
                'user_type' => $userType,
                'locale' => $locale,
                'content_loaded' => !empty($currentContent),
                'versions_available' => count($versions)
            ]);

            return view('gdpr.legal.editor', compact(
                'userType',
                'locale',
                'currentContent',
                'currentVersion',
                'versions',
                'editorConfig'
            ));

        } catch (\Exception $e) {
            return $this->errorManager->handle('LEGAL_EDITOR_LOAD_ERROR', [
                'user_id' => auth()->id(),
                'user_type' => $userType,
                'locale' => $locale,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Save new version of legal terms
     *
     * @param Request $request
     * @param string $userType
     * @param string $locale
     * @return RedirectResponse
     */
    public function saveTerms(Request $request, string $userType, string $locale): RedirectResponse
    {

        $this->middleware(['auth', 'permission:legal.terms.create_version']);

        try {
            // Validate request
            $validated = $request->validate([
                'content' => 'required|string|min:100',
                'change_summary' => 'required|string|min:10|max:1000',
                'effective_date' => 'sometimes|date|after_or_equal:today',
                'auto_publish' => 'sometimes|boolean'
            ]);

            $this->logger->info('Legal Editor: Saving new terms version', [
                'user_id' => auth()->id(),
                'user_type' => $userType,
                'locale' => $locale,
                'content_length' => strlen($validated['content']),
                'auto_publish' => $validated['auto_publish'] ?? false,
                'log_category' => 'LEGAL_TERMS_SAVE'
            ]);

            // Validate parameters
            $this->validateUserType($userType);
            $this->validateLocale($locale);

            // Validate content security
            if (!$this->validateContentSecurity($validated['content'])) {
                return back()
                    ->withErrors(['content' => 'Il contenuto contiene codice non sicuro o non permesso'])
                    ->withInput();
            }

            // Create backup of current version
            $this->backupCurrentVersion($userType, $locale);

            // Create new version
            $newVersion = $this->createNewTermsVersion(
                $userType,
                $locale,
                $validated['content'],
                $validated['change_summary'],
                $validated['effective_date'] ?? null,
                auth()->id(),
                $validated['auto_publish'] ?? false
            );

            // Clear cache
            $this->clearTermsCache($userType, $locale);

            // Log success
            $this->auditService->logUserAction(auth()->user(), 'legal_terms_saved', [
                'user_type' => $userType,
                'locale' => $locale,
                'new_version' => $newVersion,
                'change_summary' => $validated['change_summary'],
                'auto_published' => $validated['auto_publish'] ?? false
            ]);

            $successMessage = "Nuova versione {$newVersion} creata con successo per {$userType} ({$locale})";
            if ($validated['auto_publish'] ?? false) {
                $successMessage .= " e pubblicata automaticamente.";
            }

            return redirect()
                ->route('legal.edit', compact('userType', 'locale'))
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            return $this->errorManager->handle('LEGAL_TERMS_SAVE_ERROR', [
                'user_id' => auth()->id(),
                'user_type' => $userType,
                'locale' => $locale,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    // ═══════════════════════════════════════════════════════════════════════════════
    // PUBLIC TERMS DISPLAY
    // ═══════════════════════════════════════════════════════════════════════════════

    /**
     * Show public terms for specific user type
     *
     * @param string $userType
     * @param string $locale
     * @return View
     */
    public function showTerms(string $userType, string $locale = 'it'): View|RedirectResponse
    {
        try {
            $this->logger->info('Legal Public: Accessing terms', [
                'user_id' => auth()->id() ?? 'guest',
                'user_type' => $userType,
                'locale' => $locale,
                'log_category' => 'LEGAL_TERMS_VIEW'
            ]);

            // Validate parameters
            $this->validateUserType($userType);
            $this->validateLocale($locale);

            // Load terms content
            $termsContent = $this->loadTermsContent($userType, $locale);
            $currentVersion = $this->getCurrentVersion();

            if (!$termsContent) {
                return $this->errorManager->handle('LEGAL_TERMS_NOT_FOUND', [
                    'user_type' => $userType,
                    'locale' => $locale
                ]);
            }

             // ✅ CONVERSIONE A COLLECTION
            // Convertiamo l'array di articoli in una Collection di Laravel per poter
            // usare metodi comodi come ->where('category', '...') nella vista.
            if (isset($termsContent['articles'])) {
                $termsContent['articles'] = collect($termsContent['articles']);
            }

            // Check user consent status (if logged in)
            $consentStatus = $this->getUserConsentStatus($userType, $locale);

            // Log access for audit
            if (auth()->check()) {
                $this->auditService->logUserAction(auth()->user(), 'legal_terms_viewed', [
                    'user_type' => $userType,
                    'locale' => $locale,
                    'has_accepted_current' => $consentStatus['hasAcceptedCurrent'],
                    'version_viewed' => $currentVersion
                ]);
            }

            return view('gdpr.legal.terms', compact(
                'userType',
                'locale',
                'termsContent',
                'currentVersion',
                'consentStatus'
            ));

        } catch (\Exception $e) {
            return $this->errorManager->handle('LEGAL_TERMS_VIEW_ERROR', [
                'user_id' => auth()->id() ?? 'guest',
                'user_type' => $userType,
                'locale' => $locale,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * Accept terms of service for current user
     *
     * @param Request $request
     * @param string $userType
     * @return JsonResponse
     */
    public function acceptTerms(Request $request, string $userType): JsonResponse
    {
        try {
            $validated = $request->validate([
                'version' => 'required|string',
                'locale' => 'required|string|in:it,en,es,pt,fr,de'
            ]);

            $user = auth()->user();

            $this->logger->info('Legal Public: User accepting terms', [
                'user_id' => $user->id,
                'user_type' => $userType,
                'version' => $validated['version'],
                'locale' => $validated['locale'],
                'log_category' => 'LEGAL_TERMS_ACCEPT'
            ]);

            // Validate user type matches user's actual type
            if ($user->usertype !== $userType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo utente non corrispondente'
                ], 400);
            }

            // Record consent through existing ConsentService
            $consentRecorded = $this->consentService->grantConsent(
                $user,
                'terms-of-service',
                [
                    'user_type' => $userType,
                    'version' => $validated['version'],
                    'locale' => $validated['locale'],
                    'acceptance_method' => 'web_form',
                    'source' => 'legal_terms_page'
                ]
            );

            if ($consentRecorded) {
                $this->auditService->logUserAction($user, 'legal_terms_accepted', [
                    'user_type' => $userType,
                    'version' => $validated['version'],
                    'locale' => $validated['locale']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Termini accettati con successo',
                    'version' => $validated['version'],
                    'redirect_url' => route('dashboard')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Errore nella registrazione del consenso'
            ], 500);

        } catch (\Exception $e) {
            return $this->errorManager->handle('LEGAL_TERMS_ACCEPT_ERROR', [
                'user_id' => auth()->id(),
                'user_type' => $userType,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    // ═══════════════════════════════════════════════════════════════════════════════
    // VERSION HISTORY & AUDIT
    // ═══════════════════════════════════════════════════════════════════════════════

    /**
     * Show version history for terms
     *
     * @param string $userType
     * @param string $locale
     * @return View
     */
    public function termsHistory(string $userType, string $locale = 'it'): View | RedirectResponse
    {

        $this->middleware(['auth', 'permission:legal.history.view']);

        try {
            $this->logger->info('Legal History: Accessing terms history', [
                'user_id' => auth()->id(),
                'user_type' => $userType,
                'locale' => $locale,
                'log_category' => 'LEGAL_HISTORY_ACCESS'
            ]);

            // Validate parameters
            $this->validateUserType($userType);
            $this->validateLocale($locale);

            // Get detailed version history
            $versions = $this->getDetailedVersionHistory($userType, $locale);
            $metadata = $this->getVersionsMetadata();
            $consentStats = $this->getConsentStatistics($userType);

            $this->auditService->logUserAction(auth()->user(), 'legal_history_viewed', [
                'user_type' => $userType,
                'locale' => $locale,
                'versions_count' => count($versions)
            ]);

            return view('gdpr.legal.history', compact(
                'userType',
                'locale',
                'versions',
                'metadata',
                'consentStats'
            ));

        } catch (\Exception $e) {
            return $this->errorManager->handle('LEGAL_HISTORY_VIEW_ERROR', [
                'user_id' => auth()->id(),
                'user_type' => $userType,
                'locale' => $locale,
                'error' => $e->getMessage()
            ], $e);
        }
    }

    // ═══════════════════════════════════════════════════════════════════════════════
    // HELPER METHODS
    // ═══════════════════════════════════════════════════════════════════════════════

    /**
     * Load terms content from file system
     *
     * @param string $userType
     * @param string $locale
     * @return array|null
     */
    private function loadTermsContent(string $userType, string $locale): ?array
    {
        $cacheKey = "legal_terms_{$userType}_{$locale}";

        return Cache::remember($cacheKey, 3600, function () use ($userType, $locale) {
            $filePath = resource_path("legal/terms/versions/current/{$locale}/{$userType}.php");

            if (!File::exists($filePath)) {
                $this->logger->warning('Legal: Terms file not found', [
                    'file_path' => $filePath,
                    'user_type' => $userType,
                    'locale' => $locale
                ]);
                return null;
            }

            try {
                return include $filePath;
            } catch (\Exception $e) {
                $this->logger->error('Legal: Failed to load terms content', [
                    'file_path' => $filePath,
                    'error' => $e->getMessage()
                ]);
                return null;
            }
        });
    }

    /**
     * Get user consent status for terms
     *
     * @param string $userType
     * @param string $locale
     * @return array
     */
    private function getUserConsentStatus(string $userType, string $locale): array
    {
        if (!auth()->check()) {
            return [
                'hasAcceptedCurrent' => false,
                'userAcceptedVersion' => null,
                'needsAcceptance' => true
            ];
        }

        $user = auth()->user();
        $hasAcceptedCurrent = $this->consentService->hasConsent($user, 'terms-of-service');

        // Get user's current consent to extract version info
        $userConsent = $user->consents()
            ->where('consent_type', 'terms-of-service')
            ->where('granted', true)
            ->latest('created_at')
            ->first();

        $userAcceptedVersion = null;
        if ($userConsent && isset($userConsent->metadata['version'])) {
            $userAcceptedVersion = $userConsent->metadata['version'];
        }

        return [
            'hasAcceptedCurrent' => $hasAcceptedCurrent,
            'userAcceptedVersion' => $userAcceptedVersion,
            'needsAcceptance' => !$hasAcceptedCurrent || $user->usertype !== $userType
        ];
    }

    /**
     * Validate user type parameter
     *
     * @param string $userType
     * @throws \InvalidArgumentException
     */
    private function validateUserType(string $userType): void
    {
        if (!in_array($userType, self::VALID_USER_TYPES)) {
            throw new \InvalidArgumentException("Invalid user type: {$userType}");
        }
    }

    /**
     * Validate locale parameter
     *
     * @param string $locale
     * @throws \InvalidArgumentException
     */
    private function validateLocale(string $locale): void
    {
        if (!in_array($locale, self::VALID_LOCALES)) {
            throw new \InvalidArgumentException("Invalid locale: {$locale}");
        }
    }

    /**
     * Validate content for security
     *
     * @param string $content
     * @return bool
     */
    private function validateContentSecurity(string $content): bool
    {
        // Basic PHP syntax check
        if (!$this->isValidPHPSyntax($content)) {
            return false;
        }

        // Check for dangerous functions
        $dangerousFunctions = [
            'exec', 'system', 'shell_exec', 'eval', 'file_get_contents',
            'file_put_contents', 'fopen', 'fwrite', 'include', 'require'
        ];

        foreach ($dangerousFunctions as $func) {
            if (stripos($content, $func) !== false) {
                $this->logger->warning('Legal: Dangerous function detected in content', [
                    'function' => $func,
                    'user_id' => auth()->id()
                ]);
                return false;
            }
        }

        return true;
    }

    /**
     * Check if PHP syntax is valid
     *
     * @param string $content
     * @return bool
     */
    private function isValidPHPSyntax(string $content): bool
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'legal_syntax_check');
        file_put_contents($tempFile, "<?php\n" . $content);

        $output = [];
        $returnCode = 0;
        exec("php -l {$tempFile} 2>&1", $output, $returnCode);

        unlink($tempFile);

        return $returnCode === 0;
    }

    /**
     * Get current version from metadata
     *
     * @return string
     */
    private function getCurrentVersion(): string
    {
        $metadataPath = resource_path('legal/terms/versions/current/metadata.php');

        if (File::exists($metadataPath)) {
            try {
                $metadata = include $metadataPath;
                return $metadata['version'] ?? '1.0.0';
            } catch (\Exception $e) {
                $this->logger->error('Legal: Failed to load version metadata', [
                    'error' => $e->getMessage()
                ]);
            }
        }

        return '1.0.0';
    }

    /**
     * Clear terms cache
     *
     * @param string $userType
     * @param string $locale
     */
    private function clearTermsCache(string $userType, string $locale): void
    {
        Cache::forget("legal_terms_{$userType}_{$locale}");
        Cache::tags(['legal', 'terms', $userType, $locale])->flush();
    }

    /**
     * Get version history for user type and locale
     *
     * @param string $userType
     * @param string $locale
     * @return array
     */
    private function getVersionHistory(string $userType, string $locale): array
    {
        $versionsPath = resource_path('legal/terms/versions');
        $versions = [];

        if (!File::exists($versionsPath)) {
            return $versions;
        }

        $directories = File::directories($versionsPath);

        foreach ($directories as $dir) {
            $version = basename($dir);
            if ($version === 'current') continue;

            $metadataPath = "{$dir}/metadata.php";
            $filePath = "{$dir}/{$locale}/{$userType}.php";

            if (File::exists($metadataPath) && File::exists($filePath)) {
                try {
                    $metadata = include $metadataPath;
                    $versions[] = [
                        'version' => $version,
                        'metadata' => $metadata,
                        'file_exists' => true,
                        'file_size' => File::size($filePath),
                        'last_modified' => File::lastModified($filePath)
                    ];
                } catch (\Exception $e) {
                    $this->logger->warning('Legal: Failed to load version metadata', [
                        'version' => $version,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        // Sort by version descending
        usort($versions, fn($a, $b) => version_compare($b['version'], $a['version']));

        return array_slice($versions, 0, 10); // Limit to last 10 versions
    }

    /**
     * Create new terms version in file system
     *
     * @param string $userType
     * @param string $locale
     * @param string $content
     * @param string $changeSummary
     * @param string|null $effectiveDate
     * @param int $createdBy
     * @param bool $autoPublish
     * @return string New version number
     */
    private function createNewTermsVersion(
        string $userType,
        string $locale,
        string $content,
        string $changeSummary,
        ?string $effectiveDate,
        int $createdBy,
        bool $autoPublish = false
    ): string {
        // Generate new version number
        $newVersion = $this->generateNewVersionNumber();

        // Create new version directory
        $versionPath = resource_path("legal/terms/versions/{$newVersion}");
        $localePath = "{$versionPath}/{$locale}";

        if (!File::exists($localePath)) {
            File::makeDirectory($localePath, 0755, true);
        }

        // Save content file
        $filePath = "{$localePath}/{$userType}.php";
        File::put($filePath, $content);

        // Update metadata
        $this->updateVersionMetadata($newVersion, $userType, $locale, $changeSummary, $effectiveDate, $createdBy);

        // Update current symlink if auto-publish
        if ($autoPublish) {
            $this->updateCurrentVersion($newVersion);
        }

        return $newVersion;
    }

    /**
     * Generate new version number
     *
     * @return string
     */
    private function generateNewVersionNumber(): string
    {
        $currentVersion = $this->getCurrentVersion();
        $parts = explode('.', $currentVersion);

        // Increment patch version
        $parts[2] = (string)((int)$parts[2] + 1);

        return implode('.', $parts);
    }

    /**
     * Backup current version before creating new one
     *
     * @param string $userType
     * @param string $locale
     */
    private function backupCurrentVersion(string $userType, string $locale): void
    {
        $currentFile = resource_path("legal/terms/versions/current/{$locale}/{$userType}.php");

        if (File::exists($currentFile)) {
            $backupDir = resource_path('legal/terms/backups/' . date('Y-m-d'));
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            $backupFile = "{$backupDir}/{$userType}_{$locale}_" . time() . ".php";
            File::copy($currentFile, $backupFile);
        }
    }

    /**
     * Update version metadata
     *
     * @param string $version
     * @param string $userType
     * @param string $locale
     * @param string $changeSummary
     * @param string|null $effectiveDate
     * @param int $createdBy
     */
    private function updateVersionMetadata(
        string $version,
        string $userType,
        string $locale,
        string $changeSummary,
        ?string $effectiveDate,
        int $createdBy
    ): void {
        $metadataPath = resource_path("legal/terms/versions/{$version}/metadata.php");

        $metadata = [
            'version' => $version,
            'release_date' => now()->toDateString(),
            'effective_date' => $effectiveDate ?? now()->toDateString(),
            'created_by' => auth()->user()->email,
            'created_by_id' => $createdBy,
            'summary_of_changes' => $changeSummary,
            'updated_user_types' => [$userType],
            'updated_locales' => [$locale]
        ];

        $metadataContent = "<?php\n\nreturn " . var_export($metadata, true) . ";\n";
        File::put($metadataPath, $metadataContent);
    }

    /**
     * Update current version symlink
     *
     * @param string $newVersion
     */
    private function updateCurrentVersion(string $newVersion): void
    {
        $currentPath = resource_path('legal/terms/versions/current');
        $targetPath = resource_path("legal/terms/versions/{$newVersion}");

        // Remove existing symlink if it exists
        if (is_link($currentPath)) {
            unlink($currentPath);
        }

        // Create new symlink
        symlink($targetPath, $currentPath);
    }

    /**
     * Get detailed version history with additional metadata
     *
     * @param string $userType
     * @param string $locale
     * @return array
     */
    private function getDetailedVersionHistory(string $userType, string $locale): array
    {
        $versions = $this->getVersionHistory($userType, $locale);

        // Enhance with consent statistics
        foreach ($versions as &$version) {
            $version['consent_count'] = $this->getVersionConsentCount($version['version'], $userType);
        }

        return $versions;
    }

    /**
     * Get consent statistics for a specific version
     *
     * @param string $version
     * @param string $userType
     * @return int
     */
    private function getVersionConsentCount(string $version, string $userType): int
    {
        // This would require integration with the consent database
        // For now, return 0 - implement later when needed
        return 0;
    }

    /**
     * Get versions metadata summary
     *
     * @return array
     */
    private function getVersionsMetadata(): array
    {
        return [
            'total_versions' => count(File::directories(resource_path('legal/terms/versions'))) - 1, // Exclude 'current'
            'current_version' => $this->getCurrentVersion(),
            'last_update' => now()->toDateString()
        ];
    }

    /**
     * Get consent statistics for user type
     *
     * @param string $userType
     * @return array
     */
    private function getConsentStatistics(string $userType): array
    {
        // This would require integration with the consent database
        // For now, return empty - implement later when needed
        return [
            'total_consents' => 0,
            'active_consents' => 0,
            'withdrawal_rate' => 0
        ];
    }
}