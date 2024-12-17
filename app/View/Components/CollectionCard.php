<?php

namespace App\View\Components;

use App\Models\Collection;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CollectionCard extends Component
{
    public $collection;
    public $id;

    /**
     * Create a new component instance.
     */
    public function __construct($id)
    {

        $this->id = $id;

        // Carica la collection usando l'ID
        $this->collection = Collection::findOrFail($id);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.collection-card', [
            'collection' => $this->collection,
            'id' => $this->id
        ]);
    }
}
