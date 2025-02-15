<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\DataTransferObjects\Notifications\Wallets\WalletCreateRequest;
use App\DataTransferObjects\Notifications\Wallets\WalletNotificationData;
use App\DataTransferObjects\Notifications\Wallets\WalletUpdateRequest;
use App\DataTransferObjects\Payloads\Wallets\WalletAcceptRequest;
use App\DataTransferObjects\Payloads\Wallets\WalletError;
use App\DataTransferObjects\Payloads\Wallets\WalletQuotaValidation;
use App\DataTransferObjects\Payloads\Wallets\WalletRejectRequest;
use App\Enums\NotificationHandlerType;
use App\Enums\NotificationStatus;
use App\Enums\PlatformRole;
use App\Exceptions\WalletException;
use App\Models\CustomDatabaseNotification;
use App\Models\NotificationPayloadWallet;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service per la gestione delle operazioni sui wallet
 */
class RequestWalletService
{
    public function __construct(
        private readonly NotificationHandlerFactory $notificationFactory
    ) {}

    // App/Services/Notifications/WalletService.php

    public function createWalletRequest(WalletCreateRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::transaction(function () use ($request) {

                $data = [
                    'collection_id' => (int) $request->collection_id,
                    'receiver_id' => (int) $request->receiver_id,
                    'proposer_id' => (int) $request->proposer_id,
                    'wallet' => $request->wallet,
                    'royalty_mint' => $request->royalty_mint,
                    'royalty_rebind' => $request->royalty_rebind,
                    'status' => NotificationStatus::PENDING_CREATE->value,
                    'type' => NotificationStatus::CREATION->value
                ];

                // Creare il payload della notifica wallet
                $walletPayload = NotificationPayloadWallet::create($data);

                // Aggiungo il messaggio dopo la creazione del payload, in quanto nel model non c'è il campo message
                $walletPayload['message'] = __('collection.wallet.wallet_creation_request');

                Log::channel('florenceegi')->info('Wallet payload created', [
                    'wallet_payload' => $walletPayload,
                ]);

                // Validare le quote richieste
                $this->validateAndAdjustCreatorQuota(
                    WalletQuotaValidation::fromPayload($walletPayload)
                );

                $this->requestNotification($walletPayload, $request);

            });
            return response()->json([
                'success' => true,
                'message' => __('collection.wallet.wallet_creation_request_success')
            ]);

        } catch (WalletException $e) {
            Log::channel('florenceegi')->error('Violazione della soglia minima durante creazione wallet:', [
                'error' => $e->getMessage(),

            ]);
            throw $e;

        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Error creating wallet request:', [
                'message' => $e->getMessage(),
                // 'trace'   => $e->getTraceAsString(),
                'request' => $request,
            ]);
            throw $e;

        } catch (\Throwable $e) {
            Log::channel('florenceegi')->error('Error creating wallet request caught in Throwable:', [
                'error' => $e->getMessage(),
                'exception_type' => get_class($e),

            ]);

            throw $e;
        }
    }

    /**
     * Gestisce l'accettazione di un wallet
     */
    public function updateWalletRequest(WalletUpdateRequest $request): void
    {

        Log::channel('florenceegi')->info('WalletService:Accepting wallet', [
            'request' => $request,
        ]);

        try {
            DB::transaction(function () use ($request) {

                $data = [
                    'collection_id' => (int) $request->collection_id,
                    'receiver_id' => (int) $request->receiver_id,
                    'proposer_id' => (int) $request->proposer_id,
                    'wallet' => $request->wallet,
                    'royalty_mint' => $request->royalty_mint,
                    'royalty_rebind' => $request->royalty_rebind,
                    'status' => NotificationStatus::PENDING_UPDATE->value,
                    'type' => NotificationStatus::UPDATE->value
                ];

                // Recupero gli oggetti
                $walletPayload = NotificationPayloadWallet::create($data);
                $walletPayload['message'] = __('collection.wallet.wallet_update_request');

                // ottengo il wallet
                $existingWallet = Wallet::where('collection_id', $request->collection_id)
                    ->where('user_id', $request->receiver_id)
                    ->where('wallet', $request->wallet)
                    ->first();

                // verifico che il wallet esista
                if (! $existingWallet) {
                    throw new Exception(__('collection.wallet.wallet_not_found'));
                }

                Log::channel('florenceegi')->info('Wallet payload update', [
                    'wallet_payload' => $walletPayload,
                ]);

                // Validare le quote richieste
                $this->validateAndAdjustCreatorQuotaOnUpdate(
                    WalletQuotaValidation::fromPayload($walletPayload), $existingWallet
                );

                $this->requestNotification($walletPayload, $request);

            });

        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Errore durante l\'accettazione del wallet:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request,
            ]);
            throw $e;
        }
    }


    /**
     * Valida e aggiorna le quote del creator
     *
     * @throws Exception Se il wallet non esiste o non ci sono quote sufficienti
     */
    private function validateAndAdjustCreatorQuota(WalletQuotaValidation $validation):void
    {
        Log::channel('florenceegi')->info('Validazione quote wallet', [
            'validation' => $validation,
        ]);

        // Recupera il wallet del creator
        $creatorWallet = Wallet::where('collection_id', $validation->collection_id)
            ->where('user_id', $validation->proposer_id)
            ->first();

        if (! $creatorWallet) {
            throw new Exception(__('collection.wallet.creator_wallet_not_found'));
        }

        // Leggo le quote minime per il creator
        $thresholdMint = config('app.creator_royalty_mint_threshold');
        $thresholdRebind = config('app.creator_royalty_rebind_threshold');

        Log::channel('florenceegi')->info('Quote wallet creator', [
            'threshold_mint' => $thresholdMint,
            'threshold_rebind' => $thresholdRebind,
        ]);

        if (
            $creatorWallet->royalty_mint < $validation->required_mint_quota ||
            $creatorWallet->royalty_rebind < $validation->required_rebind_quota ||
            ($creatorWallet->royalty_mint - $validation->required_mint_quota) < $thresholdMint ||
            ($creatorWallet->royalty_rebind - $validation->required_rebind_quota) < $thresholdRebind
        ) {
            $walletError = WalletError::quotaInsufficient(
                available: min($creatorWallet->royalty_mint, $creatorWallet->royalty_rebind),
                required: max($validation->required_mint_quota, $validation->required_rebind_quota)
            );
            Log::channel('florenceegi')->error('Il creatore non ha abbastanza quota da assegnare', [
                'error' => $walletError,
            ]);
            throw new WalletException($walletError);
        }

        Log::channel('florenceegi')->info('Quota sufficiente per creazione wallet.');
    }

    private function validateAndAdjustCreatorQuotaOnUpdate(WalletQuotaValidation $validation, Wallet $existingWallet): void
    {
        Log::channel('florenceegi')->info('Validazione modifica quote wallet', [
            'validation' => $validation,
            'existing_wallet' => $existingWallet,
        ]);

        // Recupera il wallet del creator
        $creatorWallet = Wallet::where('collection_id', $validation->collection_id)
            ->where('user_id', $validation->proposer_id)
            ->first();

        if (! $creatorWallet) {
            throw new Exception(__('collection.wallet.creator_wallet_not_found'));
        }

        // Leggo le quote minime per il creator
        $thresholdMint   = config('app.creator_royalty_mint_threshold');
        $thresholdRebind = config('app.creator_royalty_rebind_threshold');

        Log::channel('florenceegi')->info('Quote wallet creator', [
            'threshold_mint'   => $thresholdMint,
            'threshold_rebind' => $thresholdRebind,
        ]);

        // Calcola la differenza tra le nuove quote richieste e quelle già assegnate
        $additionalMintRequired   = max(0, $validation->required_mint_quota - $existingWallet->royalty_mint);
        $additionalRebindRequired = max(0, $validation->required_rebind_quota - $existingWallet->royalty_rebind);

        // Se le nuove quote sono inferiori o uguali, non occorre alcun controllo
        if ($additionalMintRequired === 0 && $additionalRebindRequired === 0) {
            Log::channel('florenceegi')->info('Nessuna quota aggiuntiva richiesta, nessun controllo necessario.');
            return;
        }

        // Verifica se il wallet del creator ha le quote sufficienti per coprire la differenza
        if (
            ($additionalMintRequired > 0 &&
                ($creatorWallet->royalty_mint < $additionalMintRequired ||
                ($creatorWallet->royalty_mint - $additionalMintRequired) < $thresholdMint))
            ||
            ($additionalRebindRequired > 0 &&
                ($creatorWallet->royalty_rebind < $additionalRebindRequired ||
                ($creatorWallet->royalty_rebind - $additionalRebindRequired) < $thresholdRebind))
        ) {
            $walletError = WalletError::quotaInsufficient(
                available: min($creatorWallet->royalty_mint, $creatorWallet->royalty_rebind),
                required: max($validation->required_mint_quota, $validation->required_rebind_quota)
            );
            Log::channel('florenceegi')->error('Il creatore non ha abbastanza quota per aggiornare il wallet', [
                'error' => $walletError,
            ]);
            throw new WalletException($walletError);
        }

        Log::channel('florenceegi')->info('Quota sufficiente per aggiornamento wallet.');
    }

    private function requestNotification($walletPayload, $request): void{
        // Preparare i dati per la notifica
        $notificationData = new WalletNotificationData(
            model_type: $walletPayload::class,
            model_id: $walletPayload->id,
            view: 'wallets.'.$walletPayload->status,
            sender_id: Auth::id(),
            prev_id: null,
            message: $walletPayload->message,
            reason: null,
            sender_name: Auth::user()->name.' '.Auth::user()->last_name,
            sender_email: Auth::user()->email,
            collection_name: $walletPayload->collection->collection_name,
            status: $walletPayload->status,
            old_royalty_mint: $request->old_royalty_mint,
            old_royalty_rebind: $request->old_royalty_rebind,
        );

        // Recuperare il ricevente della notifica
        /** @var User|null $recipient */
        $recipient = User::findOrFail($request->receiver_id);

        if (! $recipient) {
            throw new Exception(__('errors.user_not_found', [
                'id' => $request->receiver_id,
            ]));
        }

        // Inviare la notifica usando il factory pattern esistente
        $handler = $this->notificationFactory->getHandler(NotificationHandlerType::WALLET);
        $handler->handle($recipient, $notificationData);

        Log::channel('florenceegi')->info('Wallet request created successfully', [
            'wallet_payload_id' => $walletPayload->id,
            'receiver_id' => $recipient->id,
            'sender_id' => Auth::id(),
        ]);

    }



    private function acceptNotification($walletPayload, $request){

        $notification = CustomDatabaseNotification::findOrFail($request->notification_id);

        // Preparazione e invio notifica
        $notificationData = new WalletNotificationData(
            model_type: $walletPayload::class,
            model_id: $walletPayload->id,
            view: 'wallets.'.NotificationStatus::ACCEPTED->value,
            prev_id: $notification->id,
            sender_id: Auth::id(),
            message: __('collection.wallet.wallet_change_accepted'),
            reason: null,
            sender_name: Auth::user()->name.' '.Auth::user()->last_name,
            sender_email: Auth::user()->email,  // Email di chi sta inviando la notifica
            collection_name: $walletPayload->collection->collection_name,
            status: NotificationStatus::ACCEPTED->value
        );

        /** @var User|null $recipient */
        $recipient = User::find($walletPayload->proposer_id);

        if (! $recipient) {
            throw new Exception(__('collection.wallet.user_not_found', [
                'id' => $walletPayload->proposer_id,
            ]));
        }

        $handler = $this->notificationFactory->getHandler(NotificationHandlerType::WALLET);
        $handler->handle($recipient, $notificationData);
    }

    /**
     * Gestisce il rifiuto di un wallet
     */
    public function rejectWallet(WalletRejectRequest $request): void
    {
        try {
            DB::transaction(function () use ($request) {
                // Recupero gli oggetti dal database
                $notification = CustomDatabaseNotification::findOrFail($request->notification_id);
                $walletPayload = NotificationPayloadWallet::findOrFail($request->wallet_payload_id);

                // Aggiornamento stati
                $notification->update(['outcome' => NotificationStatus::REJECTED->value]);
                $walletPayload->update(['status' => NotificationStatus::REJECTED->value]);

                // Invio notifica
                /** @var User|null $recipient */
                $recipient = User::findOrFail($request->proposer_id);

                $notificationData = new WalletNotificationData(
                    model_type: $walletPayload::class,
                    model_id: $walletPayload->id,
                    view: 'wallets.'.NotificationStatus::REJECTED->value,
                    prev_id: $notification->id,
                    sender_id: Auth::id(),
                    message: __('collection.wallet.wallet_change_rejected'),
                    reason: $request->reason,
                    sender_name: Auth::user()->name.' '.Auth::user()->last_name,
                    sender_email: Auth::user()->email,  // Email di chi sta inviando la notifica
                    collection_name: $walletPayload->collection->collection_name,
                    status: NotificationStatus::REJECTED->value
                );

                $handler = $this->notificationFactory->getHandler(NotificationHandlerType::WALLET);
                $handler->handle($recipient, $notificationData);
            });
        } catch (Exception $e) {
            Log::channel('florenceegi')->error('Errore durante il rifiuto del wallet:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'notification_id' => $request->notification_id,
                'wallet_payload_id' => $request->wallet_payload_id,
            ]);
            throw $e;
        }
    }


    /**
     * Ottiene lo stato di una notifica
     */
    public function getNotificationStatus(CustomDatabaseNotification $notification): string
    {
        return match ($notification->outcome) {
            'pending_create', 'pending_update', 'pending' => 'pending',
            'Accepted' => 'accepted',
            'Rejected' => 'rejected',
            default => 'unknown',
        };
    }

    /**
     * Ottiene la classe CSS per uno stato di notifica
     */
    public function getNotificationStatusClass(string $status): string
    {
        return match ($status) {
            'pending' => 'text-yellow-500',
            'Accepted' => 'text-green-500',
            'Rejected' => 'text-red-500',
            default => 'text-gray-500',
        };
    }
}
