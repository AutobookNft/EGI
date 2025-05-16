<?php

namespace App\Services;

use App\Models\User;
use App\Models\Collection;
use Ultra\EgiModule\Contracts\UserRoleServiceInterface;
use Ultra\EgiModule\Contracts\WalletServiceInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Illuminate\Http\JsonResponse;
use Exception;
use Throwable;

/**
 * @Oracode Service for managing user collections
 * 🎯 Purpose: Handles the creation, lookup, and assignment of collections to users
 * 🧱 Core Logic: Collection lifecycle management with fallback strategies
 * 🛡️ GDPR: Manages user-collection associations with minimal data exposure
 *
 * @package App\Services
 * @author Padmin D. Curtis
 * @version 2.0.0
 * @date 2025-05-13
 *
 * @core-responsibilities
 * 1. Creates default collections for new users
 * 2. Finds or creates current user collection with fallback strategies
 * 3. Manages wallet and role assignments to collections
 * 4. Maintains consistency between user collections and current state
 *
 * @privacy-considerations
 * - Associates only necessary identifiers (user_id, collection_id)
 * - Does not expose or log personal data beyond user ID
 * - Sanitizes user names when creating collection names
 *
 * @signature [CollectionService::v2.0] florence-egi-collection-manager
 */
class CollectionService
{
    /** @var UltraLogManager PSR-3 compatible logger for operation traceability */
    private UltraLogManager $logger;

    /** @var ErrorManagerInterface UEM interface for error handling */
    private ErrorManagerInterface $errorManager;

    /** @var WalletServiceInterface Service for managing user wallets */
    private WalletServiceInterface $walletService;

    /** @var UserRoleServiceInterface Service for managing user roles */
    private UserRoleServiceInterface $roleService;

