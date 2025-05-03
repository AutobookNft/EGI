<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Egi;
use App\Models\Epp; // Assicurati di importare Epp
use Illuminate\Http\Request;
use Illuminate\View\View;

// Se stai usando DropController, rinominalo o crea HomeController
class HomeController extends Controller // o DropController
{
    public function index(): View
    {
        // EGI Casuali per il Carousel (Es: 5 EGI pubblicati con le loro collezioni)
        $randomEgis = Egi::where('is_published', true) // Solo EGI pubblicati
            ->with(['collection']) // Carica la relazione con la collezione
            ->inRandomOrder() // Ordina casualmente
            ->take(5) // Prendi un numero limitato (es. 5)
            ->get();

        // Collezioni "In Evidenza"
        $featuredCollections = Collection::where('is_published', true)
            ->with(['creator'])
            ->latest()
            ->take(3)
            ->get();

        // Ultime Gallerie
        $latestCollections = Collection::where('is_published', true)
            ->whereNotIn('id', $featuredCollections->pluck('id'))
            ->with(['creator'])
            ->latest()
            ->take(8)
            ->get();

        // EPP
        $highlightedEpps = Epp::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('home', [
            'randomEgis' => $randomEgis, // Passa gli EGI casuali alla vista
            'featuredCollections' => $featuredCollections,
            'latestCollections' => $latestCollections,
            'highlightedEpps' => $highlightedEpps,
        ]);
    }
}
