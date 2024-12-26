<?php

namespace App\Livewire\Collections;

use App\Models\Collection;
use App\Models\TeamWallet;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class Show extends Component
{
    public $collection = [
        'user_id' => null,
        'team_id' => null,
        'type' => null,
        'is_published' => null,
        'collection_name' => null,
        'position' => null,
        'EGI_number '=> null,
        'floor_price' => null,
        'description' => null,
        'url_collection_site' => null,
        'image_banner' => '',
        'image_card' => '',
        'image_avatar' => '',
    ];

    public $collections;
    public $collectionId;

    public function render()
    {

        $user = Auth::user();

        // Recupera la collection selezionata
        $collection = Collection::find($this->collectionId);

        // estrapola tutti gli wallets relazionati al team
        $wallets = TeamWallet::where('team_id', $collection->team_id)->get();

        // Recupera tutte le collection dell'utente connesso
        $this->collections = Collection::where('user_id', $user->id)->get();
        return view('livewire.collections.collection-manager',[
            'wallets' => $wallets,
        ]);
    }
}
