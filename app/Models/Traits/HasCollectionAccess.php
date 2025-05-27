<?php

namespace App\Models\Traits;

/**
 * @Oracode Trait: Collection Access Management
 * 🎯 Purpose: Manages user relationships with collections and EGIs
 * 🛡️ Privacy: Handles collection access with appropriate permissions
 * 🧱 Core Logic: Centralizes collection-related user capabilities
 */
trait HasCollectionAccess
{
    /**
     * Get user's current active collection
     */
    public function getCurrentCollection()
    {
        return $this->belongsTo(Collection::class, 'current_collection_id')->first();
    }

    /**
     * Set current active collection
     */
    public function setCurrentCollection($collectionId): bool
    {
        return $this->update(['current_collection_id' => $collectionId]);
    }

    /**
     * Check if user owns specific collection
     */
    public function ownsCollection($collectionId): bool
    {
        return $this->ownedCollections()->where('id', $collectionId)->exists();
    }

    /**
     * Check if user is member of specific collection
     */
    public function isMemberOfCollection($collectionId): bool
    {
        return $this->collections()->where('collection_id', $collectionId)->exists();
    }

    /**
     * Get user's role in specific collection
     */
    public function getRoleInCollection($collectionId): ?string
    {
        $membership = $this->collections()
            ->where('collection_id', $collectionId)
            ->first();

        return $membership?->pivot->role;
    }

    /**
     * Check if user can access collection
     */
    public function canAccessCollection($collectionId): bool
    {
        return $this->ownsCollection($collectionId) ||
               $this->isMemberOfCollection($collectionId);
    }

    /**
     * Get collections where user has specific role
     */
    public function getCollectionsByRole(string $role)
    {
        return $this->collections()
            ->wherePivot('role', $role)
            ->get();
    }

    /**
     * Get user's collection statistics
     */
    public function getCollectionStats(): array
    {
        return [
            'owned_collections' => $this->ownedCollections()->count(),
            'member_collections' => $this->collections()->count(),
            'current_collection_id' => $this->current_collection_id,
            'roles' => $this->collections()
                ->select('role')
                ->distinct()
                ->pluck('role')
                ->toArray()
        ];
    }

    /**
     * Check if user can create collections
     */
    public function canCreateCollections(): bool
    {
        // Basic check - creators and above can create collections
        return in_array($this->usertype, ['creator', 'azienda', 'epp_entity']);
    }

    /**
     * Join collection with specific role
     */
    public function joinCollection($collectionId, string $role = 'member'): bool
    {
        if ($this->isMemberOfCollection($collectionId)) {
            return false;
        }

        $this->collections()->attach($collectionId, [
            'role' => $role,
            'is_owner' => false,
            'status' => 'active',
            'joined_at' => now()
        ]);

        return true;
    }

    /**
     * Leave collection
     */
    public function leaveCollection($collectionId): bool
    {
        if ($this->ownsCollection($collectionId)) {
            return false; // Owners cannot leave their own collections
        }

        return $this->collections()->detach($collectionId) > 0;
    }
}