<?php

declare(strict_types=1);

namespace App\Http\Controllers\Notifications\Wallets;

use App\DataTransferObjects\Payloads\Wallets\{
    WalletAcceptRequest,
    WalletRejectRequest,
    WalletResponse,
    WalletError
};

use App\Enums\NotificationStatus;
use App\Http\Controllers\Controller;
use App\Models\CustomDatabaseNotification;
use App\Services\Notifications\InvitationService;
use App\Services\Notifications\ResponseWalletService;
use Exception;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Auth, Log};
use Illuminate\View\View;

class NotificationWalletResponseController extends Controller
{
    public function __construct(
        private readonly ResponseWalletService $responseWalletService,
        private readonly InvitationService $responseInvitationService,
    ) {}

    /**
     * Gestisce la risposta a una notifica wallet (accettazione o rifiuto)
     * @param Request $request
     * @param string $notificationId
     * @return JsonResponse
     * @throws Exception
     */
    public function response(Request $request): JsonResponse
    {
        $action = $request->input('action');
        $notificationId = $request->input('notificationId');

        Log::channel('florenceegi')->info('Risposta notifica wallet', [
            'notification_id' => $notificationId,
            'action' => $action
        ]);


        if (!in_array($action, [
            NotificationStatus::ACCEPTED->value,
            NotificationStatus::UPDATE->value,
            NotificationStatus::REJECTED->value,
            NotificationStatus::ARCHIVED->value,
            NotificationStatus::DONE->value])) {
            return response()->json(
                WalletResponse::error(
                    $notificationId,
                    __('collection.wallet.validation.invalid_action')
                )->toArray(),
                400
            );
        }

        Log::channel('florenceegi')->info('Risposta notifica wallet', [
            'notification_id' => $notificationId,
            'action' => $action
        ]);

        try {

            $notification = $this->findNotification($notificationId);

            return match($action) {
                NotificationStatus::ACCEPTED->value => $this->handleAcceptCreate($notification),
                NotificationStatus::UPDATE->value => $this->handleAcceptUpdate($notification),
                NotificationStatus::REJECTED->value => $this->handleReject($notification, $request),
                NotificationStatus::ARCHIVED->value => $this->handleArchive($request, $notification),
                NotificationStatus::DONE->value => $this->handleDone($notification),

                default => $this->handleDone($notification)
            };


        } catch (Exception $e) {

            Log::channel('florenceegi')->error('Errore elaborazione notifica', [
                'error' => $e->getMessage(),
                'notification_id' => $notificationId,
                'action' => $action
            ]);

            return response()->json(
                WalletResponse::error($notificationId, $e->getMessage())->toArray(),
                500
            );
        }
    }


    /**
     * Gestisce l'accettazione di una notifica wallet
     */
    private function handleAcceptCreate(CustomDatabaseNotification $notification): JsonResponse
    {

        $acceptRequest = WalletAcceptRequest::fromNotification($notification);

        Log::channel('florenceegi')->info('Accettazione notifica wallet', [
            'notification' => $notification,
        ]);

        $this->responseWalletService->acceptCreateWallet($acceptRequest);

        return response()->json(
            WalletResponse::success(
                $notification->id,
                NotificationStatus::ACCEPTED->value
            )->toArray()
        );
    }

    /**
     * Gestisce l'accettazione di una notifica wallet
     */
    private function handleAcceptUpdate(CustomDatabaseNotification $notification): JsonResponse
    {

        $acceptRequest = WalletAcceptRequest::fromNotification($notification);

        Log::channel('florenceegi')->info('Accettazione notifica wallet', [
            'notification' => $notification,
        ]);

        $this->responseWalletService->acceptUpdateWallet($acceptRequest);

        return response()->json(
            WalletResponse::success(
                $notification->id,
                NotificationStatus::ACCEPTED->value
            )->toArray()
        );
    }


    /**
     * Gestisce il rifiuto di una notifica wallet
     */
    private function handleReject(CustomDatabaseNotification $notification, Request $request): JsonResponse {

        Log::channel('florenceegi')->info('Rifiuto notifica wallet', [
            'notification' => $notification,
        ]);

        $rejectRequest = WalletRejectRequest::fromRequest(
            notification: $notification,
            reason: $request->input('reason', '')
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
    * Archivia una notifica marcandola come letta.
    *
    * @param Request $request
    * @param CustomDatabaseNotification $notification
    * @return JsonResponse
    */
    public function handleArchive(Request $request, CustomDatabaseNotification $notification): JsonResponse
    {

        Log::channel('florenceegi')->info('Archiviazione notifica', [
            'notification_id' => $notification
        ]);

        try {

            if (!$notification) {
                return response()->json(
                    WalletResponse::error(
                        $notification->id,
                        __('notifications.not_found')
                    )->toArray(),
                    404
                );
            }

            $notification->update(['read_at' => now()]);

            return response()->json(
                WalletResponse::success(
                    $notification->id,
                    $request->input('action')
                )->toArray(),
                200
            );

        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Errore archiviazione notifica', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(
                WalletResponse::error(
                    $notification->id,
                    $e->getMessage()
                )->toArray(),
                500
            );
        }
    }

    /**
     * Gestisce l'archiviazione di una notifica wallet
     */
    private function handleDone(CustomDatabaseNotification $notification): JsonResponse
    {
        $notification->markAsRead();

        return response()->json(
            WalletResponse::success(
                $notification->id,
                'archived'
            )->toArray()
        );
    }

    /**
     * Trova una notifica per l'utente corrente
     *
     * @throws Exception Se la notifica non viene trovata
     */
    private function findNotification(string $notificationId): CustomDatabaseNotification
    {

        Log::channel('florenceegi')->info('Ricerca notifica', [
            'notification_id' => $notificationId
        ]);

        $notification = Auth::user()
            ->customNotifications()
            ->where('id', $notificationId)
            ->with('model')
            ->first();

        if (!$notification) {
            throw new Exception(__('notifications.not_found'));
        }

        return $notification;
    }

    /**
     * Recupera le notifiche pendenti per l'utente corrente
     */
    public function fetchHeadThumbnailList(): View
    {

        Log::channel('florenceegi')->info('Recupero notifiche pendenti');

        $pendingNotifications = Auth::user()
            ->customNotifications()
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();


        log::channel('florenceegi')->info('Notifiche pendenti', [
            'pending_notifications' => $pendingNotifications->count()
        ]);

        // Definisci la notifica attiva (se presente)
        $activeNotificationId = $pendingNotifications->isNotEmpty()
            ? $pendingNotifications->first()->id
            : null;

        return view('livewire.partials.head-thumbnails-list', [
            'pendingNotifications' => $pendingNotifications,
            'activeNotificationId' => $activeNotificationId // 🔥 Passato alla vista
        ]);
    }


    /**
     * Prepara i dati della notifica per la visualizzazione
     */
    public function prepare(CustomDatabaseNotification $notification): array
    {
        $status = $this->responseWalletService->getNotificationStatus($notification);
        $statusClass = $this->responseWalletService->getNotificationStatusClass($status);

        Log::channel('florenceegi')->info('Preparazione notifica wallet', [
            'notification_id' => $notification->id,
            'status' => $status,
        ]);

        return [
            'notification' => $notification,
            'statusClass' => $statusClass,
            'status' => $status,
        ];
    }


}
