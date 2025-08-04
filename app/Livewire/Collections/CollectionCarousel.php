<?php

namespace App\Livewire\Collections;

use App\Helpers\FegiAuth;
use Livewire\Component;
use App\Models\Collection;
use App\Repositories\IconRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CollectionCarousel extends Component
{
    public $collections;
    public $activeSlide = 0;
    protected $iconRepository;

    // Livewire properties that sync with Alpine
    public $debugInfo = [];

    public function boot(IconRepository $iconRepository)
    {
        $this->iconRepository = $iconRepository;
    }

    public function mount()
    {

        $team_id = FegiAuth::user()->currentTeam->id;
        $this->collections = Collection::where('team_id', $team_id)->get();

        Log::channel('florenceegi')->info('Collections for team', [
            'team_id' => $team_id,
            'collections' => $this->collections,
        ]);

    }

    public function nextSlide()
    {
        $collectionsCount = count($this->collections);
        if ($collectionsCount > 0) {
            // Calculate items per view based on responsive design
            $itemsPerView = $this->getItemsPerView();
            $maxSlide = max(0, $collectionsCount - $itemsPerView);

            $this->activeSlide = min($this->activeSlide + 1, $maxSlide);

            Log::channel('florenceegi')->info('Next slide', [
                'activeSlide' => $this->activeSlide,
                'maxSlide' => $maxSlide,
                'itemsPerView' => $itemsPerView,
                'collectionsCount' => $collectionsCount
            ]);
        }
    }

    public function prevSlide()
    {
        if (count($this->collections) > 0) {
            $this->activeSlide = max(0, $this->activeSlide - 1);

            Log::channel('florenceegi')->info('Previous slide', [
                'activeSlide' => $this->activeSlide,
                'collectionsCount' => count($this->collections)
            ]);
        }
    }

    private function getItemsPerView(): int
    {
        // This is a server-side approximation
        // The real calculation happens in Alpine.js
        return 1; // Default to 1 for server-side calculations
    }

    public function updateDebugInfo($info)
    {
        $this->debugInfo = $info;
        Log::channel('florenceegi')->info('Carousel debug info updated', $info);
    }

    public function edit($id)
    {
        // Implementa la logica per modificare la collezione
    }

    public function delete($id)
    {
        // Implementa la logica per eliminare la collezione
    }

    public function render()
    {
        $iconHtml = $this->iconRepository->getIcon('camera', 'elegant', '');
        return view('livewire.collections.collection-carousel', compact('iconHtml'));
    }
}
