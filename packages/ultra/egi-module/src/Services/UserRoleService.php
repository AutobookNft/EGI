<?php

declare(strict_types=1);

namespace Ultra\EgiModule\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Ultra\EgiModule\Contracts\UserRoleServiceInterface;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Throwable;

/**
 * 📜 Oracode Service: UserRoleService
 *
 * Implementation of the UserRoleServiceInterface for managing user roles within the EGI module.
 * Handles role assignments, verification, and user retrieval with proper error handling.
 *
 * @package     Ultra\EgiModule\Services
 * @author      Padmin D. Curtis (Generated for Fabio Cherici)
 * @copyright   2024 Fabio Cherici
 * @license     MIT
 * @version     1.0.0
 * @since       2025-04-29
 *
 * @purpose     🎯 Provides functionality for managing user roles within the EGI module,
 *              with specific focus on the 'creator' role assignment and verification.
 *              Implements standardized error handling via UEM.
 *
 * @context     🧩 Used within the EGI module's workflows when creator permissions need
 *              to be assigned or verified. Operates with permissions to modify user roles.
 *
 * @state       💾 Stateless. Relies on injected UltraErrorManager and UltraLogManager.
 *
 * @feature     🗝️ Creates and assigns the 'creator' role to users
 * @feature     🗝️ Verifies if users have the 'creator' role
 * @feature     🗝️ Retrieves all users with the 'creator' role
 * @feature     🗝️ Implements standardized error handling with UEM
 *
 * @signal      🚦 Returns operation success/failure via boolean values
 * @signal      🚦 Returns collections of users for the getCreators method
 * @signal      🚦 Logs operations and errors via ULM
 *
 * @privacy     🛡️ `@privacy-internal`: Accesses user models and role assignments
 * @privacy     🛡️ `@privacy-data`: Logs minimal user identifiers
 * @privacy     🛡️ `@privacy-purpose`: Manages role-based access control
 * @privacy     🛡️ `@privacy-consideration`: Role changes affect user data access capabilities
 *
 * @dependency  🤝 Ultra\ErrorManager\Interfaces\ErrorManagerInterface
 * @dependency  🤝 Ultra\UltraLogManager\UltraLogManager
 * @dependency  🤝 App\Models\User
 * @dependency  🤝 Spatie\Permission\Models\Role
 *
 * @testing     🧪 Unit Test: Mock dependencies and verify role assignment logic
 * @testing     🧪 Unit Test: Verify error handling with UEM for various edge cases
 * @testing     🧪 Integration Test: Verify actual role changes in the database
 *
 * @rationale   💡 Centralizes role management operations for the EGI module with
 *              proper error handling and logging for audit trails.
 */
class UserRoleService implements UserRoleServiceInterface
{
    /**
     * 🧱 @dependency UltraErrorManager instance.
     * Used for standardized error handling.
     * @var ErrorManagerInterface
     */
    protected readonly ErrorManagerInterface $errorManager;

    /**
     * 🧱 @dependency UltraLogManager instance.
     * Used for standardized logging and auditing.
     * @var UltraLogManager
     */
    protected readonly UltraLogManager $logger;

    /**
     * 🧱 @property Log channel name.
     * Defines the ULM log channel to use.
     * @var string
     */
    protected string $logChannel = 'florenceegi';

    /**
     * 🎯 Constructor: Injects required dependencies.
     *
     * @param ErrorManagerInterface $errorManager UEM for standardized error handling
     * @param UltraLogManager $logger ULM for standardized logging
     */
    public function __construct(
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger
    ) {
        $this->errorManager = $errorManager;
        $this->logger = $logger;

        // Define custom error codes if not already defined in config
        $this->defineRoleErrorCodes();
    }

