<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\FegiAuth;
use App\Models\Collection;
use App\Models\User;
use App\Models\Team;

class TestCarousel extends Command {
    protected $signature = 'test:carousel {user_id?}';
    protected $description = 'Test the carousel component with mock data';

    public function handle() {
        $userId = $this->argument('user_id') ?? 1;

        $this->info("Testing Carousel Component");
        $this->info("========================");

        try {
            // Try to find user
            $user = User::find($userId);
            if (!$user) {
                $this->error("User {$userId} not found");
                return 1;
            }

            $this->info("User: {$user->name} (ID: {$user->id})");

            // Check if user has currentTeam
            $currentTeam = $user->currentTeam;
            if (!$currentTeam) {
                $this->warn("User doesn't have a currentTeam");
                $this->info("Let's create a personal team for this user...");

                // Create a personal team for the user
                $personalTeam = $user->ownedTeams()->create([
                    'name' => $user->name . "'s Team",
                    'personal_team' => true,
                ]);

                $user->update(['current_team_id' => $personalTeam->id]);
                $user->refresh();
                $currentTeam = $user->currentTeam;
            }

            $this->info("Current Team: {$currentTeam->name} (ID: {$currentTeam->id})");

            // Get collections for this team
            $collections = Collection::where('team_id', $currentTeam->id)->get();

            $this->info("Collections found: " . $collections->count());

            if ($collections->count() === 0) {
                $this->warn("No collections found for this team");
                $this->info("Creating test collections...");

                // Create some test collections
                for ($i = 1; $i <= 5; $i++) {
                    Collection::create([
                        'team_id' => $currentTeam->id,
                        'collection_name' => "Test Collection {$i}",
                        'creator_id' => $user->id,
                        'collection_slug' => "test-collection-{$i}",
                        'collection_status' => 'active',
                        'collection_description' => "Test description for collection {$i}",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $collections = Collection::where('team_id', $currentTeam->id)->get();
                $this->info("Created {$collections->count()} test collections");
            }

            foreach ($collections as $index => $collection) {
                $this->line("  {$index}: {$collection->collection_name} (ID: {$collection->id})");
            }

            $this->info("");
            $this->info("Carousel Logic Test:");
            $this->info("==================");

            // Test carousel logic
            $totalSlides = $collections->count();
            $itemsPerView = 1; // Mobile view
            $maxSlide = max(0, $totalSlides - $itemsPerView);

            $this->info("Total slides: {$totalSlides}");
            $this->info("Items per view: {$itemsPerView}");
            $this->info("Max slide: {$maxSlide}");

            // Test navigation
            for ($activeSlide = 0; $activeSlide <= $maxSlide; $activeSlide++) {
                $slideWidth = 100 / $itemsPerView;
                $translateX = -$activeSlide * $slideWidth;
                $transform = "translateX({$translateX}%)";

                $canGoPrev = $activeSlide > 0;
                $canGoNext = $activeSlide < $maxSlide;

                $this->line("Slide {$activeSlide}: {$transform} | Prev: " . ($canGoPrev ? 'YES' : 'NO') . " | Next: " . ($canGoNext ? 'YES' : 'NO'));
            }

            $this->info("");
            $this->success("Carousel component test completed successfully!");
        } catch (\Exception $e) {
            $this->error("Error testing carousel: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
