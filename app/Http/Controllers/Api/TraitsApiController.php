<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Egi;
use App\Models\TraitCategory;
use App\Models\TraitType;
use App\Models\EgiTrait;
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
     * Get all trait categories
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategories(Request $request)
    {
        $collectionId = $request->get('collection_id');
        
        $categories = Cache::remember(
            "trait_categories_{$collectionId}", 
            3600, 
            function () use ($collectionId) {
                return TraitCategory::where(function ($query) use ($collectionId) {
                    $query->where('is_system', true)
                          ->orWhere('collection_id', $collectionId);
                })
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
            }
        );
        
        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
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
        
        try {
            // Verifica che l'EGI esista
            $egi = Egi::findOrFail($egiId);
            
            // Verifica autorizzazione
            if ($egi->user_id !== auth()->id()) {
                Log::warning('Unauthorized trait save attempt for EGI: ' . $egiId);
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
            
            DB::transaction(function () use ($egiId, $traits) {
                // Elimina i traits esistenti
                EgiTrait::where('egi_id', $egiId)->delete();
                
                // Inserisci i nuovi traits
                foreach ($traits as $index => $traitData) {
                    EgiTrait::create([
                        'egi_id' => $egiId,
                        'category_id' => $traitData['category_id'],
                        'trait_type_id' => $traitData['trait_type_id'],
                        'value' => $traitData['value'],
                        'display_value' => $traitData['display_value'] ?? $traitData['value'],
                        'sort_order' => $index,
                        'is_locked' => false
                    ]);
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
}