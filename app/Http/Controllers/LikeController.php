<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Egi;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for managing likes on collections and EGIs.
 *
 * Handles the toggleable like functionality for both collections and EGIs,
 * providing both web and API endpoints for the like interactions.
 *
 * --- Core Logic ---
 * 1. Toggles likes on collections (add/remove)
 * 2. Toggles likes on EGIs (add/remove)
 * 3. Returns appropriate responses for both web and API requests
 * 4. Ensures user authentication before processing likes
 * 5. Maintains consistent like counts across the platform
 * --- End Core Logic ---
 *
 * @package App\Http\Controllers
 * @author Your Name <your.email@example.com>
 * @version 1.0.0
 * @since 1.0.0
 */
class LikeController extends Controller
{
    /**
     * Toggle a like on a collection.
     *
     * If the user has already liked the collection, the like is removed.
     * Otherwise, a new like is created.
     *
     * @param Collection $collection The collection to toggle the like on
     * @return \Illuminate\Http\RedirectResponse Redirect to the collection page
     */
    public function toggleCollectionLike(Collection $collection)
    {
        $user = Auth::user();
        
        // Check if the user has already liked this collection
        $existingLike = Like::where('user_id', $user->id)
            ->where('likeable_type', Collection::class)
            ->where('likeable_id', $collection->id)
            ->first();
        
        if ($existingLike) {
            // Remove the like
            $existingLike->delete();
            $message = 'You have unliked this collection.';
        } else {
            // Create a new like
            $like = new Like();
            $like->user_id = $user->id;
            $like->likeable_type = Collection::class;
            $like->likeable_id = $collection->id;
            $like->save();
            
            $message = 'You have liked this collection.';
        }
        
        // Redirect back with a success message
        return redirect()->back()->with('success', $message);
    }
    
    /**
     * Toggle a like on an EGI.
     *
     * If the user has already liked the EGI, the like is removed.
     * Otherwise, a new like is created.
     *
     * @param Egi $egi The EGI to toggle the like on
     * @return \Illuminate\Http\RedirectResponse Redirect to the EGI page
     */
    public function toggleEgiLike(Egi $egi)
    {
        $user = Auth::user();
        
        // Check if the user has already liked this EGI
        $existingLike = Like::where('user_id', $user->id)
            ->where('likeable_type', Egi::class)
            ->where('likeable_id', $egi->id)
            ->first();
        
        if ($existingLike) {
            // Remove the like
            $existingLike->delete();
            $message = 'You have unliked this EGI.';
        } else {
            // Create a new like
            $like = new Like();
            $like->user_id = $user->id;
            $like->likeable_type = Egi::class;
            $like->likeable_id = $egi->id;
            $like->save();
            
            $message = 'You have liked this EGI.';
        }
        
        // Redirect back with a success message
        return redirect()->back()->with('success', $message);
    }
    
    /**
     * API endpoint to toggle a like on a collection.
     *
     * Returns JSON response with the updated like status and count.
     *
     * @param Collection $collection The collection to toggle the like on
     * @return \Illuminate\Http\JsonResponse JSON response with like status
     */
    public function apiToggleCollectionLike(Collection $collection)
    {
        $user = Auth::user();
        
        // Check if the user has already liked this collection
        $existingLike = Like::where('user_id', $user->id)
            ->where('likeable_type', Collection::class)
            ->where('likeable_id', $collection->id)
            ->first();
        
        $isLiked = false;
        
        if ($existingLike) {
            // Remove the like
            $existingLike->delete();
            $message = 'You have unliked this collection.';
        } else {
            // Create a new like
            $like = new Like();
            $like->user_id = $user->id;
            $like->likeable_type = Collection::class;
            $like->likeable_id = $collection->id;
            $like->save();
            
            $isLiked = true;
            $message = 'You have liked this collection.';
        }
        
        // Get the updated like count
        $likesCount = Like::where('likeable_type', Collection::class)
            ->where('likeable_id', $collection->id)
            ->count();
        
        // Return JSON response
        return response()->json([
            'success' => true,
            'message' => $message,
            'is_liked' => $isLiked,
            'likes_count' => $likesCount
        ]);
    }
    
    /**
     * API endpoint to toggle a like on an EGI.
     *
     * Returns JSON response with the updated like status and count.
     *
     * @param Egi $egi The EGI to toggle the like on
     * @return \Illuminate\Http\JsonResponse JSON response with like status
     */
    public function apiToggleEgiLike(Egi $egi)
    {
        $user = Auth::user();
        
        // Check if the user has already liked this EGI
        $existingLike = Like::where('user_id', $user->id)
            ->where('likeable_type', Egi::class)
            ->where('likeable_id', $egi->id)
            ->first();
        
        $isLiked = false;
        
        if ($existingLike) {
            // Remove the like
            $existingLike->delete();
            $message = 'You have unliked this EGI.';
        } else {
            // Create a new like
            $like = new Like();
            $like->user_id = $user->id;
            $like->likeable_type = Egi::class;
            $like->likeable_id = $egi->id;
            $like->save();
            
            $isLiked = true;
            $message = 'You have liked this EGI.';
        }
        
        // Get the updated like count
        $likesCount = Like::where('likeable_type', Egi::class)
            ->where('likeable_id', $egi->id)
            ->count();
        
        // Return JSON response
        return response()->json([
            'success' => true,
            'message' => $message,
            'is_liked' => $isLiked,
            'likes_count' => $likesCount
        ]);
    }
}
