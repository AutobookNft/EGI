<?php

declare(strict_types=1);

namespace App\Http\Controllers\Notifications\Wallets;

use App\DataTransferObjects\Payloads\Wallets\{
    WalletAcceptRequest,
    WalletRejectRequest,
    WalletResponse,
    WalletError
};

use App\Enums\{NotificationStatus, Gdpr\GdprActivityCategory};
use App\Http\Controllers\Controller;
use App\Models\CustomDatabaseNotification;
use App\Services\{
    Notifications\InvitationService,
    Notifications\ResponseWalletService,
    Gdpr\AuditLogService
};
use Exception;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Auth, Log};
use Illuminate\View\View;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller: Wallet Notification Response Handler with GDPR Compliance
 * 🎯 Purpose: Handle wallet notification responses with full GDPR audit trail
 * 🛡️ Privacy: Logs all user interactions for GDPR compliance and transparency
 * 🧱 Core Logic: Process wallet actions while maintaining privacy rights
 *
 * @package App\Http\Controllers\Notifications\Wallets
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 2.0.0 (GDPR Enhanced)
 * @date 2025-06-12
 */
class NotificationWalletResponseController extends Controller {
    
    /**
     * Constructor with GDPR-enhanced dependency injection
     */
    public function __construct(
        private readonly ResponseWalletService $responseWalletService,
        private readonly InvitationService $responseInvitationService,
        private readonly UltraLogManager $logger,
        private readonly ErrorManagerInterface $errorManager,
        private readonly AuditLogService $auditService
    ) {
    }

    /**
     * Gestisce la risposta a una notifica wallet (accettazione o rifiuto) con supporto GDPR completo
     * @param Request $request
     * @param string $notificationId
     * @return JsonResponse
     * @throws Exception
     */
    public function response(Request $request): JsonResponse {
        $userId = Auth::id();
        $action = $request->input('action');
        $notificationId = $request->input('notificationId');

        $logContext = [
            'operation' => 'wallet_notification_response',
            'user_id' => $userId,
            'notification_id' => $notificationId,
            'action' => $action,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString()
        ];

        try {
            // ═══ VALIDATION ═══
            if (!in_array($action, [
                NotificationStatus::ACCEPTED->value,
                NotificationStatus::UPDATE->value,
                NotificationStatus::REJECTED->value,
                NotificationStatus::ARCHIVED->value,
                NotificationStatus::DONE->value
            ])) {
                $errorContext = [
                    ...$logContext,
                    'valid_actions' => [
                        NotificationStatus::ACCEPTED->value,
                        NotificationStatus::UPDATE->value,
                        NotificationStatus::REJECTED->value,
                        NotificationStatus::ARCHIVED->value,
                        NotificationStatus::DONE->value
                    ]
                ];

                return $this->errorManager->handle('WALLET_NOTIFICATION_INVALID_ACTION', $errorContext);
            }

            $this->logger->info('[WalletNotification] Processing wallet notification response', $logContext);

            // ═══ GDPR AUDIT LOG ═══
            $this->auditService->logUserAction(
                Auth::user(),
                'wallet_notification_action_initiated',
                $logContext,
                GdprActivityCategory::WALLET_MANAGEMENT
            );

            // ═══ PROCESS NOTIFICATION ═══
            $notification = $this->findNotification($notificationId);

            $result = match ($action) {
                NotificationStatus::ACCEPTED->value => $this->handleAcceptCreate($notification, $logContext),
                NotificationStatus::UPDATE->value => $this->handleAcceptUpdate($notification, $logContext),
                NotificationStatus::REJECTED->value => $this->handleReject($notification, $request, $logContext),
                NotificationStatus::ARCHIVED->value => $this->handleArchive($request, $notification, $logContext),
                NotificationStatus::DONE->value => $this->handleDone($notification, $logContext),
                default => $this->handleDone($notification, $logContext)
            };

            // ═══ SUCCESS AUDIT LOG ═══
            $this->auditService->logUserAction(
                Auth::user(),
                'wallet_notification_action_completed',
                [
                    ...$logContext,
                    'success' => true,
                    'result_status' => $result->getStatusCode()
                ],
                GdprActivityCategory::WALLET_MANAGEMENT
            );

            $this->logger->info('[WalletNotification] Wallet notification response processed successfully', [
                ...$logContext,
                'success' => true
            ]);

            return $result;

        } catch (Exception $e) {
            $errorContext = [
                ...$logContext,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ];

            // ═══ ERROR AUDIT LOG ═══
            $this->auditService->logUserAction(
                Auth::user(),
                'wallet_notification_action_failed',
                $errorContext,
                GdprActivityCategory::WALLET_MANAGEMENT
            );

            return $this->errorManager->handle('WALLET_NOTIFICATION_PROCESSING_ERROR', $errorContext, $e);
        }
    }


