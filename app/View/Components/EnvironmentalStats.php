<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Collection;
use App\Models\Egi;
use App\Models\User;
use App\Models\Epp;
use Illuminate\Support\Facades\DB;

/**
 * Componente per visualizzare statistiche sull'impatto ambientale e la piattaforma
 *
 * 🎯 Mostra le statistiche di impatto ambientale e della piattaforma FlorenceEGI
 * 📡 Interrogabile: fornisce dati di impatto e metriche in modo centralizzato
 * 🧱 Semanticamente coerente: rappresenta informazioni sulle plastiche e le collezioni
 *
 * @schema-type QuantitativeValue
 * @accessibility-trait Presenta statistiche con etichette descrittive
 */
class EnvironmentalStats extends Component {
    /**
     * Quantità totale di plastica recuperata in kg
     *
     * @var float
     */
    public float $totalPlasticRecovered;

    /**
     * Numero di progetti attivi
     *
     * @var int
     */
    public int $activeProjects;

    /**
     * Numero di items (EGI) pubblicati
     *
     * @var int
     */
    public int $totalItems;

    /**
     * Numero di proprietari/collezionisti
     *
     * @var int
     */
    public int $totalOwners;

    /**
     * Numero di collezioni pubblicate
     *
     * @var int
     */
    public int $totalCollections;

    /**
     * Totale delle prenotazioni
     *
     * @var array
     */
    public array $reservations;

    /**
     * Equilibrium (20% del totale prenotato)
     *
     * @var float
     */
    public float $equilibrium;

    /**
     * Formato di visualizzazione
     *
     * @var string Possibili valori: 'compact', 'full', 'footer', 'card-stats', 'natan-badge'
     */
    public string $format;

    /**
     * Colore dei testi (per tematizzazione)
     *
     * @var string
     */
    public string $textColor;

    /**
     * Flag per utilizzare valori hardcoded (MVP) o dati reali
     *
     * @var bool
     */
    public bool $useRealData;

