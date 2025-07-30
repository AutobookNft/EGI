<?php

namespace Ultra\EgiModule\Services;

use App\Models\Wallet;
use App\Models\User;
use App\Models\Collection;
use Ultra\EgiModule\Contracts\WalletServiceInterface;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;
use Ultra\UltraLogManager\UltraLogManager;
use Illuminate\Http\RedirectResponse;
use Throwable;

/**
 * 📜 Oracode Service: WalletService
 * Service for managing wallet operations.
 *
 * @package     App\Services
 * @author      Padmin D. Curtis
 * @copyright   2024 Fabio Cherici
 * @license     MIT
 * @version     2.0.0 // Refactored with UEM integration
 * @since       1.0.0
 *
 * @purpose     🎯 Handles wallet creation, quota validation, and royalty management
 *              for collections. Ensures proper allocation of mint and rebind royalties
 *              while maintaining quota integrity.
 *
 * @context     🧩 Used by collection management workflows to setup and validate wallet
 *              royalty distributions. Operates with high privileges for system wallets.
 *
 * @state       💾 Stateless. Relies on the injected dependencies (ULM, UEM).
 *
 * @feature     🗝️ Creates and attaches wallets to collections (creator, EPP, and platform wallets)
 * @feature     🗝️ Validates royalty quotas against thresholds
 * @feature     🗝️ Manages default wallet assignments with proper royalty distributions
 * @feature     🗝️ Uses UltraErrorManager for standardized error handling
 * @feature     🗝️ Uses UltraLogManager for consistent auditing
 *
 * @signal      🚦 Returns created Wallet models or boolean validation results
 * @signal      🚦 Throws standardized errors handled by UEM
 * @signal      🚦 Logs operation details via ULM
 *
 * @privacy     🛡️ `@privacy-internal`: Handles wallet addresses which may be considered personal financial data
 * @privacy     🛡️ `@privacy-data`: Uses minimal wallet identifiers when logging
 * @privacy     🛡️ `@privacy-purpose`: Logs essential information for auditing
 * @privacy     🛡️ `@privacy-consideration`: Ensures system accounts (EPP, Natan) are properly separated from user accounts
 *
 * @dependency  🤝 Ultra\ErrorManager\Interfaces\ErrorManagerInterface
 * @dependency  🤝 Ultra\UltraLogManager\UltraLogManager
 * @dependency  🤝 App\Models\Wallet
 * @dependency  🤝 App\Models\User
 * @dependency  🤝 App\Models\Collection
 *
 * @testing     🧪 Unit Test: Mock dependencies and verify wallet creation
 * @testing     🧪 Unit Test: Verify quota validation logic
 * @testing     🧪 Integration Test: Verify complete wallet attachment flow
 * @testing     🧪 Error Test: Verify proper error handling with UEM
 *
 * @rationale   💡 Centralizes wallet logic while providing standardized error handling and logging.
 *                 Integration with UEM ensures consistent error responses across the application.
 *
 * @changelog   2.0.0 - 2025-04-29: Refactored to use UltraErrorManager and UltraLogManager.
 *                                   Removed direct PSR-3 logger dependency.
 *                                   Added structured error handling with UEM error codes.
 *                                   Updated documentation to Oracode v1.5 standard.
 */
