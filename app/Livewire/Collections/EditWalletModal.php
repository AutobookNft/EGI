<?php

namespace App\Livewire\Collections;

use App\Enums\NotificationStatus;

use App\Models\Collection;
use App\Models\NotificationPayloadWallet;

use App\Services\Notifications\NotificationHandlerFactory;
use App\Services\Notifications\WalletChangeRequestHandler;
use App\Services\Notifications\WalletNotificationHandler;

use App\Models\User;
use App\Models\Wallet;

use App\Traits\HasPermissionTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;

use Illuminate\Http\Request;

class EditWalletModal extends Component
{

    use HasPermissionTrait;

    public $walletId; // Proprietà per identificare l'utente nella collection
    public $collectionId; // Proprietà per identificare l'utente nella collection
    public $walletAddress;
    public $royaltyMint;
    public $royaltyRebind;
    public $receiverUserId;

    public $mode = 'create'; // Modalità di apertura della modale
    public $show = false; // Proprietà per gestire la visibilità della modale

    public function mount($walletId = null)
    {

        // if ($this->walleId) {
        //     $this->loadData($this->walleId);
        // }
    }

    public function loadData()
    {

        $wallet = Wallet::findOrFail($this->walletId);

        Log::channel('florenceegi')->info('Wallet', [
            'walletId' => $this->walletId
        ]);

        $this->walletAddress = $wallet->wallet;
        $this->royaltyMint = $wallet->royalty_mint;
        $this->royaltyRebind = $wallet->royalty_rebind;
    }

    #[On('openHandleWallets')]
    public function openHandleWallets($walletId)
    {
        $this->walletId = $walletId;
        $this->loadData();
        $this->show = true; // Mostra la modale
        $this->mode = 'edit';
    }

    #[On('openForCreateNewWallets')]
    public function openForCreateNewWallets($collectionId, $userId)
    {
        $this->collectionId = $collectionId;
        $this->receiverUserId = $userId;
        $this->show = true; // Mostra la modale
    }

    #[On('closeForCreateNewWallets')]
    public function closeHandleWallets()
    {
        $this->walletAddress = null;
        $this->royaltyMint = null;
        $this->royaltyRebind = null;
        $this->show = false; // Mostra la modale
    }

    public function createNewWallet(Request $request)
    {

        $walletAddress = $request->input('wallet_address');
        $collectionId = $request->input('collection_id');
        $userId = $request->input('user_id'); // receiver

        Log::channel('florenceegi')->info('createNewWallet', [
            'collectionId' => $collectionId,
            'receiverUserId' => $userId,
            'walletAddress' => $walletAddress
        ]);

        $minRoyaltyMint = 0;
        $maxRoyaltyRebind = config('app.creator_royalty_rebind');

        $request->validate([
            // 'wallet_address' => [
            //     'required',
            //     'string',
            //     new ValidWalletAddress(),
            // ],
            'royalty_mint' => [
                'nullable',
                'numeric',
                'min:0',
                'max:' . config('app.creator_royalty_mint'),
            ],
            'royalty_rebind' => [
                'nullable',
                'numeric',
                'min:'.  $minRoyaltyMint,
                'max:' . $maxRoyaltyRebind,
            ],
        ]);


        // $walletAddress = $request->input('wallet_address');
        $royaltyMint = $request->input('royalty_mint');
        $royaltyRebind = $request->input('royalty_rebind');

        // Trovo la collection
        $collection = Collection::findOrFail($collectionId);
        // $approverUserId = $collection->creator_id;

        Log::channel('florenceegi')->info('createNewWallet PRIMA della verifica dei permessi', [
            'collectionId' => $this->collectionId,
            'approverUserId' => $this->receiverUserId
        ]);

        // Verifica e aggiorna la quota del creator
        $this->validateCreatorQuota($collectionId, $royaltyMint, $royaltyRebind);

        // Crea una proposta di wallet
        $this->proposeNewWallet($collection, $userId, $royaltyMint, $royaltyRebind, $walletAddress);

        $this->show = false;

        session()->flash('message', __('collection.wallet.creation_request_success'));

        return response()->json(['message' => __('collection.wallet.creation_request_success'), 'success' => true]);
    }

    public function proposeNewWallet($collection, $userId, $royaltyMint, $royaltyRebind, $walletAddress)
    {

        $receiver = User::findOrFail($userId);

        $type = NotificationStatus::CREATION->value;
        $status = NotificationStatus::PENDING_CREATE->value;

        $data = [
            'proposer_id' => Auth::user()->id, // Chi inoltra la richiesta
            'receiver_id' => $userId, // Chi deve approvare la richiesta
            'collection_id' => $collection->id,
            'wallet' => $walletAddress,
            'royalty_mint' =>$royaltyMint,
            'royalty_rebind' => $royaltyRebind,
            'status' => $status, // Stato della richiesta, può essere "pending", "accepted", "rejected"
            'type' => $type, // Tipo di richiesta, può essere "creation" o "update" (Il wallet può essere creato ex novo oppure può essere modificato, ecco il perché della distinzione con type)
        ];

        Log::channel('florenceegi')->info('EditWalletModal:dati di payload', $data);

        // Creazione del payload della proposta,
        $walletPayload = NotificationPayloadWallet::create($data);

        $walletPayload['collection_name'] = $collection->collection_name;
        $walletPayload['proposer_name'] = Auth::user()->name . ' ' . Auth::user()->last_name; // Nome di chi fa la proposta
        $walletPayload['model_id'] = $walletPayload->id;
        $walletPayload['model_type'] = get_class($walletPayload);
        $walletPayload['message'] = __('collection.wallet.wallet_creation_request');
        $walletPayload['view'] = 'wallets.' . $type; // La vista da mostrare

        // Usa la factory per inviare la notifica con l'action "proposal"
        $handler = NotificationHandlerFactory::getHandler(WalletNotificationHandler::class);
        $handler->handle($receiver, $walletPayload);
    }

