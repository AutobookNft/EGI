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
     * Get available data categories
     *
     * @return array
     * @privacy-safe Returns public category definitions
     */
    public function getAvailableDataCategories(): array
    {
        return $this->dataCategories;
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

        $totalCategories = count($categories);
        $currentCategory = 0;

        foreach ($categories as $category) {
            $this->updateExportProgress($export, ($currentCategory / $totalCategories) * 80);

            switch ($category) {
                case 'profile':
                    $data['profile'] = $this->collectProfileData($user);
                    break;
                case 'activities':
                    $data['activities'] = $this->collectActivityData($user);
                    break;
                case 'collections':
                    $data['collections'] = $this->collectCollectionData($user);
                    break;
                case 'wallet':
                    $data['wallet'] = $this->collectWalletData($user);
                    break;
                case 'consents':
                    $data['consents'] = $this->collectConsentData($user);
                    break;
                case 'audit':
                    $data['audit'] = $this->collectAuditData($user);
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
}
