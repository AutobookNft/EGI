<?php

namespace App\Services\Notifications;

use App\Models\Reservation;
use App\Models\User;
use App\Models\CustomDatabaseNotification;
use App\Models\NotificationPayloadReservation;
use App\Notifications\Reservations\ReservationSuperseded;
use App\Notifications\Reservations\ReservationHighest;
use App\Notifications\Reservations\RankChanged;
use App\Notifications\Reservations\RankImproved;
use App\Notifications\Reservations\CompetitorWithdrew;
use App\Enums\NotificationStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * @package App\Services\Notifications
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Reservation Notifications)
 * @date 2025-08-15
 * @purpose Handle reservation-related notifications using existing notification system
 */
class ReservationNotificationService
{
    /**
     * System user ID for platform notifications
     */
    const SYSTEM_USER_ID = 1;

    /**
     * Send notification when reservation becomes the highest
     */
    public function sendNewHighest(Reservation $reservation): void
    {
        try {
            DB::transaction(function () use ($reservation) {
                // Get competitors count
                $competitorsCount = $reservation->getCompetitors()->count();

                // Create payload
                $payload = NotificationPayloadReservation::create([
                    'reservation_id' => $reservation->id,
                    'egi_id' => $reservation->egi_id,
                    'user_id' => $reservation->user_id,
                    'type' => NotificationPayloadReservation::TYPE_HIGHEST,
                    'status' => NotificationPayloadReservation::STATUS_SUCCESS,
                    'data' => [
                        'egi_title' => $reservation->egi->title ?? 'EGI #' . $reservation->egi_id,
                        'amount_eur' => $reservation->amount_eur,
                        'total_competitors' => $competitorsCount,
                        'previous_rank' => $reservation->previous_rank,
                    ]
                ]);

                // Send notification to user
                $user = User::find($reservation->user_id);
                if ($user) {
                    Notification::send($user, new ReservationHighest($payload));

                    Log::channel('florenceegi')->info('[NOTIFICATION] New highest notification sent', [
                        'reservation_id' => $reservation->id,
                        'user_id' => $user->id,
                        'amount_eur' => $reservation->amount_eur,
                    ]);
                }

                // Record notification in reservation
                $reservation->recordNotification('highest', [
                    'amount_eur' => $reservation->amount_eur,
                    'competitors' => $competitorsCount,
                ]);
            });
        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('[NOTIFICATION] Failed to send new highest notification', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Send notification when reservation is superseded
     */
    public function sendSuperseded(Reservation $supersededReservation, Reservation $newHighest): void
    {
        try {
            DB::transaction(function () use ($supersededReservation, $newHighest) {
                // Create payload
                $payload = NotificationPayloadReservation::create([
                    'reservation_id' => $supersededReservation->id,
                    'egi_id' => $supersededReservation->egi_id,
                    'user_id' => $supersededReservation->user_id,
                    'type' => NotificationPayloadReservation::TYPE_SUPERSEDED,
                    'status' => NotificationPayloadReservation::STATUS_WARNING,
                    'data' => [
                        'egi_title' => $supersededReservation->egi->title ?? 'EGI #' . $supersededReservation->egi_id,
                        'previous_amount' => $supersededReservation->amount_eur,
                        'new_highest_amount' => $newHighest->amount_eur,
                        'new_rank' => $supersededReservation->rank_position,
                        'superseded_by_user' => $newHighest->user->name ?? 'Un altro utente',
                    ]
                ]);

                // Send notification
                $user = User::find($supersededReservation->user_id);
                if ($user) {
                    Notification::send($user, new ReservationSuperseded($payload));

                    Log::channel('florenceegi')->info('[NOTIFICATION] Superseded notification sent', [
                        'reservation_id' => $supersededReservation->id,
                        'user_id' => $user->id,
                        'new_highest' => $newHighest->amount_eur,
                    ]);
                }

                // Record notification
                $supersededReservation->recordNotification('superseded', [
                    'new_highest' => $newHighest->amount_eur,
                    'superseded_by' => $newHighest->user_id,
                ]);
            });
        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('[NOTIFICATION] Failed to send superseded notification', [
                'reservation_id' => $supersededReservation->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Send notification for rank change
     */
    public function sendRankChanged(Reservation $reservation, int $oldRank, int $newRank): void
    {
        try {
            // Only send if change is significant (2+ positions)
            if (abs($oldRank - $newRank) < 2) {
                return;
            }

            DB::transaction(function () use ($reservation, $oldRank, $newRank) {
                $isImprovement = $newRank < $oldRank;

                // Create payload
                $payload = NotificationPayloadReservation::create([
                    'reservation_id' => $reservation->id,
                    'egi_id' => $reservation->egi_id,
                    'user_id' => $reservation->user_id,
                    'type' => NotificationPayloadReservation::TYPE_RANK_CHANGED,
                    'status' => $isImprovement
                        ? NotificationPayloadReservation::STATUS_SUCCESS
                        : NotificationPayloadReservation::STATUS_INFO,
                    'data' => [
                        'egi_title' => $reservation->egi->title ?? 'EGI #' . $reservation->egi_id,
                        'amount_eur' => $reservation->amount_eur,
                        'old_rank' => $oldRank,
                        'new_rank' => $newRank,
                        'direction' => $isImprovement ? 'up' : 'down',
                        'positions_changed' => abs($oldRank - $newRank),
                    ]
                ]);

                // Send notification
                $user = User::find($reservation->user_id);
                if ($user) {
                    Notification::send($user, new RankChanged($payload));

                    Log::channel('florenceegi')->info('[NOTIFICATION] Rank changed notification sent', [
                        'reservation_id' => $reservation->id,
                        'old_rank' => $oldRank,
                        'new_rank' => $newRank,
                    ]);
                }

                // Record notification
                $reservation->recordNotification('rank_changed', [
                    'old_rank' => $oldRank,
                    'new_rank' => $newRank,
                ]);
            });
        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('[NOTIFICATION] Failed to send rank changed notification', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Send notification when rank improves (specific positive message)
     */
    public function sendRankImproved(Reservation $reservation, int $oldRank): void
    {
        try {
            DB::transaction(function () use ($reservation, $oldRank) {
                $newRank = $reservation->rank_position;

                // Create payload
                $payload = NotificationPayloadReservation::create([
                    'reservation_id' => $reservation->id,
                    'egi_id' => $reservation->egi_id,
                    'user_id' => $reservation->user_id,
                    'type' => NotificationPayloadReservation::TYPE_RANK_CHANGED,
                    'status' => NotificationPayloadReservation::STATUS_SUCCESS,
                    'data' => [
                        'egi_title' => $reservation->egi->title ?? 'EGI #' . $reservation->egi_id,
                        'amount_eur' => $reservation->amount_eur,
                        'old_rank' => $oldRank,
                        'new_rank' => $newRank,
                        'direction' => 'up',
                        'positions_gained' => $oldRank - $newRank,
                    ],
                    'message' => "Grande! Sei salito alla posizione #{$newRank} per " .
                                ($reservation->egi->title ?? 'EGI #' . $reservation->egi_id),
                ]);

                // Send notification
                $user = User::find($reservation->user_id);
                if ($user) {
                    Notification::send($user, new RankImproved($payload));

                    Log::channel('florenceegi')->info('[NOTIFICATION] Rank improved notification sent', [
                        'reservation_id' => $reservation->id,
                        'old_rank' => $oldRank,
                        'new_rank' => $newRank,
                    ]);
                }
            });
        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('[NOTIFICATION] Failed to send rank improved notification', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Send notification when rank improves due to competitor withdrawal
     */
    public function sendRankImprovedAfterWithdrawal(Reservation $reservation, int $oldRank, int $newRank): void
    {
        try {
            DB::transaction(function () use ($reservation, $oldRank, $newRank) {
                // Create payload
                $payload = NotificationPayloadReservation::create([
                    'reservation_id' => $reservation->id,
                    'egi_id' => $reservation->egi_id,
                    'user_id' => $reservation->user_id,
                    'type' => NotificationPayloadReservation::TYPE_COMPETITOR_WITHDREW,
                    'status' => NotificationPayloadReservation::STATUS_SUCCESS,
                    'data' => [
                        'egi_title' => $reservation->egi->title ?? 'EGI #' . $reservation->egi_id,
                        'amount_eur' => $reservation->amount_eur,
                        'old_rank' => $oldRank,
                        'new_rank' => $newRank,
                        'positions_gained' => $oldRank - $newRank,
                    ],
                    'message' => "Un concorrente si è ritirato! Sei salito alla posizione #{$newRank}",
                ]);

                // Send notification
                $user = User::find($reservation->user_id);
                if ($user) {
                    Notification::send($user, new CompetitorWithdrew($payload));

                    Log::channel('florenceegi')->info('[NOTIFICATION] Competitor withdrew notification sent', [
                        'reservation_id' => $reservation->id,
                        'new_rank' => $newRank,
                    ]);
                }
            });
        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('[NOTIFICATION] Failed to send competitor withdrew notification', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Send pre-launch reminder (future use)
     */
    public function sendPreLaunchReminder(Reservation $reservation, int $daysUntilLaunch): void
    {
        try {
            DB::transaction(function () use ($reservation, $daysUntilLaunch) {
                // Create payload
                $payload = NotificationPayloadReservation::create([
                    'reservation_id' => $reservation->id,
                    'egi_id' => $reservation->egi_id,
                    'user_id' => $reservation->user_id,
                    'type' => NotificationPayloadReservation::TYPE_PRE_LAUNCH_REMINDER,
                    'status' => NotificationPayloadReservation::STATUS_INFO,
                    'data' => [
                        'egi_title' => $reservation->egi->title ?? 'EGI #' . $reservation->egi_id,
                        'amount_eur' => $reservation->amount_eur,
                        'rank_position' => $reservation->rank_position,
                        'days_until_launch' => $daysUntilLaunch,
                        'is_highest' => $reservation->is_highest,
                    ],
                    'message' => "Il mint on-chain inizierà tra {$daysUntilLaunch} giorni! " .
                                "Sei in posizione #{$reservation->rank_position} per " .
                                ($reservation->egi->title ?? 'questo EGI'),
                ]);

                // Send notification
                $user = User::find($reservation->user_id);
                if ($user) {
                    // This would use a specific PreLaunchReminder notification class
                    // For now, using the base notification
                    CustomDatabaseNotification::create([
                        'type' => 'App\\Notifications\\Reservations\\PreLaunchReminder',
                        'view' => 'notifications.reservations.pre-launch-reminder',
                        'notifiable_type' => User::class,
                        'notifiable_id' => $user->id,
                        'sender_id' => self::SYSTEM_USER_ID,
                        'model_type' => get_class($payload),
                        'model_id' => $payload->id,
                        'data' => $payload->data,
                    ]);

                    Log::channel('florenceegi')->info('[NOTIFICATION] Pre-launch reminder sent', [
                        'reservation_id' => $reservation->id,
                        'days_until_launch' => $daysUntilLaunch,
                    ]);
                }

                // Record notification
                $reservation->recordNotification('pre_launch_reminder', [
                    'days_until_launch' => $daysUntilLaunch,
                ]);
            });
        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('[NOTIFICATION] Failed to send pre-launch reminder', [
                'reservation_id' => $reservation->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Send bulk notifications for an EGI (e.g., when EGI details change)
     */
    public function sendEgiBulkNotification(int $egiId, string $message, string $type = 'info'): void
    {
        try {
            $reservations = Reservation::active()->forEgi($egiId)->get();

            foreach ($reservations as $reservation) {
                DB::transaction(function () use ($reservation, $message, $type) {
                    // Create payload
                    $payload = NotificationPayloadReservation::create([
                        'reservation_id' => $reservation->id,
                        'egi_id' => $reservation->egi_id,
                        'user_id' => $reservation->user_id,
                        'type' => 'egi_update',
                        'status' => $type,
                        'data' => [
                            'egi_title' => $reservation->egi->title ?? 'EGI #' . $reservation->egi_id,
                            'rank_position' => $reservation->rank_position,
                        ],
                        'message' => $message,
                    ]);

                    // Send notification
                    $user = User::find($reservation->user_id);
                    if ($user) {
                        CustomDatabaseNotification::create([
                            'type' => 'App\\Notifications\\Reservations\\EgiUpdate',
                            'view' => 'notifications.reservations.egi-update',
                            'notifiable_type' => User::class,
                            'notifiable_id' => $user->id,
                            'sender_id' => self::SYSTEM_USER_ID,
                            'model_type' => get_class($payload),
                            'model_id' => $payload->id,
                            'data' => $payload->data,
                        ]);
                    }
                });
            }

            Log::channel('florenceegi')->info('[NOTIFICATION] Bulk EGI notification sent', [
                'egi_id' => $egiId,
                'recipients_count' => $reservations->count(),
                'message' => $message,
            ]);

        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('[NOTIFICATION] Failed to send bulk EGI notification', [
                'egi_id' => $egiId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get unread notifications count for user
     */
    public function getUnreadCount(int $userId): int
    {
        return NotificationPayloadReservation::unread()
            ->forUser($userId)
            ->count();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId): bool
    {
        $payload = NotificationPayloadReservation::find($notificationId);

        if ($payload) {
            return $payload->markAsRead();
        }

        return false;
    }

    /**
     * Get user's notification history
     */
    public function getUserNotifications(int $userId, int $limit = 20): \Illuminate\Pagination\LengthAwarePaginator
    {
        return NotificationPayloadReservation::forUser($userId)
            ->with(['reservation', 'egi'])
            ->orderByDesc('created_at')
            ->paginate($limit);
    }
}