    public function validateCreatorQuota($collectionId, $newMint, $newRebind,)
    {

        $proposer_id = Auth::user()->id;

        Log::channel('florenceegi')->info('EditWalletModal:validateAndAdjustCreatorQuota', [
            'collection_id' => $collectionId,
            'proposer_id' => $proposer_id,
        ]);

        $creatorWallet = Wallet::where('collection_id', $collectionId)
                            ->where('user_id', $proposer_id)
                            ->first();

        if (!$creatorWallet) {
            throw new \Exception(__('collection.wallet.creator_wallet_not_found'));
        }

        // Verifica se il creator ha abbastanza quota disponibile
        if ($creatorWallet->royalty_mint < $newMint || $creatorWallet->royalty_rebind < $newRebind) {
            throw new \Exception(__('collection.wallet.creator_does_not_have_enough_quota_to_allocate'));
        }
    }

    public function saveWallet()
    {
        $this->validate([
            'walletAddress' => 'required|string',
            'royaltyMint' => 'nullable|numeric|min:0|max:100',
            'royaltyRebind' => 'nullable|numeric|min:0|max:100',
        ]);

        $wallet = Wallet::findOrFail($this->walletId);

        // **Controllo dei Permessi**
        if ($wallet->user_id !== Auth::id()) {
            // Usa il trait per verificare i permessi sulla collection
            $this->hasPermission($wallet->collection, 'update_wallet');
        }

        // **Validazione delle Quote**
        $remainingMint = $this->validateCreatorModification('royalty_mint', $wallet, $this->royaltyMint);
        $remainingRebind = $this->validateCreatorModification('royalty_rebind', $wallet, $this->royaltyRebind);

        // **Gestione delle Riduzioni e Accredito all’EPP**
        $this->handleReductionsAndEpp($wallet, $remainingMint, $remainingRebind);

        // **Inserimento in notification_payload_wallets**
        if ($wallet->user_id !== Auth::id()) {
            $this->createWalletApproval($wallet);
            session()->flash('message', __('collection.wallet.modification_has_been_submitted_for_approval'));
            $this->show = false;
            return;
        }

        // **Applicazione della Modifica**
        $wallet->update([
            'wallet' => $this->walletAddress,
            'royalty_mint' => $this->royaltyMint,
            'royalty_rebind' => $this->royaltyRebind,
        ]);

        $this->dispatch('collectionMemberUpdated');
        $this->show = false;
        session()->flash('message', __('collection.wallet.wallet_updated_successfully'));
    }


    private function handleReductionsAndEpp($wallet, $remainingMint, $remainingRebind)
    {
        $eppWallet = Wallet::where('collection_id', $wallet->collection_id)->where('platform_role', 'EPP')->first();

        if ($remainingMint < 0) {
            $eppWallet->increment('royalty_mint', abs($remainingMint));
        }

        if ($remainingRebind < 0) {
            $eppWallet->increment('royalty_rebind', abs($remainingRebind));
        }
    }

    private function validateCreatorModification($type, $wallet, $newValue)
    {
        $maxValue = $type === 'royalty_mint' ? 70.0 : 4.5;
        $currentSum = Wallet::where('collection_id', $wallet->collection_id)->sum($type);

        $newSum = $currentSum - $wallet->$type + $newValue;

        if ($newSum > $maxValue) {
            throw new \Exception(__('collection.wallet.total_exceeds_the_maximum_allowed_percentage'));
        }

        return $maxValue - $newSum;
    }



    /**
     * Summary of approveChange
     * @param mixed $approvalId
     * @return void
     * @method static NotificationPayloadWallet findOrFail(int|string $id)
     */
    public function approveChange($approvalId)
    {
        $notification = NotificationPayloadWallet::findOrFail($approvalId)->first();
        $wallet = $notification->wallet;

        // $wallet->update($walletChangeApproval->change_details['new']);
        /**
         *
         * Quì c'è da implementare la logica per l'approvazione della modifica del wallet
         *
         */

        // Aggiorna lo stato della proposta a "created"
        $notification->handleCreation();

        $handler = NotificationHandlerFactory::getHandler(WalletChangeRequestHandler::class);
        $handler->handle($notification);

        session()->flash('message', __('collection.wallet.wallet_change_request_approved'));
    }

    public function render()
    {
        return view('livewire.collections.edit-wallet-modal');
    }
}
