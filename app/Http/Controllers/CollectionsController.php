<?php

namespace App\Http\Controllers;

use App\Helpers\FegiAuth;
use App\Models\Collection;
use App\Models\Epp;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\CollectionService;
use Illuminate\Validation\ValidationException;
use Throwable;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

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
    public function show($id)
    {
       $collection = Collection::with([
            'creator',
            'epp',
            'egis.user',
            'egis.owner',
            'likes'
        ])->findOrFail($id);

        // Verifica like per utente strong auth
        if (auth()->check()) {
            $collection->is_liked = $collection->likes()
                ->where('user_id', auth()->id())
                ->exists();
        }
        // Verifica like per utente weak auth
        elseif (session('connected_user_id')) {
            $collection->is_liked = $collection->likes()
                ->where('user_id', session('connected_user_id'))
                ->exists();
        } else {
            $collection->is_liked = false;
        }

        $collection->likes_count = $collection->likes()->count();

        return view('collections.show', compact('collection'));
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


    /**
     * @Oracode OS1: Enhanced Collection Creation Endpoint
     * 🎯 Purpose: Create new collection via AJAX with robust validation and UEM error handling
     * 🧱 Core Logic: Validates input, uses CollectionService, handles both success and error scenarios
     * 🛡️ GDPR: Minimal data processing, user consent implied by authentication
     * 📥 Input: Request with collection_name (required, string, 2-100 chars)
     * 📤 Output: JSON response with collection data or standardized error
     * 🔄 Flow: Validate -> Create via Service -> Handle Response -> Return JSON
     *
     * @param Request $request HTTP request containing collection_name
     * @return JsonResponse Standardized JSON response for AJAX consumption
     *
     * @oracode-enhanced-validation Multi-layer input validation with meaningful errors
     * @oracode-ajax-optimized Designed for seamless frontend integration
     * @oracode-ux-feedback Rich feedback for superior user experience
     *
     * @since OS1-v1.0
     * @author Padmin D. Curtis (for Fabio Cherici)
     */
    public function create(Request $request): JsonResponse
    {
        try {
            // 🎯 OS1 Pillar 1: Explicit Intention - Log operation start
            $operationContext = [
                'operation' => 'collection_create_request',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString()
            ];

            // Enhanced Authentication Check with UEM
            $user = FegiAuth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'AUTHENTICATION_REQUIRED',
                    'message' => __('authentication.required_for_collection_creation'),
                    'redirect' => route('login')
                ], 401);
            }

            $operationContext['creator_id'] = $user->id;

            // 🎯 OS1 Enhanced Validation with Semantic Coherence
            $validated = $request->validate([
                'collection_name' => [
                    'required',
                    'string',
                    'min:2',
                    'max:100',
                    'regex:/^[a-zA-Z0-9\s\-_\'\"À-ÿ]+$/u' // Supports international chars
                ]
            ], [
                'collection_name.required' => __('validation.collection_name_required'),
                'collection_name.min' => __('validation.collection_name_min_length'),
                'collection_name.max' => __('validation.collection_name_max_length'),
                'collection_name.regex' => __('validation.collection_name_invalid_characters')
            ]);

            $collectionName = trim($validated['collection_name']);
            $operationContext['collection_name'] = $collectionName;

            // 🎯 OS1 Pillar 4: Virtuous Circularity - Check user collection limits
            $existingCollectionsCount = Collection::where('creator_id', $user->id)->count();
            $maxCollections = config('egi.max_collections_per_user', 10);

            if ($existingCollectionsCount >= $maxCollections) {
                return response()->json([
                    'success' => false,
                    'error' => 'COLLECTION_LIMIT_EXCEEDED',
                    'message' => __('collections.limit_exceeded', ['max' => $maxCollections]),
                    'current_count' => $existingCollectionsCount,
                    'max_allowed' => $maxCollections
                ], 422);
            }

            // 🎯 OS1 Service Integration with Enhanced Error Handling
            $collectionService = app(CollectionService::class);
            $result = $collectionService->createDefaultCollection(
                $user,
                false, // Non-default collection (user created)
                $collectionName
            );

            // Handle CollectionService response types
            if ($result instanceof JsonResponse) {
                // Service returned error - forward with enhanced context
                $errorData = $result->getData(true);
                return response()->json([
                    'success' => false,
                    'error' => $errorData['error'] ?? 'COLLECTION_SERVICE_ERROR',
                    'message' => $errorData['message'] ?? __('collections.creation_failed'),
                    'service_context' => $operationContext
                ], $result->getStatusCode());
            }

            // 🎯 OS1 Success Path - Collection Created Successfully
            $collection = $result; // It's a Collection model

            // 🎯 OS1 Pillar 5: Recursive Evolution - Success logging for optimization
            $successContext = array_merge($operationContext, [
                'collection_id' => $collection->id,
                'collection_position' => $collection->position,
                'success' => true,
                'creation_duration_ms' => (microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? 0)) * 1000
            ]);

            // Log success for analytics and system improvement
            app(UltraLogManager::class)->info('[CollectionsController] Collection created successfully via create()', $successContext);

            // 🎯 OS1 Virtuous Response - Rich feedback for frontend
            return response()->json([
                'success' => true,
                'message' => __('collections.created_successfully', ['name' => $collection->collection_name]),
                'collection' => [
                    'id' => $collection->id,
                    'name' => $collection->collection_name,
                    'creator_id' => $collection->creator_id,
                    'position' => $collection->position,
                    'is_default' => $collection->is_default,
                    'created_at' => $collection->created_at->toISOString()
                ],
                'next_action' => [
                    'type' => 'redirect',
                    'url' => route('collections.open', ['collection' => $collection->id]),
                    'message' => __('collections.redirecting_to_management')
                ],
                'user_stats' => [
                    'total_collections' => $existingCollectionsCount + 1,
                    'remaining_slots' => max(0, $maxCollections - $existingCollectionsCount - 1)
                ]
            ], 201);

        } catch (ValidationException $e) {
            // 🎯 OS1 Validation Error Handling
            return response()->json([
                'success' => false,
                'error' => 'VALIDATION_FAILED',
                'message' => __('validation.failed'),
                'errors' => $e->errors()
            ], 422);

        } catch (Throwable $e) {
            // 🎯 OS1 Comprehensive Error Boundary
            $errorContext = array_merge($operationContext ?? [], [
                'error_class' => get_class($e),
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]);

            // Use UEM for standardized error handling
            app(ErrorManagerInterface::class)->handle('COLLECTION_CREATION_UNEXPECTED_ERROR', $errorContext, $e);

            return response()->json([
                'success' => false,
                'error' => 'UNEXPECTED_ERROR',
                'message' => __('collections.creation_unexpected_error'),
                'support_reference' => Str::uuid()->toString() // For support tracking
            ], 500);
        }
    }
}
