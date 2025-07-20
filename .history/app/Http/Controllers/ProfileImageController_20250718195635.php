<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @Oracode Controller: Profile Image Management
 * 🎯 Purpose: Handle user profile image upload, selection, and deletion
 * 🖼️ Media: Uses Spatie Media Library for efficient image management
 * 🛡️ Security: Validates file uploads and user permissions
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI Profile Images)
 * @date 2025-01-07
 */
class ProfileImageController extends \App\Http\Controllers\Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Upload new profile images (single or multiple)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|RedirectResponse
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|array',
            'profile_image.*' => 'image|mimes:jpeg,png,webp|max:2048',
        ]);

        try {
            $user = Auth::user();
            $uploadedMedia = [];

            // Upload each image
            foreach ($request->file('profile_image') as $file) {
                $media = $user->addMedia($file)
                    ->toMediaCollection('profile_images');

                $uploadedMedia[] = $media;
            }

            // If this is the first image(s), set the first one as current
            if ($user->getAllProfileImages()->count() === count($uploadedMedia)) {
                $user->setCurrentProfileImage($uploadedMedia[0]);
            }

            Log::info('Profile images uploaded', [
                'user_id' => $user->id,
                'media_count' => count($uploadedMedia),
                'media_ids' => collect($uploadedMedia)->pluck('id')->toArray()
            ]);

            // Return JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('profile.image_uploaded_successfully'),
                    'uploaded_count' => count($uploadedMedia)
                ]);
            }

            return redirect()->route('profile.show')
                ->with('success', __('profile.image_uploaded_successfully'));
        } catch (\Exception $e) {
            Log::error('Profile image upload failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            // Return JSON response for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('profile.image_upload_failed')
                ], 500);
            }

            return redirect()->route('profile.show')
                ->with('error', __('profile.image_upload_failed'));
        }
    }

    /**
     * Set a specific image as the current profile image
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function setCurrentImage(Request $request): RedirectResponse
    {
        $request->validate([
            'media_id' => 'required|integer|exists:media,id',
        ]);

        try {
            $user = Auth::user();
            $media = Media::findOrFail($request->media_id);

            // Verify the media belongs to the user
            if ($media->model_id !== $user->id || $media->collection_name !== 'profile_images') {
                throw new \Exception('Unauthorized access to media');
            }

            $user->setCurrentProfileImage($media);

            Log::info('Current profile image updated', [
                'user_id' => $user->id,
                'media_id' => $media->id
            ]);

            return redirect()->route('profile.show')
                ->with('success', __('profile.current_image_updated'));
        } catch (\Exception $e) {
            Log::error('Failed to set current profile image', [
                'user_id' => Auth::id(),
                'media_id' => $request->media_id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('profile.show')
                ->with('error', __('profile.failed_to_update_current_image'));
        }
    }

    /**
     * Delete a profile image
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteImage(Request $request): RedirectResponse
    {
        $request->validate([
            'media_id' => 'required|integer|exists:media,id',
        ]);

        try {
            $user = Auth::user();
            $media = Media::findOrFail($request->media_id);

            // Verify the media belongs to the user
            if ($media->model_id !== $user->id || $media->collection_name !== 'profile_images') {
                throw new \Exception('Unauthorized access to media');
            }

            // Check if this is the current profile image
            $isCurrent = $user->getCurrentProfileImage() && $user->getCurrentProfileImage()->id === $media->id;

            // Delete the media
            $media->delete();

            // If this was the current image, set another one as current (if available)
            if ($isCurrent) {
                $nextImage = $user->getAllProfileImages()->first();
                if ($nextImage) {
                    $user->setCurrentProfileImage($nextImage);
                }
            }

            Log::info('Profile image deleted', [
                'user_id' => $user->id,
                'media_id' => $media->id,
                'was_current' => $isCurrent
            ]);

            return redirect()->route('profile.show')
                ->with('success', __('profile.image_deleted_successfully'));
        } catch (\Exception $e) {
            Log::error('Failed to delete profile image', [
                'user_id' => Auth::id(),
                'media_id' => $request->media_id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('profile.show')
                ->with('error', __('profile.failed_to_delete_image'));
        }
    }
}
