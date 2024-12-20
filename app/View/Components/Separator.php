<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Separator extends Component
{
    public $class;

    /**
     * Create a new component instance.
     *
     * @param string $class
     */
    public function __construct($class = 'border-gray-200 dark:border-gray-600')
    {
        $this->class = $class;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.separator');
    }
}
