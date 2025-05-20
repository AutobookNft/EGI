<?php

namespace App\View\Components;

use Illuminate\View\Component;

/**
 * Componente per la sezione statistiche della piattaforma
 *
 * 🎯 Presenta una panoramica delle statistiche chiave di piattaforma
 * 🧱 Semanticamente coerente: container per statistiche EGI correlate
 *
 * @seo-purpose Fornisce metriche chiave della piattaforma in formato standardizzato
 */
class EgiStatsSection extends Component
{
    /**
     * Determina se mostrare l'animazione di conteggio
     *
     * @var bool
     */
    public bool $animate;

    /**
     * Create a new component instance.
     *
     * @param bool $animate Se true, mostra l'animazione di conteggio
     * @return void
     */
    public function __construct(bool $animate = true)
    {
        $this->animate = $animate;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.egi-stats-section');
    }
}
