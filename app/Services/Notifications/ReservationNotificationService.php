<?php

namespace App\Services\Notifications;

use App\Models\Reservation;
use App\Models\User;
use App\Models\NotificationPayloadReservation;
use App\Notifications\Reservations\ReservationHighest;
use App\Notifications\Reservations\ReservationSuperseded;
use App\Notifications\Reservations\RankChanged;
use App\Notifications\Reservations\CompetitorWithdrew;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Collection;

/**
 * Service for managing reservation-related notifications
 *
 * @package App\Services\Notifications
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Pre-Launch Reservation System)
 * @date 2025-08-15
 * @purpose Handle all reservation notification flows with ranking system
 */
class ReservationNotificationService
{
    /**
     * Constructor with dependency injection
     *
     * @param UltraLogManager $logger Ultra Log Manager for structured logging
     * @param ErrorManagerInterface $errorManager Ultra Error Manager for error handling
     */
    public function __construct(
        private UltraLogManager $logger,
        private ErrorManagerInterface $errorManager
    ) {}

    /**
     * Send notification when user becomes the highest bidder
     *
     * @param Reservation $reservation The reservation that became highest
     * @return void
     * @throws \Exception If notification fails
     */
    public function sendNewHighest(Reservation $reservation): void
    {
        // Log operation start
        $this->logger->info('[RESERVATION_NOTIFICATION] Starting sendNewHighest', [
            'reservation_id' => $reservation->id,
            'egi_id' => $reservation->egi_id,
            'user_id' => $reservation->user_id,
            'amount_eur' => $reservation->amount_eur,
            'rank_position' => $reservation->rank_position
        ]);

        try {
            DB::transaction(function () use ($reservation) {
                // Load relationships
                $reservation->load(['user', 'egi']);

                // Create notification payload
                $payload = NotificationPayloadReservation::create([
                    'reservation_id' => $reservation->id,
                    'egi_id' => $reservation->egi_id,
                    'user_id' => $reservation->user_id,
                    'type' => NotificationPayloadReservation::TYPE_HIGHEST,
                    'status' => 'success',
                    'data' => [
                        'amount_eur' => $reservation->amount_eur,
                        'egi_title' => $reservation->egi->title,
                        'egi_slug' => $reservation->egi->slug,
                        'previous_rank' => $reservation->getOriginal('rank_position'),
                        'new_rank' => 1
                    ]
                ]);

                $this->logger->debug('[RESERVATION_NOTIFICATION] Payload created', [
                    'payload_id' => $payload->id,
                    'type' => $payload->type
                ]);

                // Send notification to user
                Notification::send($reservation->user, new ReservationHighest($payload));

                // Log success
                $this->logger->info('[RESERVATION_NOTIFICATION] New highest notification sent', [
                    'reservation_id' => $reservation->id,
                    'payload_id' => $payload->id,
                    'user_id' => $reservation->user_id
                ]);
            });

        } catch (\Exception $e) {
            $this->errorManager->handle('RESERVATION_NOTIFICATION_SEND_ERROR', [
                'operation' => 'sendNewHighest',
                'reservation_id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'error_message' => $e->getMessage(),
                'context' => [
                    'method' => __METHOD__,
                    'line' => __LINE__
                ]
            ], $e);

            throw $e;
        }
    }

