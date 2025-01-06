<?php

namespace App\Livewire\Collections;

use App\Models\Collection;
use App\Services\Notifications\NotificationHandlerFactory;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletChangeApproval;
use App\Notifications\WalletChangeRequest;
use App\Notifications\WalletChangeResponse;
use App\Traits\HasPermissionTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Rules\ValidWalletAddress;

class EditWalletModal extends Component
{

    use HasPermissionTrait;

    public $walletId; // Proprietà per identificare l'utente nella collection
    public $collectionId; // Proprietà per identificare l'utente nella collection
    public $walletAddress;
    public $royaltyMint;
    public $royaltyRebind;
    public $approverUserId;

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
    public function openForCreateNewWallets($collectionId = null, $userId = null)
    {
        if ($collectionId) {
            Log::channel('florenceegi')->info('Collection id', [
                'collectionId' => $collectionId
            ]);
            $this->collectionId = $collectionId;
            $this->approverUserId = $userId;
        } else {
            Log::channel('florenceegi')->info('No Collection ID provided for wallet creation.');
        }

        $this->show = true; // Mostra la modale
        $this->mode = 'create';
    }


    public function closeHandleWallets()
    {
        $this->walletAddress = null;
        $this->royaltyMint = null;
        $this->royaltyRebind = null;
        $this->show = false; // Mostra la modale
    }

    public function createNewWallet()
    {
        Log::channel('florenceegi')->info('createNewWallet');

        $this->validate([
            // 'walletAddress' => [
            //     'required',
            //     'string',
            //     new ValidWalletAddress(),
            // ],
            'royaltyMint' => [
                'nullable',
                'numeric',
                'min:0',
                'max:' . config('app.creator_royalty_mint'),
            ],
            'royaltyRebind' => [
                'nullable',
                'numeric',
                'min:0',
                'max:' . config('app.creator_royalty_rebind'),
            ],
        ]);

        // Trovo la collection
        $collection = Collection::findOrFail($this->collectionId);
        // $approverUserId = $collection->creator_id;

        Log::channel('florenceegi')->info('createNewWallet PRIMA della verifica dei permessi', [
            'collectionId' => $this->collectionId,
            'approverUserId' => $this->approverUserId
        ]);

        // Verifica permessi per l'utente autenticato
        if (!$this->hasPermission($collection, 'create_wallet')) {
            session()->flash('error', __('You do not have permission to create a wallet.'));
            return;
        }

        Log::channel('florenceegi')->info('createNewWallet DOPO della verifica dei permessi', [
            'collectionId' => $this->collectionId,
            'approverUserId' => $this->approverUserId
        ]);

        // Verifica e aggiorna la quota del creator
        $this->validateAndAdjustCreatorQuota($collection, $this->royaltyMint, $this->royaltyRebind);

        // Crea una proposta di wallet
        $this->proposeNewWallet($this->collectionId, $this->approverUserId, $this->walletAddress, $this->royaltyMint, $this->royaltyRebind);

        $this->show = false;
        session()->flash('message', __('Wallet creation request sent successfully!'));
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

        // **Inserimento in wallet_change_approvals**
        if ($wallet->user_id !== Auth::id()) {
            $this->createWalletApproval($wallet);
            session()->flash('message', __('The modification has been submitted for approval.'));
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
        session()->flash('message', __('Wallet updated successfully!'));
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
            throw new \Exception(__('The total exceeds the maximum allowed percentage.'));
        }

        return $maxValue - $newSum;
    }

    public function validateAndAdjustCreatorQuota($collection, $newMint, $newRebind)
    {

        $creatorWallet = Wallet::where('collection_id', $collection->id)
                            ->where('user_id', $collection->creator_id)
                            ->first();

        if (!$creatorWallet) {
            throw new \Exception(__('Creator wallet not found.'));
        }

        // Verifica se il creator ha abbastanza quota disponibile
        if ($creatorWallet->royalty_mint < $newMint || $creatorWallet->royalty_rebind < $newRebind) {
            throw new \Exception(__('Creator does not have enough quota to allocate.'));
        }

        // Riduci la quota del creator
        // $creatorWallet->update([
        //     'royalty_mint' => $creatorWallet->royalty_mint - $newMint,
        //     'royalty_rebind' => $creatorWallet->royalty_rebind - $newRebind,
        // ]);
    }

    public function proposeNewWallet($collection, $approverUserId, $walletAddress, $mint, $rebind)
    {
        Log::channel('florenceegi')->info('proposeNewWallet', [
            'approverUserId' => $approverUserId,
        ]);

        // Creazione della proposta
        $approval = WalletChangeApproval::create([
            'wallet_id' => null, // Perché è un nuovo wallet
            'requested_by_user_id' => Auth::user()->id, // Chi inoltra la richiesta
            'approver_user_id' => $approverUserId, // Chi deve approvare la richiesta
            'change_type' => 'create',
            'change_details' => [
                'wallet_address' => $walletAddress,
                'royalty_mint' => $mint,
                'royalty_rebind' => $rebind,
            ],
            'status' => 'pending',
        ]);

        // Usa la factory per inviare la notifica con l'action "proposal"
        $handler = NotificationHandlerFactory::getHandler(WalletChangeRequest::class);
        $handler->handle($approval, 'proposal');
    }

    public function approveChange($approvalId)
    {
        $approval = WalletChangeApproval::findOrFail($approvalId);
        $wallet = $approval->wallet;

        $wallet->update($approval->change_details['new']);
        $approval->update(['status' => 'approved']);

        Notification::send($approval->requestedBy, new WalletChangeResponse($approval, 'proposal'));
        session()->flash('message', __('The wallet change has been approved.'));
    }

    public function declineChange($approvalId, $reason = null)
    {
        $approval = WalletChangeApproval::findOrFail($approvalId);

        $approval->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        Notification::send($approval->requestedBy, new WalletChangeResponse($approval, 'rejected'));
        session()->flash('message', __('The wallet change has been declined.'));
    }

    public function render()
    {
        return view('livewire.collections.edit-wallet-modal');
    }
}
