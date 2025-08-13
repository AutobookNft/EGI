<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Egi;
use App\Models\Reservation;
use App\Models\Collection;
use App\Services\PortfolioService;
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * @Oracode Test: Portfolio Fix Integration Test
 * 🎯 Purpose: Test the portfolio fixes for reservation system
 * 🚀 Enhancement: Ensure portfolio shows only winning reservations
 *
 * @package Tests\Feature
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 2.0.0 - Portfolio Fix
 * @date 2025-08-08
 */
class PortfolioFixTest extends TestCase {
    use RefreshDatabase, WithFaker;

    protected PortfolioService $portfolioService;
    protected ReservationService $reservationService;

    protected function setUp(): void {
        parent::setUp();

        $this->portfolioService = app(PortfolioService::class);
        $this->reservationService = app(ReservationService::class);
    }

    /**
     * Test that user can have only one active reservation per EGI
     *
     * @test
     */
    public function user_can_have_only_one_active_reservation_per_egi() {
        // Arrange
        $user = User::factory()->create(['is_weak_auth' => false]);
        $collection = Collection::factory()->create();
        $egi = Egi::factory()->create(['collection_id' => $collection->id, 'is_published' => true]);

        // Act - User fa prima prenotazione
        $firstReservation = $this->reservationService->createReservation([
            'egi_id' => $egi->id,
            'offer_amount_fiat' => 100.00
        ], $user);

        // User fa seconda prenotazione stesso EGI
        $secondReservation = $this->reservationService->createReservation([
            'egi_id' => $egi->id,
            'offer_amount_fiat' => 150.00
        ], $user);

        // Assert - Solo una prenotazione is_current=true
        $activeReservations = Reservation::where('user_id', $user->id)
            ->where('egi_id', $egi->id)
            ->where('is_current', true)
            ->count();

        $this->assertEquals(1, $activeReservations, 'User should have only one active reservation per EGI');

        // La seconda prenotazione dovrebbe essere quella attiva
        $firstReservation->refresh();
        $secondReservation->refresh();

        $this->assertFalse($firstReservation->is_current, 'First reservation should be deactivated');
        $this->assertEquals('superseded', $firstReservation->status, 'First reservation should be superseded');
        $this->assertTrue($secondReservation->is_current, 'Second reservation should be active');
        $this->assertEquals('active', $secondReservation->status, 'Second reservation should be active');
    }

    /**
     * Test that portfolio shows only winning reservations
     *
     * @test
     */
    public function portfolio_shows_only_winning_reservations() {
        // Arrange
        $userA = User::factory()->create(['is_weak_auth' => false]);
        $userB = User::factory()->create(['is_weak_auth' => false]);
        $collection = Collection::factory()->create();
        $egi = Egi::factory()->create(['collection_id' => $collection->id, 'is_published' => true]);

        // Act - User A prenota EGI
        $reservationA = $this->reservationService->createReservation([
            'egi_id' => $egi->id,
            'offer_amount_fiat' => 100.00
        ], $userA);

        // Verifica che EGI appare nel portfolio User A
        $portfolioA = $this->portfolioService->getCollectorActivePortfolio($userA);
        $this->assertCount(1, $portfolioA, 'User A should have 1 EGI in portfolio');

        // User B prenota con offerta maggiore
        $reservationB = $this->reservationService->createReservation([
            'egi_id' => $egi->id,
            'offer_amount_fiat' => 150.00
        ], $userB);

        // Assert - EGI sparisce da portfolio User A
        $portfolioA = $this->portfolioService->getCollectorActivePortfolio($userA);
        $this->assertCount(0, $portfolioA, 'User A should have 0 EGIs in portfolio after being outbid');

        // EGI appare in portfolio User B
        $portfolioB = $this->portfolioService->getCollectorActivePortfolio($userB);
        $this->assertCount(1, $portfolioB, 'User B should have 1 EGI in portfolio');
    }

