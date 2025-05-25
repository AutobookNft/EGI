<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;

/**
 * Collections Carousel Component Class
 *
 * Renders a reusable carousel for displaying collections with navigation controls.
 *
 * @package App\View\Components
 * @author Padmin D. Curtis
 * @seo-purpose Enable visual navigation through collections with optimized display
 * @accessibility-trait Navigation controls have appropriate ARIA attributes
 */
class CollectionsCarousel extends Component
{
    /**
     * Collection of items to display in the carousel.
     *
     * @var Collection
     */
    public Collection $collections;

    /**
     * The title for the carousel section.
     *
     * @var string
     */
    public string $title;

    /**
     * Additional classes for the title.
     *
     * @var string
     */
    public string $titleClass;

    /**
     * Background class for the container.
     *
     * @var string
     */
    public string $bgClass;

    /**
     * Margin class for the container.
     *
     * @var string
     */
    public string $marginClass;

    /**
     * Whether to enable auto-scrolling.
     *
     * @var bool
     */
    public bool $autoScroll;

    /**
     * Interval for auto-scrolling in milliseconds.
     *
     * @var int
     */
    public int $autoScrollInterval;

    /**
     * Create a new component instance.
     *
     * @param Collection|array $collections The collections to display
     * @param string $title The title for the carousel section
     * @param string $titleClass Additional classes for the title
     * @param string $bgClass Background class for the container
     * @param string $marginClass Margin class for the container
     * @param bool $autoScroll Whether to enable auto-scrolling
     * @param int $autoScrollInterval Interval for auto-scrolling in milliseconds
     * @return void
     */
    public function __construct(
        $collections = [],
        string $title = '',
        string $titleClass = '',
        string $bgClass = 'bg-gray-900',
        string $marginClass = 'mb-12',
        bool $autoScroll = false,
        int $autoScrollInterval = 5000
    ) {
        // Ensure collections is a Collection instance
        $this->collections = $collections instanceof Collection
            ? $collections
            : collect($collections);

        $this->title = $title ?: __('guest_home.featured_collections_title');
        $this->titleClass = $titleClass;
        $this->bgClass = $bgClass;
        $this->marginClass = $marginClass;
        $this->autoScroll = $autoScroll;
        $this->autoScrollInterval = $autoScrollInterval;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('components.collections-carousel');
    }

    /**
     * Determine if the carousel should display navigation controls.
     *
     * @return bool
     */
    public function shouldShowControls(): bool
    {
        return $this->collections->isNotEmpty();
    }

    /**
     * Get the JavaScript configuration for the carousel.
     *
     * @return array
     */
    public function getJsConfig(): array
    {
        return [
            'autoScroll' => $this->autoScroll,
            'autoScrollInterval' => $this->autoScrollInterval,
            'itemCount' => $this->collections->count()
        ];
    }

    /**
     * Generate a unique ID for this carousel instance.
     *
     * @return string
     */
    public function getCarouselId(): string
    {
        // Generate a unique ID based on the first collection ID or using a random string
        $firstId = $this->collections->first()?->id ?? '';
        return 'collections-carousel-' . ($firstId ?: uniqid());
    }
}
