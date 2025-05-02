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
 * @version     1.0.0
 * @since       2025-04-29
 *
 * @purpose     🎯 Provides a consistent API for modifying user roles specifically
 *              within the EGI module context, maintaining separation of concerns
 *              and allowing for implementation swapping.
 *
 * @context     🧩 This interface defines methods that will be called during EGI-related
 *              workflows where user role assignments need to be modified.
 *
 * @feature     🗝️ Role assignment for creators
 * @feature     🗝️ Role verification
 *
 * @signal      🚦 Returns success/failure status
 * @signal      🚦 Methods handle their own error reporting through injected dependencies
 *
 * @dependency  🤝 App\Models\User (implied by implementations)
 * @dependency  🤝 Spatie\Permission\Models\Role (implied by implementations)
 *
 * @privacy     🛡️ `@privacy-purpose`: Methods alter user permissions and access levels
 * @privacy     🛡️ `@privacy-consideration`: Changes to user roles affect data access authorization
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
}