    /**
     * Test that stats reflect only active portfolio
     *
     * @test
     */
    public function stats_reflect_only_active_portfolio() {
        // Arrange
        $user = User::factory()->create(['is_weak_auth' => false]);
        $collection = Collection::factory()->create();
        $egi1 = Egi::factory()->create(['collection_id' => $collection->id, 'is_published' => true]);
        $egi2 = Egi::factory()->create(['collection_id' => $collection->id, 'is_published' => true]);

        $otherUser = User::factory()->create(['is_weak_auth' => false]);

        // Act - User fa diverse prenotazioni
        $reservation1 = $this->reservationService->createReservation([
            'egi_id' => $egi1->id,
            'offer_amount_fiat' => 100.00
        ], $user);

        $reservation2 = $this->reservationService->createReservation([
            'egi_id' => $egi2->id,
            'offer_amount_fiat' => 200.00
        ], $user);

        // Other user supera una prenotazione
        $reservation3 = $this->reservationService->createReservation([
            'egi_id' => $egi1->id,
            'offer_amount_fiat' => 150.00
        ], $otherUser);

        // Assert - Stats riflettono solo portfolio attivo
        $stats = $this->portfolioService->getCollectorPortfolioStats($user);

        $this->assertEquals(1, $stats['total_owned_egis'], 'Should count only winning reservations');
        $this->assertEquals(200.00, $stats['total_spent_eur'], 'Should sum only winning reservation amounts');
        $this->assertEquals(2, $stats['total_bids_made'], 'Should count all bids made');
        $this->assertEquals(1, $stats['active_winning_bids'], 'Should count only winning bids');
        $this->assertEquals(1, $stats['outbid_count'], 'Should count outbid reservations');
    }

    /**
     * Test complete outbid scenario
     *
     * @test
     */
    public function complete_outbid_scenario() {
        // Arrange
        $userA = User::factory()->create(['is_weak_auth' => false]);
        $userB = User::factory()->create(['is_weak_auth' => false]);
        $collection = Collection::factory()->create();
        $egi = Egi::factory()->create(['collection_id' => $collection->id, 'is_published' => true]);

        // Act & Assert

        // 1. User A prenota EGI → Appare nel portfolio
        $reservationA1 = $this->reservationService->createReservation([
            'egi_id' => $egi->id,
            'offer_amount_fiat' => 100.00
        ], $userA);

        $portfolioA = $this->portfolioService->getCollectorActivePortfolio($userA);
        $this->assertCount(1, $portfolioA, 'EGI should appear in User A portfolio');

        // 2. User B prenota con offerta maggiore → EGI sparisce da portfolio User A
        $reservationB = $this->reservationService->createReservation([
            'egi_id' => $egi->id,
            'offer_amount_fiat' => 150.00
        ], $userB);

        $portfolioA = $this->portfolioService->getCollectorActivePortfolio($userA);
        $this->assertCount(0, $portfolioA, 'EGI should disappear from User A portfolio');

        // 3. User A fa controfferta maggiore → EGI riappare in portfolio User A
        $reservationA2 = $this->reservationService->createReservation([
            'egi_id' => $egi->id,
            'offer_amount_fiat' => 200.00
        ], $userA);

        $portfolioA = $this->portfolioService->getCollectorActivePortfolio($userA);
        $this->assertCount(1, $portfolioA, 'EGI should reappear in User A portfolio');

        $portfolioB = $this->portfolioService->getCollectorActivePortfolio($userB);
        $this->assertCount(0, $portfolioB, 'EGI should disappear from User B portfolio');
    }

    /**
     * Test API endpoint for status updates
     *
     * @test
     */
    public function api_endpoint_returns_status_updates() {
        // Arrange
        $user = User::factory()->create(['is_weak_auth' => false]);
        $this->actingAs($user);

        // Act
        $response = $this->getJson('/api/portfolio/status-updates');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'updates',
            'stats',
            'checked_at'
        ]);
    }

    /**
     * Test API endpoint for portfolio data
     *
     * @test
     */
    public function api_endpoint_returns_portfolio_data() {
        // Arrange
        $user = User::factory()->create(['is_weak_auth' => false]);
        $collection = Collection::factory()->create();
        $egi = Egi::factory()->create(['collection_id' => $collection->id, 'is_published' => true]);

        // Create winning reservation
        $reservation = $this->reservationService->createReservation([
            'egi_id' => $egi->id,
            'offer_amount_fiat' => 100.00
        ], $user);

        $this->actingAs($user);

        // Act
        $response = $this->getJson('/api/portfolio');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'active_portfolio' => [
                '*' => [
                    'id',
                    'title',
                    'collection_id',
                    'collection_name',
                    'current_reservation'
                ]
            ],
            'bidding_history',
            'stats'
        ]);

        $responseData = $response->json();
        $this->assertCount(1, $responseData['active_portfolio'], 'Should return 1 active EGI');
    }
}
