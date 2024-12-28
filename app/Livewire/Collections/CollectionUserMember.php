<?php

namespace App\Livewire\Collections;

use App\Models\CollectionUser;
use App\Models\Wallet;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class CollectionUserMember extends Component
{
    public $collectionUsers; // Lista membri del team
    public $wallets;
    public $collectionId;
    public $show = false; // Proprietà per gestire la visibilità della modale

    public function mount($id)
    {
        Log::channel('florenceegi')->info('Collection id', [
            'collectionId' => $id
        ]);


        $this->collectionId = $id;
        $this->loadTeamUsers();
    }

    public function openInviteModal()
    {
        $this->dispatch('open-invite-modal'); // Invia un evento ai figli compatibile con Livewire 3
    }

    // #[On('openHandleWallets')]
    // public function showHandleWallets()
    // {
    //     // $this->resetFields(); // Pulisce i campi
    //     $this->show = true; // Mostra la modale
    // }

    public function loadTeamUsers()
    {
        $this->collectionUsers = CollectionUser::where('collection_id', $this->collectionId)->get();
        $this->wallets = Wallet::where('collection_id','=',$this->collectionId)->get();
        Log::channel('florenceegi')->info('CollectionUsersMembers', [
            'collectionId' => $this->collectionId,
            'wallets' => $this->wallets
        ]);

    }

    public function render()
    {
        return view('livewire.collections.collection-user');
    }
}
