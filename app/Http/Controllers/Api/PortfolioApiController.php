<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PortfolioService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\FegiAuth;

/**
 * @Oracode Controller: Portfolio API Controller
 * 🎯 Purpose: Provides API endpoints for portfolio real-time updates
 * 🚀 Enhancement: Real-time status updates for portfolio management
 * 🛡️ Security: Authenticated access only
 *
 * @package App\Http\Controllers\Api
 * @author Padmin D. Curtis (for Fabio Cherici)
 * @version 2.0.0 - Portfolio Fix
 * @date 2025-08-08
 */
class PortfolioApiController extends Controller {
    /**
     * @var PortfolioService
     */
    protected PortfolioService $portfolioService;

    /**
     * Constructor with dependency injection
     *
     * @param PortfolioService $portfolioService
     */
    public function __construct(PortfolioService $portfolioService) {
        $this->portfolioService = $portfolioService;
    }

    /**
     * Get status updates for authenticated user's portfolio
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getStatusUpdates(Request $request): JsonResponse {
        try {
            // Usa auth() standard invece di FegiAuth per ora
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }

            // Get status updates
            $updates = $this->portfolioService->checkForStatusUpdates($user);

            // Get current portfolio stats
            $stats = $this->portfolioService->getCollectorPortfolioStats($user);

            return response()->json([
                'success' => true,
                'updates' => $updates,
                'stats' => $stats,
                'checked_at' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking status updates',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal error'
            ], 500);
        }
    }

    /**
     * Get detailed portfolio information
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getPortfolio(Request $request): JsonResponse {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }

            // Get active portfolio
            $activeEgis = $this->portfolioService->getCollectorActivePortfolio($user);

            // Get bidding history
            $biddingHistory = $this->portfolioService->getCollectorBiddingHistory($user);

            // Get stats
            $stats = $this->portfolioService->getCollectorPortfolioStats($user);

            return response()->json([
                'success' => true,
                'active_portfolio' => $activeEgis->map(function ($egi) {
                    return [
                        'id' => $egi->id,
                        'title' => $egi->title,
                        'collection_id' => $egi->collection_id,
                        'collection_name' => $egi->collection?->collection_name,
                        'current_reservation' => $egi->reservations->first() ? [
                            'id' => $egi->reservations->first()->id,
                            'offer_amount_eur' => $egi->reservations->first()->offer_amount_eur,
                            'created_at' => $egi->reservations->first()->created_at->toIso8601String(),
                            'status' => 'winning'
                        ] : null
                    ];
                }),
                'bidding_history' => $biddingHistory->map(function ($reservation) {
                    return [
                        'id' => $reservation->id,
                        'egi' => [
                            'id' => $reservation->egi->id,
                            'title' => $reservation->egi->title,
                            'collection_name' => $reservation->egi->collection?->collection_name
                        ],
                        'offer_amount_eur' => $reservation->offer_amount_eur,
                        'status' => $reservation->is_current && !$reservation->superseded_by_id ? 'winning' : 'outbid',
                        'created_at' => $reservation->created_at->toIso8601String()
                    ];
                }),
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving portfolio',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal error'
            ], 500);
        }
    }

    /**
     * Get reservation status for specific EGI
     *
     * @param Request $request
     * @param int $egiId
     * @return JsonResponse
     */
    public function getEgiStatus(Request $request, int $egiId): JsonResponse {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required'
                ], 401);
            }

            // Get EGI reservation status
            $status = $this->portfolioService->getEgiReservationStatus($user, $egiId);

            return response()->json([
                'success' => true,
                'egi_id' => $egiId,
                'reservation_status' => $status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking EGI status',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal error'
            ], 500);
        }
    }
}
