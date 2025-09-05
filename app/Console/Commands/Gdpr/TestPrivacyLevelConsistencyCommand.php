<?php

namespace App\Console\Commands\Gdpr;

use Illuminate\Console\Command;
use App\Enums\Gdpr\PrivacyLevel;
use App\Enums\Gdpr\GdprActivityCategory;
use App\Services\Gdpr\ConsentService;
use \ReflectionClass;

/**
 * @package App\Console\Commands\Gdpr
 * @author AI Partner OS2.0-Compliant for Fabio Cherici  
 * @version 1.0.0 (FlorenceEGI MVP - Privacy Level Consistency Test)
 * @os2-pillars Explicit,Coherent,Simple,Secure
 *
 * Comando per testare la coerenza del sistema dei livelli di privacy.
 * Verifica che tutti i componenti utilizzino lo stesso enum PrivacyLevel.
 */
class TestPrivacyLevelConsistencyCommand extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gdpr:test-privacy-consistency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test privacy level consistency across the GDPR system';

    /**
     * Execute the console command.
     */
    public function handle() {
        $this->info('🔍 Testing Privacy Level Consistency...');
        $this->newLine();

        $allTestsPassed = true;

        // Test 1: PrivacyLevel enum values
        $allTestsPassed &= $this->testPrivacyLevelEnum();

        // Test 2: GdprActivityCategory integration
        $allTestsPassed &= $this->testGdprActivityCategoryIntegration();

        // Test 3: ConsentService integration
        $allTestsPassed &= $this->testConsentServiceIntegration();

        // Test 4: Retention days consistency
        $allTestsPassed &= $this->testRetentionDaysConsistency();

        $this->newLine();

        if ($allTestsPassed) {
            $this->info('✅ All privacy level consistency tests passed!');
            return 0;
        } else {
            $this->error('❌ Some tests failed. Please review the output above.');
            return 1;
        }
    }

    /**
     * Test PrivacyLevel enum basic functionality
     */
    private function testPrivacyLevelEnum(): bool {
        $this->info('🧪 Test 1: PrivacyLevel Enum Functionality');

        $passed = true;
        $expectedLevels = ['standard', 'high', 'critical', 'immutable'];
        $actualLevels = collect(PrivacyLevel::cases())->pluck('value')->toArray();

        if ($expectedLevels !== $actualLevels) {
            $this->error('  ❌ Expected levels: ' . implode(', ', $expectedLevels));
            $this->error('  ❌ Actual levels: ' . implode(', ', $actualLevels));
            $passed = false;
        } else {
            $this->info('  ✅ All expected privacy levels present');
        }

        // Test retention days method
        foreach (PrivacyLevel::cases() as $level) {
            $days = $level->retentionDays();
            if ($days <= 0) {
                $this->error("  ❌ Invalid retention days for {$level->value}: {$days}");
                $passed = false;
            }
        }

        if ($passed) {
            $this->info('  ✅ All retention days are valid');
        }

        $this->newLine();
        return $passed;
    }

    /**
     * Test GdprActivityCategory integration
     */
    private function testGdprActivityCategoryIntegration(): bool {
        $this->info('🧪 Test 2: GdprActivityCategory Integration');

        $passed = true;

        foreach (GdprActivityCategory::cases() as $category) {
            try {
                $privacyLevel = $category->privacyLevel();

                if (!($privacyLevel instanceof PrivacyLevel)) {
                    $this->error("  ❌ {$category->value} does not return PrivacyLevel enum");
                    $passed = false;
                    continue;
                }

                $retentionDays = $category->retentionDays();
                $expectedDays = $privacyLevel->retentionDays();

                if ($retentionDays !== $expectedDays) {
                    $this->error("  ❌ {$category->value}: retention mismatch ({$retentionDays} vs {$expectedDays})");
                    $passed = false;
                }
            } catch (\Exception $e) {
                $this->error("  ❌ {$category->value}: " . $e->getMessage());
                $passed = false;
            }
        }

        if ($passed) {
            $this->info('  ✅ All GdprActivityCategory instances use PrivacyLevel enum correctly');
        }

        $this->newLine();
        return $passed;
    }

    /**
     * Test ConsentService integration
     */
    private function testConsentServiceIntegration(): bool {
        $this->info('🧪 Test 3: ConsentService Integration');

        $passed = true;

        try {
            $service = app(ConsentService::class);
            $reflection = new ReflectionClass($service);
            $getPrivacyLevelMethod = $reflection->getMethod('getPrivacyLevel');
            $getPrivacyLevelMethod->setAccessible(true);

            $testConsentTypes = [
                'collaboration_participation',
                'allow-personal-data-processing',
                'analytics',
                'marketing'
            ];

            foreach ($testConsentTypes as $consentType) {
                $privacyLevel = $getPrivacyLevelMethod->invoke($service, $consentType);

                if (!($privacyLevel instanceof PrivacyLevel)) {
                    $this->error("  ❌ ConsentService: {$consentType} does not return PrivacyLevel enum");
                    $passed = false;
                }
            }

            if ($passed) {
                $this->info('  ✅ ConsentService uses PrivacyLevel enum correctly');
            }
        } catch (\Exception $e) {
            $this->error('  ❌ ConsentService test failed: ' . $e->getMessage());
            $passed = false;
        }

        $this->newLine();
        return $passed;
    }

    /**
     * Test retention days consistency across components
     */
    private function testRetentionDaysConsistency(): bool {
        $this->info('🧪 Test 4: Retention Days Consistency');

        $passed = true;

        // Expected retention periods
        $expectedRetention = [
            'standard' => 730,   // 2 years
            'high' => 1095,      // 3 years  
            'critical' => 2555,  // 7 years
            'immutable' => 3650, // 10 years
        ];

        foreach (PrivacyLevel::cases() as $level) {
            $actualDays = $level->retentionDays();
            $expectedDays = $expectedRetention[$level->value];

            if ($actualDays !== $expectedDays) {
                $this->error("  ❌ {$level->value}: expected {$expectedDays} days, got {$actualDays} days");
                $passed = false;
            }
        }

        if ($passed) {
            $this->info('  ✅ All retention periods match expected values');
        }

        // Show retention period summary
        $this->newLine();
        $this->info('📊 Retention Period Summary:');
        $headers = ['Privacy Level', 'Retention Days', 'Years', 'GDPR Audit'];
        $rows = [];

        foreach (PrivacyLevel::cases() as $level) {
            $days = $level->retentionDays();
            $years = round($days / 365, 1);
            $audit = $level->requiresGdprAudit() ? 'Yes' : 'No';

            $rows[] = [
                ucfirst($level->value),
                number_format($days),
                $years,
                $audit
            ];
        }

        $this->table($headers, $rows);

        $this->newLine();
        return $passed;
    }
}
