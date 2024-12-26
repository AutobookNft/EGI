<?php

namespace App\Livewire\Collections;

use App\Models\TeamUser;
use App\Notifications\TeamInvitationNotification;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\On;
use Spatie\Permission\Models\Role;

class InviteUserToTeamModal extends Component
{
    public $email;
    public $role;
    public $roles;
    public $show = false; // Proprietà per gestire la visibilità della modale
    public $teamId;

    public $user_id;

    public function mount($teamId)
    {
        $this->teamId = $teamId;

        // Carico i ruoli disponibili da Spatie
        $this->roles = Role::all()->pluck('name'); // Recupera tutti i ruoli dalla tabella 'roles'

    }

    public function invite()
    {

        // cerca lo user usando la email
        $user = User::where('email', '=', $this->email)->first();
        if ($user) {
            $this->user_id = $user->id;
        }

        Log::channel('florenceegi')->info('Team id', [
            'email' => $this->email,
            'roles' => $this->roles,
            'user_id' => $this->user_id,
        ]);


        $this->validate([
        'user_id' => 'required|exists:users,id',
        'role' => 'required|string|in:' . implode(',', $this->roles->toArray()),
        ]);

        // Logica per invitare l'utente al team
        TeamUser::create([
            'team_id' => $this->teamId,
            'user_id' => $this->user_id,
            'role' => $this->role,
            'status' => 'pending',
        ]);

        // Invia notifica all'utente invitato
        Notification::send($user, new TeamInvitationNotification($this->user_id));

        $this->show = false;
        $this->dispatch('team-member-updated'); // Notifica il genitore
    }

    #[On('openInviteModal')]
    public function showInviteModal()
    {
        $this->resetFields(); // Pulisce i campi
        $this->show = true; // Mostra la modale
    }

    public function resetFields()
    {
        $this->email = '';
        $this->role = '';
    }

    public function closeModal()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.collections.invite-user-to-team-modal');
    }
}
