<?php

namespace App\View\Components;

use Illuminate\View\Component;

/**
 * Componente per visualizzare statistiche in stile NFT card con estetica EGI
 *
 * 🎯 Presenta statistiche di piattaforma con stile NFT visivamente coerente
 * 📡 Interrogabile: fornisce statistiche di piattaforma in formato standardizzato
 * 🧱 Semanticamente coerente: rappresenta dati quantitativi con formattazione dedicata
 *
 * @schema-type QuantitativeValue
 * @accessibility-trait Presenta statistiche con etichette descrittive
 * @seo-purpose Visualizza metriche di piattaforma per utenti e motori di ricerca
 */
class EgiStatCard extends Component
{
    /**
     * Tipo di statistica da visualizzare
     *
     * @var string
     */
    public string $type;

    /**
     * Valore della statistica
     *
     * @var int|float
     */
    public $value;

    /**
     * Etichetta della statistica
     *
     * @var string
     */
    public string $label;

    /**
     * Determina se mostrare l'animazione di conteggio
     *
     * @var bool
     */
    public bool $animate;

    /**
     * Colore della statistica (determina sia il bordo che il gradiente del testo)
     *
     * @var string
     */
    public string $color;

    /**
     * Suffisso da aggiungere al valore (es. "€", "kg")
     *
     * @var string|null
     */
    public ?string $suffix;

    /**
     * Configurazione dei colori per ciascun tipo di statistica
     *
     * @var array
     */
    private array $colorMap = [
        'egi_created' => 'purple',
        'active_collectors' => 'cyan',
        'environmental_impact' => 'green',
        'supported_projects' => 'orange',
        'plastic_recovered' => 'blue',
    ];

    /**
     * Configurazione dei gradienti per ciascun colore
     *
     * @var array
     */
    private array $gradientMap = [
        'purple' => 'from-purple-400 to-pink-400',
        'cyan' => 'from-cyan-400 to-blue-400',
        'green' => 'from-green-400 to-emerald-400',
        'blue' => 'from-blue-400 to-cyan-400',
        'orange' => 'from-orange-400 to-red-400',
    ];

    /**
     * Create a new component instance.
     *
     * @param string $type Tipo di statistica (egi_created, active_collectors, environmental_impact, supported_projects, plastic_recovered)
     * @param int|float|null $value Valore da mostrare (se null, viene utilizzato il valore predefinito per il tipo)
     * @param string|null $label Etichetta personalizzata (se null, viene utilizzata l'etichetta predefinita per il tipo)
     * @param bool $animate Se true, mostra l'animazione di conteggio
     * @param string|null $color Colore personalizzato (se null, viene utilizzato il colore predefinito per il tipo)
     * @param string|null $suffix Suffisso da aggiungere al valore (es. "€", "kg")
     * @return void
     */
    public function __construct(
        string $type,
        $value = null,
        ?string $label = null,
        bool $animate = true,
        ?string $color = null,
        ?string $suffix = null
    ) {
        $this->type = $type;
        $this->animate = $animate;
        $this->color = $color ?? $this->colorMap[$type] ?? 'purple';
        $this->suffix = $suffix;

        // Imposta valore predefinito in base al tipo
        if ($value === null) {
            $this->value = $this->getDefaultValue($type);
        } else {
            $this->value = $value;
        }

        // Imposta etichetta predefinita in base al tipo
        if ($label === null) {
            $this->label = $this->getDefaultLabel($type);
        } else {
            $this->label = $label;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.egi-stat-card', [
            'gradient' => $this->gradientMap[$this->color] ?? $this->gradientMap['purple'],
            'borderColor' => $this->color,
        ]);
    }

    /**
     * Ottiene il valore predefinito per un tipo di statistica
     *
     * @param string $type Tipo di statistica
     * @return int|float Valore predefinito
     */
    private function getDefaultValue(string $type)
    {
        // MVP: Valori hardcoded
        // TODO: In futuro, recuperare questi valori da un servizio o repository
        return match($type) {
            'egi_created' => 1234,
            'active_collectors' => 567,
            'environmental_impact' => 89000,
            'supported_projects' => 42,
            'plastic_recovered' => 5241.38,
            default => 0,
        };
    }

    /**
     * Ottiene l'etichetta predefinita per un tipo di statistica
     *
     * @param string $type Tipo di statistica
     * @return string Etichetta predefinita tradotta
     */
    private function getDefaultLabel(string $type)
    {
        // Mappa i tipi di statistiche alle chiavi di traduzione
        $translationKey = match($type) {
            'egi_created' => 'guest_home.total_egi_created',
            'active_collectors' => 'guest_home.active_collectors',
            'environmental_impact' => 'guest_home.environmental_impact',
            'supported_projects' => 'guest_home.supported_projects',
            'plastic_recovered' => 'guest_home.total_plastic_recovered',
            default => 'guest_home.stat_' . $type,
        };

        return __($translationKey);
    }

    /**
     * Formatta il valore della statistica per la visualizzazione
     *
     * @return string Valore formattato
     */
    public function formattedValue()
    {
        $value = is_float($this->value) && fmod($this->value, 1) === 0.0 ?
                (int)$this->value :
                $this->value;

        if (is_float($value)) {
            return number_format($value, 2, ',', '.');
        }

        return number_format($value, 0, ',', '.');
    }
}
