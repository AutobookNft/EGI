<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Collection;
use App\Repositories\IconRepository;

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
        $this->collections = Collection::all();
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
        return view('livewire.collection-carousel', compact('iconHtml'));
    }
}
