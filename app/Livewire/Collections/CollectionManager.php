<?php

namespace App\Livewire\Collections;

use App\Helpers\FileHelper;
use App\Livewire\Traits\HandlesCollectionUpdate;
use App\Models\Collection;
use App\Models\Team;
use App\Models\TeamWallet;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use App\Services\FileStorageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;


class CollectionManager extends Component
{
    use WithFileUploads, HandlesCollectionUpdate;

    public $collection = [
        'creator_id' => null,
        'team_id' => null,
        'type' => null,
        'is_published' => null,
        'collection_name' => null,
        'position' => null,
        'EGI_number '=> null,
        'floor_price' => null,
        'description' => null,
        'url_collection_site' => null,
        'path_image_banner' => '',
        'path_image_card' => '',
        'path_image_avatar' => '',
    ];

    public $collections;
    public $model_collection = null;

    // #[Validate('nullable|integer')]
    // public $user_id;

    #[Validate('nullable|string|max:255')]
    public $type;

    #[Validate('nullable')]
    public $path_image_banner;

    #[Validate('nullable')]
    public $path_image_card;

    #[Validate('nullable')]
    public $path_image_avatar;

    public $log;

    public $collectionId;

    public FileStorageService $fileStorageService;

    public function mount($id = null)
    {

        $this->collectionId = $id;

    }

    /**
     * Valida i dati della collection.
     *
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateCollection()
    {
        $this->validate([
            'collection.collection_name' => 'required|string|max:255',
            'collection.type' => 'required|string',
            'collection.position' => 'nullable|integer',
            'collection.EGI_number' => 'nullable|integer',
            'collection.floor_price' => 'nullable|numeric',
            'collection.description' => 'nullable|string',
            'collection.is_published' => 'nullable|boolean',
        ]);
    }

    /**
     * Crea una nuova collection nel database.
     *
     * @param array $data
     * @return \App\Models\Collection
     */
    private function storeCollection(array $data)
    {
        // Trova il team esistente
        $team = Team::findOrFail($data['team_id']);

        // Crea la collection
        $collection = Collection::create($data);

        return $collection;
   }

    public function edit($id)
    {

        if ($id) {
            $this->authorize('update_collection');

            Log::channel('florenceegi')->info('Editing collection', $this->collection);

            $this->collection = Collection::findOrFail($id);

        }

    }

    public function delete($id)
    {
        Collection::find($id)->delete();
        $this->collections = Collection::all();
    }

    private function resetInputFields()
    {

        $this->collection = [
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
            'path_image_banner' => '',
            'path_image_card' => '',
            'path_image_avatar' => '',
        ];
    }

    public function removeImage($type)
    {
        switch ($type) {
            case 'banner':
                $this->path_image_banner = '';
                break;
            case 'card':


                // Log::channel('florenceegi')->info('Class: CollectionManager. Method: removeImage(). Action: type: '. $type);

                // Elimina il riferimento anche dal database
                DB::table('collections')->where('id', $this->collectionId)->update(['path_image_card' => null]);
                Storage::delete($this->collection['path_image_card']);

                $this->collection['path_image_card'] = null;
                $this->path_image_card = null;

                // $this->dispatch('remove_image');

            case 'avatar':
                $this->path_image_avatar = '';

                break;
        }

        $this->dispatch('refresh');
    }

    public function render()
    {

        if ($this->collectionId) {
            $collection = Collection::findOrFail($this->collectionId);

            $this->collection['collection_name'] = $collection->collection_name;
            $this->collection['creator_id'] = $collection->creator_id;
            $this->collection['team_id'] = $collection->team_id;
            $this->collection['epp_id'] = $collection->epp_id;
            $this->collection['EGI_asset_id'] = $collection->EGI_asset_id;
            $this->collection['path_image_EGI'] = $collection->path_image_EGI;
            $this->collection['path_image_to_ipfs'] = $collection->path_image_to_ipfs;
            $this->collection['url_collection_site'] = $collection->url_collection_site;
            $this->collection['personal_team'] = $collection->personal_team;
            $this->collection['creator'] = $collection->creator;
            $this->collection['owner_wallet'] = $collection->owner_wallet;
            $this->collection['address'] = $collection->address;
            $this->collection['token'] = $collection->token;
            $this->collection['EGI_asset_roles'] = $collection->EGI_asset_roles;
            $this->collection['type'] = $collection->type;
            $this->collection['is_published'] = $collection->is_published;
            $this->collection['position'] = $collection->position;
            $this->collection['EGI_number'] = $collection->EGI_number;
            $this->collection['floor_price'] = $collection->floor_price;
            $this->collection['description'] = $collection->description;
            $this->collection['url_collection_site'] = $collection->url_collection_site;
            $this->collection['path_image_banner'] = $collection->path_image_banner;
            $this->collection['path_image_card'] = $collection->path_image_card;
            $this->collection['path_image_avatar'] = $collection->path_image_avatar;
        }else{
            $this->resetInputFields();
        }

        Log::channel('florenceegi')->info('Current collection', [
                'collection' => json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            ]);

        // $collection = $this->collection;
        // estrapola tutti gli wallets relazionati al team
        $wallets = TeamWallet::where('team_id', $this->collection['team_id'])->get();

        return view('livewire.collections.collection-manager', [
            'wallets' => $wallets
        ]);
    }

    // Recupera tutte le collection che appartengono ai team a cui l'utente autenticato è associato.
    // Relazioni coinvolte:
    // 1. Una Collection appartiene a un Team (relazione `belongsTo` nel modello Collection).
    // 2. Un Team può avere molti utenti associati (relazione `hasManyThrough` o `belongsToMany` con tabella pivot team_user).
    // 3. Un utente (User) può essere membro di uno o più Team (relazione `belongsToMany` nel modello User).
    //
    // La logica della query:
    // - Utilizziamo `whereHas('team')` per verificare che esista un team associato alla collection.
    // - All'interno di `whereHas('team')`, verifichiamo che il team abbia utenti associati tramite `whereHas('users')`.
    // - All'interno di `whereHas('users')`, filtriamo per includere solo i team che contengono l'utente autenticato (`auth()->id()`).
    //
    // Risultato:
    // Otteniamo solo le collection che appartengono ai team a cui l'utente autenticato è associato.

    // $this->collections = Collection::whereHas('users', function ($query) {
    //     $query->where('users.id', Auth::id());
    // })->get();

    public function readTheTeamsCollections()
    {
        $user = Auth::user();

        // Verifica se l'utente è autenticato e ha un team corrente
        if ($user && $user->currentTeam) {
            // Recupera le collections associate al team corrente dell'utente
            $this->collections = $user->currentTeam->collections()->with('teams')->get();
        } else {
            // Se l'utente non ha un team corrente, restituisce una collezione vuota
            $this->collections = collect();
        }
    }

    private function createPathImage()
    {
        $filename =  config('app.bucket_root_file_folder') . "/creator_" . Auth::id() . "/collections_".$this->collectionId;
        return $filename;
    }

}
