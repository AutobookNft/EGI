<?php

/**
 * @Oracode DatabaseSeeder: Atomic Transaction Seeding
 * 🎯 Purpose: All-or-nothing seeding - se fallisce uno, rollback tutto
 * 🔒 Safety: Database transaction protects data integrity
 * 🧱 Core Logic: Single transaction per tutti i seeder
 *
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (Atomic Seeding)
 * @date 2025-07-20
 * @purpose Atomic seeding transaction for FlorenceEGI
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seeder execution order (CRITICAL - rispettare ordine dipendenze)
     */
    private array $seederSequence = [
        RolesAndPermissionsSeeder::class,    // PRIMO - crea ruoli e permessi
        SystemUsersSeeder::class,            // SECONDO - crea utenti di sistema (usa ruoli)
        ConsentTypeSeeder::class,            // TERZO - tipi consenso GDPR
        IconSeeder::class,                   // QUARTO - icone sistema
        FlorenceEgiPrivacyPolicySeeder::class, // QUINTO - privacy policy
        // FakeUserSeeder::class,            // OPZIONALE - solo per development
    ];

    /**
     * Seed the application's database with atomic transaction
     *
     * @return void
     * @throws \Exception Se qualsiasi seeder fallisce
     */
    public function run(): void
    {
        $this->command->info('🔒 Starting ATOMIC seeding transaction...');
        $this->command->info('⚠️  If ANY seeder fails, ALL changes will be rolled back!');

        // Start timing
        $startTime = microtime(true);

        try {
            // SINGLE ATOMIC TRANSACTION per tutti i seeder
            DB::transaction(function () {
                $this->command->info('📊 Seeding sequence:');

                foreach ($this->seederSequence as $index => $seederClass) {
                    $step = $index + 1;
                    $total = count($this->seederSequence);

                    $this->command->info("🔄 Step {$step}/{$total}: {$seederClass}");

                    try {
                        // Execute seeder inside transaction
                        $this->call($seederClass);
                        $this->command->info("✅ Step {$step}/{$total}: Completed successfully");

                    } catch (\Exception $e) {
                        $this->command->error("💥 Step {$step}/{$total}: FAILED - {$e->getMessage()}");

                        // Log detailed error
                        Log::error('[DatabaseSeeder] Seeder failed in atomic transaction', [
                            'seeder_class' => $seederClass,
                            'step' => $step,
                            'total_steps' => $total,
                            'error_message' => $e->getMessage(),
                            'error_trace' => $e->getTraceAsString(),
                            'transaction_will_rollback' => true,
                        ]);

                        // Re-throw per trigger rollback
                        throw new \Exception(
                            "Seeder {$seederClass} failed: {$e->getMessage()}",
                            0,
                            $e
                        );
                    }
                }

                $this->command->info('🎯 All seeders completed successfully within transaction');

            }, 3); // 3 retry attempts per transaction deadlocks

            // Calculate execution time
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            // SUCCESS - Transaction committed
            $this->command->info('');
            $this->command->info('🎉 ATOMIC SEEDING COMPLETED SUCCESSFULLY!');
            $this->command->info('═══════════════════════════════════════════');
            $this->command->info("⏱️  Execution time: {$executionTime} seconds");
            $this->command->info('✅ All changes committed to database');
            $this->command->info('🛡️ Database integrity maintained');

            // Log success
            Log::info('[DatabaseSeeder] Atomic seeding completed successfully', [
                'seeders_executed' => $this->seederSequence,
                'execution_time_seconds' => $executionTime,
                'transaction_committed' => true,
            ]);

        } catch (\Exception $e) {
            // Calculate execution time anche per errori
            $endTime = microtime(true);
            $executionTime = round($endTime - $startTime, 2);

            // FAILURE - Transaction automatically rolled back
            $this->command->error('');
            $this->command->error('💥 ATOMIC SEEDING FAILED!');
            $this->command->error('═══════════════════════════════');
            $this->command->error("⏱️  Failed after: {$executionTime} seconds");
            $this->command->error('🔄 ALL changes have been ROLLED BACK');
            $this->command->error('🛡️ Database returned to original state');
            $this->command->error("❌ Error: {$e->getMessage()}");

            // Log failure con dettagli completi
            Log::error('[DatabaseSeeder] Atomic seeding transaction failed', [
                'error_message' => $e->getMessage(),
                'execution_time_seconds' => $executionTime,
                'seeders_sequence' => $this->seederSequence,
                'transaction_rolled_back' => true,
                'database_state' => 'reverted_to_original',
                'error_trace' => $e->getTraceAsString(),
            ]);

            // Re-throw per preservare exit code
            throw $e;
        }
    }

    /**
     * Display seeding summary info
     */
    private function displaySeedingSummary(): void
    {
        $this->command->info('');
        $this->command->info('📋 SEEDING SUMMARY:');
        $this->command->info('═══════════════════');

        foreach ($this->seederSequence as $index => $seederClass) {
            $step = $index + 1;
            $name = class_basename($seederClass);
            $this->command->info("  {$step}. {$name}");
        }

        $this->command->info('');
        $this->command->info('🔒 Transaction mode: ATOMIC (all-or-nothing)');
        $this->command->info('🛡️ Rollback: Automatic on ANY failure');
    }
}
