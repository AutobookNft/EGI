<?php

namespace App\Livewire\Collections;

use App\Models\CollectionUser;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class CollectionUserMember extends Component
{
    public $collectionUsers; // Lista membri del team
    public $teamId;
    public $collectionId;

    public function mount($id)
    {
        Log::channel('florenceegi')->info('Collection id', [
            'collectionId' => $id
        ]);


        $this->collectionId = $id;
        $this->loadTeamUsers();
    }

    public function openInviteModal()
    {
        $this->dispatch('open-invite-modal'); // Invia un evento ai figli compatibile con Livewire 3
    }

    public function loadTeamUsers()
    {
        $this->collectionUsers = CollectionUser::where('collection_id', $this->collectionId)->get();
        Log::channel('florenceegi')->info('CollectionUsers', [
            'collectionUsers' => $this->collectionUsers
        ]);

    }

    public function render()
    {
        return view('livewire.collections.collection-user');
    }
}
