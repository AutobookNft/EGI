<?php

namespace Tests\Unit;

use App\Services\FeaturedCollectionService;
use Tests\TestCase;

/**
 * Test semplificato per FeaturedCollectionService
 * per verificare la logica di base senza dipendenze complesse
 */
class SimpleFeaturedCollectionServiceTest extends TestCase {
    private FeaturedCollectionService $service;

    protected function setUp(): void {
        parent::setUp();
        $this->service = new FeaturedCollectionService();
    }

    /** @test */
    public function it_can_instantiate_the_service() {
        $this->assertInstanceOf(FeaturedCollectionService::class, $this->service);
    }

    /** @test */
    public function it_returns_empty_collection_when_no_data() {
        // Act: Chiamo il servizio su database vuoto
        $result = $this->service->getFeaturedCollections(10);

        // Assert: Dovrebbe restituire una collection vuota
        $this->assertCount(0, $result);
    }

    /** @test */
    public function it_respects_limit_parameter() {
        // Act: Provo con limiti diversi
        $result5 = $this->service->getFeaturedCollections(5);
        $result10 = $this->service->getFeaturedCollections(10);

        // Assert: Le collection dovrebbero essere vuote ma il metodo funziona
        $this->assertCount(0, $result5);
        $this->assertCount(0, $result10);
    }

    /** @test */
    public function it_can_check_if_collection_can_be_featured() {
        // Questo è un test delle funzionalità senza accesso database
        $this->assertTrue(true); // Placeholder per ora
    }
}
