<?php

namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;
use App\Models\NotificationPayloadReservation;
use Illuminate\Database\Eloquent\Model;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * Handler for reservation notification actions following the pattern
 *
 * @package App\Services\Notifications
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Pre-Launch Reservation System)
 * @date 2025-08-15
 * @purpose Handle user responses to reservation notifications (archive, dismiss, etc.)
 */
class ReservationNotificationHandler implements NotificationHandlerInterface
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
     * Handle notification response action
     *
     * @param string $action The action to perform (archive, dismiss, etc.)
     * @param Model $payload The notification payload model
     * @param array $data Additional data for the action
     * @return array Response array with success status and message
     */
    public function handle(string $action, Model $payload, array $data = []): array
    {
        if (!$payload instanceof NotificationPayloadReservation) {
            $this->logger->error('[RESERVATION_HANDLER] Invalid payload type', [
                'expected' => NotificationPayloadReservation::class,
                'received' => get_class($payload),
                'action' => $action
            ]);

            return [
                'success' => false,
                'message' => __('reservation.notifications.invalid_payload'),
                'error' => 'INVALID_PAYLOAD_TYPE'
            ];
        }

        $this->logger->info('[RESERVATION_HANDLER] Processing action', [
            'action' => $action,
            'payload_id' => $payload->id,
            'reservation_id' => $payload->reservation_id,
            'user_id' => $payload->user_id,
            'type' => $payload->type
        ]);

        try {
            return match($action) {
                'archive' => $this->handleArchive($payload, $data),
                'dismiss' => $this->handleDismiss($payload, $data),
                'view_details' => $this->handleViewDetails($payload, $data),
                'view_ranking' => $this->handleViewRanking($payload, $data),
                default => $this->handleUnknownAction($action, $payload)
            };
        } catch (\Exception $e) {
            $this->errorManager->handle('RESERVATION_HANDLER_ERROR', [
                'action' => $action,
                'payload_id' => $payload->id,
                'error_message' => $e->getMessage(),
                'context' => [
                    'method' => __METHOD__,
                    'line' => __LINE__
                ]
            ], $e);

            return [
                'success' => false,
                'message' => __('reservation.notifications.action_failed'),
                'error' => 'HANDLER_ERROR'
            ];
        }
    }

    /**
     * Handle archive action - marks notification as read
     *
     * @param NotificationPayloadReservation $payload The notification payload
     * @param array $data Additional data
     * @return array Response array
     */
    private function handleArchive(NotificationPayloadReservation $payload, array $data): array
    {
        try {
            // Mark the notification as read and archived
            if (isset($data['notification_id'])) {
                $notification = \DB::table('notifications')
                    ->where('id', $data['notification_id'])
                    ->where('notifiable_id', $payload->user_id)
                    ->first();

                if ($notification) {
                    \DB::table('notifications')
                        ->where('id', $data['notification_id'])
                        ->update([
                            'read_at' => now(),
                            'outcome' => \App\Enums\NotificationStatus::ARCHIVED->value // 🎯 QUESTO È IL FIX!
                        ]);

                    $this->logger->info('[RESERVATION_HANDLER] Notification archived', [
                        'notification_id' => $data['notification_id'],
                        'payload_id' => $payload->id,
                        'user_id' => $payload->user_id,
                        'previous_outcome' => $notification->outcome,
                        'new_outcome' => \App\Enums\NotificationStatus::ARCHIVED->value
                    ]);
                }
            }

            return [
                'success' => true,
                'message' => __('reservation.notifications.archived_success'),
                'data' => [
                    'payload_id' => $payload->id,
                    'archived_at' => now()->toIso8601String()
                ]
            ];

        } catch (\Exception $e) {
            $this->logger->error('[RESERVATION_HANDLER] Archive failed', [
                'payload_id' => $payload->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Handle dismiss action - soft delete without marking as read
     *
     * @param NotificationPayloadReservation $payload The notification payload
     * @param array $data Additional data
     * @return array Response array
     */
    private function handleDismiss(NotificationPayloadReservation $payload, array $data): array
    {
        try {
            // Soft delete the notification without marking as read
            if (isset($data['notification_id'])) {
                \DB::table('notifications')
                    ->where('id', $data['notification_id'])
                    ->where('notifiable_id', $payload->user_id)
                    ->update(['dismissed_at' => now()]);

                $this->logger->info('[RESERVATION_HANDLER] Notification dismissed', [
                    'notification_id' => $data['notification_id'],
                    'payload_id' => $payload->id
                ]);
            }

            return [
                'success' => true,
                'message' => __('reservation.notifications.dismissed_success'),
                'data' => [
                    'payload_id' => $payload->id,
                    'dismissed_at' => now()->toIso8601String()
                ]
            ];

        } catch (\Exception $e) {
            $this->logger->error('[RESERVATION_HANDLER] Dismiss failed', [
                'payload_id' => $payload->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Handle view details action - returns reservation details
     *
     * @param NotificationPayloadReservation $payload The notification payload
     * @param array $data Additional data
     * @return array Response array
     */
    private function handleViewDetails(NotificationPayloadReservation $payload, array $data): array
    {
        try {
            $payload->load(['reservation.egi', 'reservation.user']);

            $this->logger->info('[RESERVATION_HANDLER] View details requested', [
                'payload_id' => $payload->id,
                'reservation_id' => $payload->reservation_id
            ]);

            return [
                'success' => true,
                'message' => __('reservation.notifications.details_loaded'),
                'data' => [
                    'reservation' => [
                        'id' => $payload->reservation->id,
                        'amount_eur' => $payload->reservation->amount_eur,
                        'rank_position' => $payload->reservation->rank_position,
                        'created_at' => $payload->reservation->created_at->toIso8601String()
                    ],
                    'egi' => [
                        'id' => $payload->reservation->egi->id,
                        'title' => $payload->reservation->egi->title,
                        'slug' => $payload->reservation->egi->slug
                    ]
                ]
            ];

        } catch (\Exception $e) {
            $this->logger->error('[RESERVATION_HANDLER] View details failed', [
                'payload_id' => $payload->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Handle view ranking action - returns current ranking for the EGI
     *
     * @param NotificationPayloadReservation $payload The notification payload
     * @param array $data Additional data
     * @return array Response array
     */
    private function handleViewRanking(NotificationPayloadReservation $payload, array $data): array
    {
        try {
            // Get current ranking for this EGI
            $rankings = \App\Models\Reservation::active()
                ->forEgi($payload->egi_id)
                ->ranked()
                ->with('user:id,name')
                ->take(10)
                ->get(['id', 'user_id', 'amount_eur', 'rank_position', 'created_at']);

            $this->logger->info('[RESERVATION_HANDLER] Ranking requested', [
                'payload_id' => $payload->id,
                'egi_id' => $payload->egi_id,
                'rankings_count' => $rankings->count()
            ]);

            return [
                'success' => true,
                'message' => __('reservation.notifications.ranking_loaded'),
                'data' => [
                    'egi_id' => $payload->egi_id,
                    'rankings' => $rankings->map(function ($reservation) use ($payload) {
                        return [
                            'rank' => $reservation->rank_position,
                            'amount_eur' => $reservation->amount_eur,
                            'user_name' => $reservation->user->name ?? 'Anonimo',
                            'is_mine' => $reservation->user_id === $payload->user_id,
                            'created_at' => $reservation->created_at->toIso8601String()
                        ];
                    })
                ]
            ];

        } catch (\Exception $e) {
            $this->logger->error('[RESERVATION_HANDLER] View ranking failed', [
                'payload_id' => $payload->id,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Handle unknown action
     *
     * @param string $action The unknown action
     * @param NotificationPayloadReservation $payload The notification payload
     * @return array Response array
     */
    private function handleUnknownAction(string $action, NotificationPayloadReservation $payload): array
    {
        $this->logger->warning('[RESERVATION_HANDLER] Unknown action requested', [
            'action' => $action,
            'payload_id' => $payload->id,
            'supported_actions' => ['archive', 'dismiss', 'view_details', 'view_ranking']
        ]);

        return [
            'success' => false,
            'message' => __('reservation.notifications.unknown_action'),
            'error' => 'UNKNOWN_ACTION',
            'data' => [
                'requested_action' => $action,
                'supported_actions' => ['archive', 'dismiss', 'view_details', 'view_ranking']
            ]
        ];
    }

    /**
     * Get supported actions for this handler
     *
     * @return array List of supported actions
     */
    public function getSupportedActions(): array
    {
        return [
            'archive' => __('reservation.notifications.actions.archive'),
            'dismiss' => __('reservation.notifications.actions.dismiss'),
            'view_details' => __('reservation.notifications.actions.view_details'),
            'view_ranking' => __('reservation.notifications.actions.view_ranking')
        ];
    }
}