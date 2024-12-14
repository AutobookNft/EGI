<?php

namespace App\Livewire\Collections;

use Livewire\Component;
use App\Models\Collection;
use App\Repositories\IconRepository;
use Illuminate\Support\Facades\Auth;

class CollectionCarousel extends Component
{
    public $collections;
    public $activeSlide = 0;
    protected $iconRepository;

    public function boot(IconRepository $iconRepository)
    {
        $this->iconRepository = $iconRepository;
    }

    public function mount()
    {

        $id = Auth::user()->currentTeam->id;
        $this->collections = Collection::findOrFail($id);
        
    }

    public function nextSlide()
    {
        $this->activeSlide = ($this->activeSlide + 1) % count($this->collections);
    }

    public function prevSlide()
    {
        $this->activeSlide = ($this->activeSlide - 1 + count($this->collections)) % count($this->collections);
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
