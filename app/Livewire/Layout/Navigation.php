<?php

declare(strict_types=1);

namespace App\Livewire\Layout;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Navigation extends Component
{

    public $currentTeam;

    public function render(): View
    {

        // $this->currentTeam = Auth::user()->currentTeam?->name ?? NESSUN_TEAM;

        return view('livewire.layout.navigation', [
            'user' => Auth::user(),
        ]);
    }
}
