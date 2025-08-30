<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Egi;
use App\Models\TraitCategory;
use App\Models\TraitType;
use App\Models\EgiTrait;
use App\Helpers\FegiAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * API Controller for EGI Traits System
 * 
 * @package FlorenceEGI\Http\Controllers\Api
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI Traits System)
 * @date 2024-12-27
 */
class TraitsApiController extends Controller
{
    /**
     * Get all available trait categories
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategories(Request $request)
    {
        $collectionId = $request->get('collection_id');
        
        try {
            // Cache con chiave più specifica e timeout più breve
            $cacheKey = "trait_categories_v2_{$collectionId}_" . md5(serialize([
                'collection_id' => $collectionId,
                'timestamp' => now()->format('Y-m-d-H') // Invalidazione ogni ora
            ]));
            
            $categories = Cache::remember(
                $cacheKey, 
                1800, // 30 minuti invece di 1 ora
                function () use ($collectionId) {
                    $query = TraitCategory::query();
                    
                    if ($collectionId) {
                        $query->where(function ($q) use ($collectionId) {
                            $q->where('is_system', true)
                              ->orWhere('collection_id', $collectionId);
                        });
                    } else {
                        // Se non c'è collection_id, prendi solo le categorie di sistema
                        $query->where('is_system', true);
                    }
                    
                    return $query->orderBy('sort_order')
                                ->orderBy('name')
                                ->get();
                }
            );

            \Log::info('TraitsApiController: Categories loaded', [
                'collection_id' => $collectionId,
                'categories_count' => $categories->count(),
                'cache_key' => $cacheKey
            ]);

            return response()->json([
                'success' => true,
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading trait categories: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error loading categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get trait types for a category
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTraitTypes(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:trait_categories,id'
        ]);
        
        $categoryId = $request->get('category_id');
        $collectionId = $request->get('collection_id');
        
        $types = TraitType::where('category_id', $categoryId)
            ->where(function ($query) use ($collectionId) {
                $query->where('is_system', true)
                      ->orWhere('collection_id', $collectionId);
            })
            ->orderBy('name')
            ->get();
        
        return response()->json([
            'success' => true,
            'types' => $types
        ]);
    }
    
    /**
     * Get trait types for a specific category via URL parameter
     * 
     * @param int $categoryId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTraitTypesByCategory($categoryId, Request $request)
    {
        $collectionId = $request->get('collection_id');
        
        $types = TraitType::where('category_id', $categoryId)
            ->where(function ($query) use ($collectionId) {
                $query->where('is_system', true)
                      ->orWhere('collection_id', $collectionId);
            })
            ->orderBy('name')
            ->get();
        
        return response()->json([
            'success' => true,
            'types' => $types
        ]);
    }
    
    /**
     * Get traits for an EGI
     * 
     * @param int $egiId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEgiTraits($egiId)
    {
        $egi = Egi::findOrFail($egiId);
        
        $traits = EgiTrait::with(['category', 'traitType'])
            ->where('egi_id', $egiId)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($trait) use ($egi) {
                // Calculate rarity percentage
                $trait->rarity_percentage = $this->calculateRarity(
                    $trait->trait_type_id, 
                    $trait->value,
                    $egi->collection_id
                );
                
                return [
                    'id' => $trait->id,
                    'category_id' => $trait->category_id,
                    'category_name' => $trait->category->name,
                    'trait_type_id' => $trait->trait_type_id,
                    'type_name' => $trait->traitType->name,
                    'value' => $trait->value,
                    'display_value' => $trait->display_value,
                    'display_type' => $trait->traitType->display_type,
                    'unit' => $trait->traitType->unit,
                    'rarity_percentage' => $trait->rarity_percentage,
                    'sort_order' => $trait->sort_order
                ];
            });
        
        return response()->json([
            'success' => true,
            'traits' => $traits,
            'is_locked' => $egi->is_published || !empty($egi->ipfs_hash)
        ]);
    }
    
    /**
     * Save traits for an EGI
     * 
     * @param Request $request
     * @param int $egiId
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveEgiTraits(Request $request, $egiId)
    {
        Log::info('SaveEgiTraits called for EGI: ' . $egiId);
        Log::info('Authenticated user ID: ' . (auth()->id() ?? 'NULL'));
        
        try {
            // Verifica che l'EGI esista
            $egi = Egi::findOrFail($egiId);
            Log::info('EGI found - Owner ID: ' . $egi->user_id);
            
            // Verifica autorizzazione
            if ($egi->user_id !== auth()->id()) {
                Log::warning('Unauthorized trait save attempt for EGI: ' . $egiId . ' by user: ' . (auth()->id() ?? 'NULL'));
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            
            // Verifica che non sia pubblicato
            if ($egi->is_published) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot modify traits after publication'
                ], 403);
            }
            
            $traits = $request->input('traits', []);
            Log::info('Traits to save:', $traits);
            Log::info('Request data:', $request->all());
            
            if (empty($traits)) {
                Log::warning('No traits provided in request');
                return response()->json([
                    'success' => false,
                    'message' => 'No traits provided'
                ], 400);
            }
            
            DB::transaction(function () use ($egiId, $traits) {
                // Ottieni i trait esistenti
                $existingTraits = EgiTrait::where('egi_id', $egiId)->get();
                Log::info("Found {$existingTraits->count()} existing traits for EGI {$egiId}");
                
                // Prepara i trait inviati per il confronto
                $incomingTraits = collect($traits);
                $keptTraitIds = [];
                
                // Processo trait per trait
                foreach ($incomingTraits as $index => $traitData) {
                    // Se il trait ha un ID esistente, aggiornalo
                    if (isset($traitData['id']) && $traitData['id'] > 0) {
                        $existingTrait = $existingTraits->where('id', $traitData['id'])->first();
                        
                        if ($existingTrait) {
                            // Aggiorna il trait esistente
                            $existingTrait->update([
                                'category_id' => $traitData['category_id'],
                                'trait_type_id' => $traitData['trait_type_id'],
                                'value' => $traitData['value'],
                                'display_value' => $traitData['display_value'] ?? $traitData['value'],
                                'sort_order' => $index
                            ]);
                            $keptTraitIds[] = $existingTrait->id;
                            Log::info("Updated existing trait ID: {$existingTrait->id} with value: {$traitData['value']}");
                        } else {
                            // ID non trovato, crea nuovo trait
                            $created = EgiTrait::create([
                                'egi_id' => $egiId,
                                'category_id' => $traitData['category_id'],
                                'trait_type_id' => $traitData['trait_type_id'],
                                'value' => $traitData['value'],
                                'display_value' => $traitData['display_value'] ?? $traitData['value'],
                                'sort_order' => $index,
                                'is_locked' => false
                            ]);
                            $keptTraitIds[] = $created->id;
                            Log::info("Created new trait (missing ID) with ID: {$created->id} and value: {$traitData['value']}");
                        }
                    } else {
                        // Nuovo trait (nessun ID o ID negativo/temporaneo)
                        $created = EgiTrait::create([
                            'egi_id' => $egiId,
                            'category_id' => $traitData['category_id'],
                            'trait_type_id' => $traitData['trait_type_id'],
                            'value' => $traitData['value'],
                            'display_value' => $traitData['display_value'] ?? $traitData['value'],
                            'sort_order' => $index,
                            'is_locked' => false
                        ]);
                        $keptTraitIds[] = $created->id;
                        Log::info("Created new trait with ID: {$created->id} and value: {$traitData['value']}");
                    }
                }
                
                // Elimina solo i trait che non sono più presenti nei dati inviati
                $traitsToDelete = $existingTraits->whereNotIn('id', $keptTraitIds);
                if ($traitsToDelete->count() > 0) {
                    $deletedIds = $traitsToDelete->pluck('id')->toArray();
                    EgiTrait::whereIn('id', $deletedIds)->delete();
                    Log::info("Deleted " . count($deletedIds) . " traits no longer present: " . implode(', ', $deletedIds));
                } else {
                    Log::info("No traits deleted - all existing traits were preserved or updated");
                }
            });
            
            Log::info('Traits saved successfully for EGI: ' . $egiId);
            
            return response()->json([
                'success' => true,
                'message' => 'Traits saved successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saving traits: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving traits: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Calculate rarity percentage for a trait value
     * 
     * @param int $traitTypeId
     * @param string $value
     * @param int $collectionId
     * @return float
     */
    private function calculateRarity($traitTypeId, $value, $collectionId)
    {
        $cacheKey = "trait_rarity_{$collectionId}_{$traitTypeId}_{$value}";
        
        return Cache::remember($cacheKey, 3600, function () use ($traitTypeId, $value, $collectionId) {
            // Total EGIs in collection
            $totalEgis = Egi::where('collection_id', $collectionId)->count();
            
            if ($totalEgis === 0) {
                return 0;
            }
            
            // EGIs with this trait value
            $egisWithTrait = EgiTrait::join('egis', 'egis.id', '=', 'egi_traits.egi_id')
                ->where('egis.collection_id', $collectionId)
                ->where('egi_traits.trait_type_id', $traitTypeId)
                ->where('egi_traits.value', $value)
                ->count();
            
            return round(($egisWithTrait / $totalEgis) * 100, 2);
        });
    }
    
