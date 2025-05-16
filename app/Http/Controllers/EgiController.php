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
    public function show($id): View
    {
        $egi = Egi::with([
            'collection.creator',
            'collection.epp',
            'user',
            'owner',
            'likes'
        ])->findOrFail($id);

        // Verifica like per utente strong auth
        if (auth()->check()) {
            $egi->is_liked = $egi->likes()
                ->where('user_id', auth()->id())
                ->exists();
        }
        // Verifica like per utente weak auth
        elseif (session('connected_user_id')) {
            $egi->is_liked = $egi->likes()
                ->where('user_id', session('connected_user_id'))
                ->exists();
        } else {
            $egi->is_liked = false;
        }

        $egi->likes_count = $egi->likes()->count();

        $collection = $egi->collection;

        return view('egis.show', compact('egi', 'collection'));
    }

    // ... altri metodi del controller (index, create, store, edit, update, destroy)...
}
