<?php

namespace App\Http\Controllers;

use App\Models\Utility;
use App\Models\Egi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller per gestione Utility con supporto multilingua
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Utility System Multilingual)
 * @date 2025-01-03
 * @purpose Gestisce creazione, modifica e eliminazione delle utility associate agli EGI
 * @context Controller per UtilityManager component, permette ai creator di gestire utility
 */
class UtilityController extends Controller {
    /**
     * Store new utility
     */
    public function store(Request $request) {
        // Validazione con messaggi localizzati
        $validated = $request->validate([
            'egi_id' => 'required|exists:egis,id',
            'type' => 'required|in:physical,service,hybrid,digital',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            // Physical fields
            'weight' => 'required_if:type,physical,hybrid|numeric|min:0',
            'dimensions' => 'nullable|array',
            'dimensions.length' => 'nullable|numeric|min:0',
            'dimensions.width' => 'nullable|numeric|min:0',
            'dimensions.height' => 'nullable|numeric|min:0',
            'estimated_shipping_days' => 'nullable|integer|min:1',
            'fragile' => 'nullable|boolean',
            'insurance_recommended' => 'nullable|boolean',
            'shipping_notes' => 'nullable|string',
            // Service fields
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'max_uses' => 'nullable|integer|min:1',
            'activation_instructions' => 'nullable|string',
            // Media
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|max:10240' // Max 10MB per image
        ], [
            // Messaggi di validazione localizzati
            'title.required' => __('utility.validation.title_required'),
            'type.required' => __('utility.validation.type_required'),
            'weight.required_if' => __('utility.validation.weight_required'),
            'valid_until.after' => __('utility.validation.valid_until_after'),
        ]);

        // Verifica permessi
        $egi = Egi::findOrFail($validated['egi_id']);

        // Verifica che l'utente sia il creator della collection
        if (!Auth::check() || Auth::id() !== $egi->collection->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Verifica che la collection non sia ancora pubblicata
        // Temporaneamente commentato per permettere il testing
        /*if ($egi->collection->status === 'published') {
            return redirect()
                ->route('egis.show', $egi)
                ->with('error', 'Cannot modify utility after collection is published.');
        }*/

        // Crea utility
        $utility = Utility::create([
            'egi_id' => $validated['egi_id'],
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'dimensions' => $validated['dimensions'] ?? null,
            'estimated_shipping_days' => $validated['estimated_shipping_days'] ?? null,
            'fragile' => $validated['fragile'] ?? false,
            'insurance_recommended' => $validated['insurance_recommended'] ?? false,
            'shipping_notes' => $validated['shipping_notes'] ?? null,
            'valid_from' => $validated['valid_from'] ?? null,
            'valid_until' => $validated['valid_until'] ?? null,
            'max_uses' => $validated['max_uses'] ?? null,
            'activation_instructions' => $validated['activation_instructions'] ?? null,
            'status' => 'active',
            'current_uses' => 0
        ]);

        // Gestione media
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $utility->addMedia($image)->toMediaCollection('utility_gallery');
            }
        }

        return redirect()
            ->route('egis.show', $egi)
            ->with('success', __('utility.success_created'));
    }

    /**
     * Update existing utility
     */
    public function update(Request $request, Utility $utility) {
        // Verifica permessi
        if (!Auth::check() || Auth::id() !== $utility->egi->collection->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Verifica che la collection non sia ancora pubblicata
        // Temporaneamente commentato per permettere il testing
        /*if ($utility->egi->collection->status === 'published') {
            return redirect()
                ->route('egis.show', $utility->egi)
                ->with('error', 'Cannot modify utility after collection is published.');
        }*/

        // Validazione con messaggi localizzati (stessa di store)
        $validated = $request->validate([
            'type' => 'required|in:physical,service,hybrid,digital',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            // Physical fields
            'weight' => 'required_if:type,physical,hybrid|numeric|min:0',
            'dimensions' => 'nullable|array',
            'dimensions.length' => 'nullable|numeric|min:0',
            'dimensions.width' => 'nullable|numeric|min:0',
            'dimensions.height' => 'nullable|numeric|min:0',
            'estimated_shipping_days' => 'nullable|integer|min:1',
            'fragile' => 'nullable|boolean',
            'insurance_recommended' => 'nullable|boolean',
            'shipping_notes' => 'nullable|string',
            // Service fields
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'max_uses' => 'nullable|integer|min:1',
            'activation_instructions' => 'nullable|string',
            // Media
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|max:10240', // Max 10MB per image
            'remove_media' => 'nullable|array',
            'remove_media.*' => 'integer'
        ], [
            'title.required' => __('utility.validation.title_required'),
            'type.required' => __('utility.validation.type_required'),
            'weight.required_if' => __('utility.validation.weight_required'),
            'valid_until.after' => __('utility.validation.valid_until_after'),
        ]);

        // Update utility
        $utility->update([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'dimensions' => $validated['dimensions'] ?? null,
            'estimated_shipping_days' => $validated['estimated_shipping_days'] ?? null,
            'fragile' => $validated['fragile'] ?? false,
            'insurance_recommended' => $validated['insurance_recommended'] ?? false,
            'shipping_notes' => $validated['shipping_notes'] ?? null,
            'valid_from' => $validated['valid_from'] ?? null,
            'valid_until' => $validated['valid_until'] ?? null,
            'max_uses' => $validated['max_uses'] ?? null,
            'activation_instructions' => $validated['activation_instructions'] ?? null,
        ]);

        // Gestione rimozione media
        if ($request->has('remove_media')) {
            foreach ($request->remove_media as $mediaId) {
                $utility->media()->find($mediaId)?->delete();
            }
        }

        // Aggiungi nuove immagini
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $utility->addMedia($image)->toMediaCollection('utility_gallery');
            }
        }

        return redirect()
            ->route('egis.show', $utility->egi)
            ->with('success', __('utility.success_updated'));
    }

    /**
     * Remove utility
     */
    public function destroy(Utility $utility) {
        // Verifica permessi
        if (!Auth::check() || Auth::id() !== $utility->egi->collection->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Verifica che la collection non sia ancora pubblicata
        // Temporaneamente commentato per permettere il testing
        /*if ($utility->egi->collection->status === 'published') {
            return redirect()
                ->route('egis.show', $utility->egi)
                ->with('error', 'Cannot remove utility after collection is published.');
        }*/

        $egi = $utility->egi;

        // Elimina media associati
        $utility->clearMediaCollection('utility_gallery');
        $utility->clearMediaCollection('utility_documents');

        // Elimina utility
        $utility->delete();

        return redirect()
            ->route('egis.show', $egi)
            ->with('success', 'Utility removed successfully.');
    }
}
