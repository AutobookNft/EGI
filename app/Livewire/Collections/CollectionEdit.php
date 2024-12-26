<?php

namespace App\Livewire\Collections;

use App\Helpers\FileHelper;
use App\Livewire\Traits\HandlesCollectionUpdate;
use App\Models\Collection;
use App\Models\Team;
use App\Models\TeamUser;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;


class CollectionEdit extends Component
{
    use WithFileUploads, HandlesCollectionUpdate;

    #[Validate('required|string|max:255')]
    public $collection_name;

    #[Validate('required|string')]
    public $type;

    #[Validate('nullable|integer')]
    public $position;

    #[Validate('nullable|integer')]
    public $EGI_number;

    #[Validate('nullable|numeric')]
    public $floor_price;

    #[Validate('nullable|string')]
    public $description;

    #[Validate('nullable|string')]
    public $url_collection_site;

    #[Validate('nullable|boolean')]
    public $is_published;

    public $collections;

    public $collection =[];

    public $model_collection = null;

    public $log;

    public $collectionId;

    public $teamUsers;

    public $teamId;

    public function mount($id = null)
    {

        $this->collectionId = $id;
        $collection = Collection::findOrFail($this->collectionId);

        // estrapola il team_id dalla collection, serve per poter aprire la modale per la gestione dei memebri del team
        $this->teamId = $collection->team_id;


        $this->collection_name = $collection->collection_name;
        $this->type = $collection->type;
        $this->position = $collection->position;
        $this->EGI_number = $collection->EGI_number;
        $this->floor_price = $collection->floor_price;
        $this->description = $collection->description;
        $this->url_collection_site = $collection->url_collection_site;
        $this->is_published = $collection->is_published;

    }

    public function save()
    {
        $this->validate();

        $collection = Collection::findOrFail($this->collectionId);
        $collection->update([
            'collection_name' => $this->collection_name,
            'type' => $this->type,
            'position' => $this->position,
            'EGI_number' => $this->EGI_number,
            'floor_price' => $this->floor_price,
            'description' => $this->description,
            'is_published' => $this->is_published,
        ]);

        session()->flash('message', 'Collezione aggiornata con successo!');
    }

    #[On('team-member-updated')] // Ascolta l'evento dal componente figlio
    public function loadTeamUsers()
    {
        $this->teamUsers = TeamUser::with('user')
            ->where('team_id', $this->collectionId)
            ->get();
    }

    public function render()
    {
        return view('livewire.collections.collection-manager', [
            'teamUsers' => $this->teamUsers,
            'teamId' => $this->teamId,
            'userId' => Auth::id(),
            'collectionId' => $this->collectionId,
        ]);
    }

}
