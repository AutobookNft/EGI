<?php

namespace App\Livewire\Collections;

use App\Models\TeamWallet;
use App\Repositories\IconRepository;
use Livewire\Component;
use App\Models\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;

class Open extends Component
{
    public $collections;

    protected $team_id;

    public $noTeamMessage = null;

    protected $iconRepository;

    #[Validate('image|nullable')]
    public $path_image_banner;

    #[Validate('image|nullable')]
    public $path_image_card;

    #[Validate('image|nullable')]
    public $path_image_avatar;

    public function boot(IconRepository $iconRepository)
    {
        $this->iconRepository = $iconRepository;
    }


    public function mount()
    {
        $user = Auth::user();

        // Recupera tutte le collection dell'utente connesso
        $this->collections = Collection::where('user_id', $user->id)->get();

        // Se non ci sono collection, imposta un messaggio di avviso
        if ($this->collections->isEmpty()) {
            $this->noTeamMessage = 'Non hai nessuna collection associata. Crea una collection per iniziare.';
        }
    }

    public function selectCollection($collectionId)
    {
        $collection = Collection::findOrFail($collectionId);

        // Cambia il currentTeam utilizzando la rotta di Jetstream
        $this->redirect(route('current-team.update', ['team' => $collection->team_id]));

        // Reindirizza al CollectionManager per la collection selezionata
        return redirect()->route('collection-manager', ['id' => $collectionId]);
    }

    public function render()
    {

        // Se l'utente non ha un team, mostro un messaggio di avviso
        if ($this->noTeamMessage) {
            return view('livewire.collections.no-team', [
                'message' => $this->noTeamMessage,
            ]);
        }

        $user = Auth::user();

        // Se ci sono più di una collection, mostro il carousel
        if ($this->collections->count() > 1) {
            $iconHtml = $this->iconRepository->getIcon('camera', 'elegant', '');
            return view('livewire.collections.collection-carousel', compact('iconHtml', 'collections'));
        }

        // estrapola tutti gli wallets relazionati al team
        $wallets = TeamWallet::where('team_id', $this->collections->team_id)->get();

        // Altrimenti mostro il collection-manager per la prima collection
        return view('livewire.collections.collection-manager', [
            'collection' => $this->collections->first(),
        ]);
    }
}
