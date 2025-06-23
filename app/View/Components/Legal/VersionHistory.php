<?php

namespace App\View\Components\Legal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class VersionHistory extends Component
{
    /**
     * Create a new component instance.
     *
     * @param array $versions Dati delle versioni passati dal controller.
     */
    public function __construct(public array $versions = [])
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.legal.version-history');
    }
}
