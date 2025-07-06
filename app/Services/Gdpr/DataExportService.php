<?php

namespace App\Services\Gdpr;

use App\Models\User;
use App\Models\DataExport;
use App\Models\Collection;
use App\Models\UserConsent;
use App\Models\UserActivity;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Carbon\Carbon;
use ZipArchive;

/**
 * @Oracode Service: Data Export Management
 * 🎯 Purpose: Handles GDPR data portability with multiple formats
 * 🛡️ Privacy: Exports only user's own data with full audit trail
 * 🧱 Core Logic: Generates, stores, and delivers user data exports
 *
 * @package App\Services
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 1.0.0
 * @date 2025-05-22
 */
class DataExportService
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
     * Maximum export file size in bytes (50MB)
     * @var int
     */
    protected int $maxExportSize = 52428800;

    /**
     * Export retention period in days
     * @var int
     */
    protected int $retentionDays = 30;

    /**
     * Available data categories for export
     * @var array
     */
    protected array $dataCategories = [
        'profile' => [
            'name' => 'Profile Information',
            'description' => 'Basic profile data, settings, and preferences',
            'includes' => ['name', 'email', 'bio', 'avatar', 'settings']
        ],
        'activities' => [
            'name' => 'Activity History',
            'description' => 'Platform usage, logins, and interactions',
            'includes' => ['login_history', 'page_views', 'actions', 'sessions']
        ],
        'collections' => [
            'name' => 'Collections and NFTs',
            'description' => 'Created collections, NFT metadata, and ownership',
            'includes' => ['collections', 'nfts', 'metadata', 'ownership_history']
        ],
        'wallet' => [
            'name' => 'Wallet and Transactions',
            'description' => 'Wallet connections and transaction history',
            'includes' => ['wallet_address', 'transactions', 'blockchain_data']
        ],
        'consents' => [
            'name' => 'Privacy Consents',
            'description' => 'Consent history and privacy preferences',
            'includes' => ['consent_history', 'privacy_settings', 'cookie_preferences']
        ],
        'audit' => [
            'name' => 'Audit Trail',
            'description' => 'Security logs and account changes',
            'includes' => ['security_events', 'account_changes', 'gdpr_requests']
        ]
    ];

    /**
     * Supported export formats
     * @var array
     */
    protected array $supportedFormats = [
        'json' => [
            'name' => 'JSON',
            'description' => 'Machine-readable structured data format',
            'mime_type' => 'application/json',
            'extension' => 'json'
        ],
        'csv' => [
            'name' => 'CSV',
            'description' => 'Spreadsheet-compatible comma-separated values',
            'mime_type' => 'text/csv',
            'extension' => 'csv'
        ],
        'pdf' => [
            'name' => 'PDF',
            'description' => 'Human-readable document format',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf'
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
     * Get user's export history
     *
     * @param User $user
     * @param int $limit
     * @return EloquentCollection
     * @privacy-safe Returns user's own export history only
     */
    public function getUserExportHistory(User $user, int $limit = 20): EloquentCollection
    {
        try {
            $this->logger->info('Data Export Service: Getting user export history', [
                'user_id' => $user->id,
                'limit' => $limit,
                'log_category' => 'EXPORT_SERVICE_OPERATION'
            ]);

            return $user->dataExports()
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($export) {
                    return [
                        'id' => $export->id,
                        'token' => $export->token,
                        'format' => $export->format,
                        'categories' => $export->categories,
                        'status' => $export->status,
                        'file_size' => $export->file_size,
                        'progress' => $export->progress,
                        'created_at' => $export->created_at,
                        'completed_at' => $export->completed_at,
                        'expires_at' => $export->expires_at,
                        'download_count' => $export->download_count,
                        'is_expired' => $export->expires_at < now()
                    ];
                });
        } catch (\Exception $e) {
            $this->logger->error('Data Export Service: Failed to get export history', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'log_category' => 'EXPORT_SERVICE_ERROR'
            ]);

            throw $e;
        }
    }

    /**
     * Get supported export formats
     *
     * @return array
     * @privacy-safe Returns public format definitions
     */
    public function getSupportedFormats(): array
    {
        return $this->supportedFormats;
    }

    /**
     * Generate user data export
     *
     * @param User $user
     * @param string $format
     * @param array $categories
     * @return string Export token
     * @privacy-safe Generates export for authenticated user only
     */
    public function generateUserDataExport(User $user, string $format, array $categories): string
    {
        try {
            $this->logger->info('Data Export Service: Generating user data export', [
                'user_id' => $user->id,
                'format' => $format,
                'categories' => $categories,
                'log_category' => 'EXPORT_SERVICE_OPERATION'
            ]);

            // Validate format
            if (!isset($this->supportedFormats[$format])) {
                throw new \InvalidArgumentException("Unsupported export format: {$format}");
            }

            // Validate categories
            $invalidCategories = array_diff($categories, array_keys($this->dataCategories));
            if (!empty($invalidCategories)) {
                throw new \InvalidArgumentException("Invalid categories: " . implode(', ', $invalidCategories));
            }

            // Check for existing pending exports
            $pendingExport = $user->dataExports()
                ->whereIn('status', ['pending', 'processing'])
                ->first();

            if ($pendingExport) {
                $this->logger->warning('Data Export Service: User has pending export', [
                    'user_id' => $user->id,
                    'existing_export_id' => $pendingExport->id,
                    'log_category' => 'EXPORT_SERVICE_WARNING'
                ]);
                return $pendingExport->token;
            }

            // Create export record
            $token = Str::random(64);
            $export = DataExport::create([
                'user_id' => $user->id,
                'token' => $token,
                'format' => $format,
                'categories' => $categories,
                'status' => 'pending',
                'progress' => 0,
                'expires_at' => now()->addDays($this->retentionDays),
                'metadata' => [
                    'requested_at' => now()->toISOString(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'session_id' => session()->getId()
                ]
            ]);

            // Queue export job for background processing
            Queue::push(new \App\Jobs\ProcessDataExport($export));

            return $token;
        } catch (\Exception $e) {
            $this->logger->error('Data Export Service: Failed to generate export', [
                'user_id' => $user->id,
                'format' => $format,
                'categories' => $categories,
                'error' => $e->getMessage(),
                'log_category' => 'EXPORT_SERVICE_ERROR'
            ]);

            throw $e;
        }
    }

    /**
     * Process data export (called by queue job)
     *
     * @param DataExport $export
     * @return bool
     * @privacy-safe Processes export for specified user only
     */
    public function processDataExport(DataExport $export): bool
    {
        try {
            $this->logger->info('Data Export Service: Processing data export', [
                'export_id' => $export->id,
                'user_id' => $export->user_id,
                'format' => $export->format,
                'log_category' => 'EXPORT_SERVICE_PROCESSING'
            ]);

            $export->update(['status' => 'processing', 'progress' => 0]);

            // Collect data
            $userData = $this->collectUserData($export->user, $export->categories, $export);

            // Generate file
            $filePath = $this->generateExportFile($userData, $export);

            // Update export record
            $fileSize = Storage::disk('exports')->size($filePath);
            $export->update([
                'status' => 'completed',
                'progress' => 100,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'completed_at' => now()
            ]);

            $this->logger->info('Data Export Service: Export completed successfully', [
                'export_id' => $export->id,
                'user_id' => $export->user_id,
                'file_size' => $fileSize,
                'log_category' => 'EXPORT_SERVICE_SUCCESS'
            ]);

            return true;
        } catch (\Exception $e) {
            $export->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            $this->logger->error('Data Export Service: Export processing failed', [
                'export_id' => $export->id,
                'user_id' => $export->user_id,
                'error' => $e->getMessage(),
                'log_category' => 'EXPORT_SERVICE_ERROR'
            ]);

            throw $e;
        }
    }

    /**
     * Get export by token
     *
     * @param string $token
     * @param User $user
     * @return DataExport|null
     * @privacy-safe Returns export only if it belongs to user
     */
    public function getExportByToken(string $token, User $user): ?DataExport
    {
        return $user->dataExports()->where('token', $token)->first();
    }

    /**
     * Stream export file for download
     *
     * @param DataExport $export
     * @return StreamedResponse
     * @privacy-safe Streams file only for authorized user
     */
    public function streamExportFile(DataExport $export): StreamedResponse
    {
        try {
            $this->logger->info('Data Export Service: Streaming export file', [
                'export_id' => $export->id,
                'user_id' => $export->user_id,
                'log_category' => 'EXPORT_SERVICE_DOWNLOAD'
            ]);

            if ($export->status !== 'completed') {
                throw new \RuntimeException('Export is not ready for download');
            }

            if ($export->expires_at < now()) {
                throw new \RuntimeException('Export has expired');
            }

            if (!Storage::disk('exports')->exists($export->file_path)) {
                throw new \RuntimeException('Export file not found');
            }

            // Update download count
            $export->increment('download_count');
            $export->update(['last_downloaded_at' => now()]);

            $formatConfig = $this->supportedFormats[$export->format];
            $filename = "florence_egi_data_export_{$export->user_id}_{$export->created_at->format('Y-m-d')}.{$formatConfig['extension']}";

            return Storage::disk('exports')->download($export->file_path, $filename, [
                'Content-Type' => $formatConfig['mime_type'],
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Data Export Service: Failed to stream export file', [
                'export_id' => $export->id,
                'user_id' => $export->user_id,
                'error' => $e->getMessage(),
                'log_category' => 'EXPORT_SERVICE_ERROR'
            ]);

            throw $e;
        }
    }

    /**
     * Clean expired exports
     *
     * @return int Number of cleaned exports
     * @privacy-safe Cleans only expired exports
     */
    public function cleanExpiredExports(): int
    {
        try {
            $this->logger->info('Data Export Service: Cleaning expired exports', [
                'log_category' => 'EXPORT_SERVICE_MAINTENANCE'
            ]);

            $expiredExports = DataExport::where('expires_at', '<', now())
                ->where('status', 'completed')
                ->get();

            $cleanedCount = 0;
            foreach ($expiredExports as $export) {
                if ($export->file_path && Storage::disk('exports')->exists($export->file_path)) {
                    Storage::disk('exports')->delete($export->file_path);
                }
                $export->update(['status' => 'expired', 'file_path' => null]);
                $cleanedCount++;
            }

            $this->logger->info('Data Export Service: Expired exports cleaned', [
                'cleaned_count' => $cleanedCount,
                'log_category' => 'EXPORT_SERVICE_MAINTENANCE'
            ]);

            return $cleanedCount;
        } catch (\Exception $e) {
            $this->logger->error('Data Export Service: Failed to clean expired exports', [
                'error' => $e->getMessage(),
                'log_category' => 'EXPORT_SERVICE_ERROR'
            ]);

            throw $e;
        }
    }

    // ===================================================================
    // PRIVATE HELPER METHODS
    // ===================================================================

    /**
     * Collect user data for export
     *
     * @param User $user
     * @param array $categories
     * @param DataExport $export
     * @return array
     * @privacy-safe Collects data for specified user only
     */
    private function collectUserData(User $user, array $categories, DataExport $export): array
    {
        $data = [
            'export_info' => [
                'generated_at' => now()->toISOString(),
                'user_id' => $user->id,
                'categories' => $categories,
                'format' => $export->format,
                'florence_egi_version' => config('app.version', '1.0.0')
            ]
        ];

        // 🔥 VERIFICA CHE LE CATEGORIE SIANO VALIDE DALLA CONFIG
        $validCategories = array_keys(config('gdpr.export.data_categories', []));
        $categoriesToProcess = array_intersect($categories, $validCategories);

        $totalCategories = count($categoriesToProcess);
        $currentCategory = 0;

        foreach ($categoriesToProcess as $category) {
            $this->updateExportProgress($export, ($currentCategory / $totalCategories) * 80);

            switch ($category) {
                case 'profile':
                    $data['profile'] = $this->collectProfileData($user);
                    break;
                case 'account':
                    $data['account'] = $this->collectAccountData($user);
                    break;
                case 'preferences':
                    $data['preferences'] = $this->collectPreferencesData($user);
                    break;
                case 'activity':
                    $data['activity'] = $this->collectActivityData($user);
                    break;
                case 'consents':
                    $data['consents'] = $this->collectConsentData($user);
                    break;
                case 'collections':
                    $data['collections'] = $this->collectCollectionData($user);
                    break;
                case 'purchases':
                    $data['purchases'] = $this->collectPurchasesData($user);
                    break;
                case 'comments':
                    $data['comments'] = $this->collectCommentsData($user);
                    break;
                case 'messages':
                    $data['messages'] = $this->collectMessagesData($user);
                    break;
                case 'biography':
                    $data['biography'] = $this->collectBiographyData($user);
                    break;
                // Mantieni anche i metodi esistenti se ci sono
                case 'wallet':
                    $data['wallet'] = $this->collectWalletData($user);
                    break;
                case 'audit':
                    $data['audit'] = $this->collectAuditData($user);
                    break;
                default:
                    // Log unknown category
                    $this->logger->warning('Unknown export category requested', [
                        'category' => $category,
                        'user_id' => $user->id
                    ]);
                    break;
            }

            $currentCategory++;
        }
        return $data;
    }

    /**
     * Collect profile data
     *
     * @param User $user
     * @return array
     * @privacy-safe Returns user's profile data only
     */
    private function collectProfileData(User $user): array
    {
        return [
            'basic_info' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'bio' => $user->bio,
                'created_at' => $user->created_at->toISOString(),
                'updated_at' => $user->updated_at->toISOString()
            ],
            'settings' => [
                'notification_preferences' => $user->notification_preferences ?? [],
                'privacy_settings' => $user->privacy_settings ?? [],
                'language' => $user->language ?? 'en',
                'timezone' => $user->timezone ?? 'UTC'
            ],
            'verification_status' => [
                'email_verified' => !is_null($user->email_verified_at),
                'email_verified_at' => $user->email_verified_at?->toISOString()
            ]
        ];
    }

    /**
     * Collect activity data
     *
     * @param User $user
     * @return array
     * @privacy-safe Returns user's activity data only
     */
    private function collectActivityData(User $user): array
    {
        return [
            'login_history' => $user->loginHistory()
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get()
                ->map(function ($login) {
                    return [
                        'timestamp' => $login->created_at->toISOString(),
                        'ip_address' => $this->maskIpAddress($login->ip_address),
                        'user_agent' => $login->user_agent,
                        'success' => $login->success
                    ];
                })->toArray(),
            'platform_usage' => [
                'total_logins' => $user->loginHistory()->where('success', true)->count(),
                'last_login' => $user->last_login_at?->toISOString(),
                'account_age_days' => $user->created_at->diffInDays(now())
            ]
        ];
    }

    /**
     * Collect collection data
     *
     * @param User $user
     * @return array
     * @privacy-safe Returns user's collection data only
     */
    private function collectCollectionData(User $user): array
    {
        return [
            'owned_collections' => $user->collections()
                ->with('egis')
                ->get()
                ->map(function ($collection) {
                    return [
                        'id' => $collection->id,
                        'name' => $collection->name,
                        'description' => $collection->description,
                        'created_at' => $collection->created_at->toISOString(),
                        'egi_count' => $collection->egis->count(),
                        'status' => $collection->status,
                        'blockchain_data' => [
                            'contract_address' => $collection->contract_address,
                            'blockchain_id' => $collection->blockchain_id
                        ]
                    ];
                })->toArray(),
            'collaboration_collections' => $user->collaboratingCollections()
                ->get()
                ->map(function ($collection) {
                    return [
                        'id' => $collection->id,
                        'name' => $collection->name,
                        'role' => $collection->pivot->role,
                        'joined_at' => $collection->pivot->created_at->toISOString()
                    ];
                })->toArray()
        ];
    }

    /**
     * Collect wallet data
     *
     * @param User $user
     * @return array
     * @privacy-safe Returns user's wallet data only
     */
    private function collectWalletData(User $user): array
    {
        return [
            'wallet_connections' => [
                'primary_wallet' => $user->wallet_address,
                'connected_at' => $user->wallet_connected_at?->toISOString(),
                'wallet_type' => $user->wallet_type
            ],
            'blockchain_activity' => [
                'total_transactions' => $user->blockchainTransactions()->count(),
                'last_transaction' => $user->blockchainTransactions()
                    ->latest()
                    ->first()?->created_at?->toISOString()
            ]
        ];
    }

    /**
     * Collect consent data
     *
     * @param User $user
     * @return array
     * @privacy-safe Returns user's consent data only
     */
    private function collectConsentData(User $user): array
    {
        return [
            'current_consents' => $user->consent_summary ?? [],
            'consent_history' => $user->consents()
                ->with('consentVersion')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($consent) {
                    return [
                        'consent_type' => $consent->consent_type,
                        'granted' => $consent->granted,
                        'timestamp' => $consent->created_at->toISOString(),
                        'version' => $consent->consentVersion?->version,
                        'legal_basis' => $consent->legal_basis,
                        'withdrawal_method' => $consent->withdrawal_method
                    ];
                })->toArray(),
            'last_updated' => $user->consents_updated_at?->toISOString()
        ];
    }

    /**
     * Collect audit data
     *
     * @param User $user
     * @return array
     * @privacy-safe Returns user's audit data only
     */
    private function collectAuditData(User $user): array
    {
        return [
            'gdpr_requests' => $user->gdprRequests()
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($request) {
                    return [
                        'type' => $request->type,
                        'status' => $request->status,
                        'created_at' => $request->created_at->toISOString(),
                        'processed_at' => $request->processed_at?->toISOString(),
                        'notes' => $request->notes
                    ];
                })->toArray(),
            'security_events' => $user->securityEvents()
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
                ->map(function ($event) {
                    return [
                        'type' => $event->type,
                        'description' => $event->description,
                        'timestamp' => $event->created_at->toISOString(),
                        'ip_address' => $this->maskIpAddress($event->ip_address)
                    ];
                })->toArray()
        ];
    }

    /**
     * Generate export file in requested format
     *
     * @param array $data
     * @param DataExport $export
     * @return string File path
     * @privacy-safe Generates file for specified export only
     */
    private function generateExportFile(array $data, DataExport $export): string
    {
        $this->updateExportProgress($export, 90);

        $fileName = "export_{$export->token}_{$export->format}";

        switch ($export->format) {
            case 'json':
                return $this->generateJsonFile($data, $fileName);
            case 'csv':
                return $this->generateCsvFile($data, $fileName);
            case 'pdf':
                return $this->generatePdfFile($data, $fileName);
            default:
                throw new \InvalidArgumentException("Unsupported format: {$export->format}");
        }
    }

    /**
     * Generate JSON export file
     *
     * @param array $data
     * @param string $fileName
     * @return string
     * @privacy-safe Generates JSON file with user data
     */
    private function generateJsonFile(array $data, string $fileName): string
    {
        $filePath = $fileName . '.json';
        $jsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        Storage::disk('exports')->put($filePath, $jsonContent);

        return $filePath;
    }

    /**
     * Generate CSV export file
     *
     * @param array $data
     * @param string $fileName
     * @return string
     * @privacy-safe Generates CSV file with user data
     */
    private function generateCsvFile(array $data, string $fileName): string
    {
        $zipPath = $fileName . '.zip';
        $zip = new ZipArchive();
        $tempZipPath = storage_path('app/temp/' . $zipPath);

        if ($zip->open($tempZipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($data as $category => $categoryData) {
                if ($category === 'export_info') continue;

                $csvContent = $this->arrayToCsv($categoryData);
                $zip->addFromString($category . '.csv', $csvContent);
            }
            $zip->close();
        }

        Storage::disk('exports')->putFileAs('', $tempZipPath, $zipPath);
        unlink($tempZipPath);

        return $zipPath;
    }

    /**
     * Generate PDF export file
     *
     * @param array $data
     * @param string $fileName
     * @return string
     * @privacy-safe Generates PDF file with user data
     */
    private function generatePdfFile(array $data, string $fileName): string
    {
        // Implementation would use a PDF library like TCPDF or DomPDF
        // For now, return a simple text-based approach
        $filePath = $fileName . '.pdf';
        $textContent = $this->arrayToText($data);

        // This would be replaced with actual PDF generation
        Storage::disk('exports')->put($filePath, $textContent);

        return $filePath;
    }

    /**
     * Update export progress
     *
     * @param DataExport $export
     * @param int $progress
     * @return void
     * @privacy-safe Updates progress for specified export
     */
    private function updateExportProgress(DataExport $export, int $progress): void
    {
        $export->update(['progress' => min(100, max(0, $progress))]);
    }

    /**
     * Convert array to CSV format
     *
     * @param array $data
     * @return string
     * @privacy-safe Converts data to CSV format
     */
    private function arrayToCsv(array $data): string
    {
        $output = fopen('php://temp', 'r+');

        if (is_array($data) && !empty($data)) {
            $first = reset($data);
            if (is_array($first)) {
                // Write headers
                fputcsv($output, array_keys($first));
                // Write data rows
                foreach ($data as $row) {
                    fputcsv($output, $row);
                }
            } else {
                // Simple key-value pairs
                fputcsv($output, ['Key', 'Value']);
                foreach ($data as $key => $value) {
                    fputcsv($output, [$key, is_array($value) ? json_encode($value) : $value]);
                }
            }
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Convert array to text format
     *
     * @param array $data
     * @return string
     * @privacy-safe Converts data to text format
     */
    private function arrayToText(array $data): string
    {
        $text = "FlorenceEGI Data Export\n";
        $text .= "Generated: " . now()->toISOString() . "\n\n";

        foreach ($data as $section => $sectionData) {
            $text .= strtoupper($section) . "\n";
            $text .= str_repeat('=', strlen($section)) . "\n\n";
            $text .= $this->arrayToTextRecursive($sectionData, 0);
            $text .= "\n\n";
        }

        return $text;
    }

    /**
     * Recursively convert array to text
     *
     * @param mixed $data
     * @param int $depth
     * @return string
     * @privacy-safe Helper for text conversion
     */
    private function arrayToTextRecursive($data, int $depth): string
    {
        $text = '';
        $indent = str_repeat('  ', $depth);

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $text .= $indent . $key . ': ';
                if (is_array($value)) {
                    $text .= "\n" . $this->arrayToTextRecursive($value, $depth + 1);
                } else {
                    $text .= $value . "\n";
                }
            }
        } else {
            $text .= $indent . $data . "\n";
        }

        return $text;
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
     * Get available data categories
     *
     * @return array
     * @privacy-safe Returns public category definitions
     */
    public function getAvailableDataCategories(): array
    {
        // 🔥 USA LA CONFIGURAZIONE ESISTENTE invece di $this->dataCategories
        $categories = config('gdpr.export.data_categories', []);

        $result = [];
        foreach ($categories as $key => $translationKey) {
            $result[$key] = [
                'name' => __($translationKey),
                'key' => $key,
                'translation_key' => $translationKey
            ];
        }

        return $result;
    }

    /**
     * @Oracode Collect Account Data
     * 🎯 Purpose: Export core account information and settings
     * 🛡️ Privacy: User's account data only
     *
     * @param User $user
     * @return array
     * @privacy-safe Returns user's account data only
     */
    private function collectAccountData(User $user): array
    {
        return [
            'account_info' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'email_verified' => !is_null($user->email_verified_at),
                'email_verified_at' => $user->email_verified_at?->toISOString(),
                'account_created' => $user->created_at->toISOString(),
                'last_updated' => $user->updated_at->toISOString(),
                'last_login' => $user->last_login_at?->toISOString()
            ],
            'account_status' => [
                'active' => true, // Se può fare export, è attivo
                'verified' => !is_null($user->email_verified_at),
                'two_factor_enabled' => !is_null($user->two_factor_secret ?? null)
            ],
            'account_metadata' => [
                'timezone' => $user->timezone ?? config('app.timezone'),
                'language' => $user->language ?? 'it',
                'created_via' => $user->created_via ?? 'web'
            ]
        ];
    }

    /**
     * @Oracode Collect User Preferences
     * 🎯 Purpose: Export user preferences and settings
     * 🛡️ Privacy: User's preferences only
     *
     * @param User $user
     * @return array
     * @privacy-safe Returns user's preferences only
     */
    private function collectPreferencesData(User $user): array
    {
        return [
            'ui_preferences' => [
                'language' => $user->language ?? 'it',
                'timezone' => $user->timezone ?? config('app.timezone'),
                'theme' => $user->theme ?? 'light',
                'notifications_enabled' => $user->notifications_enabled ?? true
            ],
            'privacy_preferences' => [
                'profile_visibility' => $user->profile_visibility ?? 'public',
                'allow_contact' => $user->allow_contact ?? true,
                'show_online_status' => $user->show_online_status ?? true
            ],
            'platform_preferences' => [
                'newsletter_subscribed' => $user->newsletter_subscribed ?? false,
                'marketing_emails' => $user->marketing_emails ?? false,
                'product_updates' => $user->product_updates ?? true
            ],
            'notification_settings' => [
                // Placeholder per future notifiche
                'email_notifications' => $user->email_notifications ?? [],
                'push_notifications' => $user->push_notifications ?? [],
                'sms_notifications' => $user->sms_notifications ?? []
            ]
        ];
    }

    /**
     * @Oracode Collect Purchases Data
     * 🎯 Purpose: Export purchase history and transaction data
     * 🛡️ Privacy: User's purchase data only
     *
     * @param User $user
     * @return array
     * @privacy-safe Returns user's purchase data only
     */
    private function collectPurchasesData(User $user): array
    {
        // Placeholder per future implementazione e-commerce
        // Al momento FlorenceEGI potrebbe non avere un sistema acquisti completo

        return [
            'purchase_summary' => [
                'total_purchases' => 0,
                'total_spent' => 0,
                'currency' => 'EUR',
                'first_purchase' => null,
                'last_purchase' => null
            ],
            'purchase_history' => [
                // Placeholder per quando implementerete il sistema acquisti
            ],
            'payment_methods' => [
                // Placeholder per metodi di pagamento salvati
            ],
            'invoices' => [
                // Placeholder per fatture
            ],
            'refunds' => [
                // Placeholder per rimborsi
            ],
            'note' => 'Purchase system not yet implemented in FlorenceEGI MVP'
        ];
    }

    /**
     * @Oracode Collect Messages Data
     * 🎯 Purpose: Export user messages and communications
     * 🛡️ Privacy: User's messages only
     *
     * @param User $user
     * @return array
     * @privacy-safe Returns user's messages only
     */
    private function collectMessagesData(User $user): array
    {
        // Placeholder per future sistema messaggi
        // Al momento FlorenceEGI potrebbe non avere un sistema messaggi

        return [
            'message_summary' => [
                'total_sent' => 0,
                'total_received' => 0,
                'conversations' => 0,
                'first_message' => null,
                'last_message' => null
            ],
            'conversations' => [
                // Placeholder per conversazioni future
            ],
            'sent_messages' => [
                // Placeholder per messaggi inviati
            ],
            'received_messages' => [
                // Placeholder per messaggi ricevuti
            ],
            'message_settings' => [
                'allow_messages' => true,
                'message_privacy' => 'contacts_only'
            ],
            'note' => 'Messaging system not yet implemented in FlorenceEGI MVP'
        ];
    }

    /**
     * @Oracode Collect Comments Data
     * 🎯 Purpose: Export user comments and reviews
     * 🛡️ Privacy: User's comments only
     *
     * @param User $user
     * @return array
     * @privacy-safe Returns user's comments only
     */
    private function collectCommentsData(User $user): array
    {
        // Placeholder per future sistema commenti

        return [
            'comment_summary' => [
                'total_comments' => 0,
                'total_reviews' => 0,
                'average_rating_given' => null,
                'first_comment' => null,
                'last_comment' => null
            ],
            'comments' => [
                // Placeholder per commenti futuri
            ],
            'reviews' => [
                // Placeholder per recensioni future
            ],
            'comment_settings' => [
                'allow_public_comments' => true,
                'moderate_comments' => false
            ],
            'note' => 'Comment system not yet implemented in FlorenceEGI MVP'
        ];
    }

    /**
     * @Oracode Collect Biography Data with GDPR Compliance
     * 🎯 Purpose: Export complete biography data including chapters and media
     * 🛡️ Privacy: Only user's own biographies with privacy level tracking
     * 🧱 Core Logic: Structured export with timeline integrity and media references
     *
     * @param User $user
     * @return array
     * @privacy-safe Returns user's biography data only
     */
    private function collectBiographyData(User $user): array
    {
        $biographies = $user->biographies()
            ->with(['chapters' => function ($query) {
                $query->orderBy('sort_order')->orderBy('date_from');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'summary' => [
                'total_biographies' => $biographies->count(),
                'public_biographies' => $biographies->where('is_public', true)->count(),
                'completed_biographies' => $biographies->where('is_completed', true)->count(),
                'total_chapters' => $biographies->sum(fn($bio) => $bio->chapters->count()),
                'export_date' => now()->toISOString()
            ],
            'biographies' => $biographies->map(function ($biography) {
                return [
                    'biography_info' => [
                        'id' => $biography->id,
                        'type' => $biography->type,
                        'title' => $biography->title,
                        'slug' => $biography->slug,
                        'excerpt' => $biography->excerpt,
                        'is_public' => $biography->is_public,
                        'is_completed' => $biography->is_completed,
                        'created_at' => $biography->created_at->toISOString(),
                        'updated_at' => $biography->updated_at->toISOString()
                    ],
                    'content' => [
                        'main_content' => $biography->content,
                        'content_length' => strlen($biography->content ?? ''),
                        'estimated_reading_time' => $biography->getEstimatedReadingTime()
                    ],
                    'settings' => $biography->settings ?? [],
                    'chapters' => $biography->chapters->map(function ($chapter) {
                        return [
                            'chapter_info' => [
                                'id' => $chapter->id,
                                'title' => $chapter->title,
                                'chapter_type' => $chapter->chapter_type,
                                'slug' => $chapter->slug,
                                'sort_order' => $chapter->sort_order,
                                'is_published' => $chapter->is_published,
                                'is_ongoing' => $chapter->is_ongoing,
                                'created_at' => $chapter->created_at->toISOString(),
                                'updated_at' => $chapter->updated_at->toISOString()
                            ],
                            'content' => [
                                'content' => $chapter->content,
                                'content_length' => strlen($chapter->content),
                                'reading_time' => $chapter->getReadingTime()
                            ],
                            'timeline' => [
                                'date_from' => $chapter->date_from?->toDateString(),
                                'date_to' => $chapter->date_to?->toDateString(),
                                'date_range_display' => $chapter->date_range_display,
                                'duration_formatted' => $chapter->duration_formatted,
                                'is_current_period' => $chapter->isCurrentPeriod()
                            ],
                            'formatting' => $chapter->formatting_data ?? [],
                            'media_info' => [
                                'total_media' => $chapter->getMedia()->count(),
                                'media_collections' => $chapter->getMedia()
                                    ->groupBy('collection_name')
                                    ->map(function ($mediaGroup, $collection) {
                                        return [
                                            'collection' => $collection,
                                            'count' => $mediaGroup->count(),
                                            'files' => $mediaGroup->map(function ($media) {
                                                return [
                                                    'filename' => $media->file_name,
                                                    'mime_type' => $media->mime_type,
                                                    'size' => $media->size,
                                                    'url' => $media->getUrl(),
                                                    'created_at' => $media->created_at->toISOString()
                                                ];
                                            })->toArray()
                                        ];
                                    })->toArray()
                            ]
                        ];
                    })->toArray(),
                    'media_summary' => [
                        'total_media' => $biography->getMedia()->count(),
                        'media_collections' => $biography->getMedia()
                            ->groupBy('collection_name')
                            ->map(function ($mediaGroup, $collection) {
                                return [
                                    'collection' => $collection,
                                    'count' => $mediaGroup->count(),
                                    'total_size' => $mediaGroup->sum('size'),
                                    'files' => $mediaGroup->map(function ($media) {
                                        return [
                                            'filename' => $media->file_name,
                                            'mime_type' => $media->mime_type,
                                            'size' => $media->size,
                                            'url' => $media->getUrl(),
                                            'created_at' => $media->created_at->toISOString()
                                        ];
                                    })->toArray()
                                ];
                            })->toArray()
                    ],
                    'privacy_info' => [
                        'visibility_level' => $biography->is_public ? 'public' : 'private',
                        'published_chapters' => $biography->chapters->where('is_published', true)->count(),
                        'draft_chapters' => $biography->chapters->where('is_published', false)->count(),
                        'gdpr_notes' => 'This biography data is exported under Article 20 GDPR - Right to data portability'
                    ]
                ];
            })->toArray(),
            'export_metadata' => [
                'legal_basis' => 'Article 20 GDPR - Right to data portability',
                'processing_purpose' => 'User data export request',
                'retention_note' => 'This export file will be automatically deleted after 30 days',
                'data_controller' => 'FlorenceEGI S.r.l.',
                'export_format_note' => 'Structured data for portability and interoperability'
            ]
        ];
    }

}
