<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\View\Component;
use App\Models\Egi;

class EgiCollectionNavigator extends Component {
    /**
     * The collection of EGIs for navigation.
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    public EloquentCollection $collectionEgis;

    /**
     * The current EGI being displayed.
     *
     * @var \App\Models\Egi
     */
    public Egi $currentEgi;

    /**
     * Create a new component instance.
     *
     * @param \Illuminate\Database\Eloquent\Collection $collectionEgis
     * @param \App\Models\Egi $currentEgi
     */
    public function __construct(EloquentCollection $collectionEgis, Egi $currentEgi) {
        $this->collectionEgis = $collectionEgis;
        $this->currentEgi = $currentEgi;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View {
        return view('components.egi-collection-navigator');
    }
}
