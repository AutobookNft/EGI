<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Egi;
use App\Models\Collection;
use App\Models\Reservation;

class CollectorHomeTest extends TestCase {
    use RefreshDatabase;

    public function test_collector_home_page_displays_correctly() {
        // Create a user with collector role
        $collector = User::factory()->create([
            'name' => 'Test Collector',
            'email' => 'collector@test.com'
        ]);

        // Create some test data
        $creator = User::factory()->create(['name' => 'Test Creator']);
        $collection = Collection::factory()->create(['creator_id' => $creator->id]);
        $egi = Egi::factory()->create([
            'collection_id' => $collection->id,
            'owner_id' => $collector->id,
            'is_published' => true
        ]);

        // Create reservation
        $reservation = Reservation::factory()->create([
            'user_id' => $collector->id,
            'egi_id' => $egi->id,
            'status' => 'completed',
            'offer_amount_eur' => 100.00
        ]);

        // Visit collector home page
        $response = $this->get(route('collector.home', $collector->id));

        $response->assertStatus(200);
        $response->assertSee($collector->name);
        $response->assertSee('EGI Collector');
        $response->assertViewHas('collector', $collector);
        $response->assertViewHas('stats');
        $response->assertViewHas('featuredEgis');
    }

    public function test_non_collector_user_returns_404() {
        // Create a user without any collector activity
        $user = User::factory()->create();

        $response = $this->get(route('collector.home', $user->id));

        $response->assertStatus(404);
    }

    public function test_collector_stats_are_calculated_correctly() {
        $collector = User::factory()->create();
        $creator = User::factory()->create();
        $collection = Collection::factory()->create(['creator_id' => $creator->id]);

        // Create multiple EGIs owned by collector
        $egi1 = Egi::factory()->create([
            'collection_id' => $collection->id,
            'owner_id' => $collector->id,
            'is_published' => true
        ]);
        $egi2 = Egi::factory()->create([
            'collection_id' => $collection->id,
            'owner_id' => $collector->id,
            'is_published' => true
        ]);

        // Create reservations
        Reservation::factory()->create([
            'user_id' => $collector->id,
            'egi_id' => $egi1->id,
            'status' => 'completed',
            'offer_amount_eur' => 150.00
        ]);
        Reservation::factory()->create([
            'user_id' => $collector->id,
            'egi_id' => $egi2->id,
            'status' => 'completed',
            'offer_amount_eur' => 200.00
        ]);

        $response = $this->get(route('collector.home', $collector->id));

        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total_owned_egis'] == 2 &&
                $stats['total_spent'] == 350.00 &&
                $stats['total_collections'] == 1;
        });
    }
}
