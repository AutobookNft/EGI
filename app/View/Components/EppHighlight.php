<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Database\Eloquent\Collection;

class EppHighlight extends Component
{
    /**
     * The collection of highlighted EPP projects.
     *
     * @var Collection
     */
    public $highlightedEpps;

    /**
     * Create a new component instance.
     *
     * @param Collection $highlightedEpps
     * @return void
     */
    public function __construct($highlightedEpps = null)
    {
        // If no EPPs are provided, we can either:
        // 1. Load default/featured projects from the database
        // 2. Use an empty collection (handled in the view with a fallback message)

        if ($highlightedEpps) {
            $this->highlightedEpps = $highlightedEpps;
        } else {
            // Option to load default projects if none provided
            // This assumes you have an Epp model with a 'featured' scope or similar
            // You can customize this based on your actual data model

            // Uncomment and adapt the code below based on your model:
            // $this->highlightedEpps = \App\Models\Epp::featured()->take(3)->get();

            // Or use an empty collection for now:
            $this->highlightedEpps = new Collection();
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.epp-highlight');
    }

    /**
     * Get the badge color class based on project index.
     *
     * @param int $index
     * @return string
     */
    public function getBadgeClass($index)
    {
        return match($index) {
            0 => 'bg-amber-100 text-amber-800 border border-amber-200',
            1 => 'bg-purple-100 text-purple-800 border border-purple-200',
            default => 'bg-blue-100 text-blue-800 border border-blue-200',
        };
    }

    /**
     * Get the icon container class based on project index.
     *
     * @param int $index
     * @return string
     */
    public function getIconContainerClass($index)
    {
        return match($index) {
            0 => 'bg-amber-100 text-amber-700',
            1 => 'bg-purple-100 text-purple-700',
            default => 'bg-blue-100 text-blue-700',
        };
    }

    /**
     * Get the appropriate icon based on EPP type.
     *
     * @param string $type
     * @return string
     */
    public function getIconForType($type)
    {
        return match($type) {
            'ARF' => 'forest',
            'APR' => 'waves',
            'BPE' => 'hive',
            default => 'eco',
        };
    }

    /**
     * Get the rarity label based on project index.
     *
     * @param int $index
     * @return string
     */
    public function getRarityLabel($index)
    {
        return match($index) {
            0 => 'LEGENDARY',
            1 => 'EPIC',
            default => 'RARE',
        };
    }
}
