<?php

namespace App\View\Components;

use Illuminate\View\Component;

class GdprLayout extends Component
{
    public $pageTitle;
    public $pageSubtitle;
    public $breadcrumbItems;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($pageTitle = null, $pageSubtitle = null, $breadcrumbItems = [])
    {
        $this->pageTitle = $pageTitle ?? __('gdpr.default_title');
        $this->pageSubtitle = $pageSubtitle;
        $this->breadcrumbItems = $breadcrumbItems;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.gdpr-layout');
    }
}
