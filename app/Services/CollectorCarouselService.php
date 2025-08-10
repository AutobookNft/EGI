<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @Oracode Service: Top Collectors Carousel for Guest Homepage
 * 🎯 Purpose: Calculate and retrieve top collectors based on total spending
 * 🛡️ Strategy: Marketing visibility for top spenders to incentivize purchases
 * 🧱 Core Logic: Rank collectors by sum of their winning reservations
 *
 * Business Logic:
 * - Collectors ranked by total spending (winning reservations)
 * - Future evolution: Include completed purchases
 * - Top 10 collectors get homepage visibility
 * - Incentivizes higher spending for social recognition
 *
 * @package App\Services
 * @author Padmin D. Curtis (AI Partner OS2.0-Compliant) for Fabio Cherici
 * @version 1.0.0 (Top Collectors Marketing Strategy)
 * @date 2025-08-10
 */
class CollectorCarouselService
{
    /**
     * Get top collectors ranked by total spending
     * 
     * @param int $limit Number of top collectors to retrieve
     * @return Collection Collection of collectors with spending stats
     */
    public function getTopCollectors(int $limit = 10): Collection
    {
        return User::select([
                'users.*',
                DB::raw('
                    COALESCE((
                        SELECT SUM(reservations.offer_amount_eur)
                        FROM reservations
                        WHERE reservations.user_id = users.id
                        AND reservations.is_current = 1
                        AND reservations.status = "active"
                        AND NOT EXISTS (
                            SELECT 1 
                            FROM reservations r2 
                            WHERE r2.egi_id = reservations.egi_id 
                            AND r2.offer_amount_eur > reservations.offer_amount_eur
                            AND r2.is_current = 1
                            AND r2.status = "active"
                        )
                    ), 0) as total_spending
                ')
            ])
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('reservations')
                      ->whereColumn('reservations.user_id', 'users.id')
                      ->where('reservations.is_current', true)
                      ->where('reservations.status', 'active');
            })
            ->having('total_spending', '>', 0)
            ->orderByDesc('total_spending')
            ->limit($limit)
            ->get()
            ->map(function ($collector) {
                // Add additional stats for each collector
                $collector->winning_reservations_count = $this->getWinningReservationsCount($collector->id);
                $collector->activated_egis_count = $this->getActivatedEgisCount($collector->id);
                $collector->average_spending = $collector->winning_reservations_count > 0 
                    ? $collector->total_spending / $collector->winning_reservations_count 
                    : 0;
                
                return $collector;
            });
    }

    /**
     * Get count of winning reservations for a collector
     */
    private function getWinningReservationsCount(int $userId): int
    {
        return DB::table('reservations')
            ->where('user_id', $userId)
            ->where('is_current', true)
            ->where('status', 'active')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('reservations as r2')
                      ->whereColumn('r2.egi_id', 'reservations.egi_id')
                      ->whereColumn('r2.offer_amount_eur', '>', 'reservations.offer_amount_eur')
                      ->where('r2.is_current', true)
                      ->where('r2.status', 'active');
            })
            ->count();
    }

    /**
     * Get count of activated EGIs (EGIs where this collector has winning reservation)
     */
    private function getActivatedEgisCount(int $userId): int
    {
        return DB::table('egis')
            ->whereExists(function ($query) use ($userId) {
                $query->select(DB::raw(1))
                      ->from('reservations')
                      ->whereColumn('reservations.egi_id', 'egis.id')
                      ->where('reservations.user_id', $userId)
                      ->where('reservations.is_current', true)
                      ->where('reservations.status', 'active')
                      ->whereNotExists(function ($subquery) {
                          $subquery->select(DB::raw(1))
                                   ->from('reservations as r2')
                                   ->whereColumn('r2.egi_id', 'reservations.egi_id')
                                   ->whereColumn('r2.offer_amount_eur', '>', 'reservations.offer_amount_eur')
                                   ->where('r2.is_current', true)
                                   ->where('r2.status', 'active');
                      });
            })
            ->count();
    }

    /**
     * Get collector stats for display
     */
    public function getCollectorStats(int $userId): array
    {
        $collector = User::find($userId);
        
        if (!$collector) {
            return [];
        }

        $totalSpending = DB::table('reservations')
            ->where('user_id', $userId)
            ->where('is_current', true)
            ->where('status', 'active')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('reservations as r2')
                      ->whereColumn('r2.egi_id', 'reservations.egi_id')
                      ->whereColumn('r2.offer_amount_eur', '>', 'reservations.offer_amount_eur')
                      ->where('r2.is_current', true)
                      ->where('r2.status', 'active');
            })
            ->sum('offer_amount_eur');

        return [
            'total_spending' => $totalSpending ?? 0,
            'winning_reservations_count' => $this->getWinningReservationsCount($userId),
            'activated_egis_count' => $this->getActivatedEgisCount($userId),
        ];
    }
}
