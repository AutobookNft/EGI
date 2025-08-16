<?php

namespace App\View\Components;

use Illuminate\View\Component;

/**
 * 💱 Currency Badge Component
 * Componente autonomo per visualizzare il badge di conversione EUR → ALGO
 * con aggiornamento real-time del tasso di cambio.
 *
 * @version 1.0.0
 * @date 2025-08-16
 * @author Fabio Cherici
 */
class CurrencyBadge extends Component
{
    public string $size;
    public string $position;
    public string $uniqueId;
    public string $responsiveClasses;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $size = 'normal',
        string $position = 'header'
    ) {
        $this->size = $size;
        $this->position = $position;

        // Genera un ID univoco per questo badge
        $this->uniqueId = 'currency-badge-' . uniqid();

        // Imposta le classi responsive
        $this->responsiveClasses = $this->getResponsiveClasses();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.currency-badge');
    }

    /**
     * Get responsive classes based on size
     */
    public function getResponsiveClasses(): string
    {
        return match ($this->size) {
            'mobile' => 'flex md:hidden items-center', // Solo mobile
            'desktop' => 'hidden md:flex items-center ml-3', // Solo desktop con margin
            default => 'flex items-center' // Sempre visibile
        };
    }

    /**
     * Get the CSS classes based on size
     */
    public function getSizeClasses(): array
    {
        return match($this->size) {
            'small' => [
                'container' => 'px-1.5 py-1',
                'text' => 'text-xs',
                'arrow' => 'w-2.5 h-2.5',
                'dot' => 'w-1 h-1',
                'spacing' => 'space-x-1 ml-1'
            ],
            'mobile' => [
                'container' => 'px-1.5 py-1',
                'text' => 'text-xs',
                'arrow' => 'w-2.5 h-2.5',
                'dot' => 'w-1 h-1',
                'spacing' => 'space-x-1 ml-1'
            ],
            'large' => [
                'container' => 'px-4 py-2',
                'text' => 'text-base',
                'arrow' => 'w-4 h-4',
                'dot' => 'w-2 h-2',
                'spacing' => 'space-x-2 ml-3'
            ],
            default => [ // normal/desktop
                'container' => 'px-3 py-1.5',
                'text' => 'text-sm',
                'arrow' => 'w-3 h-3',
                'dot' => 'w-1.5 h-1.5',
                'spacing' => 'space-x-2 ml-3'
            ]
        };
    }

    /**
     * Get position-specific CSS classes
     */
    public function getPositionClasses(): string
    {
        return match($this->position) {
            'sidebar' => 'mb-2',
            'inline' => 'inline-flex',
            'footer' => 'mt-2',
            default => 'items-center' // header
        };
    }

    /**
     * Get the status dot ID for this instance
     */
    public function getStatusDotId(): string
    {
        return $this->uniqueId . '-status-dot';
    }

    /**
     * Get the rate value ID for this instance
     */
    public function getRateValueId(): string
    {
        return $this->uniqueId . '-rate-value';
    }
}
