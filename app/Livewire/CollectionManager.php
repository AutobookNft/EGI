<?php

namespace App\Livewire;

use App\Helpers\FileHelper;
use App\Models\Collection;
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
    use WithFileUploads;

    public $collection = [
        'user_id' => null,
        'team_id'=> null,
        'collection_name'=> null,
        'type'=> null,
        'show'=> null,
        'position'=> null,
        'EGI_number'=> null,
        'floor_price'=> null,
        'description'=> null,
        'url_collection_site'=> null,
        'path_image_banner' => '',
        'path_image_card' => '',
        'path_image_avatar' => '',
    ];

    public $collections;
    public $collectionId = null;

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

    public FileStorageService $fileStorageService;

    public function mount()
    {

        $this->collectionId = null;

        // Log::channel('florenceegi')->info('Class: CollectionManager. Method: mount(). Action: Collections loaded: '. $this->collections);
        // UltraLog::log('error', 'Start handling the exception', 'Exception message');

    }

    public function create()
    {
        Log::channel('florenceegi')->info('Class: CollectionManager. Method: create()');

        try {
            // Prepara i dati della collection
            $collectionData = $this->prepareCollectionData();

            Log::channel('florenceegi')->info('Class: CollectionManager. Method: create(). Action: collection data', $this->collectionData);

            // Valida i dati della collection
            $this->validateCollection($collectionData);

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
        return [
            'user_id' => Auth::check() ? Auth::id() : null,
            'team_id' => Auth::user() && Auth::user()->currentTeam ? Auth::user()->currentTeam->id : null,
            'EPP_id' => config('app.epp_id'),
            'show' => $this->collection['show'] ?? false,
            'type' => $this->collection['type'] ?? __('collection.type_image'),
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
     * @param array $data
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateCollection(array $data)
    {
        $this->validate([
            'collection.collection_name' => 'required|string|max:255',
            'collection.team_id' => 'required|exists:teams,id',
            'collection.user_id' => 'required|exists:users,id',
            'collection.type' => 'required|string',
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
        return Collection::create($data);
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

    public function update()
    {

        try {

            $this->validate();

            // Ottengo l'istanza del servizio FileStorageService
            $fileStorageService = app(FileStorageService::class);

            // Recupera la collection da aggiornare
            $collection = Collection::findOrFail($this->collectionId);

            // Log::channel('florenceegi')->info('Class: CollectionManager. Method: update().Action: collection data', $this->collection);

            // Creo il percorso dove salvare le immagini
            $path = $this->createPathImage();

              // Salva le immagini solo se sono state caricate
            if (is_object($this->path_image_banner)) {

                // Crea il nome del file
                $filename = 'banner_' . $this->collectionId . '.' . $this->path_image_banner->extension();
                // Memorizza l'immagine nel db e salva l'immagine nel filesystem
                $this->collection['path_image_banner'] = $fileStorageService->saveFile($this->path_image_banner, $path,$filename);
            }

            if (is_object($this->path_image_card)) {

                $filename = 'card_' . $this->collectionId . '.' . $this->path_image_card->extension();
                $this->collection['path_image_card'] = $fileStorageService->saveFile($this->path_image_card, $path);

            }

            if (is_object($this->path_image_avatar)) {
                $filename = 'avatar_' . $this->collectionId . '.' . $this->path_image_avatar->extension();
                $this->collection['path_image_avatar'] =$fileStorageService->saveFile($this->path_image_avatar, $path, $filename);
            }

            // Salvo i dati della collection nel database
            $collection->update($this->collection);

            $this->path_image_card = $collection->verified_image_card_path;
            $this->path_image_avatar = $collection->verified_image_avatar_path;
            $this->path_image_banner = $collection->verified_image_banner_path;

            // Log::channel('florenceegi')->info('Collection updated successfully', $this->collection);

            // Resetta i campi del form
            $this->resetInputFields();

            // Aggiorna la lista delle collection
            $this->readTheTeamsCollections();

            // Mostra un messaggio di successo
            session()->flash('message', __('collection.updated_successfully'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::channel('florenceegi')->error('Errore durante la validazione dei dati in update', $e->errors());
            session()->flash('error', __('collection.update.validation_error'));
            throw $e;

        } catch (ModelNotFoundException $e) {
            Log::channel('florenceegi')->error('Collection not found during update', [
                'collection_id' => $this->collectionId
            ]);
            session()->flash('error', __('collection.not_found'));

        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('Failed to update collection', [
                'collection_id' => $this->collectionId,
                'error' => $e->getMessage(),
                'collection_data' => $this->collection,
                'stack_trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', __('collection.update_failed'));
        }
    }

    public function edit($collectionId)
    {
        Log::channel('florenceegi')->info('Editing collection', $this->collection);

        $collection = Collection::findOrFail($collectionId);
        $this->collectionId = $collectionId;

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

    }

    public function delete($id)
    {
        Collection::find($id)->delete();
        $this->collections = Collection::all();
    }

    private function resetInputFields()
    {
        $this->collectionId = null;
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
        // if (!app()->environment('production')) {
        //     logger()->info('Rendering Livewire Component: ' . get_class($this));
        // }

        $this->readTheTeamsCollections();

        $currentTeam = Auth::user()->currentTeam;
        $wallets = $currentTeam ? $currentTeam->wallets : collect();

        return view('livewire.collection-manager', [
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

    public function readTheTeamsCollections(){
        $user = Auth::user();

        // Verifica se l'utente è autenticato e ha team associati
        if ($user && $user->teams()->exists()) {
            $this->collections = Collection::whereHas('team', function ($query) use ($user) {
                $query->whereHas('users', function ($userQuery) use ($user) {
                    $userQuery->where('users.id', $user->id);
                });
            })->get();
        } else {
            // Collezione vuota se l'utente non è associato a nessun team
            $this->collections = collect();
        }
    }

    private function createPathImage()
    {
        $filename =  config('app.bucket_root_file_folder') . "/creator_" . Auth::id() . "/collections_".$this->collectionId;
        return $filename;
    }

}
