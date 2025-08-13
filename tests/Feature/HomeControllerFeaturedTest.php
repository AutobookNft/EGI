<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Egi;
use App\Models\Epp;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Test di integrazione per verificare il comportamento del controller
 * con le featured collections nella homepage
 */
class HomeControllerFeaturedTest extends TestCase {
    use RefreshDatabase;

    /** @test */
    public function homepage_displays_featured_collections_correctly() {
        // Arrange: Creo dati necessari per la homepage
        $creator = User::factory()->create(['usertype' => 'creator']);
        $user = User::factory()->create();

        // EPP necessario per evitare errori nella homepage
        $epp = Epp::factory()->create(['status' => 'active']);

        // Collection featured con posizione forzata
        $featuredCollection1 = Collection::factory()->create([
            'creator_id' => $creator->id,
            'is_published' => true,
            'featured_in_guest' => true,
            'featured_position' => 1,
            'collection_name' => 'Featured Collection 1'
        ]);

        // Collection featured con posizione automatica e alto impatto
        $featuredCollection2 = Collection::factory()->create([
            'creator_id' => $creator->id,
            'is_published' => true,
            'featured_in_guest' => true,
            'featured_position' => null,
            'collection_name' => 'High Impact Collection'
        ]);

        // EGI con prenotazioni per creare impatto
        $egi = Egi::factory()->create([
            'collection_id' => $featuredCollection2->id,
            'is_published' => true
        ]);

        Reservation::factory()->create([
            'egi_id' => $egi->id,
            'user_id' => $user->id,
            'offer_amount_fiat' => 2000,
            'is_current' => true,
            'status' => 'active'
        ]);

        // Collection non featured (non dovrebbe apparire)
        Collection::factory()->create([
            'creator_id' => $creator->id,
            'is_published' => true,
            'featured_in_guest' => false,
            'collection_name' => 'Not Featured Collection'
        ]);

        // Act: Visito la homepage
        $response = $this->get('/');

        // Assert: La pagina si carica correttamente
        $response->assertStatus(200);

        // Verifica che i dati delle featured collections siano passati alla vista
        $response->assertViewHas('featuredCollections');

        $featuredCollections = $response->viewData('featuredCollections');

        // Verifica che vengano restituite solo le Collection featured e pubblicate
        $this->assertCount(2, $featuredCollections);

        // Verifica l'ordine basato su posizione forzata
        $this->assertEquals('Featured Collection 1', $featuredCollections->first()->collection_name);
        $this->assertEquals(1, $featuredCollections->first()->featured_position);

        // La seconda dovrebbe essere quella con alto impatto
        $this->assertEquals('High Impact Collection', $featuredCollections->get(1)->collection_name);
        $this->assertNull($featuredCollections->get(1)->featured_position);
    }

    /** @test */
    public function homepage_works_without_featured_collections() {
        // Arrange: Creo dati minimi per la homepage senza featured collections
        $creator = User::factory()->create(['usertype' => 'creator']);
        $epp = Epp::factory()->create(['status' => 'active']);

        // Collection normale (non featured)
        Collection::factory()->create([
            'creator_id' => $creator->id,
            'is_published' => true,
            'featured_in_guest' => false
        ]);

        // Act: Visito la homepage
        $response = $this->get('/');

        // Assert: La pagina si carica correttamente anche senza featured
        $response->assertStatus(200);
        $response->assertViewHas('featuredCollections');

        $featuredCollections = $response->viewData('featuredCollections');
        $this->assertCount(0, $featuredCollections);
    }

    /** @test */
    public function featured_collections_are_limited_to_maximum() {
        // Arrange: Creo più di 10 Collection featured
        $creator = User::factory()->create(['usertype' => 'creator']);
        $epp = Epp::factory()->create(['status' => 'active']);

        for ($i = 1; $i <= 15; $i++) {
            Collection::factory()->create([
                'creator_id' => $creator->id,
                'is_published' => true,
                'featured_in_guest' => true,
                'collection_name' => "Collection $i"
            ]);
        }

        // Act: Visito la homepage
        $response = $this->get('/');

        // Assert: Vengono restituite massimo 10 Collection
        $response->assertStatus(200);
        $featuredCollections = $response->viewData('featuredCollections');
        $this->assertLessThanOrEqual(10, $featuredCollections->count());
    }

    /** @test */
    public function unpublished_collections_are_not_featured() {
        // Arrange
        $creator = User::factory()->create(['usertype' => 'creator']);
        $epp = Epp::factory()->create(['status' => 'active']);

        // Collection featured ma non pubblicata
        Collection::factory()->create([
            'creator_id' => $creator->id,
            'is_published' => false,
            'featured_in_guest' => true,
            'featured_position' => 1,
            'collection_name' => 'Unpublished Featured'
        ]);

        // Collection pubblicata e featured
        Collection::factory()->create([
            'creator_id' => $creator->id,
            'is_published' => true,
            'featured_in_guest' => true,
            'featured_position' => 2,
            'collection_name' => 'Published Featured'
        ]);

        // Act
        $response = $this->get('/');

        // Assert: Solo la Collection pubblicata dovrebbe apparire
        $response->assertStatus(200);
        $featuredCollections = $response->viewData('featuredCollections');

        $this->assertCount(1, $featuredCollections);
        $this->assertEquals('Published Featured', $featuredCollections->first()->collection_name);
    }
}
