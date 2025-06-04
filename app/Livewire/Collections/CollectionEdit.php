<?php

namespace App\Livewire\Collections;

use App\Helpers\FegiAuth;
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
use App\Traits\SaveCollectionTraits;


/**
 * Class CollectionEdit
 * @package App\Livewire\Collections
 * NOTA BENE: il metodo Save è all'interno del trait SaveCollectionTraits
 */

class CollectionEdit extends Component
{
    use WithFileUploads, SaveCollectionTraits;

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

        Log::channel('florenceegi')->info('CollectionEdit:mount', ['collection' => $collection]);

        $this->collection_name = $collection->collection_name;
        $this->type = $collection->type;
        $this->position = $collection->position;
        $this->EGI_number = $collection->EGI_number;
        $this->floor_price = $collection->floor_price;
        $this->description = $collection->description;
        $this->url_collection_site = $collection->url_collection_site;
        $this->is_published = $collection->is_published;

    }

    public function render()
    {
        return view('livewire.collections.collection-manager', [
            'userId' => FegiAuth::id(),
            'collectionId' => $this->collectionId,
        ]);
    }

}
