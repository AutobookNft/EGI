<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection; // Alias per evitare conflitti
use Illuminate\View\Component;

class EgiCarousel extends Component
{
    /**
     * La collezione di EGI da mostrare nel carousel.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    public EloquentCollection $egis;

    /**
     * Create a new component instance.
     *
     * @param \Illuminate\Database\Eloquent\Collection $egis
     */
    public function __construct(EloquentCollection $egis)
    {
        $this->egis = $egis;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        // Passa solo gli EGI che hanno effettivamente un'immagine valida
        // Questo evita errori nel rendering delle slide se un EGI non ha immagine
        $validEgis = $this->egis->filter(function ($egi) {
            return $egi->collection_id && $egi->user_id && $egi->key_file && $egi->extension;
        });

        return view('components.egi-carousel', ['validEgis' => $validEgis]);
    }
}
