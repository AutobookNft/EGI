<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Epp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Controller for managing collections display and interaction.
 *
 * Handles the listing, filtering, and viewing of EGI collections in the
 * marketplace. Implements Oracode principles for readable, maintainable code.
 *
 * --- Core Logic ---
 * 1. Retrieves collections based on filter criteria
 * 2. Sorts collections by various parameters
 * 3. Loads necessary relationships for efficient display
 * 4. Handles pagination for scalable collection browsing
 * 5. Tracks engagement metrics for collections
 * --- End Core Logic ---
 *
 * @package App\Http\Controllers
 * @author Your Name <your.email@example.com>
 * @version 1.0.0
 * @since 1.0.0
 */
class CollectionsController extends Controller
{
    /**
     * Display a paginated listing of collections.
     *
     * Retrieves collections with filters applied based on query parameters.
     * Loads necessary relationships to minimize database queries.
     * Orders results based on user preference.
     *
     * @param Request $request The HTTP request with optional filter parameters
     * @return \Illuminate\View\View The view with paginated collection data
     */
    public function index(Request $request)
    {
        // Ottieni tutti gli EPP per il dropdown dei filtri
        $epps = Epp::select('id', 'name')->get();

        // Costruisci la query di base con le relazioni necessarie
        $query = Collection::with(['creator', 'epp', 'egis'])
            ->select([
                'id',
                'creator_id',
                'epp_id',
                'collection_name',
                'description',
                'image_card',
                'status',
                'is_published',
                'EGI_number',
                'floor_price',
                'created_at',
            ]);

        // Filtro per stato
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Mostra solo collezioni pubblicate per utenti non autenticati o senza permessi
            if (!auth()->user() || !auth()->user()->can('view_draft_collections')) {
                $query->where('is_published', true);
            }
        }

        // Filtro per EPP
        if ($request->filled('epp')) {
            $query->where('epp_id', $request->epp);
        }

        // Ordinamento
        $sortBy = $request->input('sort', 'newest');

        switch ($sortBy) {
            case 'oldest':
                $query->oldest('created_at');
                break;
            case 'name':
                $query->orderBy('collection_name', 'asc');
                break;
            case 'popularity':
                // Assumo che `EGI_number` o `floor_price` possano essere proxy per popolarità
                // Se esiste una tabella `likes`, usa quella
                if (Schema::hasTable('likes')) {
                    $query->leftJoin('likes', 'collections.id', '=', 'likes.collection_id')
                        ->groupBy('collections.id')
                        ->orderByDesc(DB::raw('COUNT(likes.id)'));
                } else {
                    // Fallback: ordina per numero di EGI
                    $query->orderByDesc('EGI_number');
                }
                break;
            case 'newest':
            default:
                $query->latest('created_at');
                break;
        }

        // Paginazione (12 elementi per pagina)
        $collections = $query->paginate(12)->appends($request->query());

        // Aggiungi attributi calcolati senza query extra
        foreach ($collections as $collection) {
            // Numero di EGI (usiamo EGI_number dal database, se disponibile)
            $collection->egi_count = $collection->EGI_number ?? $collection->egis->count();

            // Conteggi di likes e reservations (se le relazioni esistono)
            $collection->likes_count = Schema::hasTable('likes') ? $collection->likes()->count() : 0;
            $collection->reservations_count = Schema::hasTable('reservations') ? $collection->reservations()->count() : 0;

            // Verifica se l'utente autenticato ha messo "like"
            $collection->is_liked = auth()->check() && Schema::hasTable('likes')
                ? $collection->likes()->where('user_id', auth()->id())->exists()
                : false;
        }

        return view('collections.index', compact('collections', 'epps'));
    }

    /**
     * Display the specified collection.
     *
     * Shows detailed information about a single collection, including
     * its EGIs, creator information, and related metadata.
     *
     * @param Collection $collection The collection to display
     * @return \Illuminate\View\View The view with collection details
     */
    public function show(Collection $collection)
    {
        // Load necessary relationships
        $collection->load(['creator', 'owner', 'egis']);

        // Track view count (optional)
        // $this->trackView($collection);

        // Get related collections (optional - by same creator or similar topic)
        $relatedCollections = Collection::where('creator_id', $collection->creator_id)
            ->where('id', '!=', $collection->id)
            ->where('status', 'published')
            ->limit(4)
            ->get();

        return view('collections.show', compact('collection', 'relatedCollections'));
    }

    /**
     * Track a view of a collection.
     *
     * Increments view count for analytics purposes.
     * Consider implementing rate limiting to prevent abuse.
     *
     * @param Collection $collection The collection being viewed
     * @return void
     */
    protected function trackView(Collection $collection)
    {
        // This is a placeholder for view tracking functionality
        // You would implement your actual tracking logic here

        // Example:
        // $collection->incrementViewCount();
        // or
        // ViewLog::create([
        //     'collection_id' => $collection->id,
        //     'user_id' => auth()->id(),
        //     'ip_address' => request()->ip()
        // ]);
    }
}
