<?php

namespace App\Livewire\Collections;

use App\Helpers\FegiAuth;
use App\Models\Collection;
use App\Repositories\IconRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use App\Traits\SaveCollectionTraits;

/**
 * Class CollectionEdit
 * @package App\Livewire\Collections
 * NOTA BENE: il metodo Save è all'interno del trait SaveCollectionTraits
 */

class CollectionOpen extends Component
{
    use WithFileUploads, SaveCollectionTraits; // Usa il nuovo trait

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

    public $activeSlide = 0;
    public $collections;
    public $collection;
    public $noCollectionMessage = 'Non ci sono collection disponibili.';
    protected $iconRepository;
    protected $user;

    public $collectionId;

    public function boot(IconRepository $iconRepository)
    {
        $this->iconRepository = $iconRepository;
    }

    public function mount()
    {

        $this->collections = collect(); // Inizializza come una Collection vuota
        $this->loadCollections();

    }

    public function loadCollections()
    {
        // Recupera l'utente autenticato
        $this->user = FegiAuth::user();

        $user = $this->user; // Nella callback non si puù usare $this per questo motivo si crea una variabile locale

        // Recupera tutte le collection associate all'utente
        $this->collections = Collection::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        // Verifica se c'è una sola collection
        if ($this->collections->count() === 1) {

            $this->collection = $this->collections->first();
            Log::channel('florenceegi')->info('CollectionOpen: loadCollections', ['collection' => $this->collection]);

        }
    }

    public function render()
    {

        Log::channel('florenceegi')->info('CollectionOpen: render', [
            'collection' => $this->collection,
        ]);

        // Se l'utente non ha alcuna collection, mostra un messaggio di avviso
        if (!$this->collections || $this->collections->isEmpty()) {
            return view('livewire.collections.no-collection', [
                'message' => $this->noCollectionMessage,
            ]);
        }

        // Se ci sono più di una collection, mostra il carousel
        if ($this->collections->count() > 1) {
            $iconHtml = $this->iconRepository->getIcon('camera', 'elegant', '');

            return view('livewire.collections.collection-carousel', [
                'iconHtml' => $iconHtml,
                'collections' => $this->collections,
            ]);
        }

        // Se c'è una sola collection, carica i dettagli
        $this->collectionId = $this->collection->id;
        $this->collection_name = $this->collection->collection_name;
        $this->type = $this->collection->type;
        $this->position = $this->collection->position;
        $this->EGI_number = $this->collection->EGI_number;
        $this->floor_price = $this->collection->floor_price;
        $this->description = $this->collection->description;
        $this->url_collection_site = $this->collection->url_collection_site;
        $this->is_published = $this->collection->is_published;

        return view('livewire.collections.collection-manager');
    }

    public function nextSlide()
    {
        $this->activeSlide = ($this->activeSlide + 1) % $this->collections->count();
    }

    public function prevSlide()
    {
        $this->activeSlide = ($this->activeSlide - 1 + $this->collections->count()) % $this->collections->count();
    }
}