    /**
     * Generate IPFS metadata for EGI traits
     * 
     * @param int $egiId
     * @return array
     */
    public function generateMetadata($egiId)
    {
        $egi = Egi::with(['traits.traitType'])->findOrFail($egiId);
        
        $attributes = $egi->traits->map(function ($trait) {
            $metadata = [
                'trait_type' => $trait->traitType->name,
                'value' => $trait->value
            ];
            
            if ($trait->traitType->display_type !== 'text') {
                $metadata['display_type'] = $trait->traitType->display_type;
            }
            
            if ($trait->traitType->unit) {
                $metadata['unit'] = $trait->traitType->unit;
            }
            
            if ($trait->traitType->display_type === 'boost_number') {
                $metadata['max_value'] = 100;
            }
            
            return $metadata;
        });
        
        return [
            'name' => $egi->title,
            'description' => $egi->description,
            'image' => $egi->ipfs_image_hash ? "ipfs://{$egi->ipfs_image_hash}" : $egi->main_image_url,
            'attributes' => $attributes,
            'external_url' => route('egis.show', $egi->id),
            'background_color' => 'D4A574' // Oro Fiorentino
        ];
    }
    
    /**
     * Clear traits cache (utile per testing o aggiornamenti)
     */
    public function clearCache(Request $request)
    {
        try {
            // Per semplicità, usiamo artisan cache:clear per pulire tutto
            \Artisan::call('cache:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'Traits cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing cache: ' . $e->getMessage()
            ], 500);
        }
    }
}