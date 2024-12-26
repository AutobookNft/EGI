<?php

namespace App\Livewire\Collections;

use Livewire\Component;
use App\Models\TeamUser;
use Illuminate\Support\Facades\Log;

class CollectionUserTeam extends Component
{
    public $teamUsers; // Lista membri del team
    public $teamId;
    public $collectionId;

    public function mount($id, $teamId)
    {
        Log::channel('florenceegi')->info('Team id', [
            'collectionId' => $id
        ]);

        $this->teamId = $teamId;
        $this->collectionId = $id;
        $this->loadTeamUsers();
    }

    public function openInviteModal()
    {
        $this->dispatch('open-invite-modal'); // Invia un evento ai figli compatibile con Livewire 3
    }

    public function loadTeamUsers()
    {
        $this->teamUsers = TeamUser::where('team_id', $this->teamId)->get();

    }

    public function render()
    {
        return view('livewire.collections.collection-user-team');
    }
}
