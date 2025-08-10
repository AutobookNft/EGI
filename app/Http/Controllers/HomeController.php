<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Egi;
use App\Models\Epp;
use App\Models\User; // Importa il modello User
use App\Services\CollectorCarouselService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * HomeController - Gestisce la presentazione della homepage
 *
 * @package App\Http\Controllers
 *
 * 🎯 Il controller si occupa di recuperare e presentare i dati essenziali per la homepage FlorenceEGI
 * 🧱 Semanticamente coerente: ogni metodo ha uno scopo chiaro relativo alle entità del dominio
 * 📡 Interrogabile: i metodi specificano chiaramente cosa presentano e perché
 * 🛡️ GDPR-friendly: utilizza solo dati pubblici (is_published = true)
 *
 * @seo-purpose Fornisce contenuti dinamici rilevanti per la homepage FlorenceEGI
 * @schema-type WebPage
 */
class HomeController extends Controller {
    
    protected CollectorCarouselService $collectorCarouselService;

    public function __construct(CollectorCarouselService $collectorCarouselService)
    {
        $this->collectorCarouselService = $collectorCarouselService;
    }

    /**
     * Visualizza la homepage con contenuti dinamici
     *
     * 🎯 Presenta una panoramica dell'ecosistema FlorenceEGI
     * 📥 Recupera EGI casuali, collezioni in evidenza, ultime gallerie, progetti EPP
     * e statistiche di impatto ambientale, e ora anche i Creator.
     * 📤 Restituisce la vista home con tutti i dati necessari
     *
     * @seo-purpose Pagina principale del sito con showcase delle collezioni NFT e impatto ambientale
     * @accessibility-trait Contiene contatori e statistiche con etichette esplicative
     *
     * @return View La vista home popolata con i dati
     */
    public function index(): View {
        // Recupera dati per la homepage
        $randomEgis = $this->getRandomEgis();
        $featuredCollections = $this->getFeaturedCollections();
        $latestCollections = $this->getLatestCollections($featuredCollections->pluck('id'));
        $highlightedEpps = $this->getHighlightedEpps();
        $featuredCreators = $this->getFeaturedCreators(); // Nuovo: recupera i Creator
        $topCollectors = $this->collectorCarouselService->getTopCollectors(10); // Nuovo: top collectors

        // Dati impatto ambientale - valore hardcoded per MVP
        // TODO: In futuro, recuperare da database o API dedicata
        $totalPlasticRecovered = $this->getTotalPlasticRecovered();

        return view('home', [
            'randomEgis' => $randomEgis,
            'featuredCollections' => $featuredCollections,
            'latestCollections' => $latestCollections,
            'highlightedEpps' => $highlightedEpps,
            'totalPlasticRecovered' => $totalPlasticRecovered,
            'featuredCreators' => $featuredCreators, // Nuovo: passa i Creator alla vista
            'topCollectors' => $topCollectors, // Nuovo: passa i Top Collectors alla vista
        ]);
    }

    /**
     * Ottiene EGI casuali per il carousel
     *
     * @privacy-safe Utilizza solo EGI pubblicati pubblicamente
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRandomEgis() {
        return Egi::where('is_published', true)
            ->with(['collection'])
            ->inRandomOrder()
            ->take(5)
            ->get();
    }

    /**
     * Ottiene collezioni in evidenza basate su metriche di impatto e override manuale
     *
     * 🎯 Implementa algoritmo di selezione intelligente:
     * - Filtra per featured_in_guest = true e is_published = true
     * - Priorità alle posizioni forzate (featured_position 1-10)
     * - Ordina le restanti per impatto stimato (quota EPP 20% delle prenotazioni più alte)
     * - Limita a massimo 10 Collection nel carousel
     *
     * @privacy-safe Utilizza solo collezioni pubblicate pubblicamente
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getFeaturedCollections() {
        // Utilizziamo il service dedicato per la logica complessa di selezione
        $featuredService = app(\App\Services\FeaturedCollectionService::class);

        return $featuredService->getFeaturedCollections(10);
    }

    /**
     * Ottiene le ultime gallerie create
     *
     * @privacy-safe Utilizza solo collezioni pubblicate pubblicamente
     * @param \Illuminate\Support\Collection $excludeIds IDs da escludere (es. collezioni già in evidenza)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getLatestCollections($excludeIds) {
        return Collection::where('is_published', true)
            ->whereNotIn('id', $excludeIds)
            ->with(['creator'])
            ->latest()
            ->take(8)
            ->get();
    }

    /**
     * Ottiene progetti ambientali (EPP) in evidenza
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getHighlightedEpps() {
        return Epp::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
    }

    /**
     * Ottiene i Creator in evidenza per il carousel
     *
     * 🎯 Recupera utenti con usertype 'creator' in ordine casuale per la homepage
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getFeaturedCreators() {
        return User::where('usertype', 'creator')
            // Assicurati che esista un campo 'is_published' o 'is_active' se necessario
            // ->where('is_published', true)
            ->inRandomOrder()
            ->take(8) // Puoi regolare il numero di creator da mostrare
            ->get();
    }

    /**
     * Ottiene il totale di plastica recuperata in kg
     *
     * 📡 Interrogabile: fornisce i dati su impatto ambientale
     *
     * @schema-type QuantitativeValue
     * @return float Quantità in kg di plastica recuperata dagli oceani
     */
    private function getTotalPlasticRecovered(): float {
        // MVP: Valore hardcoded
        // TODO: In futuro, calcolare somma da transazioni o recuperare da API dedicata
        return 5241.38;

        // Implementazione futura:
        // return Transaction::where('type', 'plastic_recovery')
        //      ->where('status', 'confirmed')
        //      ->sum('amount');
    }
}
