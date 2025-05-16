<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CollectionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Ultra\UltraLogManager\UltraLogManager;
use Ultra\ErrorManager\Interfaces\ErrorManagerInterface;

/**
 * @Oracode Controller for wallet connection with Secret Link system
 * 🎯 Purpose: Manages weak authentication through wallet address and secret key
 * 🧱 Core Logic: Handles wallet connection, secret validation, and user creation
 * 🛡️ GDPR: Minimal data collection, secure secret handling
 *
 * @package App\Http\Controllers
 * @author Padmin D. Curtis
 * @version 3.0.0
 * @date 2025-05-13
 *
 * @core-features
 * 1. Wallet address validation (Algorand format)
 * 2. Secret key generation and validation
 * 3. Weak authentication session management
 * 4. New user creation with default collection
 *
 * @security-model
 * - Two-factor weak auth: wallet address + secret key
 * - Secrets hashed with bcrypt
 * - Session-based authentication state
 *
 * @signature [WalletConnectController::v3.0] florence-egi-weak-auth
 */
class WalletConnectController extends Controller
{
    /** @var UltraLogManager ULM instance for structured logging */
    private UltraLogManager $logger;

    /** @var ErrorManagerInterface UEM interface for error handling */
    private ErrorManagerInterface $errorManager;

    /** @var CollectionService Service for collection management */
    private CollectionService $collectionService;

    /**
     * @Oracode Constructor with dependency injection
     * 🎯 Purpose: Initialize controller with required services
     * 📥 Input: Logger, error manager, collection service
     *
     * @param UltraLogManager $logger Structured logger instance
     * @param ErrorManagerInterface $errorManager Error handler interface
     * @param CollectionService $collectionService Collection management service
     *
     * @oracode-di-pattern Full dependency injection for testability
     * @oracode-ultra-integrated ULM and UEM properly injected
     */
    public function __construct(
        UltraLogManager $logger,
        ErrorManagerInterface $errorManager,
        CollectionService $collectionService
    ) {
        $this->logger = $logger;
        $this->errorManager = $errorManager;
        $this->collectionService = $collectionService;
    }

