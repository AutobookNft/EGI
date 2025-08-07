<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class CollectorCard extends Component {
    public $collector;
    public $imageType;
    public $displayType;

    /**
     * Create a new component instance.
     */
    public function __construct($collector, $imageType = 'card', $displayType = 'default') {
        $this->collector = $collector;
        $this->imageType = $imageType;
        $this->displayType = $displayType;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View {
        return view('components.collector-card');
    }
}
