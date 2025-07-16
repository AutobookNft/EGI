<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NavigationMenu extends Component
{
    public function render()
    {
        return view('livewire.navigation-menu', [
            'user' => Auth::user(),
        ]);
    }
}