    /**
     * Create a new component instance.
     *
     * @param string $format Formato di visualizzazione ('compact', 'full', 'footer', 'card-stats', 'natan-badge')
     * @param string $textColor Colore del testo principale ('emerald', 'cyan', 'blue')
     * @param bool $useRealData Flag per utilizzare dati reali dal database invece di hardcoded
     * @return void
     */
    public function __construct(string $format = 'full', string $textColor = 'cyan', bool $useRealData = true) {
        $this->format = $format;
        $this->textColor = $textColor;
        $this->useRealData = $useRealData;

        // Inizializzazione statistiche
        $this->totalPlasticRecovered = $this->getTotalPlasticRecovered();
        $this->activeProjects = $this->getActiveProjects();
        $this->totalItems = $this->getTotalItems();
        $this->totalOwners = $this->getTotalOwners();
        $this->totalCollections = $this->getTotalCollections();
        $this->reservations = $this->getTotalReservations();
        $this->equilibrium = $this->getEquilibrium();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render() {
        return view('components.environmental-stats');
    }

    /**
     * Ottiene il totale di plastica recuperata in kg
     *
     * @privacy-safe Non contiene dati personali
     * @return float
     */
    private function getTotalPlasticRecovered(): float {
        if (!$this->useRealData) {
            // MVP: Valore hardcoded
            return 5241.38;
        }

        // In un sistema reale, la plastica recuperata potrebbe essere
        // proporzionale all'Equilibrium generato
        $equilibrium = $this->getEquilibrium();

        // Esempio: 1 euro di Equilibrium = 0.1 kg di plastica recuperata
        // È un'approssimazione, in futuro potrebbe essere basata su dati reali
        $plasticFactor = 0.1;

        return $equilibrium * $plasticFactor;
    }

    /**
     * Ottiene il numero di progetti ambientali attivi
     *
     * @privacy-safe Non contiene dati personali
     * @return int
     */
    private function getActiveProjects(): int {
        if (!$this->useRealData) {
            // MVP: Valore hardcoded
            return 42;
        }

        // Implementazione con dati reali
        return Epp::where('status', 'active')->count();
    }

    /**
     * Ottiene il numero totale di items (EGI) pubblicati
     *
     * @privacy-safe Non contiene dati personali
     * @return int
     */
    private function getTotalItems(): int {
        if (!$this->useRealData) {
            // MVP: Valore hardcoded
            return 721;
        }

        // Implementazione con dati reali
        return Egi::where('is_published', true)->count();
    }

    /**
     * Ottiene il numero totale di proprietari/collezionisti
     *
     * @privacy-safe Non contiene dati personali
     * @return int
     */
    private function getTotalOwners(): int {
        if (!$this->useRealData) {
            // MVP: Valore hardcoded
            return 143;
        }

        // Implementazione con dati reali - utenti unici che hanno una collezione
        return Collection::where('is_published', true)
            ->distinct('creator_id')
            ->count('creator_id');
    }

    /**
     * Ottiene il numero totale di collezioni pubblicate
     *
     * @privacy-safe Non contiene dati personali
     * @return int
     */
    private function getTotalCollections(): int {
        if (!$this->useRealData) {
            // MVP: Valore hardcoded
            return 57;
        }

        // Implementazione con dati reali
        return Collection::where('is_published', true)->count();
    }

    /**
     * Ottiene il totale prenotato suddiviso per tipo di prenotazione
     *
     * @privacy-safe Non contiene dati personali
     * @return array Associative array con i totali ['weak' => x, 'strong' => y, 'total' => z]
     */
    private function getTotalReservations(): array {
        if (!$this->useRealData) {
            // MVP: Valori hardcoded
            return [
                'weak' => 28750.00,
                'strong' => 36500.00,
                'total' => 65250.00
            ];
        }

        // Query per ottenere la prenotazione con l'offerta più alta per ogni EGI e tipo
        $highestReservations = DB::table('reservations')
            ->select('egi_id', 'type', DB::raw('MAX(offer_amount_fiat) as highest_offer'))
            ->where('status', 'active') // Solo prenotazioni attive
            ->groupBy('egi_id', 'type') // Raggruppa per EGI e tipo
            ->get();

        // Calcola i totali
        $totalWeak = 0;
        $totalStrong = 0;

        foreach ($highestReservations as $reservation) {
            if ($reservation->type === 'weak') {
                $totalWeak += $reservation->highest_offer;
            } else {
                $totalStrong += $reservation->highest_offer;
            }
        }

        $total = $totalWeak + $totalStrong;

        return [
            'weak' => $totalWeak,
            'strong' => $totalStrong,
            'total' => $total
        ];
    }

    /**
     * Ottiene l'Equilibrium (20% del totale prenotato destinato agli EPP)
     *
     * @privacy-safe Non contiene dati personali
     * @return float Ammontare dell'Equilibrium in EUR
     */
    private function getEquilibrium(): float {
        if (!$this->useRealData) {
            // MVP: Valore hardcoded (calcolato come 20% di 65250)
            return 13050.00;
        }

        // Calcola l'Equilibrium come 20% del totale prenotato
        $reservations = $this->getTotalReservations();
        return $reservations['total'] * 0.2;
    }

    /**
     * Formatta il numero con separatori localizzati e notazione abbreviata per mobile
     *
     * @param float $value Il valore da formattare
     * @param int $decimals Numero di decimali da mostrare
     * @param bool $useAbbreviation Se utilizzare notazione abbreviata (K, M, B)
     * @return string
     */
    public function formatNumber(float $value, int $decimals = 2, bool $useAbbreviation = false): string {
        if ($useAbbreviation) {
            return formatNumberAbbreviated($value, $decimals);
        }
        return number_format($value, $decimals, ',', '.');
    }

    /**
     * Formatta un valore monetario con notazione abbreviata per mobile
     *
     * @param float $value Il valore da formattare
     * @param int $decimals Numero di decimali da mostrare
     * @param bool $useAbbreviation Se utilizzare notazione abbreviata (K, M, B)
     * @return string
     */
    public function formatCurrency(float $value, int $decimals = 1, bool $useAbbreviation = true): string {
        if ($useAbbreviation) {
            return formatPriceAbbreviated($value, $decimals);
        }
        return '€ ' . number_format($value, 2, ',', '.');
    }

    /**
     * Formatta il totale di plastica recuperata
     *
     * @param int $decimals Numero di decimali da mostrare
     * @return string
     */
    public function formattedTotal(int $decimals = 2): string {
        return $this->formatNumber($this->totalPlasticRecovered, $decimals);
    }

    /**
     * Formatta l'Equilibrium
     *
     * @param int $decimals Numero di decimali da mostrare
     * @return string
     */
    public function formattedEquilibrium(int $decimals = 2): string {
        return $this->formatNumber($this->equilibrium, $decimals);
    }

    /**
     * Formatta il totale delle prenotazioni
     *
     * @param string $type Tipo di prenotazione ('weak', 'strong', o 'total')
     * @param int $decimals Numero di decimali da mostrare
     * @return string
     */
    public function formattedReservations(string $type = 'total', int $decimals = 2): string {
        $value = $this->reservations[$type] ?? $this->reservations['total'];
        return $this->formatNumber($value, $decimals);
    }
}
