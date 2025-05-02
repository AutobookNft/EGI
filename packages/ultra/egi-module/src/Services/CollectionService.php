<?php

namespace Ultra\EgiModule\Services;

use App\Models\User;
use App\Models\Collection;
use Ultra\EgiModule\Contracts\UserRoleServiceInterface;
use Ultra\EgiModule\Contracts\WalletServiceInterface;

use Psr\Log\LoggerInterface;
use Exception;
use Throwable;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * Service for managing user collections.
 *
 * Handles the creation, lookup, and assignment of collections to users,
 * maintaining data consistency and operation traceability.
 * Implements logic for default collections and user experience continuity.
 *
 * --- Core Logic ---
 * 1. Creates default collections for new users
 * 2. Finds or creates the current user collection with fallback strategies
 * 3. Manages wallet and role assignments to collections
 * 4. Maintains consistency between user collections and current user state
 * --- End Core Logic ---
 *
 * --- GDPR Considerations ---
 * - Manages the connection between users (data subjects) and their collections
 * - Does not directly expose personal data but serves as an intermediary
 * - Associates only strictly necessary identifiers
 * --- End GDPR Considerations ---
 *
 * @package App\Services
 * @author Padmin D. Curtis
 * @since 1.0.0
 */
class CollectionService
{
    /** @var UltraLogManager PSR-3 compatible logger for operation traceability */
    private UltraLogManager $logger;

    /** @var WalletServiceInterface Service for managing user wallets */
    private WalletServiceInterface $walletService;

    /** @var UserRoleServiceInterface Service for managing user roles */
    private UserRoleServiceInterface $roleService;

    /** @var string Name of the log channel used by the service */
    private string $logChannel;

    /**
     * Collection management service constructor.
     *
     * Injects the dependencies necessary for the service to function correctly,
     * ensuring inversion of control and testability.
     *
     * @param UltraLogManager $logger Logger for operation traceability
     * @param WalletServiceInterface $walletService Service for wallet management
     * @param UserRoleServiceInterface $roleService Service for role assignment
     * @param string $logChannel Name of the log channel to use (default: 'florenceegi')
     */
    public function __construct(
        UltraLogManager $logger,
        WalletServiceInterface $walletService,
        UserRoleServiceInterface $roleService,
        string $logChannel = 'florenceegi'
    ) {
        $this->logger = $logger;
        $this->walletService = $walletService;
        $this->roleService = $roleService;
        $this->logChannel = $logChannel;
    }

    /**
     * Creates a default collection for the user.
     *
     * Generates a new collection with default values associated with the specified user,
     * setting up necessary relationships and assigning appropriate roles.
     * The collection name is automatically derived from the user's name.
     *
     * @param User $user The user for whom to create the collection
     *                   - `@internal @gdpr-input`: Uses user ID as foreign key
     *                   - `@internal @sanitizer`: Only first name is extracted for collection title
     *
     * @return Collection The created collection configured with relationships and wallets
     *
     * @throws Exception If errors occur during the creation process
     *
     * @sideEffect Creates a new record in the collections table
     * @sideEffect Creates pivot relationships between user and collection
     * @sideEffect Assigns default wallets to the collection
     * @sideEffect Assigns 'creator' role to the user
     * @sideEffect Logs the creation operation
     */
    public function createDefaultCollection(User $user): Collection
    {
        // Extract only the user's first name for the collection name (sanitization)
        $firstName = explode(' ', $user->name, 2)[0];
        $collectionName = "{$firstName}'s Collection";

        try {
            $collection = Collection::create([
                'user_id'         => $user->id,
                'owner_id'        => $user->id,
                'epp_id'          => config('app.epp_id'),
                'is_default'      => true,
                'collection_name' => $collectionName,
                'description'     => __('collection.default_description'),
                'creator_id'      => $user->id,
                'type'            => 'standard',
                'position'        => 1,
                'EGI_number'      => 1,
                'floor_price'     => 0.0,
                'is_published'    => false,
            ]);

            // Log successful creation
            $this->logger->info('Collection created successfully', [
                'collection_id' => $collection->id,
                'user_id' => $user->id,
                'channel' => $this->logChannel
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
                'error' => $e->getMessage(),
                'channel' => $this->logChannel
            ]);
            throw new Exception("Unable to create default collection: " . $e->getMessage(), 500, $e);
        }
    }