    /**
     * Gestisce l'accettazione di una notifica wallet create con GDPR audit
     */
    private function handleAcceptCreate(CustomDatabaseNotification $notification, array $logContext): JsonResponse {
        $acceptRequest = WalletAcceptRequest::fromNotification($notification);

        $this->logger->info('[WalletNotification] Processing wallet create acceptance', [
            ...$logContext,
            'notification_id' => $notification->id
        ]);

        // ═══ GDPR AUDIT LOG ═══
        $this->auditService->logUserAction(
            Auth::user(),
            'wallet_notification_accept_create',
            [
                ...$logContext,
                'notification_data' => $notification->data
            ],
            GdprActivityCategory::WALLET_MANAGEMENT
        );

        $this->responseWalletService->acceptCreateWallet($acceptRequest);

        return response()->json(
            WalletResponse::success(
                $notification->id,
                NotificationStatus::ACCEPTED->value
            )->toArray()
        );
    }

    /**
     * Gestisce l'accettazione di una notifica wallet update con GDPR audit
     */
    private function handleAcceptUpdate(CustomDatabaseNotification $notification, array $logContext): JsonResponse {
        $acceptRequest = WalletAcceptRequest::fromNotification($notification);

        $this->logger->info('[WalletNotification] Processing wallet update acceptance', [
            ...$logContext,
            'notification_id' => $notification->id
        ]);

        // ═══ GDPR AUDIT LOG ═══
        $this->auditService->logUserAction(
            Auth::user(),
            'wallet_notification_accept_update',
            [
                ...$logContext,
                'notification_data' => $notification->data
            ],
            GdprActivityCategory::WALLET_MANAGEMENT
        );

        $this->responseWalletService->acceptUpdateWallet($acceptRequest);

        return response()->json(
            WalletResponse::success(
                $notification->id,
                NotificationStatus::ACCEPTED->value
            )->toArray()
        );
    }

    /**
     * Gestisce il rifiuto di una notifica wallet con GDPR audit
     */
    private function handleReject(CustomDatabaseNotification $notification, Request $request, array $logContext): JsonResponse {
        $rejectRequest = WalletRejectRequest::fromNotificationAndRequest($notification, $request);

        $this->logger->info('[WalletNotification] Processing wallet rejection', [
            ...$logContext,
            'notification_id' => $notification->id,
            'reason' => $request->input('reason')
        ]);

        // ═══ GDPR AUDIT LOG ═══
        $this->auditService->logUserAction(
            Auth::user(),
            'wallet_notification_rejected',
            [
                ...$logContext,
                'rejection_reason' => $request->input('reason'),
                'notification_data' => $notification->data
            ],
            GdprActivityCategory::WALLET_MANAGEMENT
        );

        $this->responseWalletService->rejectWallet($rejectRequest);

        return response()->json(
            WalletResponse::success(
                $notification->id,
                NotificationStatus::REJECTED->value
            )->toArray()
        );
    }

    /**
     * Gestisce l'archiviazione di una notifica wallet con GDPR audit
     */
    private function handleArchive(Request $request, CustomDatabaseNotification $notification, array $logContext): JsonResponse {
        $this->logger->info('[WalletNotification] Processing wallet archive', [
            ...$logContext,
            'notification_id' => $notification->id
        ]);

        // ═══ GDPR AUDIT LOG ═══
        $this->auditService->logUserAction(
            Auth::user(),
            'wallet_notification_archived',
            [
                ...$logContext,
                'notification_data' => $notification->data
            ],
            GdprActivityCategory::WALLET_MANAGEMENT
        );

        $this->responseWalletService->archiveWallet($notification);

        return response()->json(
            WalletResponse::success(
                $notification->id,
                NotificationStatus::ARCHIVED->value
            )->toArray()
        );
    }

    /**
     * Gestisce il completamento di una notifica wallet con GDPR audit
     */
    private function handleDone(CustomDatabaseNotification $notification, array $logContext): JsonResponse {
        $this->logger->info('[WalletNotification] Processing wallet done', [
            ...$logContext,
            'notification_id' => $notification->id
        ]);

        // ═══ GDPR AUDIT LOG ═══
        $this->auditService->logUserAction(
            Auth::user(),
            'wallet_notification_completed',
            [
                ...$logContext,
                'notification_data' => $notification->data
            ],
            GdprActivityCategory::WALLET_MANAGEMENT
        );

        $this->responseWalletService->doneWallet($notification);

        return response()->json(
            WalletResponse::success(
                $notification->id,
                NotificationStatus::DONE->value
            )->toArray()
        );
    }

