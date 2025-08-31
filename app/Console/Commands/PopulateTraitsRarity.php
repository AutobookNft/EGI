<?php

namespace App\Console\Commands;

use App\Models\Egi;
use App\Models\EgiTrait;
use App\Models\Collection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PopulateTraitsRarity extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'traits:populate-rarity {--collection= : Specific collection ID to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate rarity percentages for all traits in database';

    /**
     * Execute the console command.
     */
    public function handle() {
        $this->info('🎯 Starting traits rarity population...');

        $collectionId = $this->option('collection');

        if ($collectionId) {
            // Process specific collection
            $this->processCollection($collectionId);
        } else {
            // Process all collections
            $collections = Collection::all();
            $this->info("Found {$collections->count()} collections to process");

            foreach ($collections as $collection) {
                $this->processCollection($collection->id);
            }
        }

        $this->info('✅ Traits rarity population completed!');
    }

    /**
     * Process a specific collection
     */
    private function processCollection(int $collectionId): void {
        $this->info("Processing collection ID: {$collectionId}");

        try {
            // Get total EGIs in collection
            $totalEgis = Egi::where('collection_id', $collectionId)->count();

            if ($totalEgis === 0) {
                $this->warn("  No EGIs found in collection {$collectionId}, skipping");
                return;
            }

            $this->info("  Total EGIs: {$totalEgis}");

            // Get all unique trait combinations (trait_type_id + value) in this collection
            $uniqueTraits = EgiTrait::join('egis', 'egis.id', '=', 'egi_traits.egi_id')
                ->where('egis.collection_id', $collectionId)
                ->select('egi_traits.trait_type_id', 'egi_traits.value')
                ->distinct()
                ->get();

            $this->info("  Unique trait combinations: {$uniqueTraits->count()}");

            $progressBar = $this->output->createProgressBar($uniqueTraits->count());
            $progressBar->start();

            // Calculate and update rarity for each unique trait combination
            foreach ($uniqueTraits as $uniqueTrait) {
                // Count how many EGIs have this trait
                $egisWithTrait = EgiTrait::join('egis', 'egis.id', '=', 'egi_traits.egi_id')
                    ->where('egis.collection_id', $collectionId)
                    ->where('egi_traits.trait_type_id', $uniqueTrait->trait_type_id)
                    ->where('egi_traits.value', $uniqueTrait->value)
                    ->count();

                // Calculate percentage
                $percentage = round(($egisWithTrait / $totalEgis) * 100, 2);

                // Update all traits with this combination
                $updatedCount = EgiTrait::join('egis', 'egis.id', '=', 'egi_traits.egi_id')
                    ->where('egis.collection_id', $collectionId)
                    ->where('egi_traits.trait_type_id', $uniqueTrait->trait_type_id)
                    ->where('egi_traits.value', $uniqueTrait->value)
                    ->update(['egi_traits.rarity_percentage' => $percentage]);

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine();
            $this->info("  ✅ Collection {$collectionId} processed successfully");
        } catch (\Exception $e) {
            $this->error("  ❌ Error processing collection {$collectionId}: " . $e->getMessage());
        }
    }
}
