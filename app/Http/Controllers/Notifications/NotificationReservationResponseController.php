<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Models\NotificationPayloadReservation;
use App\Services\Notifications\ReservationNotificationHandler;
use App\Helpers\FegiAuth;
use App\Services\Gdpr\AuditLogService;
use App\Enums\Gdpr\GdprActivityCategory;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Controller for handling reservation notification responses
 *
 * @package App\Http\Controllers\Notifications
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Pre-Launch Reservation System)
 * @date 2025-08-15
 * @purpose Handle API endpoints for reservation notification user interactions
 */
class NotificationReservationResponseController extends Controller
{
    /**
     * Constructor with dependency injection
     *
     * @param ReservationNotificationHandler $handler Notification handler
     * @param UltraLogManager $logger Ultra Log Manager
     * @param ErrorManagerInterface $errorManager Ultra Error Manager
     * @param AuditLogService $auditLogService GDPR audit logging service
     */
    public function __construct(
        private ReservationNotificationHandler $handler,
        private UltraLogManager $logger,
        private ErrorManagerInterface $errorManager,
        private AuditLogService $auditLogService
    ) {}

    /**
     * Handle notification response (main endpoint)
     *
     * @param Request $request The incoming request
     * @return JsonResponse API response
     */
    public function response(Request $request): JsonResponse
    {
        // Validate request
        $validated = $request->validate([
            'notificationId' => 'required|uuid',
            'action' => ['required', 'string', Rule::in(['archive', 'dismiss', 'view_details', 'view_ranking'])],
            'payload' => 'required|string|in:reservation',
            'data' => 'array'
        ]);

        $this->logger->info('[RESERVATION_RESPONSE] Processing notification response', [
            'notification_id' => $validated['notificationId'],
            'action' => $validated['action'],
            'user_id' => FegiAuth::id(),
            'ip' => $request->ip()
        ]);

        // GDPR: Log dell'azione di risposta reservation
        $this->auditLogService->logUserAction(
            user: FegiAuth::user(),
            action: 'reservation_notification_response',
            context: [
                'notification_id' => $validated['notificationId'],
                'action' => $validated['action'],
                'payload_type' => $validated['payload'],
                'request_data' => $validated['data'] ?? [],
            ],
            category: GdprActivityCategory::NOTIFICATION_MANAGEMENT
        );

        try {
            // Get the notification
            $notification = DB::table('notifications')
                ->where('id', $validated['notificationId'])
                ->where('notifiable_id', FegiAuth::id())
                ->where('notifiable_type', 'App\Models\User')
                ->first();

            if (!$notification) {
                // GDPR: Log dell'errore notifica non trovata
                $this->auditLogService->logUserAction(
                    user: FegiAuth::user(),
                    action: 'reservation_notification_not_found',
                    context: [
                        'notification_id' => $validated['notificationId'],
                        'error_type' => 'notification_not_found',
                    ],
                    category: GdprActivityCategory::NOTIFICATION_MANAGEMENT
                );

                $this->logger->warning('[RESERVATION_RESPONSE] Notification not found', [
                    'notification_id' => $validated['notificationId'],
                    'user_id' => FegiAuth::id()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => __('reservation.notifications.not_found'),
                    'error' => 'NOTIFICATION_NOT_FOUND'
                ], 404);
            }

            // Decode notification data to get payload ID
            $notificationData = json_decode($notification->data, true);
            $payloadId = $notificationData['payload_id'] ?? null;

            if (!$payloadId) {
                $this->logger->error('[RESERVATION_RESPONSE] Payload ID not found in notification', [
                    'notification_id' => $validated['notificationId'],
                    'notification_data' => $notificationData
                ]);

                return response()->json([
                    'success' => false,
                    'message' => __('reservation.notifications.invalid_notification'),
                    'error' => 'INVALID_NOTIFICATION_DATA'
                ], 422);
            }

            // Get the payload
            $payload = NotificationPayloadReservation::find($payloadId);

            if (!$payload) {
                // GDPR: Log dell'errore payload non trovato
                $this->auditLogService->logUserAction(
                    user: FegiAuth::user(),
                    action: 'reservation_payload_not_found',
                    context: [
                        'payload_id' => $payloadId,
                        'notification_id' => $validated['notificationId'],
                        'error_type' => 'payload_not_found',
                    ],
                    category: GdprActivityCategory::NOTIFICATION_MANAGEMENT
                );

                $this->logger->error('[RESERVATION_RESPONSE] Payload not found', [
                    'payload_id' => $payloadId,
                    'notification_id' => $validated['notificationId']
                ]);

                return response()->json([
                    'success' => false,
                    'message' => __('reservation.notifications.payload_not_found'),
                    'error' => 'PAYLOAD_NOT_FOUND'
                ], 404);
            }

            // Verify ownership
            if ($payload->user_id !== FegiAuth::id()) {
                // GDPR: Log del tentativo di accesso non autorizzato
                $this->auditLogService->logUserAction(
                    user: FegiAuth::user(),
                    action: 'reservation_unauthorized_access_attempt',
                    context: [
                        'payload_id' => $payloadId,
                        'payload_user_id' => $payload->user_id,
                        'requesting_user_id' => FegiAuth::id(),
                        'error_type' => 'unauthorized_access',
                    ],
                    category: GdprActivityCategory::NOTIFICATION_MANAGEMENT
                );

                $this->logger->warning('[RESERVATION_RESPONSE] Unauthorized access attempt', [
                    'payload_id' => $payloadId,
                    'payload_user_id' => $payload->user_id,
                    'request_user_id' => FegiAuth::id()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => __('reservation.notifications.unauthorized'),
                    'error' => 'UNAUTHORIZED'
                ], 403);
            }

            // Handle the action
            $handlerData = array_merge(
                $validated['data'] ?? [],
                ['notification_id' => $validated['notificationId']]
            );

            $result = $this->handler->handle(
                $validated['action'],
                $payload,
                $handlerData
            );

            // Log result
            $this->logger->info('[RESERVATION_RESPONSE] Action completed', [
                'notification_id' => $validated['notificationId'],
                'action' => $validated['action'],
                'success' => $result['success'] ?? false
            ]);

            // GDPR: Log del completamento azione
            $this->auditLogService->logUserAction(
                user: FegiAuth::user(),
                action: 'reservation_action_completed',
                context: [
                    'notification_id' => $validated['notificationId'],
                    'action' => $validated['action'],
                    'payload_id' => $payloadId,
                    'success' => $result['success'] ?? false,
                ],
                category: GdprActivityCategory::NOTIFICATION_MANAGEMENT
            );

            return response()->json($result);

        } catch (\Exception $e) {
            // GDPR: Log dell'errore di sistema
            $this->auditLogService->logUserAction(
                user: FegiAuth::user(),
                action: 'reservation_response_system_error',
                context: [
                    'notification_id' => $validated['notificationId'] ?? null,
                    'action' => $validated['action'] ?? null,
                    'error_type' => 'Exception',
                    'error_message' => $e->getMessage(),
                ],
                category: GdprActivityCategory::NOTIFICATION_MANAGEMENT
            );

            $this->errorManager->handle('RESERVATION_RESPONSE_ERROR', [
                'notification_id' => $validated['notificationId'] ?? null,
                'action' => $validated['action'] ?? null,
                'user_id' => FegiAuth::id(),
                'error_message' => $e->getMessage()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => __('reservation.notifications.error_processing'),
                'error' => 'PROCESSING_ERROR'
            ], 500);
        }
    }

