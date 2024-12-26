<?php

namespace App\Livewire\Collections;

use Livewire\Component;
use App\Models\TeamUser;

class EditWalletModal extends Component
{
    public $teamUserId; // Aggiungiamo questa proprietà pubblica
    public $walletAddress;
    public $royaltyMint;
    public $royaltyRebind;
    public $show = false; // Proprietà per gestire la visibilità della modale

    public function mount($teamUserId = null)
    {
        $this->teamUserId = $teamUserId;

        if ($teamUserId) {
            $this->loadData();
        }
    }

    public function loadData()
    {
        $user = TeamUser::findOrFail($this->teamUserId);
        $this->walletAddress = $user->wallet;
        $this->royaltyMint = $user->royalty_mint;
        $this->royaltyRebind = $user->royalty_rebind;
    }

    public function saveChanges()
    {
        $this->validate([
            'walletAddress' => 'required|string',
            'royaltyMint' => 'nullable|numeric|min:0|max:100',
            'royaltyRebind' => 'nullable|numeric|min:0|max:100',
        ]);

        $user = TeamUser::findOrFail($this->teamUserId);
        $user->update([
            'wallet' => $this->walletAddress,
            'royalty_mint' => $this->royaltyMint,
            'royalty_rebind' => $this->royaltyRebind,
        ]);

        $this->emit('teamMemberUpdated');
        $this->show = false;
        session()->flash('message', __('Wallet updated successfully!'));
    }

    public function render()
    {
        return view('livewire.collections.edit-wallet-modal');
    }
}
