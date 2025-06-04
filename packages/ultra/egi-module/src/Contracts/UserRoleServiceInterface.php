<?php

declare(strict_types=1);

namespace Ultra\EgiModule\Contracts;

/**
 * 📜 Oracode Interface: UserRoleServiceInterface
 *
 * Defines the contract for user role management operations within the EGI module.
 *
 * @package     Ultra\EgiModule\Contracts
 * @author      Padmin D. Curtis (Generated for Fabio Cherici)
 * @copyright   2024 Fabio Cherici
 * @license     MIT
 * @version     1.1.0
 * @since       2025-04-29
 * @updated     2025-06-04 - Added updateUserCurrentCollection method
 *
 * @purpose     🎯 Provides a consistent API for modifying user roles and user-collection
 *              relationships specifically within the EGI module context, maintaining
 *              separation of concerns and allowing for implementation swapping.
 *
 * @context     🧩 This interface defines methods that will be called during EGI-related
 *              workflows where user role assignments and collection associations need
 *              to be modified.
 *
 * @feature     🗝️ Role assignment for creators
 * @feature     🗝️ Role verification
 * @feature     🗝️ Collection-user relationship management
 * @feature     🗝️ User current collection state management
 *
 * @signal      🚦 Returns success/failure status
 * @signal      🚦 Methods handle their own error reporting through injected dependencies
 *
 * @dependency  🤝 App\Models\User (implied by implementations)
 * @dependency  🤝 App\Models\Collection (implied by implementations)
 * @dependency  🤝 Spatie\Permission\Models\Role (implied by implementations)
 *
 * @privacy     🛡️ `@privacy-purpose`: Methods alter user permissions and access levels
 * @privacy     🛡️ `@privacy-consideration`: Changes to user roles affect data access authorization
 * @privacy     🛡️ `@privacy-data`: User-collection associations track user context
 *
 * @testing     🧪 Interface methods should be tested for both success and failure scenarios
 *
 * @rationale   💡 Decouples role management from direct controller/handler code, allowing
 *              for better testability and potential changes to the permission system.
 */
interface UserRoleServiceInterface
{
    /**
     * 🎯 Assigns the creator role to a specific user.
     *
     * Manages the allocation of the 'creator' role to users,
     * creating the role first if it doesn't exist.
     *
     * @param int $userId The ID of the user to assign the creator role to
     * @return bool True if the assignment succeeded or was already in place, false otherwise
     *
     * @privacy-purpose User role assignment for EGI creator operations
     */
    public function assignCreatorRole(int $userId): bool;

    /**
     * 🔍 Checks if a user has the creator role.
     *
     * Verifies whether a given user has the 'creator' role already assigned.
     *
     * @param int $userId The ID of the user to check
     * @return bool True if the user has the creator role, false otherwise
     *
     * @privacy-purpose User role verification for access control
     */
    public function hasCreatorRole(int $userId): bool;

    /**
     * 📋 Gets all users with the creator role.
     *
     * Retrieves all users that currently have the 'creator' role assigned.
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of users with the creator role
     *
     * @privacy-purpose Access multiple users for administrative operations
     */
    public function getCreators();

    /**
     * 🔗 Creates a collection-user pivot record with proper role assignment.
     *
     * Establishes the relationship between a user and collection in the pivot table
     * with appropriate role, permissions, and metadata for FlorenceEGI operations.
     *
     * @param int $userId The ID of the user to associate with the collection
     * @param int $collectionId The ID of the collection to associate the user with
     * @param string $role The role to assign ('creator', 'collaborator', 'viewer', etc.)
     * @return bool True if the record was created successfully, false otherwise
     *
     * @privacy-purpose Collection access control and permission management
     */
    public function createCollectionUserRecord(int $userId, int $collectionId, string $role): bool;

    /**
     * 🎯 Updates user's current collection ID with enhanced validation and error handling.
     *
     * This method is responsible for safely updating the current_collection_id field
     * in the users table. It provides comprehensive validation, error handling, and
     * audit logging following Oracode OS1 principles.
     *
     * @param int $userId The ID of the user to update
     * @param int $collectionId The ID of the collection to set as current
     * @param array $logContext Optional context for enhanced logging
     * @return bool True if update successful, method will throw/block on errors
     *
     * @throws \Exception Via UEM error handling for critical failures
     *
     * @privacy-purpose Collection association management for user experience
     * @privacy-data Updates user table with collection reference only
     * @privacy-consideration Collection changes affect user's default context
     *
     * @oracode-dimension technical|governance
     * @value-flow Maintains user-collection state consistency for FlorenceEGI operations
     * @community-impact Ensures users maintain proper collection context for uploads
     * @transparency-level Full audit trail of current collection changes
     * @sustainability-factor Maintains data integrity through proper validation
     * @narrative-coherence Supports FlorenceEGI user experience continuity
     */
    public function updateUserCurrentCollection(int $userId, int $collectionId, array $logContext = []): bool;
}