    /**
     * {@inheritdoc}
     * 🎯 Assigns the creator role to a specific user.
     *
     * --- Logic ---
     * 1. Find the user by ID
     * 2. Verify the user exists
     * 3. Find or create the 'creator' role
     * 4. Check if the user already has the role
     * 5. Assign the role if needed
     * 6. Log the operation and handle any errors
     * --- End Logic ---
     *
     * @param int $userId The ID of the user to assign the creator role to
     * @return bool True if the assignment succeeded or was already in place, false otherwise
     *
     * @privacy-purpose User role assignment for EGI creator operations
     */
    public function assignCreatorRole(int $userId): bool
    {
        // Create context for logging and error handling
        $context = [
            'user_id' => $userId,
            'role' => 'creator'
        ];

        try {
            // Find the user by ID
            $user = User::find($userId);

            // Verify the user exists
            if (!$user) {
                $this->logger->error('User not found during role assignment', $context);

                // Handle the error with UEM without throwing (return false)
                $this->errorManager->handle(
                    'ROLE_USER_NOT_FOUND',
                    $context,
                    null,
                    false // Don't throw
                );

                return false;
            }

            // Find or create the 'creator' role
            $creatorRole = Role::firstOrCreate(['name' => 'creator']);

            // Check if role already assigned
            if ($user->hasRole('creator')) {
                $this->logger->info('User already has creator role, no action needed', $context);
                return true; // Already assigned, consider it success
            }

            // Assign the role to the user
            $user->assignRole($creatorRole);

            // Log successful assignment
            $this->logger->info('Creator role assigned to user', $context);

            return true;

        } catch (Throwable $e) {
            // Log the error
            $this->logger->error('Error during role assignment', array_merge($context, [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e)
            ]));

            // Handle the error with UEM without throwing
            $this->errorManager->handle(
                'ROLE_ASSIGNMENT_FAILED',
                array_merge($context, [
                    'error_message' => $e->getMessage()
                ]),
                $e,
                false // Don't throw
            );

            return false;
        }
    }

    /**
     * {@inheritdoc}
     * 🔍 Checks if a user has the creator role.
     *
     * @param int $userId The ID of the user to check
     * @return bool True if the user has the creator role, false otherwise
     *
     * @privacy-purpose User role verification for access control
     */
    public function hasCreatorRole(int $userId): bool
    {
        // Create context for logging and error handling
        $context = [
            'user_id' => $userId,
            'role' => 'creator'
        ];

        try {
            // Find the user by ID
            $user = User::find($userId);

            // Verify the user exists
            if (!$user) {
                $this->logger->notice('User not found during role check', $context);
                return false;
            }

            // Check if the user has the role
            $hasRole = $user->hasRole('creator');

            // Log at appropriate level
            if ($hasRole) {
                $this->logger->debug('User has creator role', $context);
            } else {
                $this->logger->debug('User does not have creator role', $context);
            }

            return $hasRole;

        } catch (Throwable $e) {
            // Log the error
            $this->logger->error('Error checking user role', array_merge($context, [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e)
            ]));

            // Handle the error with UEM without throwing
            $this->errorManager->handle(
                'ROLE_CHECK_FAILED',
                array_merge($context, [
                    'error_message' => $e->getMessage()
                ]),
                $e,
                false // Don't throw
            );

            return false;
        }
    }

    /**
     * {@inheritdoc}
     * 📋 Gets all users with the creator role.
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of users with the creator role
     *
     * @privacy-purpose Access multiple users for administrative operations
     */
    public function getCreators()
    {
        $context = ['role' => 'creator'];

        try {
            // Get the role by name
            $creatorRole = Role::where('name', 'creator')->first();

            // If role doesn't exist, return empty collection
            if (!$creatorRole) {
                $this->logger->notice('Creator role does not exist', $context);
                return collect(); // Return empty collection
            }

            // Get all users with the role
            $creators = User::role('creator')->get();

            $this->logger->info('Retrieved creator users', array_merge($context, [
                'count' => $creators->count()
            ]));

            return $creators;

        } catch (Throwable $e) {
            // Log the error
            $this->logger->error('Error retrieving creators', array_merge($context, [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e)
            ]));

            // Handle the error with UEM without throwing
            $this->errorManager->handle(
                'ROLE_USERS_RETRIEVAL_FAILED',
                array_merge($context, [
                    'error_message' => $e->getMessage()
                ]),
                $e,
                false // Don't throw
            );

            return collect(); // Return empty collection on error
        }
    }

    /**
     * 🧱 Define custom error codes specific to role operations.
     * Registers these codes with UEM for consistent error handling.
     *
     * @return void
     */
    protected function defineRoleErrorCodes(): void
    {
        // Define error for user not found during role operations
        $this->errorManager->defineError('ROLE_USER_NOT_FOUND', [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message' => 'User ID :user_id not found during role operation',
            'user_message' => 'The specified user could not be found.',
            'http_status_code' => 404,
            'msg_to' => 'div',
            'devTeam_email_need' => false,
            'notify_slack' => false,
        ]);

        // Define error for role assignment failures
        $this->errorManager->defineError('ROLE_ASSIGNMENT_FAILED', [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message' => 'Failed to assign role :role to user :user_id: :error_message',
            'user_message' => 'There was a problem updating user permissions. Please try again or contact support.',
            'http_status_code' => 500,
            'msg_to' => 'sweet-alert',
            'devTeam_email_need' => false,
            'notify_slack' => false,
        ]);

        // Define error for role check failures
        $this->errorManager->defineError('ROLE_CHECK_FAILED', [
            'type' => 'error',
            'blocking' => 'not',
            'dev_message' => 'Error checking if user :user_id has role :role: :error_message',
            'user_message' => null, // No user message, internal error
            'http_status_code' => 500,
            'msg_to' => 'log-only',
            'devTeam_email_need' => false,
            'notify_slack' => false,
        ]);

        // Define error for user retrieval failures
        $this->errorManager->defineError('ROLE_USERS_RETRIEVAL_FAILED', [
            'type' => 'error',
            'blocking' => 'semi-blocking',
            'dev_message' => 'Error retrieving users with role :role: :error_message',
            'user_message' => 'Unable to load user list. Please try again later.',
            'http_status_code' => 500,
            'msg_to' => 'div',
            'devTeam_email_need' => false,
            'notify_slack' => false,
        ]);
    }
}
