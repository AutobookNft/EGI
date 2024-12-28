<?php

namespace App\Livewire;

use App\Models\CollectionInvitation;
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

    public function acceptInvitation($invitationId)
    {
        $invitation = CollectionInvitation::findOrFail($invitationId);

        // Aggiorna lo stato dell'invito
        $invitation->update(['status' => 'accepted']);

        // Aggiunge l'utente alla collection
        CollectionUser::create([
            'collection_id' => $invitation->collection_id,
            'user_id' => Auth::id(),
            'role' => $invitation->role,
        ]);

        // Elimina la notifica associata
        Auth::user()->notifications()->where('data->invitation_id', $invitationId)->delete();

        session()->flash('message', __('Invitation accepted successfully!'));
        $this->loadStats();
        $this->loadNotifications();
    }

    public function declineInvitation($invitationId)
    {
        $invitation = CollectionInvitation::findOrFail($invitationId);

        // Aggiorna lo stato dell'invito a 'declined'
        $invitation->update(['status' => 'declined']);

        // Elimina la notifica associata
        Auth::user()->notifications()->where('data->invitation_id', $invitationId)->delete();

        session()->flash('message', __('Invitation declined successfully!'));
        $this->loadStats();
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = Auth::user()->notifications;
        Log::channel('florenceegi')->info('Notifications', [
            'notifications' => $this->notifications
        ]);
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
