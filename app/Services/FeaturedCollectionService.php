<?php

namespace App\Services;

use App\Models\Collection;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection as IlluminateCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * FeaturedCollectionService
 *
 * 🎯 Gestisce la selezione e l'ordinamento delle Collection in evidenza per il carousel guest
 * 📊 Calcola l'impatto stimato basato sulle prenotazioni più alte di ciascun EGI
 * 🏆 Applica logica di override manuale tramite featured_position
 *
 * @package App\Services
 */
class FeaturedCollectionService {
    /**
     * EPP ID da considerare per il calcolo dell'impatto (MVP: solo EPP id=2)
     */
    private const TARGET_EPP_ID = 2;

    /**
     * Percentuale EPP applicata alle prenotazioni (20%)
     */
    private const EPP_PERCENTAGE = 0.20;

    /**
     * Numero massimo di Collection nel carousel
     */
    private const MAX_CAROUSEL_ITEMS = 10;

    /**
     * Ottiene le Collection in evidenza per il carousel guest
     *
     * 🎯 Applica l'algoritmo di selezione completo:
     * 1. Filtra per featured_in_guest = true e is_published = true
     * 2. Ordina per featured_position (se presente), poi per impatto stimato
     * 3. Limita a massimo 10 elementi
     *
     * @param int $limit Numero massimo di Collection da restituire
     * @return IlluminateCollection Collection di Collection con impatto calcolato
     */
    public function getFeaturedCollections(int $limit = self::MAX_CAROUSEL_ITEMS): IlluminateCollection {
        try {
            // Approccio semplificato: prima ottengo le Collection candidate, poi calcolo l'impatto
            $candidateCollections = Collection::where('is_published', true)
                ->where(function ($query) {
                    $query->where('featured_in_guest', true)
                        ->orWhereExists(function ($subquery) {
                            $subquery->select(DB::raw(1))
                                ->from('egis')
                                ->join('reservations', 'reservations.egi_id', '=', 'egis.id')
                                ->whereColumn('egis.collection_id', 'collections.id')
                                ->where('reservations.is_current', true);
                        });
                })
                ->with(['creator', 'egis.reservations' => function ($query) {
                    $query->where('is_current', true)
                        ->orderBy('offer_amount_eur', 'desc');
                }])
                ->withCount('egis')
                ->get();

            // Calcolo l'impatto per ogni Collection e ordino
            $collections = $candidateCollections->map(function ($collection) {
                $estimatedImpact = $collection->egis->sum(function ($egi) {
                    $highestReservation = $egi->reservations->first();
                    return $highestReservation ? $highestReservation->offer_amount_eur * self::EPP_PERCENTAGE : 0;
                });

                $collection->estimated_impact = $estimatedImpact;
                return $collection;
            })->sortBy([
                // Prima ordinamento: posizione forzata
                function ($collection) {
                    return $collection->featured_position ?? 999;
                },
                // Secondo ordinamento: featured prima di non-featured
                function ($collection) {
                    return $collection->featured_in_guest ? 0 : 1;
                },
                // Terzo ordinamento: impatto decrescente
                function ($collection) {
                    return -$collection->estimated_impact;
                }
            ])->take($limit);            // Log per debugging in ambiente di sviluppo
            if (config('app.debug')) {
                Log::info('Featured Collections retrieved', [
                    'count' => $collections->count(),
                    'collections' => $collections->map(function ($collection) {
                        return [
                            'id' => $collection->id,
                            'name' => $collection->collection_name,
                            'featured_position' => $collection->featured_position,
                            'estimated_impact' => $collection->estimated_impact ?? 0,
                        ];
                    })->toArray()
                ]);
            }

            return $collections;
        } catch (\Exception $e) {
            Log::error('Error retrieving featured collections', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback: restituisce Collection senza il calcolo dell'impatto
            return $this->getFallbackFeaturedCollections($limit);
        }
    }

    /**
     * Calcola l'impatto stimato per una Collection specifica
     *
     * 🎯 Utilizza la stessa logica del carousel ma per una singola Collection
     * 📊 Somma delle quote EPP (20%) delle prenotazioni più alte per EGI
     *
     * @param Collection $collection La Collection di cui calcolare l'impatto
     * @return float L'impatto stimato in EUR
     */
    public function calculateEstimatedImpact(Collection $collection): float {
        try {
            $impact = $collection->egis()
                ->whereHas('reservations', function (Builder $query) {
                    $query->where('is_current', true);
                })
                ->with(['reservations' => function ($query) {
                    $query->where('is_current', true)
                        ->orderBy('offer_amount_eur', 'desc')
                        ->orderBy('created_at', 'asc'); // Tiebreaker per stesso importo
                }])
                ->get()
                ->sum(function ($egi) {
                    // Ottieni la prenotazione con l'offerta più alta per questo EGI
                    $highestReservation = $egi->reservations->first();
                    if (!$highestReservation) {
                        return 0;
                    }

                    // Calcola la quota EPP
                    return $highestReservation->offer_amount_eur * self::EPP_PERCENTAGE;
                });

            return round($impact, 2);
        } catch (\Exception $e) {
            Log::error('Error calculating estimated impact', [
                'collection_id' => $collection->id,
                'error' => $e->getMessage()
            ]);

            return 0.0;
        }
    }

    /**
     * Verifica se una Collection può essere inclusa nel carousel guest
     *
     * @param Collection $collection
     * @return bool True se può essere inclusa
     */
    public function canBeFeaturedinGuest(Collection $collection): bool {
        return $collection->is_published && $collection->featured_in_guest;
    }

    /**
     * Imposta una Collection come in evidenza con posizione opzionale
     *
     * @param Collection $collection
     * @param int|null $position Posizione forzata (1-10) o null per automatica
     * @return bool True se l'operazione è riuscita
     */
    public function setAsFeatured(Collection $collection, ?int $position = null): bool {
        try {
            // Validazione posizione
            if ($position !== null && ($position < 1 || $position > self::MAX_CAROUSEL_ITEMS)) {
                throw new \Exception("Featured position must be between 1 and " . self::MAX_CAROUSEL_ITEMS);
            }

            // Se viene specificata una posizione, verifica conflitti
            if ($position !== null) {
                $existingCollection = Collection::where('featured_position', $position)
                    ->where('id', '!=', $collection->id)
                    ->first();

                if ($existingCollection) {
                    // Sposta la Collection esistente in posizione automatica
                    $existingCollection->update(['featured_position' => null]);
                }
            }

            return $collection->update([
                'featured_in_guest' => true,
                'featured_position' => $position
            ]);
        } catch (\Exception $e) {
            Log::error('Error setting collection as featured', [
                'collection_id' => $collection->id,
                'position' => $position,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Rimuove una Collection dal carousel featured
     *
     * @param Collection $collection
     * @return bool True se l'operazione è riuscita
     */
    public function removeFromFeatured(Collection $collection): bool {
        try {
            return $collection->update([
                'featured_in_guest' => false,
                'featured_position' => null
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing collection from featured', [
                'collection_id' => $collection->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Metodo di fallback nel caso di errori nella query principale
     *
     * @param int $limit
     * @return IlluminateCollection
     */
    private function getFallbackFeaturedCollections(int $limit): IlluminateCollection {
        return Collection::where('is_published', true)
            ->where('featured_in_guest', true)
            ->with(['creator'])
            ->withCount('egis')
            ->orderByRaw('CASE WHEN featured_position IS NOT NULL THEN featured_position ELSE 999 END ASC')
            ->orderBy('updated_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Ottiene statistiche sui featured collections per l'admin
     *
     * @return array Array con statistiche
     */
    public function getFeaturedCollectionsStats(): array {
        try {
            $totalFeatured = Collection::where('featured_in_guest', true)->count();
            $withForcedPosition = Collection::where('featured_in_guest', true)
                ->whereNotNull('featured_position')->count();
            $withoutPosition = $totalFeatured - $withForcedPosition;

            return [
                'total_featured' => $totalFeatured,
                'with_forced_position' => $withForcedPosition,
                'automatic_position' => $withoutPosition,
                'max_allowed' => self::MAX_CAROUSEL_ITEMS
            ];
        } catch (\Exception $e) {
            Log::error('Error getting featured collections stats', [
                'error' => $e->getMessage()
            ]);

            return [
                'total_featured' => 0,
                'with_forced_position' => 0,
                'automatic_position' => 0,
                'max_allowed' => self::MAX_CAROUSEL_ITEMS
            ];
        }
    }
}
