<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class CollectorCarousel extends Component
{
    public Collection $collectors;

    /**
     * Create a new component instance.
     */
    public function __construct(Collection $collectors)
    {
        $this->collectors = $collectors;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.collector-carousel');
    }
}
