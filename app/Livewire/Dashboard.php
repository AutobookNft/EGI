<?php

namespace App\Livewire;

use App\Models\NotificationPayloadWallet;
use App\Models\WalletChangeApproval;
use App\Services\Notifications\NotificationHandlerFactory;
use Livewire\Component;
use App\Models\Collection;
use App\Models\CollectionUser;
use Illuminate\Support\Facades\Auth;
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
        Log::channel('florenceegi')->info('Dashboard: openAcceptModal', [
            'notification' => $notification,
        ]);

        // il listener si trova in app/Livewire/Proposals/AcceptProposalModal.php
        $this->dispatch('open-accept-modal', $notification);
    }

    public function notificationArchive($notificationId, $action)
    {
        $notification = Auth::user()->notifications()->findOrFail($notificationId);
        $notification->update([
            'read_at' => now(),
            'outcome' => $action,
        ]);

        $this->loadNotifications();
    }

    public function deleteNotificationAction($notificationId)
    {
        $notification = Auth::user()->notifications()->findOrFail($notificationId);
        $notification->delete();

        $this->loadNotifications();
    }

    public function loadNotifications()
    {

        // Notifiche pendenti
        $this->pendingNotifications = Auth::user()
            ->customNotifications()
            ->where(function ($query) {
                $query->where('outcome', 'pending');
            })
            ->with('model') // Carica la relazione polimorfica (NotificationPayloadWallet, ecc.)
            ->tap(function($query) {
                Log::channel('florenceegi')->info('SQL Query:', [
                    'sql' => $query->toSql(),
                    'bindings' => $query->getBindings()
                ]);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                $notification->approval_details = $notification->model;
                Log::channel('florenceegi')->info('Dashboard: loadNotifications', [
                    'notification' => $notification,
                ]);

                return $notification;
            });

        Log::channel('florenceegi')->info('Active notification:', [
            'pendingNotifications' => $this->pendingNotifications,
        ]);


        // Notifiche storiche
        $this->historicalNotifications = Auth::user()
            ->customNotifications()
            ->whereIn('outcome', ['accepted', 'rejected', 'done'])
            ->with('model')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                $notification->approval_details = $notification->model;
                return $notification;
            });
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
        $this->showHistoricalNotifications = !$this->showHistoricalNotifications;
    }

    public function getNotificationView($notification)
    {

        $notificationViews = [
            'App\Notifications\WalletChangeRequestCreation' => 'notifications.wallet-change-request',
            'App\Notifications\WalletChangeResponseRejection' => 'notifications.wallet-change-response-rejected',
            'App\Notifications\InvitationProposal' => 'notifications.invitation',
            'App\Livewire\Proposals\ProposalDeclinedNotification' => 'notifications.proposa-declined-notification',
        ];

        return $notificationViews[$notification->type] ;
    }

    public function setActiveNotification($id)
    {

        $this->activeNotificationId = $id;

        $this->dispatch('notification-changed');  // Dispatch un evento
    }

    public function getActiveNotification()
    {
        if (!$this->activeNotificationId) {
            return null;
        }

        $notification = Auth::user()
            ->customNotifications()
            ->where('id', $this->activeNotificationId) // Filtra per l'ID specifico
            ->with('model') // Carica la relazione polimorfica
            ->first();

        if ($notification) {
            $notification->approval_details = $notification->model; // Aggiungi approval_details se necessario
            Log::channel('florenceegi')->info('Notification loaded:', [
                'notification' => $notification,
            ]);
        }

        // Se non trovata, cerca nelle notifiche storiche
        if (!$notification && $this->showHistoricalNotifications) {
            $notification = $this->historicalNotifications->firstWhere('id', $this->activeNotificationId);
        }

        return $notification;
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'pendingNotifications' => $this->pendingNotifications,
            'historicalNotifications' => $this->historicalNotifications,
        ]);
    }
}
