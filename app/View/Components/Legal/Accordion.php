<?php

namespace App\View\Components\Legal;

use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Accordion extends Component
{
    /**
     * Create a new component instance.
     *
     * @param Collection|array $items La collezione di articoli da mostrare.
     * @param bool $isFaq Flag per cambiare lo stile se è una sezione FAQ.
     */
    public function __construct(
        public Collection|array $items,
        public bool $isFaq = false
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.legal.accordion');
    }
}
