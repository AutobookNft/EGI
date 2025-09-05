<?php

namespace App\Console\Commands\Gdpr;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Enums\Gdpr\PrivacyLevel;
use App\Enums\Gdpr\GdprActivityCategory;

/**
 * @package App\Console\Commands\Gdpr
 * @author AI Partner OS2.0-Compliant for Fabio Cherici  
 * @version 1.0.0 (FlorenceEGI MVP - Privacy Level Migration)
 * @os2-pillars Explicit,Coherent,Simple,Secure
 *
 * Comando per migrare i livelli di privacy esistenti nel database.
 * Allinea user_activities table con il nuovo enum PrivacyLevel.
 */
class MigratePrivacyLevelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gdpr:migrate-privacy-levels {--dry-run : Preview changes without executing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing privacy levels in user_activities table to use PrivacyLevel enum values';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting privacy level migration...');
        
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made to the database');
        }

        // Check if user_activities table exists
        if (!DB::getSchemaBuilder()->hasTable('user_activities')) {
            $this->error('user_activities table does not exist. Migration aborted.');
            return 1;
        }

        // Count records to migrate
        $totalRecords = DB::table('user_activities')->count();
        $this->info("Found {$totalRecords} records in user_activities table");

        if ($totalRecords == 0) {
            $this->info('No records to migrate. Migration completed.');
            return 0;
        }

        // Update privacy levels based on activity category
        $updated = 0;
        $bar = $this->output->createProgressBar($totalRecords);
        $bar->start();

        DB::table('user_activities')->orderBy('id')->chunk(100, function ($activities) use (&$updated, $dryRun, $bar) {
            foreach ($activities as $activity) {
                $newPrivacyLevel = $this->determinePrivacyLevel($activity->category);
                
                if ($activity->privacy_level !== $newPrivacyLevel) {
                    if (!$dryRun) {
                        DB::table('user_activities')
                            ->where('id', $activity->id)
                            ->update([
                                'privacy_level' => $newPrivacyLevel,
                                'updated_at' => now()
                            ]);
                    }
                    $updated++;
                }
                
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();

        if ($dryRun) {
            $this->info("DRY RUN: Would update {$updated} records");
        } else {
            $this->info("Successfully updated {$updated} records");
        }

        // Show summary of privacy level distribution
        $this->showPrivacyLevelSummary();

        return 0;
    }

    /**
     * Determine privacy level based on activity category
     *
     * @param string $category
     * @return string
     */
    private function determinePrivacyLevel(string $category): string
    {
        try {
            $activityCategory = GdprActivityCategory::tryFrom($category);
            
            if ($activityCategory) {
                return $activityCategory->privacyLevel()->value;
            }
            
            // Fallback for unknown categories
            return PrivacyLevel::STANDARD->value;
            
        } catch (\Exception $e) {
            // If category doesn't exist in enum, use standard privacy level
            return PrivacyLevel::STANDARD->value;
        }
    }

    /**
     * Show summary of privacy level distribution
     */
    private function showPrivacyLevelSummary()
    {
        $this->newLine();
        $this->info('Privacy Level Distribution:');
        
        $distribution = DB::table('user_activities')
            ->select('privacy_level', DB::raw('count(*) as count'))
            ->groupBy('privacy_level')
            ->orderBy('count', 'desc')
            ->get();

        $headers = ['Privacy Level', 'Count', 'Percentage'];
        $rows = [];
        $total = $distribution->sum('count');

        foreach ($distribution as $item) {
            $percentage = $total > 0 ? round(($item->count / $total) * 100, 2) : 0;
            $rows[] = [
                $item->privacy_level,
                number_format($item->count),
                $percentage . '%'
            ];
        }

        $this->table($headers, $rows);
    }
}