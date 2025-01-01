<?php

namespace App\Livewire;

use App\Models\CollectionInvitation;
use App\Services\Notifications\NotificationHandlerFactory;
use Livewire\Component;
use App\Models\Collection;
use App\Models\CollectionUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Dashboard extends Component
{
    public $collectionsCount;
    public $collectionMembersCount;
    public $notifications;

    public $iconRepository;

    public function mount()
    {
        $this->loadStats();
        $this->loadNotifications();
    }

    public function loadStats()
    {
        // Conta il numero di collection create dall'utente autenticato
        $this->collectionsCount = Collection::where('creator_id', Auth::id())->count();

        // Conta il numero totale di membri delle collection, escludendo l'utente autenticato
        $this->collectionMembersCount = CollectionUser::whereHas('collection', function ($query) {
            $query->where('creator_id', Auth::id());
        })
        ->where('user_id', '!=', Auth::id())
        ->count();
    }

    public function handleNotificationAction($notificationId, $action)
    {
        $notification = Auth::user()->notifications()->findOrFail($notificationId);
        $type = $notification->type;

        $handler = NotificationHandlerFactory::getHandler($type);
        $handler->handle($notification, $action);

        $this->loadStats();
        $this->loadNotifications();
    }

    // public function acceptInvitation($invitationId)
    // {
    //     $invitation = CollectionInvitation::findOrFail($invitationId);

    //     // Aggiorna lo stato dell'invito
    //     $invitation->update(['status' => 'accepted']);

    //     // Aggiunge l'utente alla collection
    //     CollectionUser::create([
    //         'collection_id' => $invitation->collection_id,
    //         'user_id' => Auth::id(),
    //         'role' => $invitation->role,
    //     ]);

    //     // Aggiorna l'outcome della notifica associata
    //     $notification = Auth::user()->notifications()->where('data->invitation_id', $invitationId)->first();
    //     if ($notification) {
    //         $notification->update(['outcome' => 'accepted']);
    //     }

    //     session()->flash('message', __('Invitation accepted successfully!'));
    //     $this->loadStats();
    //     $this->loadNotifications();
    // }

    //     public function declineInvitation($invitationId)
    // {
    //     $invitation = CollectionInvitation::findOrFail($invitationId);

    //     // Aggiorna lo stato dell'invito a 'declined'
    //     $invitation->update(['status' => 'declined']);

    //     // Aggiorna l'outcome della notifica associata
    //     $notification = Auth::user()->notifications()->where('data->invitation_id', $invitationId)->first();
    //     if ($notification) {
    //         $notification->update(['outcome' => 'declined']);
    //     }

    //     session()->flash('message', __('Invitation declined successfully!'));
    //     $this->loadStats();
    //     $this->loadNotifications();
    // }


    public function loadNotifications()
    {
        $this->notifications = Auth::user()->notifications;
        Log::channel('florenceegi')->info('Notifications', [
            'notifications' => $this->notifications
        ]);
    }

    public function getNotificationView($notification)
    {
        $notificationViews = [
            'App\Notifications\WalletChangeRequest' => 'notifications.wallet-change-request',
            'App\Notifications\CollectionInvitationNotification' => 'notifications.invitation',
        ];

        return $notificationViews[$notification->type] ?? 'notifications.default';
    }


    public function render()
    {
        return view('livewire.dashboard');
    }
}
