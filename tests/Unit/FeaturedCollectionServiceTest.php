<?php

namespace Tests\Unit;

use App\Models\Collection;
use App\Models\Egi;
use App\Models\Reservation;
use App\Models\User;
use App\Services\FeaturedCollectionService;
use Database\Factories\TestUserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Test per FeaturedCollectionService
 *
 * Verifica il corretto funzionamento dell'algoritmo di selezione delle Collection featured,
 * il calcolo dell'impatto stimato e le funzionalità di gestione delle posizioni.
 */
class FeaturedCollectionServiceTest extends TestCase {
    use RefreshDatabase;

    private FeaturedCollectionService $service;

    protected function setUp(): void {
        parent::setUp();
        $this->service = new FeaturedCollectionService();
    }

    /**
     * Helper per creare utente creator
     */
    protected function createCreator(): User {
        return User::create([
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'usertype' => 'creator',
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'wallet' => Str::random(42),
            'wallet_balance' => fake()->randomFloat(2, 0, 1000),
        ]);
    }

    /**
     * Helper per creare utente normale
     */
    protected function createUser(): User {
        return User::create([
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'usertype' => 'user',
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'wallet' => Str::random(42),
            'wallet_balance' => fake()->randomFloat(2, 0, 1000),
        ]);
    }

    /** @test */
    public function it_returns_featured_collections_ordered_by_position_and_impact() {
        // Arrange: Creo utenti
        $creator1 = $this->createCreator();
        $creator2 = $this->createCreator();
        $user = $this->createUser();

        // Creo Collection con diversi setup
        $collection1 = Collection::factory()->create([
            'creator_id' => $creator1->id,
            'is_published' => true,
            'featured_in_guest' => true,
            'featured_position' => 2,
            'collection_name' => 'Collection Posizione Forzata'
        ]);

        $collection2 = Collection::factory()->create([
            'creator_id' => $creator2->id,
            'is_published' => true,
            'featured_in_guest' => true,
            'featured_position' => 1,
            'collection_name' => 'Collection Prima Posizione'
        ]);

        $collection3 = Collection::factory()->create([
            'creator_id' => $creator1->id,
            'is_published' => true,
            'featured_in_guest' => true,
            'featured_position' => null, // Posizione automatica
            'collection_name' => 'Collection Alto Impatto'
        ]);

        // Creo EGI e prenotazioni per simulare diversi impatti
        $egi1 = Egi::factory()->create(['collection_id' => $collection3->id, 'price' => 100]);
        $egi2 = Egi::factory()->create(['collection_id' => $collection3->id, 'price' => 200]);

        // Prenotazioni con valori alti per collection3 (impatto stimato alto)
        Reservation::factory()->create([
            'egi_id' => $egi1->id,
            'user_id' => $user->id,
            'offer_amount_fiat' => 1000,
            'is_current' => true,
            'status' => 'active'
        ]);

        Reservation::factory()->create([
            'egi_id' => $egi2->id,
            'user_id' => $user->id,
            'offer_amount_fiat' => 2000,
            'is_current' => true,
            'status' => 'active'
        ]);

        // Act
        $result = $this->service->getFeaturedCollections(10);

        // Assert: Verifica ordine
        $this->assertCount(3, $result);
        $this->assertEquals('Collection Prima Posizione', $result->first()->collection_name);
        $this->assertEquals('Collection Posizione Forzata', $result->get(1)->collection_name);
        $this->assertEquals('Collection Alto Impatto', $result->get(2)->collection_name);
    }

    /** @test */
    public function it_calculates_estimated_impact_correctly() {
        // Arrange
        $creator = $this->createCreator();
        $user = $this->createUser();

        $collection = Collection::factory()->create([
            'creator_id' => $creator->id,
            'is_published' => true
        ]);

        $egi1 = Egi::factory()->create(['collection_id' => $collection->id]);
        $egi2 = Egi::factory()->create(['collection_id' => $collection->id]);

        // Prenotazioni: EGI1 = 1000 EUR, EGI2 = 500 EUR
        Reservation::factory()->create([
            'egi_id' => $egi1->id,
            'user_id' => $user->id,
            'offer_amount_fiat' => 1000,
            'is_current' => true,
            'status' => 'active'
        ]);

        Reservation::factory()->create([
            'egi_id' => $egi2->id,
            'user_id' => $user->id,
            'offer_amount_fiat' => 500,
            'is_current' => true,
            'status' => 'active'
        ]);

        // Act
        $impact = $this->service->calculateEstimatedImpact($collection);

        // Assert: (1000 + 500) * 0.20 = 300 EUR
        $this->assertEquals(300.00, $impact);
    }

    /** @test */
    public function it_only_considers_highest_reservation_per_egi() {
        // Arrange
        $creator = $this->createCreator();
        $user1 = $this->createUser();
        $user2 = $this->createUser();

        $collection = Collection::factory()->create([
            'creator_id' => $creator->id,
            'is_published' => true
        ]);

        $egi = Egi::factory()->create(['collection_id' => $collection->id]);

        // Più prenotazioni per lo stesso EGI - solo quella più alta deve essere considerata
        Reservation::factory()->create([
            'egi_id' => $egi->id,
            'user_id' => $user1->id,
            'offer_amount_fiat' => 500,
            'is_current' => true,
            'status' => 'active'
        ]);

        Reservation::factory()->create([
            'egi_id' => $egi->id,
            'user_id' => $user2->id,
            'offer_amount_fiat' => 1200, // Questa è la più alta
            'is_current' => true,
            'status' => 'active'
        ]);

        Reservation::factory()->create([
            'egi_id' => $egi->id,
            'user_id' => $user1->id,
            'offer_amount_fiat' => 800,
            'is_current' => true,
            'status' => 'active'
        ]);

        // Act
        $impact = $this->service->calculateEstimatedImpact($collection);

        // Assert: Solo 1200 * 0.20 = 240 EUR
        $this->assertEquals(240.00, $impact);
    }

