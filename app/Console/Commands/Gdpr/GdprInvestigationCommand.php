<?php

namespace App\Console\Commands\Gdpr;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\Gdpr\ConsentService;

/**
 * @Oracode GDPR Investigation Command
 * 🎯 Purpose: Quick and reliable GDPR architecture analysis
 * 🛡️ Privacy: Read-only investigation, no data modification
 * 🧱 Core Logic: Direct database inspection with clear output
 */
class GdprInvestigationCommand extends Command
{
    protected $signature = 'gdpr:investigate';
    protected $description = 'Investigate current GDPR architecture and data relationships';

    public function handle(): int
    {
        $this->info('🔍 GDPR Architecture Investigation');
        $this->line('=====================================');

        $this->checkConsentTypes();
        $this->checkConsentVersions();
        $this->checkUserConsents();
        $this->checkUserDomainTables();
        $this->checkConsentService();
        $this->provideSummary();

        return 0;
    }

    protected function checkConsentTypes(): void
    {
        $this->line('');
        $this->info('📋 1. CONSENT_TYPES TABLE');
        $this->line('------------------------');

        try {
            $count = DB::table('consent_types')->count();
            $this->line("Records: {$count}");

            if ($count > 0) {
                $sample = DB::table('consent_types')
                    ->select('slug', 'name', 'legal_basis', 'is_required')
                    ->limit(3)
                    ->get();

                $this->line('Sample records:');
                foreach ($sample as $record) {
                    $this->line("  - {$record->slug} ({$record->legal_basis})");
                }
            } else {
                $this->warn('⚠️  consent_types is EMPTY - not being used as master');
            }

            // Check for 'category' column that GdprSeeder expects
            $columns = DB::select("SHOW COLUMNS FROM consent_types LIKE '%category%'");
            if (empty($columns)) {
                $this->warn('⚠️  No "category" column found - explains GdprSeeder error');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error checking consent_types: ' . $e->getMessage());
        }
    }

    protected function checkConsentVersions(): void
    {
        $this->line('');
        $this->info('📋 2. CONSENT_VERSIONS TABLE');
        $this->line('---------------------------');

        try {
            $count = DB::table('consent_versions')->count();
            $this->line("Records: {$count}");

            if ($count > 0) {
                $versions = DB::table('consent_versions')
                    ->select('version', 'consent_types', 'is_active', 'effective_date')
                    ->get();

                foreach ($versions as $version) {
                    $this->line("Version: {$version->version} (Active: {$version->is_active})");
                    $types = json_decode($version->consent_types, true);
                    if (is_array($types)) {
                        $this->line("  Types: " . implode(', ', $types));
                    }
                    $this->line("  Date: {$version->effective_date}");
                }

                if (in_array('allow_personal_data_processing', $types ?? [])) {
                    $this->info('✅ allow_personal_data_processing found in consent_versions');
                } else {
                    $this->warn('⚠️  allow_personal_data_processing NOT found in consent_versions');
                }
            }

        } catch (\Exception $e) {
            $this->error('❌ Error checking consent_versions: ' . $e->getMessage());
        }
    }

    protected function checkUserConsents(): void
    {
        $this->line('');
        $this->info('📋 3. USER_CONSENTS TABLE');
        $this->line('------------------------');

        try {
            $total = DB::table('user_consents')->count();
            $this->line("Total records: {$total}");

            if ($total > 0) {
                // Check relationship patterns
                $patterns = DB::table('user_consents')
                    ->select('consent_type', 'consent_version_id', DB::raw('COUNT(*) as count'))
                    ->groupBy('consent_type', 'consent_version_id')
                    ->orderBy('count', 'desc')
                    ->get();

                $this->line('Relationship patterns:');
                foreach ($patterns as $pattern) {
                    $versionText = $pattern->consent_version_id ? "v{$pattern->consent_version_id}" : 'NULL';
                    $this->line("  - {$pattern->consent_type} → {$versionText} ({$pattern->count} records)");
                }

                // Check for NULL version IDs
                $nullVersions = DB::table('user_consents')
                    ->whereNull('consent_version_id')
                    ->count();

                if ($nullVersions > 0) {
                    $this->warn("⚠️  {$nullVersions} records have NULL consent_version_id (direct type usage)");
                }

                // Check for allow_personal_data_processing
                $personalDataConsents = DB::table('user_consents')
                    ->where('consent_type', 'allow_personal_data_processing')
                    ->count();

                if ($personalDataConsents > 0) {
                    $this->info("✅ {$personalDataConsents} allow_personal_data_processing consents found");
                } else {
                    $this->warn('⚠️  No allow_personal_data_processing consents in user_consents');
                }
            }

        } catch (\Exception $e) {
            $this->error('❌ Error checking user_consents: ' . $e->getMessage());
        }
    }

    protected function checkUserDomainTables(): void
    {
        $this->line('');
        $this->info('📋 4. USER DOMAIN TABLES');
        $this->line('----------------------');

        try {
            // Check user_personal_data
            $personalDataCount = DB::table('user_personal_data')->count();
            $this->line("user_personal_data records: {$personalDataCount}");

            if ($personalDataCount > 0) {
                $allowProcessingTrue = DB::table('user_personal_data')
                    ->where('allow_personal_data_processing', true)
                    ->count();

                $allowProcessingFalse = DB::table('user_personal_data')
                    ->where('allow_personal_data_processing', false)
                    ->count();

                $this->line("  allow_personal_data_processing: TRUE={$allowProcessingTrue}, FALSE={$allowProcessingFalse}");

                $withPurposes = DB::table('user_personal_data')
                    ->whereNotNull('processing_purposes')
                    ->count();

                $this->line("  Records with processing_purposes: {$withPurposes}");
            }

            // Check users table GDPR fields
            $usersWithConsentSummary = DB::table('users')
                ->whereNotNull('consent_summary')
                ->count();

            $this->line("users with consent_summary: {$usersWithConsentSummary}");

        } catch (\Exception $e) {
            $this->error('❌ Error checking domain tables: ' . $e->getMessage());
        }
    }

    protected function checkConsentService(): void
    {
        $this->line('');
        $this->info('📋 5. CONSENTSERVICE ANALYSIS');
        $this->line('---------------------------');

        try {
            $consentService = app(ConsentService::class);
            $serviceTypes = $consentService->getAvailableConsentTypes();

            $this->line('ConsentService defines:');
            foreach (array_keys($serviceTypes) as $type) {
                $this->line("  - {$type}");
            }

            // Compare with database
            $dbTypes = DB::table('user_consents')
                ->select('consent_type')
                ->distinct()
                ->pluck('consent_type')
                ->toArray();

            $this->line('Database user_consents has:');
            foreach ($dbTypes as $type) {
                $this->line("  - {$type}");
            }

            // Check for mismatches
            $serviceKeys = array_keys($serviceTypes);
            $missingInDb = array_diff($serviceKeys, $dbTypes);
            $extraInDb = array_diff($dbTypes, $serviceKeys);

            if (!empty($missingInDb)) {
                $this->warn('⚠️  ConsentService types MISSING in database:');
                foreach ($missingInDb as $missing) {
                    $this->line("    - {$missing}");
                }
            }

            if (!empty($extraInDb)) {
                $this->warn('⚠️  Database types NOT in ConsentService:');
                foreach ($extraInDb as $extra) {
                    $this->line("    - {$extra}");
                }
            }

            if (empty($missingInDb) && empty($extraInDb)) {
                $this->info('✅ ConsentService and database are in sync');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error checking ConsentService: ' . $e->getMessage());
        }
    }

    protected function provideSummary(): void
    {
        $this->line('');
        $this->info('🎯 ARCHITECTURE SUMMARY');
        $this->line('=====================');

        $consentTypesCount = DB::table('consent_types')->count();
        $consentVersionsCount = DB::table('consent_versions')->count();
        $userConsentsCount = DB::table('user_consents')->count();

        if ($consentTypesCount == 0 && $consentVersionsCount > 0) {
            $this->line('📊 PATTERN IDENTIFIED: ConsentService → consent_versions (bypassing consent_types)');
            $this->info('✅ This explains why Step 3 works and Step 4 fails');
        }

        if ($userConsentsCount > 0) {
            $nullVersionCount = DB::table('user_consents')->whereNull('consent_version_id')->count();
            if ($nullVersionCount > 0) {
                $this->line('📊 DUAL PATTERN: Both FK and direct consent_type usage detected');
            }
        }

        $this->line('');
        $this->info('💡 RECOMMENDATION:');
        if ($consentTypesCount == 0) {
            $this->line('- consent_types is unused - can be bypassed or removed');
            $this->line('- Current ConsentService → consent_versions pattern is working');
            $this->line('- Fix GdprSeeder to skip consent_types population');
        } else {
            $this->line('- consent_types has data - investigate integration path');
            $this->line('- Consider unified architecture decision');
        }
    }
}
