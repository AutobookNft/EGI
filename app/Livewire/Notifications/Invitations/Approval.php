<?php

namespace App\Livewire\Notifications\Invitations;


use Livewire\Component;

class Approval extends Component
{

    public $notification;

    public function mount($notification)
    {
        $this->notification = $notification;
    }

    public function render()
    {
        return view('livewire.notifications.invitations.approval');
    }

}
