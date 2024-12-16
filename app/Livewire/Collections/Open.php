<?php

namespace App\Livewire\Collections;

use App\Http\Resources\CollectionResource;
use App\Livewire\Traits\HandlesCollectionUpdate;
use App\Models\Collection;
use App\Models\TeamWallet;
use App\Repositories\IconRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class Open extends Component
{

    use WithFileUploads, HandlesCollectionUpdate;
    public $defaultCollection = [
        'user_id' => null,
        'team_id' => null,
        'type' => null,
        'show' => null,
        'collection_name' => null,
        'position' => null,
        'EGI_number' => null,
        'floor_price' => null,
        'description' => null,
        'url_collection_site' => null,
        'path_image_banner' => '',
        'path_image_card' => '',
        'path_image_avatar' => '',
    ];

    
    public $activeSlide = 0;

    public $collections;

    public $collection = [];

    public $noTeamMessage = 'Non ci sono team o collection disponibili.';

    protected $iconRepository;

    #[Validate('nullable')]
    public $path_image_banner;

    #[Validate('nullable')]
    public $path_image_card;

    #[Validate('nullable')]
    public $path_image_avatar;

    protected $user;

    public function boot(IconRepository $iconRepository)
    {
        $this->iconRepository = $iconRepository;
    }

    public function mount()
    {

        // Inizializza come una Collection vuota
        $this->collections = new Collection();
        $this->loadSingleCollection();

    }

    public function loadSingleCollection()
    {
        // Recupera l'utente autenticato
        $this->user = Auth::user();
        $user = $this->user;

        // Trova tutte le collection attive dei team a cui l'utente è associato
        $this->collections = Collection::whereHas('team', function ($query) use ($user) {
            $query->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $this->user->id);
            });
        })->get();

        // Verifica se c'è una sola collection attiva
        if ($this->collections->count() === 1) {
            $this->collection = (new CollectionResource($this->collections->first()))->toArray(request());
        } else {
            $this->collection = $this->defaultCollection;
        }


    }

    public function render()
    {
        // Se l'utente non ha alcuna collection, mostra un messaggio di avviso
        if (!$this->collections || $this->collections->isEmpty()) {
            return view('livewire.collections.no-team', [
                'message' => $this->noTeamMessage,
            ]);
        }

        // Se ci sono più di una collection, mostra il carousel
        if ($this->collections->count() > 1) {
            $iconHtml = $this->iconRepository->getIcon('camera', 'elegant', '');

            Log::channel('florenceegi')->info('Current collection', [
                'collections' => json_encode($this->collections, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            ]);

            return view('livewire.collections.collection-carousel', [
                'iconHtml' => $iconHtml,
                'collections' => $this->collections,
            ]);
        }

        // Se c'è una sola collection, carica i wallet del team associato
        $team = $this->collections->first()->team ?? null;

        // carico i wallet del team
        $wallets = $team ? $team->wallets : [];

        // Mostra il collection-manager per la prima collection
        return view('livewire.collections.collection-manager', [
            'collection' => $this->collection,
            'wallets' => $wallets,
        ]);
    }

    public function nextSlide()
    {
        $this->activeSlide = ($this->activeSlide + 1) % count($this->collections);
    }

    public function prevSlide()
    {
        $this->activeSlide = ($this->activeSlide - 1 + count($this->collections)) % count($this->collections);
    }


}
