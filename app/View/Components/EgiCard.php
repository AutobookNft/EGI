<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EgiCard extends Component {
    public $egi;
    public $collection;
    public $showPurchasePrice;
    public $hideReserveButton;
    public $portfolioContext;
    public $portfolioOwner;
    public $creatorPortfolioContext;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $egi = null,
        $collection = null,
        $showPurchasePrice = false,
        $hideReserveButton = false,
        $portfolioContext = false,
        $portfolioOwner = null,
        $creatorPortfolioContext = false
    ) {
        $this->egi = $egi;
        $this->collection = $collection;
        $this->showPurchasePrice = $showPurchasePrice;
        $this->hideReserveButton = $hideReserveButton;
        $this->portfolioContext = $portfolioContext;
        $this->portfolioOwner = $portfolioOwner;
        $this->creatorPortfolioContext = $creatorPortfolioContext;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string {
        return view('components.egi-card');
    }
}