    /**
     * @Oracode Handle wallet connection request
     * 🎯 Purpose: Process wallet connection with optional secret validation
     * 📥 Input: Request with wallet_address and optional secret
     * 📤 Output: JsonResponse with connection status or error
     * 📡 API: POST /wallet/connect
     *
     * @param Request $request HTTP request containing:
     *                        - wallet_address: 58-char Algorand address
     *                        - secret: Optional secret key for existing users
     *
     * @return JsonResponse Success with user data or error response
     *
     * @oracode-flow
     * 1. Validate input (Algorand address format)
     * 2. Check if user exists
     * 3. If exists: validate secret if required
     * 4. If new: create user with generated secret
     * 5. Establish connected session
     *
     * @error-boundary Handles all failures with UEM
     * @privacy-safe Logs only non-sensitive data
     * @seo-purpose Enables weak authentication for Web3 experience
     */
    public function connect(Request $request): JsonResponse
    {
        $this->logger->info('Wallet Connect attempt initiated', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        try {
            // 1. Input validation with Algorand format
            $validated = $request->validate([
                'wallet_address' => ['required', 'string', 'size:58', 'regex:/^[A-Z2-7]{58}$/'],
                'secret' => ['nullable', 'string', 'min:10', 'max:50']
            ]);

            $walletAddress = $validated['wallet_address'];
            $providedSecret = $validated['secret'] ?? null;

            $this->logger->info('Wallet address received for connection', [
                'address_prefix' => substr($walletAddress, 0, 6) . '...',
                'has_secret' => !is_null($providedSecret)
            ]);

            // 2. Find existing user
            $user = User::where('wallet', $walletAddress)->first();

            if ($user) {
                return $this->handleExistingUser($request, $user, $walletAddress, $providedSecret);
            } else {
                return $this->handleNewUser($request, $walletAddress);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->logger->warning('Wallet Connect validation failed', [
                'errors' => $e->errors(),
                'ip' => $request->ip()
            ]);

            return $this->errorManager->handle('WALLET_VALIDATION_FAILED', [
                'errors' => $e->errors()
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Unexpected error during wallet connection', [
                'message' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 500)
            ]);

            return $this->errorManager->handle('WALLET_CONNECTION_FAILED', [
                'message' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * @Oracode Handle existing user connection
     * 🎯 Purpose: Validate secret for returning users
     * 📥 Input: Request, User, wallet address, provided secret
     * 📤 Output: JsonResponse with connection result
     *
     * @param Request $request Current HTTP request
     * @param User $user Existing user model
     * @param string $walletAddress User's wallet address
     * @param string|null $providedSecret Secret provided by user
     *
     * @return JsonResponse Connection success or secret required
     *
     * @internal Validates secret if user has one stored
     * @error-boundary Returns appropriate UEM error on invalid secret
     */
    protected function handleExistingUser(
        Request $request,
        User $user,
        string $walletAddress,
        ?string $providedSecret
    ): JsonResponse {
        $this->logger->info('Existing user found for wallet address', [
            'user_id' => $user->id,
            'has_personal_secret' => !is_null($user->personal_secret)
        ]);

        // Check if secret is required
        if ($user->personal_secret) {
            if (!$providedSecret) {
                $this->logger->info('Secret required but not provided', [
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'requires_secret' => true,
                    'message' => trans('collection.wallet_secret_required')
                ], 200);
            }

            // Validate secret
            if (!Hash::check($providedSecret, $user->personal_secret)) {
                $this->logger->warning('Invalid secret provided', [
                    'user_id' => $user->id,
                    'wallet_prefix' => substr($walletAddress, 0, 6) . '...'
                ]);

                return $this->errorManager->handle('WALLET_INVALID_SECRET', [
                    'wallet' => substr($walletAddress, 0, 6) . '...',
                    'ip' => $request->ip()
                ]);
            }
        }

        // Establish session
        $this->establishConnectedSession($request, $user, $walletAddress);

        return response()->json([
            'success' => true,
            'message' => trans('collection.wallet_existing_connection'),
            'wallet_address' => $walletAddress,
            'user_status' => $this->getUserStatus($user),
            'user_name' => $user->name
        ]);
    }

    /**
     * @Oracode Handle new user creation
     * 🎯 Purpose: Create new weak auth user with secret
     * 📥 Input: Request and wallet address
     * 📤 Output: JsonResponse with new user data and secret
     *
     * @param Request $request Current HTTP request
     * @param string $walletAddress New user's wallet address
     *
     * @return JsonResponse New user creation response with secret
     *
     * @oracode-side-effects
     * - Creates new user record
     * - Generates unique secret
     * - Creates default collection via service
     * - Assigns guest role
     *
     * @security Secret shown only once on creation
     * @privacy-safe Generates anonymous user data
     */
    protected function handleNewUser(Request $request, string $walletAddress): JsonResponse
    {
        $this->logger->info('No user found for wallet address. Creating wallet-only user with secret.');

        // Generate unique secret
        $personalSecret = 'FEGI-' . date('Y') . '-' . strtoupper(Str::random(15));

        // Create anonymous email
        $uniqueEmail = 'weak_' . Str::random(10) . '@florenceegi.local';

        $newUser = User::create([
            'name' => 'User-' . substr($walletAddress, 0, 6),
            'email' => $uniqueEmail,
            'password' => Hash::make(Str::random(60)),
            'wallet' => $walletAddress,
            'personal_secret' => Hash::make($personalSecret),
            'is_weak_auth' => true,
            'email_verified_at' => null,
        ]);

        // Assign guest role
        $guestRole = \Spatie\Permission\Models\Role::where('name', 'guest')->first();
        if ($guestRole) {
            $newUser->assignRole($guestRole);
        }

        $this->logger->info('Wallet-only user created with secret', [
            'user_id' => $newUser->id
        ]);

        // Create default collection via service
        $collection = $this->collectionService->createDefaultCollection($newUser);

        // Check if collection creation returned error
        if ($collection instanceof JsonResponse) {
            return $collection;
        }

        // Establish session
        $this->establishConnectedSession($request, $newUser, $walletAddress);

        return response()->json([
            'success' => true,
            'message' => trans('collection.wallet_new_connection'),
            'wallet_address' => $walletAddress,
            'user_status' => 'new_weak_auth',
            'user_name' => $newUser->name,
            'secret' => $personalSecret, // IMPORTANT: Show only on first creation
            'show_secret_warning' => true
        ]);
    }

    /**
     * @Oracode Establish connected session
     * 🎯 Purpose: Create session state for connected user
     * 📥 Input: Request, User, wallet address
     *
     * @param Request $request Current HTTP request
     * @param User $user User model to connect
     * @param string $walletAddress User's wallet address
     *
     * @return void
     *
     * @oracode-side-effects Sets session variables
     * @internal Sets auth_status, wallet, user_id, weak_auth flag
     */
    protected function establishConnectedSession(Request $request, User $user, string $walletAddress): void
    {
        $request->session()->put([
            'auth_status' => 'connected',
            'connected_wallet' => $walletAddress,
            'connected_user_id' => $user->id,
            'is_weak_auth' => $user->is_weak_auth ?? true
        ]);

        // Forza il salvataggio della sessione
        $request->session()->save();

        $this->logger->info('Connected session established', [
            'user_id' => $user->id,
            'wallet_prefix' => substr($walletAddress, 0, 6) . '...',
            'session_id' => $request->session()->getId(),
            'is_weak_auth' => $user->is_weak_auth ?? true
        ]);
    }

    /**
     * @Oracode Get user authentication status
     * 🎯 Purpose: Determine user's auth level
     * 📥 Input: User model
     * 📤 Output: Status string
     *
     * @param User $user User model
     * @return string Authentication status
     *
     * @internal Returns: weak_auth|verified|registered
     */
    protected function getUserStatus(User $user): string
    {
        if ($user->is_weak_auth) {
            return 'weak_auth';
        }
        return $user->email_verified_at ? 'verified' : 'registered';
    }

    /**
     * @Oracode Disconnect wallet
     * 🎯 Purpose: Clear weak auth session
     * 📥 Input: HTTP request
     * 📤 Output: JsonResponse with disconnect status
     * 📡 API: POST /wallet/disconnect
     *
     * @param Request $request Current HTTP request
     * @return JsonResponse Disconnect success or error
     *
     * @oracode-side-effects Clears session data
     * @error-boundary Handles failures with UEM
     */
    public function disconnect(Request $request): JsonResponse
    {
        $this->logger->info('Wallet disconnect requested', [
            'session_id' => $request->session()->getId()
        ]);

        try {
            // Clear session
            $request->session()->forget([
                'connected_wallet',
                'auth_status',
                'connected_user_id',
                'is_weak_auth'
            ]);

            return response()->json([
                'success' => true,
                'message' => trans('collection.wallet_disconnected_successfully')
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Error during wallet disconnect', [
                'error' => $e->getMessage()
            ]);

            return $this->errorManager->handle('WALLET_DISCONNECT_FAILED', [
                'error' => $e->getMessage()
            ], $e);
        }
    }

    /**
     * @Oracode Get wallet connection status
     * 🎯 Purpose: Check current authentication state
     * 📥 Input: HTTP request
     * 📤 Output: JsonResponse with auth status
     * 📡 API: GET /wallet/status
     *
     * @param Request $request Current HTTP request
     * @return JsonResponse Authentication status data
     *
     * @oracode-response
     * - success: boolean
     * - connected_wallet: string|null
     * - is_authenticated: boolean
     * - is_weak_auth: boolean
     */
    public function status(Request $request): JsonResponse
    {
        if (auth()->check()) {
            return response()->json([
                'success' => true,
                'connected_wallet' => auth()->user()->wallet,
                'is_authenticated' => true,
                'is_weak_auth' => false
            ]);
        }

        if ($request->session()->has('connected_wallet')) {
            return response()->json([
                'success' => true,
                'connected_wallet' => $request->session()->get('connected_wallet'),
                'is_authenticated' => false,
                'is_weak_auth' => true
            ]);
        }

        return response()->json([
            'success' => false,
            'connected_wallet' => null,
            'is_authenticated' => false,
            'is_weak_auth' => false
        ]);
    }
}
