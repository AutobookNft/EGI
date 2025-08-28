<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CollectionBannerController extends Controller {
    /**
     * Upload/replace banner image for a Collection using Spatie Media.
     */
    public function store(Request $request, Collection $collection) {
        // Auth check: only the creator can update the banner
        if (!Auth::check() || Auth::id() !== (int) $collection->creator_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'banner' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,avif', 'max:8192'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = $request->file('banner');

        try {
            // Save as single file in media collection 'head'
            $media = $collection
                ->addMedia($file)
                ->usingFileName('banner_' . time() . '.' . $file->getClientOriginalExtension())
                ->toMediaCollection('head');

            return response()->json([
                'success' => true,
                'original_url' => $media->getUrl(),
                'banner_url' => $media->getUrl('banner'),
                'card_url' => $media->getUrl('card'),
                'thumb_url' => $media->getUrl('thumb'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Collection banner upload failed', [
                'collection_id' => $collection->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Upload failed',
            ], 500);
        }
    }
}
