<?php

declare(strict_types=1);

namespace App\Http\Controllers\Notifications\Wallets;

use App\DataTransferObjects\Notifications\Wallets\WalletCreateRequest;
use App\DataTransferObjects\Notifications\Wallets\WalletUpdateRequest;
use App\Exceptions\WalletException;
use App\Http\Controllers\Controller;
use App\Services\Notifications\RequestWalletService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotificationWalletRequestController extends Controller
{
    public function __construct(
        private readonly RequestWalletService $requestWalletService
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

            Log::channel('florenceegi')->info('Wallet creation request:', [
                'request' => $validated
            ]);

            $walletRequest = WalletCreateRequest::fromRequest($validated, Auth::id());

            $this->requestWalletService->createWalletRequest($walletRequest);

            $data = [
                'collection_id' => $collectionId,
                'receiver_id' => $receiver_id,
                'proposer_id' => $proposer_id,
                'wallet' => $wallet,
                'royalty_mint' => $royaltyMint,
                'royalty_rebind' => $royaltyRebind,
            ];

            Log::channel('florenceegi')->info('Wallet creation request DONE:', [
                'data' => $data
            ]);

            return response()->json(['data' => $data], 200,);

        } catch (WalletException $e) {
            Log::channel('florenceegi')->error('Error creating wallet request:', [
                'error' => $e->getMessage(),
                // 'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'message' => $e->getMessage(),
                'success' => false
            ], 422);

        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Error creating wallet request:', [
                'error' => $e->getMessage(),
                // 'trace' => $e->getTraceAsString(),
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

            Log::channel('florenceegi')->info('Wallet update request:', [
                'request' => $validated
            ]);

            $walletRequest = WalletUpdateRequest::fromRequest($validated, Auth::id());

            $this->requestWalletService->updateWalletRequest($walletRequest);

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

            Log::channel('florenceegi')->info('Wallet update request DONE:', [
                'data' => $data
            ]);

            return response()->json(['data' => $data], 200,);

        } catch (WalletException $e) {
            Log::channel('florenceegi')->error('Error updating wallet request:', [
                'error' => $e->getMessage(),
                // 'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'message' => $e->getMessage(),
                'success' => false
            ], 422);

        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Error updating wallet request:', [
                'error' => $e->getMessage(),
                // 'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'message' => $e->getMessage(),
                'success' => false
            ], 422);
        }

    }
}