class WalletService implements WalletServiceInterface {
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
        $this->defineWalletErrorCodes();
    }

    /**
     * 🚀 Attaches default wallets to a collection.
     *
     * Creates three default wallets for each new collection:
     * 1. Creator wallet - belongs to the collection owner
     * 2. EPP wallet - belongs to the EPP user (system account)
     * 3. Platform wallet - belongs to the Natan user (platform account)
     *
     * Each wallet is configured with specific royalty values from application config.
     *
     * --- Logic ---
     * 1. Retrieve system user IDs from configuration
     * 2. Create creator wallet with appropriate royalty values
     * 3. Create EPP wallet with appropriate royalty values
     * 4. Create Platform (Natan) wallet with appropriate royalty values
     * 5. Log successful operation or handle exceptions via UEM
     * --- End Logic ---
     *
     * @param Collection $collection The collection to attach wallets to
     * @param User $user The owner/creator of the collection
     *
     * @return void
     *
     * @throws Throwable When UEM handling results in thrown exception
     *
     * @sideEffect Creates three new wallet records in the database
     * @sideEffect Logs the wallet creation operations via ULM
     *
     * @privacy-purpose Wallet creation for royalty management
     * @privacy-data Uses user IDs and wallet addresses
     */
    public function attachDefaultWalletsToCollection(Collection $collection, User $user): void {
        // Create context for logging and error handling
        $context = [
            'collection_id' => $collection->id,
            'user_id' => $user->id,
            'collection_name' => $collection->name
        ];

        try {
            // Get system user IDs
            $eppId = config('app.epp_id', 2);
            $natanId = config('app.natan_id', 1);

            // 1. Create creator wallet
            $creatorWallet = $this->createWallet(
                $collection->id,
                $user->id,
                $user->wallet ?? null,
                config('app.creator_royalty_mint', 100),
                config('app.creator_royalty_rebind', 100),
                'Creator'
            );

            // 2. Create EPP wallet
            $eppWallet = $this->createWallet(
                $collection->id,
                $eppId,
                config('app.epp_wallet_address'),
                config('app.epp_royalty_mint', 0),
                config('app.epp_royalty_rebind', 0),
                'EPP'

            );

            // 3. Create Natan (platform) wallet
            $natanWallet = $this->createWallet(
                $collection->id,
                $natanId,
                config('app.natan_wallet_address'),
                config('app.natan_royalty_mint', 0),
                config('app.natan_royalty_rebind', 0),
                'Natan'

            );

            // Log success with wallet IDs
            $this->logger->info('All default wallets attached to collection', array_merge($context, [
                'creator_wallet_id' => $creatorWallet->id ?? 'failed',
                'epp_wallet_id' => $eppWallet->id ?? 'failed',
                'natan_wallet_id' => $natanWallet->id ?? 'failed'
            ]));
        } catch (Throwable $e) {
            // Log error with detailed context
            $this->logger->error('Failed to attach default wallets to collection', array_merge($context, [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e)
            ]));

            // Let UEM handle the error, potentially throwing
            $this->errorManager->handle(
                'WALLET_CREATION_FAILED',
                array_merge($context, [
                    'error_message' => $e->getMessage(),
                    'error_code' => $e->getCode()
                ]),
                $e,
                true // Always throw to maintain backward compatibility with existing code
            );
        }
    }

    /**
     * 🔧 Creates a single wallet with specified parameters.
     *
     * Helper method to create individual wallets with the specified
     * configuration values. Used by attachDefaultWalletsToCollection
     * to create the three required wallets.
     *
     * @param int $collectionId Collection ID to associate the wallet with
     * @param int $userId User ID to associate the wallet with
     * @param string|null $walletAddress Wallet address (blockchain address)
     * @param float $royaltyMint Mint royalty percentage value
     * @param float $royaltyRebind Rebind royalty percentage value
     * @param string $platform_role Role of the wallet (Creator, EPP, Natan)
     *
     * @return Wallet The created wallet
     * @return RedirectResponse If UEM handles the error and returns a response
     *
     * @throws Throwable If wallet creation fails and UEM throws
     *
     * @internal Used by attachDefaultWalletsToCollection to avoid code duplication
     *
     * @privacy-purpose Create individual wallet record with assigned royalties
     * @privacy-data Processes blockchain wallet address
     */
    public function createWallet(
        int $collectionId,
        int $userId,
        ?string $walletAddress,
        float $royaltyMint,
        float $royaltyRebind,
        string $platform_role

    ): Wallet|RedirectResponse {
        // Create context for logging and error handling
        $context = [
            'collection_id' => $collectionId,
            'user_id' => $userId,
            'royalty_mint' => $royaltyMint,
            'royalty_rebind' => $royaltyRebind,
            'platform_role' => $platform_role,
        ];

        try {
            // If no wallet address is provided, use a placeholder
            $address = $walletAddress ?? config('app.default_wallet_placeholder', 'pending_wallet_address');

            // Check if wallet already exists for this collection/user/address combination
            $existingWallet = Wallet::where('collection_id', $collectionId)
                ->where('user_id', $userId)
                ->where('wallet', $address)
                ->first();

            if ($existingWallet) {
                // Update existing wallet with new values
                $existingWallet->update([
                    'royalty_mint' => $royaltyMint,
                    'royalty_rebind' => $royaltyRebind,
                    'platform_role' => $platform_role,
                ]);

                // Log wallet update
                $this->logger->info('Existing wallet updated for collection', array_merge($context, [
                    'wallet_id' => $existingWallet->id,
                    'action' => 'updated_existing'
                ]));

                return $existingWallet;
            }

            // Check if this wallet address already exists in the system (unique constraint)
            $duplicateWallet = Wallet::where('wallet', $address)->first();

            if ($duplicateWallet) {
                // If the same wallet exists for a different user or collection, we need to handle this
                $this->logger->warning('Wallet address already exists in system', array_merge($context, [
                    'existing_wallet_id' => $duplicateWallet->id,
                    'existing_user_id' => $duplicateWallet->user_id,
                    'existing_collection_id' => $duplicateWallet->collection_id,
                    'requested_address' => $address
                ]));

                // Return the existing wallet if it's for the same user, otherwise throw an error
                if ($duplicateWallet->user_id === $userId) {
                    return $duplicateWallet;
                } else {
                    throw new \Exception("Wallet address {$address} is already associated with another user (ID: {$duplicateWallet->user_id})");
                }
            }

            // Create the wallet only if it doesn't exist
            $wallet = Wallet::create([
                'collection_id' => $collectionId,
                'user_id' => $userId,
                'wallet' => $address,
                'royalty_mint' => $royaltyMint,
                'royalty_rebind' => $royaltyRebind,
                'platform_role' => $platform_role,
            ]);

            // Log successful wallet creation
            $this->logger->info('New wallet created for collection', array_merge($context, [
                'wallet_id' => $wallet->id,
                'action' => 'created_new'
            ]));

            return $wallet;
        } catch (Throwable $e) {
            // Log error with detailed context
            $this->logger->error('Failed to create wallet', array_merge($context, [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e)
            ]));

            // Let UEM handle the error, potentially throwing
            return $this->errorManager->handle(
                'WALLET_CREATION_FAILED',
                array_merge($context, [
                    'error_message' => $e->getMessage(),
                    'wallet_address_provided' => !is_null($walletAddress)
                ]),
                $e,
                true // Always throw to maintain backward compatibility
            );
        }
    }

    /**
     * 🔍 Validates if a user has sufficient royalty quota.
     *
     * Checks if the user's wallet has enough mint and rebind royalties
     * to meet the specified requirements while maintaining minimum thresholds.
     *
     * --- Logic ---
     * 1. Retrieve the user's wallet for the specified collection
     * 2. Check if wallet exists
     * 3. Read minimum threshold values from configuration
     * 4. Verify both current quota and post-operation remaining quota
     * --- End Logic ---
     *
     * @param int $userId User ID to check quota for
     * @param int $collectionId Collection ID to find the wallet
     * @param float $requiredMintQuota Minimum mint quota required
     * @param float $requiredRebindQuota Minimum rebind quota required
     *
     * @return bool True if quota is sufficient, false otherwise
     *
     * @privacy-purpose Validate available royalty quota
     * @privacy-data Uses user ID and collection ID only
     */
    public function hasUserSufficientQuota(
        int $userId,
        int $collectionId,
        float $requiredMintQuota,
        float $requiredRebindQuota
    ): bool {
        // Create context for logging
        $context = [
            'user_id' => $userId,
            'collection_id' => $collectionId,
            'required_mint_quota' => $requiredMintQuota,
            'required_rebind_quota' => $requiredRebindQuota
        ];

        try {
            // Get user's wallet for this collection
            $wallet = Wallet::where('collection_id', $collectionId)
                ->where('user_id', $userId)
                ->first();

            if (!$wallet) {
                $this->logger->notice('Wallet not found during quota check', $context);
                return false;
            }

            // Read minimum thresholds
            $thresholdMint = config('app.creator_royalty_mint_threshold', 0);
            $thresholdRebind = config('app.creator_royalty_rebind_threshold', 0);

            // Enhanced context with current values
            $detailedContext = array_merge($context, [
                'wallet_id' => $wallet->id,
                'current_mint_quota' => $wallet->royalty_mint,
                'current_rebind_quota' => $wallet->royalty_rebind,
                'threshold_mint' => $thresholdMint,
                'threshold_rebind' => $thresholdRebind
            ]);

            // Check both available quota and resulting thresholds
            $hasQuota = (
                $wallet->royalty_mint >= $requiredMintQuota &&
                $wallet->royalty_rebind >= $requiredRebindQuota &&
                ($wallet->royalty_mint - $requiredMintQuota) >= $thresholdMint &&
                ($wallet->royalty_rebind - $requiredRebindQuota) >= $thresholdRebind
            );

            // Log the result with appropriate level
            if ($hasQuota) {
                $this->logger->debug('User has sufficient quota', $detailedContext);
            } else {
                $this->logger->notice('User has insufficient quota', $detailedContext);
            }

            return $hasQuota;
        } catch (Throwable $e) {
            // Log error with detailed context
            $this->logger->error('Error during quota validation', array_merge($context, [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e)
            ]));

            // Handle error non-blocking via UEM (no throw)
            $this->errorManager->handle(
                'WALLET_QUOTA_CHECK_ERROR',
                array_merge($context, [
                    'error_message' => $e->getMessage()
                ]),
                $e,
                false // Don't throw - default to false
            );

            return false;
        }
    }

    /**
     * 🧱 Define custom error codes specific to wallet operations.
     * Registers these codes with UEM for consistent error handling.
     *
     * @return void
     */
    protected function defineWalletErrorCodes(): void {
        // Define error for wallet creation failure
        $this->errorManager->defineError('WALLET_CREATION_FAILED', [
            'type' => 'critical',
            'blocking' => 'blocking',
            'dev_message' => 'Failed to create wallet: :error_message',
            'user_message' => 'Unable to setup the wallet for this collection. Please try again later.',
            'http_status_code' => 500,
            'msg_to' => 'sweet-alert',
            'devTeam_email_need' => true,
            'notify_slack' => true,
        ]);

        // Define error for quota check failures
        $this->errorManager->defineError('WALLET_QUOTA_CHECK_ERROR', [
            'type' => 'error',
            'blocking' => 'not', // Non-blocking, just log
            'dev_message' => 'Error checking wallet quota: :error_message',
            'user_message' => null, // No user-visible message needed
            'http_status_code' => 500,
            'msg_to' => 'log-only',
            'devTeam_email_need' => false,
            'notify_slack' => false,
        ]);

        // Define error for quota insufficiency
        $this->errorManager->defineError('WALLET_INSUFFICIENT_QUOTA', [
            'type' => 'warning',
            'blocking' => 'semi-blocking',
            'dev_message' => 'Wallet has insufficient quota for requested operation',
            'user_message' => 'You do not have sufficient royalty quota for this operation.',
            'http_status_code' => 400,
            'msg_to' => 'div',
            'devTeam_email_need' => false,
            'notify_slack' => false,
        ]);
    }
}
