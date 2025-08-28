<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CollectionCrudController extends Controller
{
    public function update(Request $request, Collection $collection)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $owns = (int)$collection->creator_id === (int)$user->id;
        $canUpdate = $user->can('update_collection') || ($user->can('edit_own_collection') && $owns);
        if (!$canUpdate) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

    $rules = [
            'collection_name'   => ['required', 'string', 'max:150'],
            'description'       => ['nullable', 'string', 'max:2000'],
            'url_collection_site' => ['nullable', 'url', 'max:255'],
            'type'              => ['nullable', 'string', 'max:50'],
            'floor_price'       => ['nullable', 'numeric', 'min:0'],
            'is_published'      => ['nullable', 'boolean'],
        ];

        $data = $request->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $payload = $validator->validated();

        // Business rule: publishing guard
        if (array_key_exists('is_published', $payload) && $payload['is_published']) {
            if (method_exists($collection, 'canBePublished') && !$collection->canBePublished()) {
                return response()->json([
                    'message' => 'Cannot publish due to pending approvals or invalid state',
                    'errors' => ['is_published' => ['Publishing conditions not met']],
                ], 422);
            }
        }

        // Aggiorna solo i campi previsti
        $collection->fill([
            'collection_name'   => $payload['collection_name'] ?? $collection->collection_name,
            'description'       => $payload['description'] ?? $collection->description,
            'url_collection_site' => $payload['url_collection_site'] ?? $collection->url_collection_site,
            'type'              => $payload['type'] ?? $collection->type,
            'floor_price'       => $payload['floor_price'] ?? $collection->floor_price,
            'is_published'      => array_key_exists('is_published', $payload) ? (bool)$payload['is_published'] : $collection->is_published,
        ]);

        // Default EPP: se non valorizzato, forziamo id=2
        if (is_null($collection->epp_id)) {
            $collection->epp_id = 2;
        }
        $collection->save();

        // Prepara risposta aggiornata per UI live
        $collection->load(['epp']);

        // Calcola schema image (usa Spatie banner se disponibile)
        $schemaImage = null;
        if (method_exists($collection, 'getFirstMediaUrl')) {
            $schemaImage = $collection->getFirstMediaUrl('head', 'banner') ?: null;
        }

        return response()->json([
            'success' => true,
            'collection' => [
                'id' => $collection->id,
                'collection_name' => $collection->collection_name,
                'description' => $collection->description,
                'url_collection_site' => $collection->url_collection_site,
                'type' => $collection->type,
                'floor_price' => $collection->floor_price,
                'is_published' => (bool)$collection->is_published,
                'epp_id' => $collection->epp_id,
                'epp' => $collection->epp ? [
                    'id' => $collection->epp->id,
                    'name' => $collection->epp->name,
                    'description' => $collection->epp->description,
                ] : null,
            ],
            'schema_image' => $schemaImage,
        ]);
    }

    public function destroy(Request $request, Collection $collection)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $owns = (int)$collection->creator_id === (int)$user->id;
        $canDelete = $user->can('delete_collection') || ($user->can('edit_own_collection') && $owns);
        if (!$canDelete) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $collection->delete();

        return response()->json([
            'success' => true,
            'redirect_url' => route('home.collections.index'),
        ]);
    }
}
