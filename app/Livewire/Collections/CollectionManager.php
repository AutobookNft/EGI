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
        'user_id' => null,
        'team_id' => null,
        'type' => null,
        'show' => null,
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

    #[Validate('image|nullable')]
    public $path_image_banner;

    #[Validate('image|nullable')]
    public $path_image_card;

    #[Validate('image|nullable')]
    public $path_image_avatar;

    public $log;

    public $collectionId;

    public FileStorageService $fileStorageService;

    public function mount($id = null)
    {

        $this->collectionId = $id;

        if ($this->collectionId) {
            $this->authorize('read_collection_header');
            $this->collection = Collection::findOrFail($id);
        }else{
            $this->collection = [
                'user_id' => null,
                'team_id' => null,
                'type' => null,
                'show' => null,
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

    }

        public function create()
        {
            Log::channel('florenceegi')->info('Class: CollectionManager. Method: create()');

            // Controlla se l'utente ha il permesso 'create collections'
            if (!Auth::user()->can('create_collection')) {
                abort(403, 'Non hai il permesso di creare una collection.');
            }

            try {

                // Prepara i dati della collection
                $collectionData = $this->prepareCollectionData();

                Log::channel('florenceegi')->info('Class: CollectionManager. Method: create(). Action: collection data:' , $collectionData);

                // Valida i dati della collection
                $this->validateCollection();

                // Crea la collection
                $collection = $this->storeCollection($collectionData);

                // Associa la collection al team nella tabella pivot
                $this->attachCollectionToTeam($collection, $collectionData['team_id']);

                // Log di successo
                Log::channel('florenceegi')->info('Collection created successfully', [
                    'collection_id' => $collection->id,
                    'collection_name' => $collection->collection_name,
                ]);

                // Reset dei campi di input e aggiornamento della lista delle collections
                $this->resetInputFields();
                $this->collections = Collection::all();

                session()->flash('message', __('collection.created_successfully'));

            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::channel('florenceegi')->warning('Validation failed during collection creation', [
                    'errors' => $e->errors(),
                    'collection_data' => $this->collection,
                ]);
                session()->flash('error', __('collection.create_validation_error'));
                throw $e;

            } catch (\Exception $e) {
                Log::channel('florenceegi')->error('Failed to create collection', [
                    'error' => $e->getMessage(),
                    'collection_data' => $this->collection,
                    'stack_trace' => $e->getTraceAsString(),
                ]);
                session()->flash('error', __('collection.creation_failed'));
            }
        }

    /**
     * Prepara i dati per la creazione della collection.
     *
     * @return array
     */
    private function prepareCollectionData()
    {

        Log::channel('florenceegi')->info('Class: CollectionManager. Method: prepareCollectionData(). Action: collection data', [
            'user' => Auth::id(),
            'team' => Auth::user()->currentTeam->id,
            'show' => $this->collection['show'] ?? false,
            'type' => $this->collection['type'] ?? __('collection.type_image'),
        ]);

        // Imposta i valori predefiniti per i campi della collection
        $this->collection['user_id'] = Auth::id() ?? null;
        $this->collection['team_id'] = Auth::user()->currentTeam->id ?? null;

        // type deve avere un valore predefinito, in quanto l'utente potrebbe dimentarsi di selezionare un tipo
        $this->collection['type'] = __('collection.type_image') ?? 'image';

        // show deve avere un valore predefinito impostato a false, se il controllo non viene modificato dall'utente, il valore di default potrebbe non essere gestito correttamente dal controllo stesso
        $this->collection['show'] = $this->collection['show'] ?? false;

        // EPP_id è un valore predefinito che può essere impostato nel file .env
        /**
         * NOTA
         * In questa prima versione dell'applicazione l'EPP è fisso, in seguito gestiremo gli EPP mediante db
         * */
        $this->collection['EPP_id'] = config('app.epp_id', null);

        return [
            'user_id' => $this->collection['user_id'],
            'team_id' => $this->collection['team_id'],
            'EPP_id' => $this->collection['EPP_id'],
            'show' => $this->collection['show'],
            'type' => $this->collection['type'],
            'collection_name' => $this->collection['collection_name'],
            'description' => $this->collection['description'],
            'url_collection_site' => $this->collection['url_collection_site'],
            'position' => $this->collection['position'] ?? 0,
            'EGI_number' => $this->collection['EGI_number'] ?? 0,
            'floor_price' => $this->collection['floor_price'] ?? 0,
        ];
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
            'collection.user_id' => 'required|exists:users,id',
            'collection.team_id' => 'required|exists:teams,id',
            'collection.collection_name' => 'required|string|max:255',
            'collection.type' => 'required|string',
            'collection.position' => 'nullable|integer',
            'collection.EGI_number' => 'nullable|integer',
            'collection.floor_price' => 'nullable|numeric',
            'collection.description' => 'nullable|string',
            'collection.url_collection_site' => 'nullable|url',
            'collection.show' => 'nullable|boolean',
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

        // Associa la collection al team
        $collection->teams()->attach($team);

        return $collection;
    }

    /**
     * Associa la collection al team nella tabella pivot.
     *
     * @param \App\Models\Collection $collection
     * @param int $teamId
     * @return void
     */
    private function attachCollectionToTeam(Collection $collection, int $teamId)
    {
        $collection->teams()->attach($teamId);

    }

    public function index(){
        $this->collections = Collection::all();
    }

    public function edit($id)
    {

        if ($id) {
            $this->authorize('update_collection');

            Log::channel('florenceegi')->info('Editing collection', $this->collection);

            $this->collection = Collection::findOrFail($id);

            // $this->collection = [
            //     'collection_name' => $collection->collection_name,
            //     'user_id' => $collection->user_id,
            //     'team_id' => $collection->team_id,
            //     'type' => $collection->type,
            //     'position' => $collection->position,
            //     'show' => $collection->show,
            //     'EGI_number' => $collection->EGI_number,
            //     'floor_price' => $collection->floor_price,
            //     'description' => $collection->description,
            //     'url_collection_site' => $collection->url_collection_site,
            //     'path_image_banner' => $collection->path_image_banner,
            //     'path_image_card' => $collection->verified_image_card_path,
            //     'path_image_avatar' => $collection->path_image_avatar,
            // ];
        }

    }

    public function delete($id)
    {
        Collection::find($id)->delete();
        $this->collections = Collection::all();
    }

    private function resetInputFields()
    {

        // $this->user_id = '';
        // $this->name = '';
        // $this->show = false;
        // $this->personal_team = false;
        // $this->creator = '';
        // $this->owner_wallet = '';
        // $this->address = '';
        // $this->collection_name = '';
        // $this->description = '';
        // $this->type = '';
        // $this->position = '';
        // $this->floor_price = '';
        $this->path_image_banner = '';
        $this->path_image_card = '';
        $this->path_image_avatar = '';
        // $this->path_image_EGI = '';
        // $this->url_collection_site = '';
        // $this->EGI_number = '';
        // $this->EGI_asset_roles = '';
        // $this->path_image_to_ipfs = '';
        // $this->url_image_ipfs = '';

        // Resetta gli altri campi
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

        $collection = $this->Collection::findOrFail($this->collectionId);
        // estrapola tutti gli wallets relazionati al team
        $wallets = TeamWallet::where('team_id', $this->collection['team_id'])->get();

        Log::channel('florenceegi')->info('Class: CollectionManager. Method: render(). Action: collection data: '. json_encode($collection));

        $this->collection = [
            'collection_name' => $collection->collection_name,
            'user_id' => $collection->user_id,
            'team_id' => $collection->team_id,
            'type' => $collection->type,
            'position' => $collection->position,
            'show' => $collection->show,
            'EGI_number' => $collection->EGI_number,
            'floor_price' => $collection->floor_price,
            'description' => $collection->description,
            'url_collection_site' => $collection->url_collection_site,
            'path_image_banner' => $collection->path_image_banner,
            'path_image_card' => $collection->verified_image_card_path,
            'path_image_avatar' => $collection->path_image_avatar,
        ];

        return view('livewire.collections.collection-manager', [
            'wallets' => $wallets,
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
