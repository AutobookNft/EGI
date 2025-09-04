<?php

namespace App\View\Components;

use App\Helpers\FegiAuth;
use App\Models\CustomDatabaseNotification;
use Illuminate\View\Component;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * NotificationBadge Component
 *
 * @package App\View\Components
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Notification Badge)
 * @date 2025-08-17
 * @purpose Autonomous notification badge component with TypeScript selection
 */
class NotificationBadge extends Component {
    protected UltraLogManager $logger;
    protected ErrorManagerInterface $errorManager;

    public array $notifications;
    public int $unreadCount;
    public bool $hasNotifications;

    public function __construct(UltraLogManager $logger, ErrorManagerInterface $errorManager) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;

        // Inizializza le proprietà typed prima di loadNotifications()
        $this->notifications = [];
        $this->unreadCount = 0;
        $this->hasNotifications = false;

        $this->loadNotifications();
    }

    /**
     * Load notifications for current user
     */
    protected function loadNotifications(): void {
        try {
            $user = FegiAuth::user();

            if (!$user) {
                $this->notifications = [];
                $this->unreadCount = 0;
                $this->hasNotifications = false;
                return;
            }

            $this->logger->info('[NOTIFICATION_BADGE] Loading notifications for user', [
                'user_id' => $user->id,
                'user_type' => $user->is_weak_auth ? 'weak' : 'strong'
            ]);

            // Get latest 5 unread notifications (same logic as Dashboard)
            $notifications = $user->customNotifications()
                ->where(function ($query) {
                    $query->where(function ($subQuery) {
                        $subQuery->where('outcome', 'LIKE', '%pending%')
                            ->whereNull('read_at');
                    })->orWhere(function ($subQuery) {
                        $subQuery->whereIn('outcome', ['accepted', 'rejected', 'expired'])
                            ->whereNull('read_at');
                    });
                })
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Get unread count
            $this->unreadCount = $user->customNotifications()
                ->whereNull('read_at')
                ->count();

            // Format notifications for display
            $this->notifications = $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $this->getNotificationType($notification),
                    'view' => $notification->data['view'] ?? 'notification',
                    'message' => $this->getNotificationMessage($notification),
                    'created_at' => $notification->created_at->diffForHumans(),
                    'is_read' => $notification->read_at !== null,
                    'url' => route('dashboard') . '#notification-' . $notification->id
                ];
            })->toArray();

            $this->hasNotifications = count($this->notifications) > 0;

            $this->logger->info('[NOTIFICATION_BADGE] Notifications loaded successfully', [
                'user_id' => $user->id,
                'total_notifications' => count($this->notifications),
                'unread_count' => $this->unreadCount
            ]);
        } catch (\Exception $e) {
            $this->logger->error('[NOTIFICATION_BADGE] Failed to load notifications', [
                'user_id' => FegiAuth::id(),
                'error' => $e->getMessage()
            ]);

            $this->notifications = [];
            $this->unreadCount = 0;
            $this->hasNotifications = false;
        }
    }

    /**
     * Get human-readable notification type
     */
    protected function getNotificationType($notification): string {
        // Try to extract from model_type first
        if ($notification->model_type) {
            $modelClass = class_basename($notification->model_type);

            switch ($modelClass) {
                case 'NotificationPayloadReservation':
                    return 'Prenotazione';
                case 'NotificationPayloadGdpr':
                    return 'GDPR';
                case 'NotificationPayloadCollection':
                    return 'Collezione';
                case 'NotificationPayloadEgi':
                    return 'EGI';
                default:
                    break;
            }
        }

        // Fallback to notification type
        if ($notification->type) {
            $typeClass = class_basename($notification->type);

            if (str_contains($typeClass, 'Reservation')) {
                return 'Prenotazione';
            } elseif (str_contains($typeClass, 'Gdpr')) {
                return 'GDPR';
            } elseif (str_contains($typeClass, 'Collection')) {
                return 'Collezione';
            } elseif (str_contains($typeClass, 'Egi')) {
                return 'EGI';
            }
        }

        return 'Notifica';
    }

    /**
     * Get notification message text
     */
    protected function getNotificationMessage($notification): string {
        try {
            // Try to get message from data
            $data = is_string($notification->data) ? json_decode($notification->data, true) : $notification->data;

            if (isset($data['message'])) {
                return $data['message'];
            }

            // Try to get from model if available
            if ($notification->model && method_exists($notification->model, 'getMessage')) {
                return $notification->model->getMessage();
            }

            // Fallback based on type
            $type = $this->getNotificationType($notification);

            switch ($type) {
                case 'Prenotazione':
                    return 'Nuova attività sulla tua prenotazione';
                case 'GDPR':
                    return 'Aggiornamento privacy';
                case 'Collezione':
                    return 'Aggiornamento collezione';
                case 'EGI':
                    return 'Aggiornamento EGI';
                default:
                    return 'Hai una nuova notifica';
            }
        } catch (\Exception $e) {
            $this->logger->warning('[NOTIFICATION_BADGE] Failed to extract message', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);

            return 'Hai una nuova notifica';
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render() {
        return view('components.notification-badge');
    }
}