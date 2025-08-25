<?php

namespace App\View\Components;

use App\Helpers\ResponsiveImageHelper;
use Illuminate\View\Component;

/**
 * ResponsiveImage Component
 *
 * Blade component per immagini responsive ottimizzate
 */
class ResponsiveImage extends Component {
    public string $src;
    public string $alt;
    public string $class;
    public string $loading;
    public ?string $fetchpriority;
    public string $type;
    public bool $fallbackOnly;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $src,
        string $alt = '',
        string $class = '',
        string $loading = 'lazy',
        ?string $fetchpriority = null,
        string $type = 'egi',
        bool $fallbackOnly = false
    ) {
        $this->src = $src;
        $this->alt = $alt;
        $this->class = $class;
        $this->loading = $loading;
        $this->fetchpriority = $fetchpriority;
        $this->type = $type;
        $this->fallbackOnly = $fallbackOnly;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render() {
        // Se fallbackOnly è true o non ci sono varianti ottimizzate, usa img semplice
        if ($this->fallbackOnly || !ResponsiveImageHelper::hasOptimizedVariants($this->src, $this->type)) {
            return view('components.responsive-image-fallback');
        }

        // Altrimenti usa il tag picture completo
        return view('components.responsive-image-picture');
    }

    /**
     * Genera il tag picture HTML
     */
    public function getPictureHtml(): string {
        return ResponsiveImageHelper::picture($this->src, [
            'alt' => $this->alt,
            'class' => $this->class,
            'loading' => $this->loading,
            'fetchpriority' => $this->fetchpriority,
            'type' => $this->type,
        ]);
    }

    /**
     * Ottiene attributi per img tag
     */
    public function getImgAttributes(): array {
        $attributes = [
            'src' => $this->src,
            'alt' => $this->alt,
            'loading' => $this->loading,
        ];

        if ($this->class) {
            $attributes['class'] = $this->class;
        }

        if ($this->fetchpriority) {
            $attributes['fetchpriority'] = $this->fetchpriority;
        }

        return $attributes;
    }
}