    /**
     * Archive notification (dedicated endpoint)
     *
     * @param Request $request The incoming request
     * @return JsonResponse API response
     */
    public function notificationArchive(Request $request): JsonResponse
    {
        // Validate request
        $validated = $request->validate([
            'notificationId' => 'required|uuid',
            'payload' => 'required|string|in:reservation'
        ]);

        $this->logger->info('[RESERVATION_RESPONSE] Archive notification request', [
            'notification_id' => $validated['notificationId'],
            'user_id' => FegiAuth::id()
        ]);

        // GDPR: Log della richiesta di archiviazione
        $this->auditLogService->logUserAction(
            user: FegiAuth::user(),
            action: 'reservation_notification_archive_request',
            context: [
                'notification_id' => $validated['notificationId'],
                'payload_type' => $validated['payload'],
            ],
            category: GdprActivityCategory::NOTIFICATION_MANAGEMENT
        );

        // Forward to main response method with archive action
        $request->merge(['action' => 'archive']);
        return $this->response($request);
    }

    /**
     * Get notification details
     *
     * @param Request $request The incoming request
     * @return JsonResponse API response
     */
    public function getDetails(Request $request): JsonResponse
    {
        // Validate request
        $validated = $request->validate([
            'notificationId' => 'required|uuid'
        ]);

        $this->logger->info('[RESERVATION_RESPONSE] Get notification details', [
            'notification_id' => $validated['notificationId'],
            'user_id' => FegiAuth::id()
        ]);

        // GDPR: Log della richiesta dettagli notifica
        $this->auditLogService->logUserAction(
            user: FegiAuth::user(),
            action: 'reservation_notification_details_request',
            context: [
                'notification_id' => $validated['notificationId'],
            ],
            category: GdprActivityCategory::NOTIFICATION_MANAGEMENT
        );

        try {
            // Get notification with payload
            $notification = DB::table('notifications')
                ->where('id', $validated['notificationId'])
                ->where('notifiable_id', FegiAuth::id())
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => __('reservation.notifications.not_found'),
                    'error' => 'NOT_FOUND'
                ], 404);
            }

            // Decode notification data
            $notificationData = json_decode($notification->data, true);
            $payloadId = $notificationData['payload_id'] ?? null;

            if (!$payloadId) {
                return response()->json([
                    'success' => false,
                    'message' => __('reservation.notifications.invalid_notification'),
                    'error' => 'INVALID_DATA'
                ], 422);
            }

            // Get payload with relationships
            $payload = NotificationPayloadReservation::with(['reservation.egi', 'reservation.user'])
                ->find($payloadId);

            if (!$payload || $payload->user_id !== FegiAuth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => __('reservation.notifications.unauthorized'),
                    'error' => 'UNAUTHORIZED'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'notification' => [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'read_at' => $notification->read_at,
                        'created_at' => $notification->created_at
                    ],
                    'payload' => [
                        'id' => $payload->id,
                        'type' => $payload->type,
                        'status' => $payload->status,
                        'data' => $payload->data,
                        'message' => $payload->getMessage()
                    ],
                    'reservation' => [
                        'id' => $payload->reservation->id,
                        'amount_eur' => $payload->reservation->amount_eur,
                        'rank_position' => $payload->reservation->rank_position,
                        'is_highest' => $payload->reservation->is_highest
                    ],
                    'egi' => [
                        'id' => $payload->reservation->egi->id,
                        'title' => $payload->reservation->egi->title,
                        'slug' => $payload->reservation->egi->slug
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            // GDPR: Log dell'errore dettagli
            $this->auditLogService->logUserAction(
                user: FegiAuth::user(),
                action: 'reservation_notification_details_error',
                context: [
                    'notification_id' => $validated['notificationId'],
                    'error_type' => 'Exception',
                    'error_message' => $e->getMessage(),
                ],
                category: GdprActivityCategory::NOTIFICATION_MANAGEMENT
            );

            $this->errorManager->handle('RESERVATION_DETAILS_ERROR', [
                'notification_id' => $validated['notificationId'],
                'user_id' => FegiAuth::id(),
                'error_message' => $e->getMessage()
            ], $e);

            return response()->json([
                'success' => false,
                'message' => __('reservation.notifications.error_loading_details'),
                'error' => 'LOADING_ERROR'
            ], 500);
        }
    }

    /**
     * Get current ranking for an EGI from notification
     *
     * @param Request $request The incoming request
     * @return JsonResponse API response
     */
    public function getRanking(Request $request): JsonResponse
    {
        // Validate request
        $validated = $request->validate([
            'notificationId' => 'required|uuid',
            'limit' => 'integer|min:5|max:50'
        ]);

        $limit = $validated['limit'] ?? 10;

        $this->logger->info('[RESERVATION_RESPONSE] Get ranking request', [
            'notification_id' => $validated['notificationId'],
            'limit' => $limit,
            'user_id' => FegiAuth::id()
        ]);

        // Forward to main response method with view_ranking action
        $request->merge([
            'action' => 'view_ranking',
            'payload' => 'reservation',
            'data' => ['limit' => $limit]
        ]);

        return $this->response($request);
    }
}
