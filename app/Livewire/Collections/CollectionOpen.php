<?php

namespace App\Livewire\Collections;

use App\Http\Resources\CollectionResource;
use App\Livewire\Traits\HandlesCollectionUpdate;
use App\Models\Collection;
use App\Repositories\IconRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class CollectionOpen extends Component
{
    use WithFileUploads, HandlesCollectionUpdate;

    public $defaultCollection = [
        'creator_id' => null,
        'type' => null,
        'status' => null,
        'collection_name' => null,
        'position' => null,
        'EGI_number' => null,
        'floor_price' => null,
        'description' => null,
        'url_collection_site' => null,
        'image_banner' => '',
        'image_card' => '',
        'image_avatar' => '',
    ];

    public $activeSlide = 0;
    public $collections;
    public $collection = [];
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

        Log::channel('florenceegi')->info('CollectionOpen', [
            'collections' => $this->collections,
            'collection' => $this->collection,
        ]);

        $this->collections = collect(); // Inizializza come una Collection vuota
        $this->loadCollections();


    }

    public function loadCollections()
    {
        // Recupera l'utente autenticato
        $this->user = Auth::user();
        $user = $this->user;

        // Recupera tutte le collection associate all'utente
        $this->collections = Collection::whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        // Verifica se c'è una sola collection
        if ($this->collections->count() === 1) {
            $this->collection = (new CollectionResource($this->collections->first()))->toArray(request());
        } else {
            $this->collection = $this->defaultCollection;
        }
    }

    public function render()
    {

        Log::channel('florenceegi')->info('CollectionOpen', [
            'collections' => $this->collections,
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
        $this->collectionId = $this->collections->first()->id;

        return view('livewire.collections.collection-manager', [
            'collection' => $this->collection,
        ]);
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