    /**
     * Send notification when user's reservation is superseded
     *
     * @param Reservation $supersededReservation The reservation that was superseded
     * @param Reservation $newHighest The new highest reservation
     * @return void
     * @throws \Exception If notification fails
     */
    public function sendSuperseded(Reservation $supersededReservation, Reservation $newHighest): void
    {
        $this->logger->info('[RESERVATION_NOTIFICATION] Starting sendSuperseded', [
            'superseded_id' => $supersededReservation->id,
            'new_highest_id' => $newHighest->id,
            'egi_id' => $supersededReservation->egi_id
        ]);

        try {
            DB::transaction(function () use ($supersededReservation, $newHighest) {
                // Load relationships
                $supersededReservation->load(['user', 'egi']);

                // Create notification payload
                $payload = NotificationPayloadReservation::create([
                    'reservation_id' => $supersededReservation->id,
                    'egi_id' => $supersededReservation->egi_id,
                    'user_id' => $supersededReservation->user_id,
                    'type' => NotificationPayloadReservation::TYPE_SUPERSEDED,
                    'status' => 'warning',
                    'data' => [
                        'amount_eur' => $supersededReservation->amount_eur,
                        'new_highest_amount' => $newHighest->amount_eur,
                        'egi_title' => $supersededReservation->egi->title,
                        'egi_slug' => $supersededReservation->egi->slug,
                        'superseded_by_user' => $newHighest->user->name,
                        'previous_rank' => $supersededReservation->getOriginal('rank_position'),
                        'new_rank' => $supersededReservation->rank_position
                    ]
                ]);

                // Update superseded_by_id
                $supersededReservation->update([
                    'superseded_by_id' => $newHighest->id
                ]);

                // Send notification
                Notification::send($supersededReservation->user, new ReservationSuperseded($payload));

                $this->logger->info('[RESERVATION_NOTIFICATION] Superseded notification sent', [
                    'superseded_id' => $supersededReservation->id,
                    'payload_id' => $payload->id,
                    'user_id' => $supersededReservation->user_id
                ]);
            });

        } catch (\Exception $e) {
            $this->errorManager->handle('RESERVATION_NOTIFICATION_SEND_ERROR', [
                'operation' => 'sendSuperseded',
                'superseded_id' => $supersededReservation->id,
                'new_highest_id' => $newHighest->id,
                'error_message' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Send notification when user's rank changes significantly
     *
     * @param Reservation $reservation The reservation with changed rank
     * @param int $previousRank The previous rank position
     * @param int $threshold Minimum rank change to trigger notification (default: 3)
     * @return void
     */
    public function sendRankChanged(Reservation $reservation, int $previousRank, int $threshold = 3): void
    {
        // Calculate rank change
        $rankChange = abs($reservation->rank_position - $previousRank);

        // Only notify if change is significant
        if ($rankChange < $threshold) {
            $this->logger->debug('[RESERVATION_NOTIFICATION] Rank change below threshold', [
                'reservation_id' => $reservation->id,
                'previous_rank' => $previousRank,
                'new_rank' => $reservation->rank_position,
                'change' => $rankChange,
                'threshold' => $threshold
            ]);
            return;
        }

        $this->logger->info('[RESERVATION_NOTIFICATION] Starting sendRankChanged', [
            'reservation_id' => $reservation->id,
            'previous_rank' => $previousRank,
            'new_rank' => $reservation->rank_position,
            'change' => $rankChange
        ]);

        try {
            DB::transaction(function () use ($reservation, $previousRank) {
                $reservation->load(['user', 'egi']);

                // Determine if improved or worsened
                $improved = $reservation->rank_position < $previousRank;

                // Create notification payload
                $payload = NotificationPayloadReservation::create([
                    'reservation_id' => $reservation->id,
                    'egi_id' => $reservation->egi_id,
                    'user_id' => $reservation->user_id,
                    'type' => NotificationPayloadReservation::TYPE_RANK_CHANGED,
                    'status' => $improved ? 'success' : 'warning',
                    'data' => [
                        'amount_eur' => $reservation->amount_eur,
                        'egi_title' => $reservation->egi->title,
                        'egi_slug' => $reservation->egi->slug,
                        'previous_rank' => $previousRank,
                        'new_rank' => $reservation->rank_position,
                        'direction' => $improved ? 'up' : 'down',
                        'positions_changed' => abs($reservation->rank_position - $previousRank)
                    ]
                ]);

                // Send notification
                Notification::send($reservation->user, new RankChanged($payload));

                $this->logger->info('[RESERVATION_NOTIFICATION] Rank change notification sent', [
                    'reservation_id' => $reservation->id,
                    'payload_id' => $payload->id,
                    'direction' => $improved ? 'improved' : 'worsened'
                ]);
            });

        } catch (\Exception $e) {
            $this->errorManager->handle('RESERVATION_NOTIFICATION_SEND_ERROR', [
                'operation' => 'sendRankChanged',
                'reservation_id' => $reservation->id,
                'previous_rank' => $previousRank,
                'new_rank' => $reservation->rank_position,
                'error_message' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Send notification when a competitor withdraws their reservation
     *
     * @param Collection $affectedReservations Reservations that improved rank
     * @param Reservation $withdrawnReservation The reservation that was withdrawn
     * @return void
     */
    public function sendCompetitorWithdrew(Collection $affectedReservations, Reservation $withdrawnReservation): void
    {
        $this->logger->info('[RESERVATION_NOTIFICATION] Starting sendCompetitorWithdrew', [
            'withdrawn_id' => $withdrawnReservation->id,
            'affected_count' => $affectedReservations->count(),
            'egi_id' => $withdrawnReservation->egi_id
        ]);

        try {
            DB::transaction(function () use ($affectedReservations, $withdrawnReservation) {
                $withdrawnReservation->load('egi');

                foreach ($affectedReservations as $reservation) {
                    $reservation->load('user');

                    // Create notification payload for each affected user
                    $payload = NotificationPayloadReservation::create([
                        'reservation_id' => $reservation->id,
                        'egi_id' => $reservation->egi_id,
                        'user_id' => $reservation->user_id,
                        'type' => NotificationPayloadReservation::TYPE_COMPETITOR_WITHDREW,
                        'status' => 'info',
                        'data' => [
                            'amount_eur' => $reservation->amount_eur,
                            'egi_title' => $withdrawnReservation->egi->title,
                            'egi_slug' => $withdrawnReservation->egi->slug,
                            'previous_rank' => $reservation->getOriginal('rank_position'),
                            'new_rank' => $reservation->rank_position,
                            'withdrawn_amount' => $withdrawnReservation->amount_eur,
                            'withdrawn_user' => $withdrawnReservation->user->name ?? 'Un utente'
                        ]
                    ]);

                    // Send notification
                    Notification::send($reservation->user, new CompetitorWithdrew($payload));
                }

                $this->logger->info('[RESERVATION_NOTIFICATION] Competitor withdrew notifications sent', [
                    'withdrawn_id' => $withdrawnReservation->id,
                    'notifications_sent' => $affectedReservations->count()
                ]);
            });

        } catch (\Exception $e) {
            $this->errorManager->handle('RESERVATION_NOTIFICATION_SEND_ERROR', [
                'operation' => 'sendCompetitorWithdrew',
                'withdrawn_id' => $withdrawnReservation->id,
                'affected_count' => $affectedReservations->count(),
                'error_message' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }

    /**
     * Process bulk rank changes and send appropriate notifications
     *
     * @param int $egiId The EGI ID to process
     * @return void
     */
    public function processBulkRankChanges(int $egiId): void
    {
        $this->logger->info('[RESERVATION_NOTIFICATION] Processing bulk rank changes', [
            'egi_id' => $egiId
        ]);

        try {
            // Get all active reservations for this EGI
            $reservations = Reservation::active()
                ->forEgi($egiId)
                ->ranked()
                ->with('user')
                ->get();

            if ($reservations->isEmpty()) {
                $this->logger->debug('[RESERVATION_NOTIFICATION] No active reservations found', [
                    'egi_id' => $egiId
                ]);
                return;
            }

            // Process each reservation
            foreach ($reservations as $reservation) {
                $previousRank = $reservation->getOriginal('rank_position');

                // Skip if rank hasn't changed
                if ($previousRank == $reservation->rank_position) {
                    continue;
                }

                // Handle based on new rank
                if ($reservation->rank_position === 1 && $previousRank !== 1) {
                    // Became highest
                    $this->sendNewHighest($reservation);
                } elseif ($previousRank === 1 && $reservation->rank_position !== 1) {
                    // Was highest, now superseded
                    $newHighest = $reservations->firstWhere('rank_position', 1);
                    if ($newHighest) {
                        $this->sendSuperseded($reservation, $newHighest);
                    }
                } else {
                    // Regular rank change
                    $this->sendRankChanged($reservation, $previousRank);
                }
            }

            $this->logger->info('[RESERVATION_NOTIFICATION] Bulk rank changes processed', [
                'egi_id' => $egiId,
                'reservations_processed' => $reservations->count()
            ]);

        } catch (\Exception $e) {
            $this->errorManager->handle('RESERVATION_NOTIFICATION_BULK_ERROR', [
                'operation' => 'processBulkRankChanges',
                'egi_id' => $egiId,
                'error_message' => $e->getMessage()
            ], $e);

            throw $e;
        }
    }
}
