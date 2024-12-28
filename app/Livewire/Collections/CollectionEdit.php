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

       // Recupera la collection
        $collection = Collection::findOrFail($this->collectionId);

        // Recupera l'utente autenticato
        $user = Auth::user();

        // Leggi il ruolo dell'utente nella tabella collection_user
        $roleName = $collection->users()
            ->where('user_id', $user->id)
            ->pluck('role')
            ->first();

        if (!$roleName) {
            abort(403, 'Non sei associato a questa collezione.');
        }

        // Verifica il permesso "update_collection" per il ruolo dell'utente
        $hasPermission = \Spatie\Permission\Models\Role::where('name', $roleName)
            ->whereHas('permissions', function ($query) {
                $query->where('name', 'update_collection');
            })
            ->exists();

        if (!$hasPermission) {
            // abort(403, 'Non hai i permessi necessari per aggiornare questa collezione.');
            $this->dispatch('swal:error', [
                'title' => 'Permessi insufficienti',
                'text' => 'Non hai i permessi necessari per aggiornare questa collezione.',
            ]);
            return;
        }

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

    public function render()
    {
        return view('livewire.collections.collection-manager', [
            'teamUsers' => $this->teamUsers,
            'userId' => Auth::id(),
            'collectionId' => $this->collectionId,
        ]);
    }

}
