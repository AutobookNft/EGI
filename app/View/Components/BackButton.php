<?php

namespace App\View\Components;

use Illuminate\View\Component;

class BackButton extends Component
{
    public $label;

    /**
     * Create a new component instance.
     *
     * @param string $label
     */
    public function __construct($label = 'Torna Indietro')
    {
        $this->label = __('label.came_to_back');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.back-button');
    }
}
