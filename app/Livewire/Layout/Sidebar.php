<?php

declare(strict_types=1);

namespace App\Livewire\Layout;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Sidebar extends Component
{
    public function render(): View
    {
        return view('livewire.layout.sidebar');
    }
} 