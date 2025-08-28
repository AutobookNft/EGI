<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\ImageVariantHelper;

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
     * Get optimized image URL for card display
     * Uses 'card' variant (400x400) with fallback to original
     *
     * @return string|null
     */
    public function getOptimizedImageUrl(): ?string {
        if (!$this->egi || !$this->egi->collection_id || !$this->egi->user_id || !$this->egi->key_file) {
            return null;
        }

        // Build storage base path using existing pattern
        $storageBasePath = sprintf(
            'users_files/collections_%d/creator_%d',
            $this->egi->collection_id,
            $this->egi->user_id
        );

        // Try to get optimized 'card' variant URL with fallback to original
        $variantUrl = ImageVariantHelper::getVariantUrlWithFallback(
            $storageBasePath,
            $this->egi->key_file,
            'card', // Use card variant (400x400) for card display
            'public' // Use public disk
        );

        return $variantUrl;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string {
        return view('components.egi-card');
    }
}
