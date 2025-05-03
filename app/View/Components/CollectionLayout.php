<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class CollectionLayout extends Component
{
    /**
     * The page title.
     *
     * @var string
     */
    public string $title;

    /**
     * The meta description for the page.
     *
     * @var string
     */
    public string $metaDescription;

    /**
     * Create the component instance.
     *
     * @param string $title
     * @param string $metaDescription
     */
    public function __construct(
        string $title = 'Collection Detail | FlorenceEGI',
        string $metaDescription = 'Details for this EGI collection on FlorenceEGI.'
    ) {
        $this->title = $title;
        $this->metaDescription = $metaDescription;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.collection');
    }
}
