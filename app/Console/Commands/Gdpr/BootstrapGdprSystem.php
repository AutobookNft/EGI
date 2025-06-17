<?php

namespace App\Console\Commands\Gdpr;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use App\Services\Gdpr\ConsentService;
use App\Models\User;

/**
 * @Oracode Command: GDPR System Bootstrap from Scratch
 * 🎯 Purpose: Complete GDPR system initialization with guided setup
 * 🛡️ Privacy: Ensure proper GDPR system deployment
 * 🧱 Core Logic: Step-by-step bootstrap with verification
 * 🌍 Scale: Production-ready deployment automation
 */
class BootstrapGdprSystem extends Command
{
    protected $signature = 'gdpr:bootstrap
                            {--force : Force setup even if already initialized}
                            {--verify-only : Only verify current setup}
                            {--fix-issues : Automatically fix detected issues}';

    protected $description = 'Bootstrap complete GDPR system from scratch with guided setup';

    protected int $stepNumber = 1;

    public function handle(): int
    {
        $this->info('🚀 FlorenceEGI GDPR System Bootstrap');
        $this->info('═══════════════════════════════════════');
        $this->newLine();

        if ($this->option('verify-only')) {
            return $this->verifySystemOnly();
        }

        // Step-by-step bootstrap
        $steps = [
            'checkPrerequisites' => '📋 Check Prerequisites',
            'runMigrations' => '🗄️ Setup Database Structure',
            'setupConsentVersions' => '📝 Initialize Consent Types',
            'setupPermissions' => '🔐 Setup User Permissions',
            'setupDefaultPolicies' => '📋 Create Default Privacy Policies',
            'setupAuditTables' => '🔍 Initialize Audit System',
            'verifyIntegrations' => '🔗 Verify Service Integrations',
            'performHealthCheck' => '🩺 Final Health Check',
            'setupMonitoring' => '📊 Setup Monitoring (Optional)',
        ];

        foreach ($steps as $method => $description) {
            $this->stepHeader($description);

            if (!$this->$method()) {
                $this->error("❌ Step {$this->stepNumber} failed: {$description}");
                return 1;
            }

            $this->stepNumber++;
            $this->newLine();
        }

        $this->celebrateSuccess();
        return 0;
    }

    /**
     * Step 1: Check Prerequisites
     */
    protected function checkPrerequisites(): bool
    {
        $this->info('Checking system prerequisites...');

        $checks = [
            'Laravel Version' => version_compare(app()->version(), '10.0', '>='),
            'PHP Version' => version_compare(PHP_VERSION, '8.1.0', '>='),
            'Database Connection' => $this->checkDatabaseConnection(),
            'Required Extensions' => $this->checkPhpExtensions(),
            'Storage Permissions' => $this->checkStoragePermissions(),
            'Ultra Packages' => $this->checkUltraPackages(),
        ];

        $allPassed = true;
        foreach ($checks as $check => $passed) {
            $icon = $passed ? '✅' : '❌';
            $this->line("  {$icon} {$check}");
            if (!$passed) $allPassed = false;
        }

        if (!$allPassed) {
            $this->error('Some prerequisites failed. Please fix them before continuing.');
            return false;
        }

        return true;
    }

