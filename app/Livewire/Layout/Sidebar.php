<?php

declare(strict_types=1);

namespace App\Livewire\Layout;

use Illuminate\Contracts\View\View;
use Livewire\Component;

use App\Models\ContextHasMenu;

class Sidebar extends Component
{

    public $context;
    public $menuItems;

    public function mount($context = 'general')
    {
        $this->context = $context;
        $this->menuItems = ContextHasMenu::with('barMenu')
            ->where('context', $this->context)
            ->get()
            ->pluck('barMenu');
    }
    public function render(): View
    {
        return view('livewire.sidebar');
    }
}