    /**
     * @Oracode CollectionService Constructor
     * 🎯 Purpose: Initialize service with required dependencies
     * 📥 Input: Logger, error manager, wallet service, role service
     * ✅ Dependencies: ULM for logging, UEM for errors, wallet/role services
     *
     * @param UltraLogManager $logger Logger for operation traceability
     * @param ErrorManagerInterface $errorManager Error handler interface
     * @param WalletServiceInterface $walletService Service for wallet management
     * @param UserRoleServiceInterface $roleService Service for role assignment
     *
     * @oracode-di-pattern Dependency injection for testability
     * @oracode-ultra-integrated Using ULM and UEM interfaces directly
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        WalletServiceInterface $walletService,
        UserRoleServiceInterface $roleService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->walletService = $walletService;
        $this->roleService = $roleService;
    }

    /**
     * @Oracode Creates default collection for user
     * 🎯 Purpose: Generate new collection with default values for specified user
     * 📥 Input: User instance
     * 📤 Output: Newly created Collection instance or JsonResponse on error
     * 🛡️ GDPR: Uses minimal user data (ID, sanitized name)
     *
     * @param User $user The user for whom to create the collection
     *                   - Uses user ID as foreign key
     *                   - Extracts only first name for collection title
     *
     * @return Collection|JsonResponse The created collection or error response
     *
     * @throws Exception If errors occur during creation process
     *
     * @oracode-side-effects
     * - Creates new record in collections table
     * - Creates pivot relationships between user and collection
     * - Assigns default wallets to collection
     * - Assigns 'creator' role to user
     * - Logs creation operation with ULM
     *
     * @privacy-safe Only uses user ID and sanitized first name
     * @error-boundary Handles creation failures with UEM
     */
    public function createDefaultCollection(User $user): Collection|JsonResponse
    {
        // Extract only the user's first name for collection name (sanitization)
        $firstName = explode(' ', $user->name, 2)[0];
        $collectionName = "{$firstName}'s Collection";

        $this->logger->info('Starting default collection creation', [
            'user_id' => $user->id,
            'collection_name' => $collectionName
        ]);

        try {
            $collection = Collection::create([
                'user_id'         => $user->id,
                'owner_id'        => $user->id,
                'epp_id'          => config('app.epp_id'),
                'is_default'      => true,
                'collection_name' => $collectionName,
                'description'     => trans('collection.default_description'),
                'creator_id'      => $user->id,
                'type'            => 'standard',
                'position'        => 1,
                'EGI_number'      => 1,
                'floor_price'     => 0.0,
                'is_published'    => false,
            ]);

            $this->logger->info('Collection created successfully', [
                'collection_id' => $collection->id,
                'user_id' => $user->id
            ]);

            // Set up pivot relationship between user and collection
            $collection->users()->attach($user->id, ['role' => 'creator']);

            // Attach default wallets
            $this->attachDefaultWallets($collection, $user);

            // Assign creator role
            $this->roleService->assignCreatorRole($user->id);

            return $collection;

        } catch (Throwable $e) {
            $this->logger->error('Failed to create default collection', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('COLLECTION_CREATION_FAILED', [
                'user_id' => $user->id,
                'error_details' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * @Oracode Finds or creates user collection with fallback strategy
     * 🎯 Purpose: Ensure user has an active collection
     * 📥 Input: User instance and optional log context
     * 📤 Output: Found or created Collection instance or JsonResponse on error
     * 📡 Fallback: Current -> Default -> Create New
     *
     * @param User $user The user for whom to find or create collection
     * @param array $logContext Optional logging context
     *
     * @return Collection|JsonResponse The found/created collection or error response
     *
     * @throws Exception If unable to find or create collection
     *
     * @oracode-fallback-strategy
     * 1. Try to find current collection from session/user record
     * 2. If not found, try to find user's default collection
     * 3. If still not found, create new default collection
     *
     * @oracode-side-effects
     * - May update user's current_collection_id
     * - May create new collection if none exists
     * - Logs lookup and creation operations
     *
     * @error-boundary Handles all failures with UEM
     */
    public function findOrCreateUserCollection(User $user, array $logContext = []): Collection|JsonResponse
    {
        $this->logger->info('Finding user collection', array_merge($logContext, [
            'user_id' => $user->id
        ]));

        try {
            // 1. Try to find the current collection
            $currentCollection = $this->findCurrentCollection($user);
            if ($currentCollection) {
                $this->logger->info('Current collection found', [
                    'collection_id' => $currentCollection->id,
                    'user_id' => $user->id
                ]);
                return $currentCollection;
            }

            // 2. Try to find the user's default collection
            $defaultCollection = $this->findDefaultCollection($user);
            if ($defaultCollection) {
                // Set the default collection as current
                $user->current_collection_id = $defaultCollection->id;
                $user->save();

                $this->logger->info('Default collection set as current', [
                    'user_id' => $user->id,
                    'collection_id' => $defaultCollection->id
                ]);

                return $defaultCollection;
            }

            // 3. Create a new collection if needed
            $this->logger->info('No collection found for user. Creating new collection.', [
                'user_id' => $user->id
            ]);

            $newCollection = $this->createDefaultCollection($user);

            // Check if createDefaultCollection returned an error
            if ($newCollection instanceof JsonResponse) {
                return $newCollection;
            }

            // Set the new collection as current
            $user->current_collection_id = $newCollection->id;
            $user->save();

            $this->logger->info('New collection set as current', [
                'user_id' => $user->id,
                'collection_id' => $newCollection->id
            ]);

            return $newCollection;

        } catch (Throwable $e) {
            $this->logger->error('Error during collection finding/creation', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('COLLECTION_FIND_CREATE_FAILED', [
                'user_id' => $user->id,
                'error_details' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * @Oracode Finds user's current collection
     * 🎯 Purpose: Locate active collection from session or user record
     * 📥 Input: User instance
     * 📤 Output: Collection instance or null
     *
     * @param User $user The user
     *
     * @return Collection|null The current collection or null if not found
     *
     * @internal Checks both session and user record for collection ID
     * @internal Handles potential return of Collection instance or Eloquent Collection
     * @signature finds-current-collection
     */
    protected function findCurrentCollection(User $user): ?Collection
    {
        $currentCollectionId = session('current_collection_id') ?? $user->current_collection_id;

        if (!$currentCollectionId) {
            return null;
        }

        $collection = Collection::find($currentCollectionId);

        // Handle potential return of Eloquent Collection instead of model instance
        if ($collection instanceof \Illuminate\Database\Eloquent\Collection) {
            $collection = $collection->first();
        }

        return $collection;
    }

    /**
     * @Oracode Finds user's default collection
     * 🎯 Purpose: Query for collection marked as default for user
     * 📥 Input: User instance
     * 📤 Output: Collection instance or null
     *
     * @param User $user The user
     *
     * @return Collection|null The default collection or null if not found
     *
     * @internal Filters collections by user_id and is_default flag
     * @signature finds-default-collection
     */
    protected function findDefaultCollection(User $user): ?Collection
    {
        return Collection::where('user_id', $user->id)
            ->where('is_default', true)
            ->first();
    }

    /**
     * @Oracode Attaches default wallets to collection
     * 🎯 Purpose: Delegate wallet creation to wallet service
     * 📥 Input: Collection and User instances
     *
     * @param Collection $collection The collection
     * @param User $user The owner user
     *
     * @return void
     *
     * @internal Delegates to injected wallet service
     * @signature attach-default-wallets
     */
    protected function attachDefaultWallets(Collection $collection, User $user): void
    {
        $this->walletService->attachDefaultWalletsToCollection($collection, $user);
    }

}
