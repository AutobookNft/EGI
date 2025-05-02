<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Collection;
use App\Traits\HasUtilitys;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Ultra\EgiModule\Contracts\UserRoleServiceInterface;
use Ultra\EgiModule\Services\CollectionService;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;

/**
 * 📜 Oracode Action: CreateNewUser
 *
 * Handles new user registration for Fortify authentication.
 *
 * @package     App\Actions\Fortify
 * @author      Padmin D. Curtis (Updated for Fabio Cherici)
 * @copyright   2024 Fabio Cherici
 * @license     MIT
 * @version     2.0.0 // Refactored with UEM integration and service abstraction
 * @since       1.0.0
 *
 * @purpose     🎯 Creates new user accounts, handling registration flow including wallet initialization,
 *              default collection setup, and role assignment using dedicated services.
 *
 * @context     🧩 Used by Laravel Fortify during the registration process.
 *
 * @state       💾 Stateless. Does not maintain internal state between invocations.
 *
 * @feature     🗝️ User validation and creation
 * @feature     🗝️ Default collection creation via CollectionService
 * @feature     🗝️ Uses UltraErrorManager for standardized error handling
 * @feature     🗝️ Uses UltraLogManager for consistent auditing
 *
 * @signal      🚦 Returns created User model
 * @signal      🚦 Throws ValidationException on invalid input
 * @signal      🚦 Logs operations via ULM
 *
 * @privacy     🛡️ `@privacy-internal`: Handles sensitive user registration data
 * @privacy     🛡️ `@privacy-data`: Logs minimal user identifiers and sanitized details
 * @privacy     🛡️ `@privacy-purpose`: User creation and initial setup
 * @privacy     🛡️ `@privacy-consideration`: Password is properly hashed
 *
 * @dependency  🤝 Ultra\ErrorManager\Interfaces\ErrorManagerInterface
 * @dependency  🤝 Ultra\UltraLogManager\UltraLogManager
 * @dependency  🤝 Ultra\EgiModule\Contracts\UserRoleServiceInterface
 * @dependency  🤝 Ultra\EgiModule\Services\CollectionService
 * @dependency  🤝 Laravel Fortify (CreatesNewUsers)
 *
 * @testing     🧪 Unit Test: Mock dependencies and verify user creation flow
 * @testing     🧪 Integration Test: Verify complete creation process with database
 *
 * @rationale   💡 Refactored to utilize dedicated services for collection management and role assignment,
 *              improving separation of concerns and testability while standardizing error handling.
 *
 * @changelog   2.0.0 - 2025-04-29: Refactored to use:
 *                      - CollectionService for collection management (including default wallets)
 *                      - UserRoleService for role management
 *                      - UltraLogManager for standardized logging
 *                      - ErrorManagerInterface for centralized error handling
 *                      Removed the HasCreateDefaultCollectionWallets trait dependency.
 */
class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;
    use HasUtilitys;

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
     * 🧱 @dependency UserRoleService instance.
     * Used for role management operations.
     * @var UserRoleServiceInterface
     */
    protected readonly UserRoleServiceInterface $roleService;

    /**
     * 🧱 @dependency CollectionService instance.
     * Used for collection management and default wallet creation.
     * @var CollectionService
     */
    protected readonly CollectionService $collectionService;

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
     * @param UserRoleServiceInterface $roleService Service for role management
     * @param CollectionService $collectionService Service for collection and wallet management
     */
    public function __construct(
        ErrorManagerInterface $errorManager,
        UltraLogManager $logger,
        UserRoleServiceInterface $roleService,
        CollectionService $collectionService
    ) {
        $this->errorManager = $errorManager;
        $this->logger = $logger;
        $this->roleService = $roleService;
        $this->collectionService = $collectionService;
    }

    /**
     * 🚀 Creates a new registered user.
     *
     * --- Logic ---
     * 1. Validate user input data
     * 2. Generate wallet details (address, balance)
     * 3. Create the user record with transaction
     * 4. Set up initial collection using CollectionService
     * 5. Return the completed user instance
     * --- End Logic ---
     *
     * @param array<string, string> $input Registration form data
     * @return User The newly created user instance
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails
     *
     * @privacy-purpose User registration processing
     * @privacy-data Processes registration form data including password
     */
    public function create(array $input): User
    {
        $context = ['action' => 'user_registration'];
        $this->logger->info('Starting user registration process', $context);

        // Validation of the input data
        $this->validateInput($input);

        // Generate wallet details
        [$wallet_address, $wallet_balance] = $this->generateWalletDetails();

        // Create the user and related resources
        return $this->handleUserCreation($input, $wallet_address, $wallet_balance);
    }

    /**
     * 🔍 Validates the user input data.
     *
     * @param array<string, string> $input Registration form data
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException If validation fails
     *
     * @privacy-purpose Input validation for security
     * @privacy-data Processes registration form data
     */
    private function validateInput(array $input): void
    {
        $context = ['action' => 'validate_input'];

        try {
            Validator::make($input, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules(),
            ])->validate();

            $this->logger->info('Input validation successful', $context);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->logger->error('Input validation failed', array_merge($context, [
                'errors' => $e->errors()
            ]));

            // Handle validation error with UEM before re-throwing
            $this->errorManager->handle(
                'USER_VALIDATION_ERROR',
                array_merge($context, [
                    'error_fields' => array_keys($e->errors())
                ]),
                $e,
                false // Don't throw from UEM - we'll re-throw the original exception
            );

            throw $e;
        }
    }

    /**
     * 🪙 Generates wallet address and balance for the new user.
     *
     * @return array{0: string, 1: float} Wallet address and balance
     *
     * @privacy-purpose Generate wallet details for user
     * @privacy-data None - generates new data only
     */
    private function generateWalletDetails(): array
    {
        $wallet_address = $this->generateFakeAlgorandAddress();
        $wallet_balance = (float)config('app.virtual_wallet_balance');

        $this->logger->info('Generated wallet details', [
            'wallet_balance' => $wallet_balance
        ]);

        return [$wallet_address, $wallet_balance];
    }

    /**
     * 🏗️ Handles the user creation process within a transaction.
     *
     * @param array<string, string> $input Registration form data
     * @param string $wallet_address Generated wallet address
     * @param float $wallet_balance Initial wallet balance
     * @return User The newly created user
     *
     * @privacy-purpose Complete user creation process
     * @privacy-data Processes user creation data
     */
    private function handleUserCreation(array $input, string $wallet_address, float $wallet_balance): User
    {
        return DB::transaction(function () use ($input, $wallet_address, $wallet_balance) {
            // Create the user
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'wallet' => $wallet_address,
                'wallet_balance' => $wallet_balance,
                'language' => app()->getLocale(),
                'password' => Hash::make($input['password']),
            ]);

            $context = ['user_id' => $user->id];
            $this->logger->info('User record created successfully', $context);

            // Create default collection using the CollectionService
            $collection = $this->collectionService->createDefaultCollection($user);

            // Set current collection for the user
            $user->current_collection_id = $collection->id;
            $user->save();

            $this->logger->info('User registration complete', array_merge($context, [
                'current_collection_id' => $collection->id
            ]));

            return $user;
        });
    }
}
