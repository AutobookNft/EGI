<?php

namespace App\Livewire;

use App\Models\CustomDatabaseNotification;
use App\Models\User;
use App\Services\Notifications\NotificationHandlerFactory;
use Livewire\Component;
use App\Models\Collection;
use App\Models\CollectionUser;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

class Dashboard extends Component
{
    public $collectionsCount;
    public $collectionMembersCount;
    public $notifications;
    public $viewingHistoricalNotifications = false;

    public $showHistoricalNotifications = false;

    public $pendingNotifications = [];
    public $historicalNotifications = [];

    public $activeNotificationId = null;

    public function mount()
    {

        Log::channel('florenceegi')->info('Dashboard: mount');

        $this->loadStats();
        $this->loadNotifications();

        // Se ci sono notifiche pendenti, seleziona automaticamente la prima
        if ($this->pendingNotifications->isNotEmpty()) {
            $this->activeNotificationId = $this->pendingNotifications->first()->id;
        }
    }

    public function loadStats()
    {
        $this->collectionsCount = Collection::where('creator_id', Auth::id())->count();

        $this->collectionMembersCount = CollectionUser::whereHas('collection', function ($query) {
            $query->where('creator_id', Auth::id());
        })
        ->where('user_id', '!=', Auth::id())
        ->count();


        Log::channel('florenceegi')->info('Dashboard: loadStats', [
            'collectionsCount' => $this->collectionsCount,
            'collectionMembersCount' => $this->collectionMembersCount,
        ]);
    }

    /**
     * Questo metodo gestisce l'evento "proposal-declined" emesso dal metodo decline() del componente DeclineProposalModal.
     *
     * @return void
     */
    #[On('proposal-declined')]
    public function handleProposalDeclined()
    {
        // Log dell'evento per verifica
        Log::channel('florenceegi')->info('Dashboard: proposal-declined event received.');

        // Ricaricare le notifiche pendenti e storiche
        $this->loadNotifications();

        // Mostrare un messaggio di successo all'utente
        session()->flash('message', __('The proposal was declined successfully and a notification was sent to the proposer.'));
    }

    #[On('proposal-accepted')]
    public function handleProposalAccepted()
    {
        // Log dell'evento per verifica
        Log::channel('florenceegi')->info('Dashboard: proposal-accepted event received.');

        // Ricaricare le notifiche pendenti e storiche
        $this->loadNotifications();

        // Mostrare un messaggio di successo all'utente
        session()->flash('message', __('The proposal was accepted successfully and a notification was sent to the proposer.'));
    }

    public function openDeclineModal($notification)
    {
        $this->dispatch('open-decline-modal', $notification);
    }

    public function openAcceptModal($notification)
    {
        // Log::channel('florenceegi')->info('Dashboard: openAcceptModal', [
        //     'notification' => $notification,
        // ]);

        // il listener si trova in app/Livewire/Proposals/AcceptProposalModal.php
        $this->dispatch('open-accept-modal', $notification);
    }

    // public function notificationArchive($notificationId, $action)
    // {
    //     $notification = Auth::user()->notifications()->findOrFail($notificationId);
    //     $notification->update([
    //         'read_at' => now(),
    //         'outcome' => $action,
    //     ]);

    //     $this->loadNotifications();
    // }
    #[On('deleteNotification')]
    public function deleteNotification($notificationId)
    {
        Log::channel('florenceegi')->info('🗑 deleteNotificationAction() - Deleting notification:', [
            'notificationId' => $notificationId,
        ]);
        $notification = Auth::user()->notifications()->findOrFail($notificationId);
        $notification->delete();

        $this->loadNotifications();
    }
    #[On('load-notifications')]
    public function loadNotifications()
    {
        // Usa optional() per evitare errori se user è null
        $this->pendingNotifications = optional(Auth::user())->customNotifications()
            ?->where(function ($query) {
                $query->where('outcome', 'LIKE', '%pending%')
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereIn('outcome', ['accepted', 'rejected', 'expired'])
                                ->whereNull('read_at');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->with('model')
            ->get() ?? collect();

        Log::channel('florenceegi')->info('🔍 loadNotifications() - Pending Notifications:', [
            'pendingNotifications' => $this->pendingNotifications,
            'activeNotificationId' => $this->activeNotificationId,
        ]);

        // Notifiche storiche
        $this->historicalNotifications = optional(Auth::user())->customNotifications()
            ?->whereNotNull('read_at')
            ->with('model')
            ->orderBy('read_at', 'desc')
            ->get() ?? collect();

        Log::channel('florenceegi')->info('🔍 loadNotifications() - Historical Notifications:', [
            'historicalNotifications' => $this->historicalNotifications->pluck('id')->toArray(),
        ]);

        Log::channel('florenceegi')->info('🔍 loadNotifications() - Active Notification:', [
            'activeNotification' => $this->historicalNotifications,
        ]);
    }

    public function handleNotificationAction($notificationId, $action)
    {

        // crea il record della notifica corrente, per trovare i dati necessari alla risposta
        $notification = Auth::user()->notifications()->findOrFail($notificationId);

        $type = $notification->type;

        $message_to = $notification->data['user_id'];

        $handler = NotificationHandlerFactory::getHandler($type);
        $handler->handle($message_to, $notification, $action);

        $this->loadStats();
        $this->loadNotifications();
    }

    public function toggleHistoricalNotifications()
    {
        $this->loadNotifications();
        $this->showHistoricalNotifications = !$this->showHistoricalNotifications;
    }

    #[On('setActiveNotification')]
    public function setActiveNotification($id)
    {
        $this->activeNotificationId = $id;
        $this->loadNotifications();

        Log::channel('florenceegi')->info('🔄 setActiveNotification() - Active Notification Set:', [
            'activeNotificationId' => $this->activeNotificationId,
        ]);

        // Dispatch a Livewire per aggiornare solo il componente della notifica attiva
        $this->dispatch('notification-updated');
    }


    public function getActiveNotification()
    {
        Log::channel('florenceegi')->info('🔎 getActiveNotification() - Checking for active notification:', [
            'activeNotificationId' => $this->activeNotificationId,
        ]);

        if (!$this->activeNotificationId) {
            Log::channel('florenceegi')->info('⚠️ getActiveNotification() - No activeNotificationId set.');
            return null;
        }

        $notification = Auth::user()
            ->customNotifications()
            ->where('id', $this->activeNotificationId)
            ->with('model')
            ->first();

        if (!$notification) {
            Log::channel('florenceegi')->error('❌ getActiveNotification() - Notification NOT FOUND in DB!', [
                'activeNotificationId' => $this->activeNotificationId,
            ]);
            return null;
        }

        // Recupera la vista dal file di configurazione
        $viewKey = $notification->view ?? null;
        $config = $viewKey ? config('notification-views.' . $viewKey, []) : [];
        $view = $config['view'] ?? null;
        $render = $config['render'] ?? 'livewire';

        Log::channel('florenceegi')->info('✅ getActiveNotification() - View retrieved:', [
            'viewKey' => $viewKey,
            'view' => $view,
            'render' => $render,
        ]);

        return $notification;
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'pendingNotifications' => $this->pendingNotifications ?? [],
            'historicalNotifications' => $this->historicalNotifications ?? [],
        ]);
    }
}
