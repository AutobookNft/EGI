<?php

namespace App\Livewire\Notifications\Invitations;

use App\Models\CustomDatabaseNotification;
use Livewire\Component;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Approval extends Component
{
    public $notification;

    public function mount(CustomDatabaseNotification $notification)
    {
        $this->notification = $notification;
        Log::channel('florenceegi')->info('Livewire Component Mounted', [
            'notification' => $notification,
        ]);
    }

    public function archive()
    {

        $this->notification->update(['read_at' => now()]); // Imposta la notifica come letta

    }

    public function render()
    {
        return view('livewire.notifications.invitations.approval');

    }
}

