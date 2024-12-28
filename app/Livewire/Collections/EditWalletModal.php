<?php

namespace App\Livewire\Collections;

use App\Models\Wallet;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;

class EditWalletModal extends Component
{
    public $walletId; // Proprietà per identificare l'utente nella collection
    public $collectionId; // Proprietà per identificare l'utente nella collection
    public $walletAddress;
    public $royaltyMint;
    public $royaltyRebind;

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
    public function openForCreateNewWallets($collectionId)
    {
        Log::channel('florenceegi')->info('Collection id', [
            'collectionId' => $collectionId
        ]);
        $this->collectionId = $collectionId;
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
        $this->validate([
            'walletAddress' => 'required|string',
            'royaltyMint' => 'nullable|numeric|min:0|max:100',
            'royaltyRebind' => 'nullable|numeric|min:0|max:100',
        ]);

        Wallet::create([
            'collection_id' => $this->collectionId,
            'wallet' => $this->walletAddress,
            'royalty_mint' => $this->royaltyMint,
            'royalty_rebind' => $this->royaltyRebind,
        ]);

        $this->dispatch('collectionMemberUpdated');
        $this->show = false;
        session()->flash('message', __('Wallet created successfully!'));
    }

    public function saveWallet()
    {
        $this->validate([
            'walletAddress' => 'required|string',
            'royaltyMint' => 'nullable|numeric|min:0|max:100',
            'royaltyRebind' => 'nullable|numeric|min:0|max:100',
        ]);

        $wallet = Wallet::findOrFail($this->walletId);
        $wallet->update([
            'wallet' => $this->walletAddress,
            'royalty_mint' => $this->royaltyMint,
            'royalty_rebind' => $this->royaltyRebind,
        ]);

        $this->dispatch('collectionMemberUpdated');
        $this->show = false;
        session()->flash('message', __('Wallet updated successfully!'));
    }

    public function render()
    {
        return view('livewire.collections.edit-wallet-modal');
    }
}