    /** @test */
    public function it_excludes_non_current_reservations() {
        // Arrange
        $creator = $this->createCreator();
        $user = $this->createUser();

        $collection = Collection::factory()->create([
            'creator_id' => $creator->id,
            'is_published' => true
        ]);

        $egi = Egi::factory()->create(['collection_id' => $collection->id]);

        // Prenotazione non corrente (superseded)
        Reservation::factory()->create([
            'egi_id' => $egi->id,
            'user_id' => $user->id,
            'offer_amount_fiat' => 1000,
            'is_current' => false, // Non corrente
            'status' => 'active'
        ]);

        // Prenotazione corrente con valore minore
        Reservation::factory()->create([
            'egi_id' => $egi->id,
            'user_id' => $user->id,
            'offer_amount_fiat' => 300,
            'is_current' => true,
            'status' => 'active'
        ]);

        // Act
        $impact = $this->service->calculateEstimatedImpact($collection);

        // Assert: Solo 300 * 0.20 = 60 EUR (ignora la prenotazione da 1000)
        $this->assertEquals(60.00, $impact);
    }

    /** @test */
    public function it_filters_by_featured_in_guest_and_is_published() {
        // Arrange
        $creator = $this->createCreator();

        // Collection pubblicata ma non featured
        Collection::factory()->create([
            'creator_id' => $creator->id,
            'is_published' => true,
            'featured_in_guest' => false,
            'collection_name' => 'Not Featured'
        ]);

        // Collection featured ma non pubblicata
        Collection::factory()->create([
            'creator_id' => $creator->id,
            'is_published' => false,
            'featured_in_guest' => true,
            'collection_name' => 'Not Published'
        ]);

        // Collection featured e pubblicata
        Collection::factory()->create([
            'creator_id' => $creator->id,
            'is_published' => true,
            'featured_in_guest' => true,
            'collection_name' => 'Valid Featured'
        ]);

        // Act
        $result = $this->service->getFeaturedCollections(10);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals('Valid Featured', $result->first()->collection_name);
    }

    /** @test */
    public function it_can_set_collection_as_featured_with_position() {
        // Arrange
        $creator = $this->createCreator();
        $collection = Collection::factory()->create([
            'creator_id' => $creator->id,
            'is_published' => true,
            'featured_in_guest' => false
        ]);

        // Act
        $result = $this->service->setAsFeatured($collection, 3);

        // Assert
        $this->assertTrue($result);
        $collection->refresh();
        $this->assertTrue($collection->featured_in_guest);
        $this->assertEquals(3, $collection->featured_position);
    }

    /** @test */
    public function it_moves_existing_collection_when_position_conflicts() {
        // Arrange
        $creator = $this->createCreator();

        $existingCollection = Collection::factory()->create([
            'creator_id' => $creator->id,
            'is_published' => true,
            'featured_in_guest' => true,
            'featured_position' => 5
        ]);

        $newCollection = Collection::factory()->create([
            'creator_id' => $creator->id,
            'is_published' => true,
            'featured_in_guest' => false
        ]);

        // Act: Assegno posizione 5 alla nuova Collection
        $result = $this->service->setAsFeatured($newCollection, 5);

        // Assert
        $this->assertTrue($result);

        $existingCollection->refresh();
        $newCollection->refresh();

        // La vecchia Collection dovrebbe essere spostata in posizione automatica
        $this->assertNull($existingCollection->featured_position);
        $this->assertTrue($existingCollection->featured_in_guest); // Rimane featured

        // La nuova Collection dovrebbe avere posizione 5
        $this->assertEquals(5, $newCollection->featured_position);
        $this->assertTrue($newCollection->featured_in_guest);
    }

    /** @test */
    public function it_can_remove_collection_from_featured() {
        // Arrange
        $creator = $this->createCreator();
        $collection = Collection::factory()->create([
            'creator_id' => $creator->id,
            'is_published' => true,
            'featured_in_guest' => true,
            'featured_position' => 2
        ]);

        // Act
        $result = $this->service->removeFromFeatured($collection);

        // Assert
        $this->assertTrue($result);
        $collection->refresh();
        $this->assertFalse($collection->featured_in_guest);
        $this->assertNull($collection->featured_position);
    }

    /** @test */
    public function it_respects_max_carousel_limit() {
        // Arrange: Creo 15 Collection featured
        $creator = $this->createCreator();

        for ($i = 1; $i <= 15; $i++) {
            Collection::factory()->create([
                'creator_id' => $creator->id,
                'is_published' => true,
                'featured_in_guest' => true,
                'collection_name' => "Collection $i"
            ]);
        }

        // Act: Richiedo solo 10
        $result = $this->service->getFeaturedCollections(10);

        // Assert
        $this->assertCount(10, $result);
    }

    /** @test */
    public function it_returns_empty_collection_when_no_featured_collections() {
        // Arrange: Nessuna Collection featured

        // Act
        $result = $this->service->getFeaturedCollections(10);

        // Assert
        $this->assertCount(0, $result);
    }

    /** @test */
    public function it_validates_position_range() {
        // Arrange
        $creator = $this->createCreator();
        $collection = Collection::factory()->create([
            'creator_id' => $creator->id,
            'is_published' => true
        ]);

        // Act & Assert: Posizione non valida
        $this->expectException(\Exception::class);
        $this->service->setAsFeatured($collection, 15); // Fuori range (max 10)
    }
}
