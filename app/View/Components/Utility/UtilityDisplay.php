<?php

namespace App\View\Components\Utility;

use Illuminate\View\Component;
use App\Models\Utility;

/**
 * 📜 Oracode Blade Component: UtilityDisplay
 * Public display component for utility information and image gallery
 *
 * @package     App\View\Components\Utility
 * @version     1.0.0 (FlorenceEGI - Utility System)
 * @author      Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @copyright   2025 Fabio Cherici
 * @license     Proprietary
 *
 * @purpose     Displays utility title, description, and image carousel
 *              for public viewing in EGI details and cards.
 *
 * @context     Used in EGI detail right sidebar and EGI cards bottom section.
 *              Shows utility information to all users (not just owners).
 *
 * @state       Read-only display of utility data with image carousel.
 */
class UtilityDisplay extends Component {
    public $utility;
    public $showTitle;
    public $showDescription;
    public $carouselSize;

    /**
     * Create a new component instance.
     *
     * @param Utility|null $utility The utility to display
     * @param bool $showTitle Whether to show the title
     * @param bool $showDescription Whether to show the description
     * @param string $carouselSize Size variant: 'small', 'medium', 'large'
     */
    public function __construct(
        ?Utility $utility = null,
        bool $showTitle = true,
        bool $showDescription = true,
        string $carouselSize = 'medium'
    ) {
        $this->utility = $utility;
        $this->showTitle = $showTitle;
        $this->showDescription = $showDescription;
        $this->carouselSize = $carouselSize;
    }

    /**
     * Check if utility has images to display
     */
    public function hasImages(): bool {
        return $this->utility && $this->utility->getMedia('utility_gallery')->count() > 0;
    }

    /**
     * Get utility images for carousel
     */
    public function getImages() {
        if (!$this->utility) {
            return collect();
        }

        return $this->utility->getMedia('utility_gallery');
    }

    /**
     * Get carousel configuration based on size
     */
    public function getCarouselConfig(): array {
        return match ($this->carouselSize) {
            'small' => [
                'gap' => 'gap-1',
                'scroll' => 'snap-x'
            ],
            'large' => [
                'gap' => 'gap-2', 
                'scroll' => 'snap-x'
            ],
            default => [ // medium
                'gap' => 'gap-1',
                'scroll' => 'snap-x'
            ]
        };
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render() {
        return view('components.utility.utility-display');
    }
}