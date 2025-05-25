<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CollectionHeroBanner extends Component
{
    public $collections;
    public $currentIndex;

    /**
     * Create a new component instance.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $collections
     * @param  int  $currentIndex
     * @return void
     */
    public function __construct($collections, $currentIndex = 0)
    {
        $this->collections = $collections;
        $this->currentIndex = $currentIndex;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.collection-hero-banner');
    }
}
