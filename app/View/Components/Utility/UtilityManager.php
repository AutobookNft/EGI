<?php

namespace App\View\Components\Utility;

use App\Models\Egi;
use App\Models\Utility;
use Illuminate\View\Component;

/**
 * Component per gestione Utility con supporto multilingua
 * 
 * @package App\View\Components\Utility
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Utility System Multilingual)
 * @date 2025-01-03
 * @purpose Permette al creator di aggiungere/modificare utility prima della pubblicazione
 */
class UtilityManager extends Component
{
    public Egi $egi;
    public ?Utility $utility;
    public bool $canEdit;
    public array $utilityTypes;
    public array $escrowTiers;
    
    /**
     * Create component instance
     */
    public function __construct(Egi $egi)
    {
        $this->egi = $egi;
        $this->utility = $egi->utility;
        
        // Verifica permessi: solo creator della collection non pubblicata
        $this->canEdit = auth()->check() 
            && auth()->id() === $egi->collection->user_id
            && $egi->collection->status !== 'published';
            
        // Definizione tipi utility con localizzazione
        $this->utilityTypes = [
            'physical' => [
                'label' => __('utility.types.physical.label'),
                'icon' => '📦',
                'description' => __('utility.types.physical.description')
            ],
            'service' => [
                'label' => __('utility.types.service.label'),
                'icon' => '🎯',
                'description' => __('utility.types.service.description')
            ],
            'hybrid' => [
                'label' => __('utility.types.hybrid.label'),
                'icon' => '🔄',
                'description' => __('utility.types.hybrid.description')
            ],
            'digital' => [
                'label' => __('utility.types.digital.label'),
                'icon' => '💾',
                'description' => __('utility.types.digital.description')
            ]
        ];
        
        // Calcola tier escrow basato sul prezzo EGI
        $this->escrowTiers = $this->calculateEscrowInfo();
    }
    
    /**
     * Calcola informazioni escrow basate sul prezzo con testi localizzati
     */
    private function calculateEscrowInfo(): array
    {
        $price = $this->egi->price ?? 0;
        
        if ($price < 100) {
            return [
                'tier' => 'immediate',
                'label' => __('utility.escrow.immediate.label'),
                'description' => __('utility.escrow.immediate.description'),
                'days' => 0,
                'requirements' => []
            ];
        } elseif ($price <= 2000) {
            return [
                'tier' => 'standard',
                'label' => __('utility.escrow.standard.label'),
                'description' => __('utility.escrow.standard.description'),
                'days' => 14,
                'requirements' => [
                    __('utility.escrow.standard.requirement_tracking')
                ]
            ];
        } else {
            return [
                'tier' => 'premium',
                'label' => __('utility.escrow.premium.label'),
                'description' => __('utility.escrow.premium.description'),
                'days' => 21,
                'requirements' => [
                    __('utility.escrow.premium.requirement_tracking'),
                    __('utility.escrow.premium.requirement_signature'),
                    __('utility.escrow.premium.requirement_insurance')
                ]
            ];
        }
    }
    
    /**
     * Get the view representation
     */
    public function render()
    {
        return view('components.utility.utility-manager');
    }
}
