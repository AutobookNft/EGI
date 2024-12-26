<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Collection;
use App\Models\Team;
use App\Models\TeamUser;
use App\Notifications\TeamInvitationNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Dashboard extends Component
{
    public $collectionsCount;
    public $teamsCount;
    public $teamMembersCount;
    public $notifications;

    public $iconRepository;

    public function mount()
    {

        $this->loadStats();
        $this->loadNotifications();
    }

    public function loadStats()
    {
        $this->collectionsCount = Collection::where('creator_id', Auth::id())->count();

        $this->teamsCount = Team::where('user_id', '=',Auth::id())->count();

        // Conta quanti membri attivi sono presenti nei vari team, dal conteggio viene escluso l'utente corrente
        $this->teamMembersCount = TeamUser::whereHas('team', function ($query) {
            $query->whereHas('users', function ($subQuery) {
                $subQuery->where('user_id', Auth::id());
            });
        })
        ->where('status', 'active')
        ->where('user_id', '!=', Auth::id())
        ->count();

    }

    public function loadNotifications()
    {
        $this->notifications = Auth::user()->notifications;
        Log::channel('florenceegi')->info('Notifications', [
            'notifications' => $this->notifications
        ]);
    }

    public function acceptInvitation($invitationId)
    {
        $invitation = TeamUser::where('user_id', '=',$invitationId);
        $invitation->update(['status' => 'active']);

        // Elimina la notifica
        Auth::user()->notifications()->where('data->invitation_id', $invitationId)->delete();

        session()->flash('message', __('Invitation accepted successfully!'));
        $this->loadStats();
        $this->loadNotifications();
    }

    public function declineInvitation($invitationId)
    {
        $invitation = TeamUser::findOrFail($invitationId);
        $invitation->delete();

        // Elimina la notifica
        Auth::user()->notifications()->where('data->invitation_id', $invitationId)->delete();

        session()->flash('message', __('Invitation declined successfully!'));
        $this->loadStats();
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