    /**
     * Trova una notifica per l'utente corrente con GDPR audit
     *
     * @throws Exception Se la notifica non viene trovata
     */
    private function findNotification(string $notificationId): CustomDatabaseNotification {
        $logContext = [
            'operation' => 'find_wallet_notification',
            'notification_id' => $notificationId,
            'user_id' => Auth::id()
        ];

        $this->logger->info('[WalletNotification] Searching for notification', $logContext);

        $notification = Auth::user()
            ->customNotifications()
            ->where('id', $notificationId)
            ->with('model')
            ->first();

        if (!$notification) {
            $errorContext = [
                ...$logContext,
                'found' => false
            ];
            
            $this->errorManager->handle('WALLET_NOTIFICATION_NOT_FOUND', $errorContext);
            throw new Exception(__('notifications.not_found'));
        }

        // ═══ GDPR AUDIT LOG ═══
        $this->auditService->logUserAction(
            Auth::user(),
            'wallet_notification_accessed',
            [
                ...$logContext,
                'notification_data' => $notification->data
            ],
            GdprActivityCategory::WALLET_MANAGEMENT
        );

        return $notification;
    }

    /**
     * Recupera le notifiche pendenti per l'utente corrente con supporto GDPR completo
     * Gestisce sia richieste browser che AJAX
     */
    public function fetchHeadThumbnailList(Request $request) {
        $userId = Auth::id();
        $isAjax = $request->ajax();
        
        $logContext = [
            'operation' => 'fetch_wallet_notifications',
            'user_id' => $userId,
            'is_ajax' => $isAjax,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString()
        ];

        try {
            // ═══ AUTHENTICATION CHECK ═══
            if (!Auth::check()) {
                $errorContext = [
                    ...$logContext,
                    'authenticated' => false
                ];

                if ($isAjax) {
                    return $this->errorManager->handle('WALLET_NOTIFICATION_UNAUTHORIZED', $errorContext);
                }

                return redirect()->route('login');
            }

            $this->logger->info('[WalletNotification] Fetching pending wallet notifications', $logContext);

            // ═══ GDPR AUDIT LOG ═══
            $this->auditService->logUserAction(
                Auth::user(),
                'wallet_notifications_list_accessed',
                $logContext,
                GdprActivityCategory::WALLET_MANAGEMENT
            );

            // ═══ FETCH NOTIFICATIONS ═══
            $pendingNotifications = Auth::user()
                ->customNotifications()
                ->where(function ($query) {
                    $query->where('outcome', 'LIKE', '%pending%')
                        ->orWhere(function ($subQuery) {
                            $subQuery->whereIn('outcome', ['accepted', 'rejected', 'expired'])
                                ->whereNull('read_at');
                        });
                })
                ->orderBy('created_at', 'desc')
                ->with('model')
                ->get();

            $this->logger->info('[WalletNotification] Wallet notifications retrieved successfully', [
                ...$logContext,
                'count' => $pendingNotifications->count()
            ]);

            // ═══ PREPARE VIEW DATA ═══
            $activeNotificationId = $pendingNotifications->isNotEmpty()
                ? $pendingNotifications->first()->id
                : null;

            $viewData = [
                'pendingNotifications' => $pendingNotifications,
                'activeNotificationId' => $activeNotificationId
            ];

            // ═══ RETURN RESPONSE ═══
            if ($isAjax) {
                $html = view('livewire.partials.head-thumbnails-list', $viewData)->render();

                return response()->json([
                    'success' => true,
                    'html' => $html,
                    'count' => $pendingNotifications->count()
                ]);
            }

            // Return full view for browser requests
            return view('livewire.partials.head-thumbnails-list', $viewData);

        } catch (Exception $e) {
            $errorContext = [
                ...$logContext,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ];

            // ═══ ERROR AUDIT LOG ═══
            $this->auditService->logUserAction(
                Auth::user(),
                'wallet_notifications_fetch_failed',
                $errorContext,
                GdprActivityCategory::WALLET_MANAGEMENT
            );

            return $this->errorManager->handle('WALLET_NOTIFICATION_FETCH_ERROR', $errorContext, $e);
        }
    }


    /**
     * Prepara i dati della notifica per la visualizzazione con GDPR audit
     */
    public function prepare(CustomDatabaseNotification $notification): array {
        $logContext = [
            'operation' => 'prepare_wallet_notification',
            'notification_id' => $notification->id,
            'user_id' => Auth::id()
        ];

        $status = $this->responseWalletService->getNotificationStatus($notification);
        $statusClass = $this->responseWalletService->getNotificationStatusClass($status);

        $this->logger->info('[WalletNotification] Preparing notification for display', [
            ...$logContext,
            'status' => $status
        ]);

        // ═══ GDPR AUDIT LOG ═══
        $this->auditService->logUserAction(
            Auth::user(),
            'wallet_notification_prepared_for_display',
            [
                ...$logContext,
                'notification_status' => $status
            ],
            GdprActivityCategory::WALLET_MANAGEMENT
        );

        return [
            'notification' => $notification,
            'statusClass' => $statusClass,
            'status' => $status,
        ];
    }
}