    /**
     * Finds the current collection of the user, uses the default as fallback,
     * or creates a new one if needed.
     *
     * Implements a multi-stage lookup strategy with progressive fallbacks:
     * 1. Try to find the current collection from session or user record
     * 2. If not found, try to find the user's default collection
     * 3. If still not found, create a new default collection
     *
     * @param User $user The user for whom to find or create the collection
     * @param array &$logContext Logging context, passed by reference
     *
     * @return Collection The found or created collection
     *
     * @throws Exception If errors occur during the process
     *
     * @sideEffect May update user's current_collection_id
     * @sideEffect May create a new collection if none exists
     * @sideEffect Logs the lookup and creation operations
     */
    public function findOrCreateUserCollection(User $user, array &$logContext = []): Collection
    {
        // 1. Try to find the current collection
        $currentCollection = $this->findCurrentCollection($user);
        if ($currentCollection) {
            return $currentCollection;
        }

        // 2. Try to find the user's default collection
        $defaultCollection = $this->findDefaultCollection($user);
        if ($defaultCollection) {
            // Set the default collection as current
            $user->current_collection_id = $defaultCollection->id;
            $user->save();

            $this->logger->info('Default collection set as current', array_merge($logContext, [
                'user_id' => $user->id,
                'collection_id' => $defaultCollection->id,
                'channel' => $this->logChannel
            ]));

            return $defaultCollection;
        }

        // 3. Create a new collection if needed
        $this->logger->info('No collection found for user. Creating new collection.', array_merge($logContext, [
            'user_id' => $user->id,
            'channel' => $this->logChannel
        ]));

        try {
            $newCollection = $this->createDefaultCollection($user);

            // Set the new collection as current
            $user->current_collection_id = $newCollection->id;
            $user->save();

            $this->logger->info('New collection set as current', array_merge($logContext, [
                'user_id' => $user->id,
                'collection_id' => $newCollection->id,
                'channel' => $this->logChannel
            ]));

            return $newCollection;
        } catch (Throwable $e) {
            $this->logger->error('Error during collection creation', array_merge($logContext, [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'channel' => $this->logChannel
            ]));
            throw new Exception("Unable to create collection: " . $e->getMessage(), 500, $e);
        }
    }

    /**
     * Finds the user's current collection.
     *
     * Attempts to locate the current collection by checking both the session
     * and the user record. Handles potential collection type inconsistencies.
     *
     * @param User $user The user
     *
     * @return Collection|null The current collection or null if not found
     *
     * @internal Checks both session and user record for collection ID
     * @internal Handles potential return of Collection instance or Eloquent Collection
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
     * Finds the user's default collection.
     *
     * Queries for a collection that is marked as default for the specified user.
     *
     * @param User $user The user
     *
     * @return Collection|null The default collection or null if not found
     *
     * @internal Filters collections by user_id and is_default flag
     */
    protected function findDefaultCollection(User $user): ?Collection
    {
        return Collection::where('user_id', $user->id)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Attaches default wallets to the collection.
     *
     * Delegates to the wallet service to create or connect default wallets
     * with the newly created collection.
     *
     * @param Collection $collection The collection
     * @param User $user The owner user
     *
     * @return void
     *
     * @internal Delegates to injected wallet service
     */
    protected function attachDefaultWallets(Collection $collection, User $user): void
    {
        $this->walletService->attachDefaultWalletsToCollection($collection, $user);
    }

 }