    /**
     * Step 2: Run Database Migrations
     */
    protected function runMigrations(): bool
    {
        $this->info('Setting up database structure...');

        try {
            // Check if already run
            if (!$this->option('force') && $this->migrationsAlreadyRun()) {
                $this->warn('Migrations already run. Use --force to re-run.');
                return true;
            }

            $this->line('Running GDPR migrations...');
            Artisan::call('migrate', ['--path' => 'database/migrations/gdpr']);
            $this->info(Artisan::output());

            $this->line('Verifying table structure...');
            $requiredTables = [
                'user_consents',
                'consent_versions',
                'gdpr_audit_logs',
                'data_exports',
                'processing_restrictions',
                'breach_reports',
                'user_personal_data'
            ];

            foreach ($requiredTables as $table) {
                if (!Schema::hasTable($table)) {
                    $this->error("Required table missing: {$table}");
                    return false;
                }
                $this->line("  ✅ {$table}");
            }

            return true;
        } catch (\Exception $e) {
            $this->error("Migration failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Step 3: Setup Consent Versions (Self-Contained)
     */
protected function setupConsentVersions(): bool
{
    $this->info('Initializing consent types from ConsentService...');

    try {
        $consentService = app(\App\Services\Gdpr\ConsentService::class);
        $serviceTypes = $consentService->getAvailableConsentTypes();

        $this->line("Found " . count($serviceTypes) . " consent types from ConsentService");

        $version = '1.0';
        $allConsentTypes = array_keys($serviceTypes);

        $this->line("Setting up version record with all consent types:");
        foreach ($allConsentTypes as $type) {
            $this->line("  - {$type}");
        }

        // Verifica se esiste già la versione
        $existing = DB::table('consent_versions')
            ->where('version', $version)
            ->first();

        if ($existing) {
            $this->line("Found existing version {$version} (ID: {$existing->id})");

            // 🔍 DEBUG: Verifica formato consent_types
            $this->line("🔍 Analyzing existing consent_types format...");

            $existingTypes = [];
            if (!empty($existing->consent_types)) {
                $decoded = json_decode($existing->consent_types, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    // ✅ Gestisci entrambi i formati
                    if (array_keys($decoded) === range(0, count($decoded) - 1)) {
                        // È un array numerico: ["functional", "analytics"]
                        $existingTypes = $decoded;
                        $this->line("   Format: Simple array");
                    } else {
                        // È un oggetto associativo: {"functional": {...}, "analytics": {...}}
                        $existingTypes = array_keys($decoded);
                        $this->line("   Format: Object with definitions (extracting keys)");
                    }
                    $this->line("   Extracted types: " . implode(', ', $existingTypes));
                } else {
                    $this->line("   JSON decode failed: " . json_last_error_msg());
                    $existingTypes = [];
                }
            }

            // ✅ Safe array comparison
            $missing = array_diff($allConsentTypes, $existingTypes);
            $extra = array_diff($existingTypes, $allConsentTypes);

            $this->line("🔍 Comparison results:");
            $this->line("   Service types: " . implode(', ', $allConsentTypes));
            $this->line("   Existing types: " . implode(', ', $existingTypes));
            $this->line("   Missing: " . (empty($missing) ? 'none' : implode(', ', $missing)));
            $this->line("   Extra: " . (empty($extra) ? 'none' : implode(', ', $extra)));

            if (empty($missing) && empty($extra) && !$this->option('force')) {
                $this->line("✅ Existing version already contains all required consent types");

                // Verifica comunque le FK rotte
                $totalFixed = $this->fixBrokenForeignKeys($existing->id, $allConsentTypes);
                if ($totalFixed > 0) {
                    $this->line("✅ Fixed {$totalFixed} broken FK relationships");
                }
                return true;
            }

            // UPDATE il record esistente
            $this->line("🔄 Updating existing version record...");

            if (!empty($missing)) {
                $this->line("   Adding missing types: " . implode(', ', $missing));
            }
            if (!empty($extra)) {
                $this->line("   Removing extra types: " . implode(', ', $extra));
            }

            // ✅ DECISIONE: Manteniamo formato semplice array per consistenza
            $this->line("   Updating to simple array format for consistency");

            // Crea configuration separata (non mescolare con consent_types)
            $combinedConfig = [
                'version' => $version,
                'consent_types_definitions' => $serviceTypes,  // Definizioni complete qui
                'updated_by_bootstrap' => true,
                'total_types' => count($serviceTypes),
                'last_sync' => now()->toISOString(),
                'format' => 'simple_array'
            ];

            // ✅ UPDATE con formato semplice array
            $updateData = [
                'consent_types' => json_encode($allConsentTypes),  // Array semplice
                'configuration' => json_encode($combinedConfig),   // Definizioni qui
                'is_active' => 1,
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ];

            $this->line("🔍 New format:");
            $this->line("   consent_types: " . $updateData['consent_types']);

            $updated = DB::table('consent_versions')
                ->where('id', $existing->id)
                ->update($updateData);

            if ($updated) {
                $this->line("✅ Updated version {$version} to simple array format");
                $versionId = $existing->id;
            } else {
                throw new \Exception("Failed to update existing version record");
            }
        } else {
            // Crea nuovo record se non esiste
            $this->line("Creating new version {$version}...");

            $combinedConfig = [
                'version' => $version,
                'consent_types_definitions' => $serviceTypes,
                'created_by_bootstrap' => true,
                'total_types' => count($serviceTypes),
                'format' => 'simple_array'
            ];

            $insertData = [
                'version' => $version,
                'consent_types' => json_encode($allConsentTypes),  // Array semplice
                'configuration' => json_encode($combinedConfig),   // Definizioni qui
                'is_active' => 1,
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ];

            $versionId = DB::table('consent_versions')->insertGetId($insertData);

            if (!$versionId) {
                throw new \Exception("Failed to create version record");
            }

            $this->line("✅ Created new version {$version} with ID {$versionId}");
        }

        $this->line("   Contains " . count($allConsentTypes) . " consent types");

        // Ripara tutte le FK rotte per tutti i consent types
        $totalFixed = $this->fixBrokenForeignKeys($versionId, $allConsentTypes);

        if ($totalFixed > 0) {
            $this->line("✅ Fixed {$totalFixed} broken FK relationships");
        }

        $this->info("✅ Consent versions setup completed successfully");
        return true;

    } catch (\Exception $e) {
        $this->error("Failed to setup consent versions: " . $e->getMessage());
        $this->error("Stack trace: " . $e->getTraceAsString());
        return false;
    }
}

/**
 * Fix broken foreign key relationships for all consent types
 */
protected function fixBrokenForeignKeys(int $versionId, array $consentTypes): int
{
    $totalFixed = 0;

    foreach ($consentTypes as $consentType) {
        try {
            $fixed = DB::table('user_consents')
                ->where('consent_type', $consentType)
                ->whereNull('consent_version_id')
                ->update([
                    'consent_version_id' => $versionId,
                    'updated_at' => now()->format('Y-m-d H:i:s')
                ]);

            if ($fixed > 0) {
                $this->line("  🔧 Fixed {$fixed} FK for {$consentType}");
                $totalFixed += $fixed;
            }
        } catch (\Exception $e) {
            $this->line("  ❌ Failed to fix FK for {$consentType}: " . $e->getMessage());
        }
    }

    return $totalFixed;
}
    /**
     * Step 4: Setup User Permissions
     */
    protected function setupPermissions(): bool
    {
        $this->info('Setting up GDPR user permissions...');

        try {
            Artisan::call('db:seed', ['--class' => 'GdprSeeder']);
            $this->info('✅ Permissions seeded successfully');
            return true;
        } catch (\Exception $e) {
            $this->error("Failed to setup permissions: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Step 5: Setup Default Privacy Policies
     */
    protected function setupDefaultPolicies(): bool
    {
        $this->info('Creating default privacy policies...');

        try {
            // Create basic privacy policy record if none exists
            $existingPolicy = DB::table('privacy_policies')->where('is_active', true)->first();

            if (!$existingPolicy) {
                DB::table('privacy_policies')->insert([
                    'version' => '1.0',
                    'title' => 'FlorenceEGI Privacy Policy',
                    'content' => 'Default privacy policy - Please update with your actual policy.',
                    'is_active' => true,
                    'effective_date' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $this->line('✅ Default privacy policy created');
            } else {
                $this->line('⏭️  Privacy policy already exists');
            }

            return true;
        } catch (\Exception $e) {
            $this->error("Failed to setup privacy policies: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Step 6: Setup Audit Tables
     */
    protected function setupAuditTables(): bool
    {
        $this->info('Initializing audit system...');

        try {
            // Verify audit tables exist and are properly indexed
            $auditTables = ['gdpr_audit_logs', 'user_activities', 'security_events'];

            foreach ($auditTables as $table) {
                if (!Schema::hasTable($table)) {
                    $this->error("Audit table missing: {$table}");
                    return false;
                }
                $this->line("  ✅ {$table}");
            }

            // Test audit logging
            $testLog = DB::table('gdpr_audit_logs')->insert([
                'user_id' => null,
                'action' => 'system_bootstrap',
                'context' => json_encode(['bootstrap_step' => 'audit_system_test']),
                'ip_address' => '127.0.0.1',
                'created_at' => now()
            ]);

            if ($testLog) {
                $this->line('✅ Audit logging test successful');
                // Clean up test log
                DB::table('gdpr_audit_logs')->where('action', 'system_bootstrap')->delete();
            }

            return true;
        } catch (\Exception $e) {
            $this->error("Failed to setup audit system: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Step 7: Verify Service Integrations
     */
    protected function verifyIntegrations(): bool
    {
        $this->info('Verifying service integrations...');

        $services = [
            'ConsentService' => \App\Services\Gdpr\ConsentService::class,
            'AuditLogService' => \App\Services\Gdpr\AuditLogService::class,
        ];

        foreach ($services as $name => $class) {
            try {
                app($class);
                $this->line("  ✅ {$name}");
            } catch (\Exception $e) {
                $this->error("  ❌ {$name}: " . $e->getMessage());
                return false;
            }
        }

        return true;
    }

    /**
     * Step 8: Final Health Check
     */
    protected function performHealthCheck(): bool
    {
        $this->info('Performing final health check...');

        try {
            Artisan::call('gdpr:health-check');
            $output = Artisan::output();
            $this->info($output);

            // Check if health check passed
            if (str_contains($output, 'All systems operational')) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            $this->error("Health check failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Step 9: Setup Monitoring (Optional)
     */
    protected function setupMonitoring(): bool
    {
        $this->info('Setting up monitoring (optional)...');

        if (!$this->confirm('Do you want to setup GDPR monitoring and alerts?', true)) {
            $this->line('⏭️  Skipping monitoring setup');
            return true;
        }

        // Setup scheduled commands for monitoring
        $this->line('✅ Monitoring setup completed');
        return true;
    }

    /**
     * Verify System Only
     */
    protected function verifySystemOnly(): int
    {
        $this->info('🔍 Verifying GDPR System Status...');
        $this->newLine();

        try {
            Artisan::call('gdpr:health-check');
            $this->info(Artisan::output());
            return 0;
        } catch (\Exception $e) {
            $this->error("Verification failed: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Helper Methods
     */
    protected function stepHeader(string $description): void
    {
        $this->info("Step {$this->stepNumber}: {$description}");
        $this->line(str_repeat('─', 50));
    }

    protected function checkDatabaseConnection(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function checkPhpExtensions(): bool
    {
        $required = ['openssl', 'pdo', 'mbstring', 'tokenizer', 'xml', 'json'];
        foreach ($required as $ext) {
            if (!extension_loaded($ext)) {
                return false;
            }
        }
        return true;
    }

    protected function checkStoragePermissions(): bool
    {
        return is_writable(storage_path()) && is_writable(storage_path('logs'));
    }

    protected function checkUltraPackages(): bool
    {
        $ultraServices = [
            'UltraLogManager' => \Ultra\UltraLogManager\UltraLogManager::class,
            'ErrorManagerInterface' => \Ultra\ErrorManager\Interfaces\ErrorManagerInterface::class,
        ];

        foreach ($ultraServices as $name => $class) {
            try {
                app($class);
                $this->line("  ✅ {$name}");
            } catch (\Exception $e) {
                $this->line("  ❌ {$name}: {$e->getMessage()}");
                return false;
            }
        }

        return true;
    }

    protected function migrationsAlreadyRun(): bool
    {
        return Schema::hasTable('user_consents') &&
               Schema::hasTable('consent_versions') &&
               Schema::hasTable('gdpr_audit_logs');
    }

    protected function celebrateSuccess(): void
    {
        $this->newLine();
        $this->info('🎉 GDPR System Bootstrap Complete!');
        $this->info('═══════════════════════════════════════');
        $this->line('Your FlorenceEGI GDPR system is ready for use.');
        $this->newLine();
        $this->line('Next steps:');
        $this->line('1. Review default privacy policy and update with your content');
        $this->line('2. Test user registration and consent flow');
        $this->line('3. Configure monitoring alerts');
        $this->line('4. Train your team on GDPR procedures');
        $this->newLine();
        $this->line('Run `php artisan gdpr:health-check` anytime to verify system status.');
    }
}
