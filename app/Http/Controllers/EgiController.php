<?php

namespace App\Http\Controllers;

use App\Models\Egi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Importa Storage
use Illuminate\View\View; // Importa View

class EgiController extends Controller
{
    /**
     * 📜 Oracode Controller Action: Show EGI Detail
     * Displays the detailed public view for a single Ecological Goods Invent (EGI).
     *
     * @param  Egi  $egi The Egi instance injected via Route Model Binding.
     * @return \Illuminate\View\View The Blade view with EGI data.
     *
     * @purpose 🎯 Renders the single item page for an EGI.
     * @context 🧩 Used by the public-facing part of the application.
     * @data ➡️ Expects an Egi model instance.
     * @data ⬅️ Passes the Egi model (with loaded relationships) to the view.
     * @logic ⚙️
     *   1. Receive Egi model via route model binding.
     *   2. Eager load necessary relationships (collection with creator/epp, owner, user).
     *   3. (Optional) Load related EGIs or other supplementary data.
     *   4. Return the 'egis.show' view with the data.
     * --- End Logic ---
     */
    public function show(Egi $egi): View
    {
        // 📡 Eager load relationships per evitare N+1 queries nella vista
        $egi->load([
            'collection' => function ($query) {
                $query->with(['creator', 'epp']); // Carica anche creator ed epp della collezione
            },
            'user', // Il creatore specifico dell'EGI (se diverso da collection->creator)
            'owner' // L'utente proprietario attuale dell'EGI
            // Aggiungi qui altre relazioni se necessarie (es. 'likes', 'reservations', 'audits')
        ]);

        // 🛡️ Controllo Visibilità (Opzionale ma consigliato)
        // Se l'EGI non è pubblicato e l'utente non è il creatore/admin, potresti voler negare l'accesso
        // if (!$egi->is_published && (!auth()->check() || auth()->id() !== $egi->collection->creator_id)) {
        //     abort(404); // O 403 Forbidden
        // }

        // Recupera collezioni correlate (opzionale, esempio)
        // $relatedCollections = $egi->collection->creator
        //     ? $egi->collection->creator->collections()
        //         ->where('id', '!=', $egi->collection_id)
        //         ->where('is_published', true)
        //         ->limit(3)
        //         ->get()
        //     : collect(); // Collection vuota se non c'è creator

        // Passa i dati alla vista
        return view('egis.show', [
            'egi' => $egi,
            'collection' => $egi->collection, // Passa anche la collection per comodità
            // 'relatedCollections' => $relatedCollections, // Se implementato
        ]);
    }

    // ... altri metodi del controller (index, create, store, edit, update, destroy)...
}
