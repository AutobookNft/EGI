<?php

declare(strict_types=1);

namespace App\Http\Controllers\Notifications\Wallets;

use App\DataTransferObjects\Notifications\Wallets\WalletCreateRequest;
use App\DataTransferObjects\Notifications\Wallets\WalletDonationRequest;
use App\DataTransferObjects\Notifications\Wallets\WalletUpdateRequest;
use App\Enums\GdprActivityCategory;
use App\Exceptions\WalletException;
use App\Http\Controllers\Controller;
use App\Rules\NoPendingWalletProposal;
use App\Services\GDPR\AuditLogService;
use App\Services\Notifications\RequestWalletService;
use App\Services\UltraErrorManager\Contracts\ErrorManagerInterface;
use App\Services\UltraLogManager\UltraLogManager;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class NotificationWalletRequestController extends Controller
{
    public function __construct(
        private readonly RequestWalletService $requestWalletService,
        private readonly AuditLogService $auditLogService,
        private readonly ErrorManagerInterface $errorManager,
        private readonly UltraLogManager $ultraLogManager
    ) {}

    public function requestCreateWallet(Request $request): JsonResponse
    {

        $receiver_id = $request->input('receiver_id');
        $proposer_id = Auth::id();
        $collectionId = (int) $request->input('collection_id');
        $wallet = $request->input('wallet');
        $royaltyMint = $request->input('royaltyMint');
        $royaltyRebind = $request->input('royaltyRebind');

        try {
            $validated = $request->validate([
                'collection_id' => 'required|string',
                'receiver_id' => 'required|integer',
                'wallet' => ['required', 'string'],
                'royaltyMint' => ['required', 'numeric', 'min:0', 'max:' . config('app.creator_royalty_mint')],
                'royaltyRebind' => ['required', 'numeric', 'min:0', 'max:' . config('app.creator_royalty_rebind')],
            ]);

            $this->ultraLogManager->info('Wallet creation request', [
                'user_id' => Auth::id(),
                'request' => $validated
            ]);

            $walletRequest = WalletCreateRequest::fromRequest($validated, Auth::id());

            $this->requestWalletService->createWalletRequest($walletRequest);

            // GDPR: Log della richiesta di creazione wallet
            $this->auditLogService->logUserAction(
                user: Auth::user(),
                action: 'wallet_create_request',
                context: [
                    'collection_id' => $collectionId,
                    'receiver_id' => $receiver_id,
                    'proposer_id' => Auth::id(),
                    'royalty_mint' => $royaltyMint,
                    'royalty_rebind' => $royaltyRebind,
                    'wallet_address_hash' => hash('sha256', $wallet), // Hash per privacy
                ],
                category: GdprActivityCategory::WALLET_MANAGEMENT
            );

            $data = [
                'collection_id' => $collectionId,
                'receiver_id' => $receiver_id,
                'proposer_id' => $proposer_id,
                'wallet' => $wallet,
                'royalty_mint' => $royaltyMint,
                'royalty_rebind' => $royaltyRebind,
            ];

            $this->ultraLogManager->info('Wallet creation request completed successfully', [
                'user_id' => Auth::id(),
                'collection_id' => $collectionId,
                'receiver_id' => $receiver_id,
                'action' => 'wallet_create_request',
                'data' => $data
            ]);

            return response()->json(['data' => $data], 200,);

        } catch (WalletException $e) {
            // GDPR: Log dell'errore wallet
            $this->auditLogService->logUserAction(
                user: Auth::user(),
                action: 'wallet_create_request_failed',
                context: [
                    'collection_id' => $collectionId ?? null,
                    'receiver_id' => $receiver_id ?? null,
                    'error_type' => 'WalletException',
                    'error_message' => $e->getMessage(),
                ],
                category: GdprActivityCategory::WALLET_MANAGEMENT
            );

            $this->errorManager->handle('WALLET_CREATE_REQUEST_VALIDATION_ERROR', [
                'user_id' => Auth::id(),
                'collection_id' => $collectionId ?? null,
                'receiver_id' => $receiver_id ?? null,
                'exception_class' => get_class($e),
                'error_message' => $e->getMessage()
            ], $e);

            return response()->json([
                'message' => $e->getMessage(),
                'success' => false
            ], 422);

        } catch (Exception $e) {
            // GDPR: Log dell'errore generico
            $this->auditLogService->logUserAction(
                user: Auth::user(),
                action: 'wallet_create_request_error',
                context: [
                    'collection_id' => $collectionId ?? null,
                    'receiver_id' => $receiver_id ?? null,
                    'error_type' => 'Exception',
                    'error_message' => $e->getMessage(),
                ],
                category: GdprActivityCategory::WALLET_MANAGEMENT
            );

            $this->errorManager->handle('WALLET_CREATE_REQUEST_SYSTEM_ERROR', [
                'user_id' => Auth::id(),
                'collection_id' => $collectionId ?? null,
                'receiver_id' => $receiver_id ?? null,
                'exception_class' => get_class($e),
                'error_message' => $e->getMessage()
            ], $e);

            $this->ultraLogManager->error('Error creating wallet request', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'message' => $e->getMessage(),
                'success' => false
            ], 422);

        }
    }

    public function requestUpdateWallet(Request $request): JsonResponse
    {
        $receiver_id = $request->input('receiver_id');
        $proposer_id = Auth::id();
        $collectionId = (int) $request->input('collection_id');
        $wallet = $request->input('wallet');
        $royaltyMint = $request->input('royaltyMint');
        $royaltyRebind = $request->input('royaltyRebind');
        $old_royalty_mint = $request->input('old_royalty_mint');
        $old_royalty_rebind = $request->input('old_royalty_rebind');

        try {

            $validated = $request->validate([
                'collection_id' => 'required|string',
                'receiver_id' => 'required|integer',
                'wallet' => ['required', 'string'],
                'royaltyMint' => ['required', 'numeric', 'min:0', 'max:' . config('app.creator_royalty_mint')],
                'royaltyRebind' => ['required', 'numeric', 'min:0', 'max:' . config('app.creator_royalty_rebind')],
                'old_royalty_mint' => 'numeric',
                'old_royalty_rebind' => 'numeric',
            ]);

            $this->ultraLogManager->info('Wallet update request', [
                'user_id' => Auth::id(),
                'request' => $validated
            ]);

            $walletRequest = WalletUpdateRequest::fromRequest($validated, Auth::id());

            $this->requestWalletService->updateWalletRequest($walletRequest);

            // GDPR: Log della richiesta di aggiornamento wallet
            $this->auditLogService->logUserAction(
                user: Auth::user(),
                action: 'wallet_update_request',
                context: [
                    'collection_id' => $collectionId,
                    'receiver_id' => $receiver_id,
                    'proposer_id' => Auth::id(),
                    'royalty_mint' => $royaltyMint,
                    'royalty_rebind' => $royaltyRebind,
                    'old_royalty_mint' => $old_royalty_mint,
                    'old_royalty_rebind' => $old_royalty_rebind,
                    'wallet_address_hash' => hash('sha256', $wallet), // Hash per privacy
                ],
                category: GdprActivityCategory::WALLET_MANAGEMENT
            );

            $data = [
                'collection_id' => $collectionId,
                'receiver_id' => $receiver_id,
                'proposer_id' => $proposer_id,
                'wallet' => $wallet,
                'royalty_mint' => $royaltyMint,
                'royalty_rebind' => $royaltyRebind,
                'old_royalty_mint' => $old_royalty_mint,
                'old_royalty_rebind' => $old_royalty_rebind,
            ];

            $this->ultraLogManager->info('Wallet update request completed successfully', [
                'user_id' => Auth::id(),
                'collection_id' => $collectionId,
                'receiver_id' => $receiver_id,
                'action' => 'wallet_update_request',
                'data' => $data
            ]);

            return response()->json(['data' => $data], 200,);

        } catch (WalletException $e) {
            // GDPR: Log dell'errore wallet update
            $this->auditLogService->logUserAction(
                user: Auth::user(),
                action: 'wallet_update_request_failed',
                context: [
                    'collection_id' => $collectionId ?? null,
                    'receiver_id' => $receiver_id ?? null,
                    'error_type' => 'WalletException',
                    'error_message' => $e->getMessage(),
                ],
                category: GdprActivityCategory::WALLET_MANAGEMENT
            );

            $this->errorManager->handle('WALLET_UPDATE_REQUEST_VALIDATION_ERROR', [
                'user_id' => Auth::id(),
                'collection_id' => $collectionId ?? null,
                'receiver_id' => $receiver_id ?? null,
                'exception_class' => get_class($e),
                'error_message' => $e->getMessage()
            ], $e);

            $this->ultraLogManager->error('Error updating wallet request', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'message' => $e->getMessage(),
                'success' => false
            ], 422);

        } catch (Exception $e) {
            // GDPR: Log dell'errore generico update
            $this->auditLogService->logUserAction(
                user: Auth::user(),
                action: 'wallet_update_request_error',
                context: [
                    'collection_id' => $collectionId ?? null,
                    'receiver_id' => $receiver_id ?? null,
                    'error_type' => 'Exception',
                    'error_message' => $e->getMessage(),
                ],
                category: GdprActivityCategory::WALLET_MANAGEMENT
            );

            $this->errorManager->handle('WALLET_UPDATE_REQUEST_SYSTEM_ERROR', [
                'user_id' => Auth::id(),
                'collection_id' => $collectionId ?? null,
                'receiver_id' => $receiver_id ?? null,
                'exception_class' => get_class($e),
                'error_message' => $e->getMessage()
            ], $e);

            $this->ultraLogManager->error('Error updating wallet request system', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'message' => $e->getMessage(),
                'success' => false
            ], 422);
        }
    }

    public function requestDonation(Request $request): JsonResponse
    {

        try {

            $validated = $request->validate([
                'royaltyMint' => ['required', 'numeric', 'min:0', 'max:' . config('app.creator_royalty_mint')],
                'royaltyRebind' => ['required', 'numeric', 'min:0', 'max:' . config('app.creator_royalty_rebind')],
            ]);

            $this->ultraLogManager->info('Wallet donation request', [
                'user_id' => Auth::id(),
                'request' => $validated
            ]);

            // Aggiungo l'id della collezione
            $validated['collection_id'] = $request->input('collection_id');

            $walletRequest = WalletDonationRequest::fromRequest($validated, Auth::id());

            $this->requestWalletService->donationWalletRequest($walletRequest);

            // GDPR: Log della richiesta di donazione wallet
            $this->auditLogService->logUserAction(
                user: Auth::user(),
                action: 'wallet_donation_request',
                context: [
                    'collection_id' => $validated['collection_id'] ?? null,
                    'proposer_id' => Auth::id(),
                    'royalty_mint' => $validated['royaltyMint'],
                    'royalty_rebind' => $validated['royaltyRebind'],
                ],
                category: GdprActivityCategory::WALLET_MANAGEMENT
            );

            $this->ultraLogManager->info('Wallet donation request completed successfully', [
                'user_id' => Auth::id(),
                'collection_id' => $validated['collection_id'] ?? null,
                'action' => 'wallet_donation_request',
                'data' => $walletRequest
            ]);

            return response()->json(['data' => $walletRequest], 200,);

        } catch (WalletException $e) {
            // GDPR: Log dell'errore donation
            $this->auditLogService->logUserAction(
                user: Auth::user(),
                action: 'wallet_donation_request_failed',
                context: [
                    'collection_id' => $request->input('collection_id') ?? null,
                    'error_type' => 'WalletException',
                    'error_message' => $e->getMessage(),
                ],
                category: GdprActivityCategory::WALLET_MANAGEMENT
            );

            $this->errorManager->handle('WALLET_DONATION_REQUEST_VALIDATION_ERROR', [
                'user_id' => Auth::id(),
                'collection_id' => $request->input('collection_id') ?? null,
                'exception_class' => get_class($e),
                'error_message' => $e->getMessage()
            ], $e);

            $this->ultraLogManager->error('Error donation wallet request', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'message' => $e->getMessage(),
                'success' => false
            ], 422);

        } catch (Exception $e) {
            // GDPR: Log dell'errore generico donation
            $this->auditLogService->logUserAction(
                user: Auth::user(),
                action: 'wallet_donation_request_error',
                context: [
                    'collection_id' => $request->input('collection_id') ?? null,
                    'error_type' => 'Exception',
                    'error_message' => $e->getMessage(),
                ],
                category: GdprActivityCategory::WALLET_MANAGEMENT
            );

            $this->errorManager->handle('WALLET_DONATION_REQUEST_SYSTEM_ERROR', [
                'user_id' => Auth::id(),
                'collection_id' => $request->input('collection_id') ?? null,
                'exception_class' => get_class($e),
                'error_message' => $e->getMessage()
            ], $e);

            $this->ultraLogManager->error('Error donation wallet request system', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'message' => $e->getMessage(),
                'success' => false
            ], 422);

        }
    }
}
