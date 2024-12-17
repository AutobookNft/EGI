<?php

namespace App\Livewire\Collections;

use App\Models\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Services\FileStorageService;

class HeadImagesManager extends Component
{
    use WithFileUploads;

    public $bannerImage;
    public $cardImage;
    public $avatarImage;
    public $EGIImage;
    public $collection;
    public $collectionId;

    public function mount($id)
    {
        $this->collectionId = $id;
        $this->collection = Collection::findOrFail($this->collectionId );
    }


    public function saveBannerImage()
    {
        $fileStorageService = new FileStorageService();

        if ($this->bannerImage) {
            $path = 'collections/banners';
            $filename = 'banner_' . $this->collection->id . '.' . $this->bannerImage->getClientOriginalExtension();
            $collectionId = $this->collection->id;
            $imageType = 'banner';

            try {
                $fileStorageService->saveFile($this->bannerImage, $path, $filename, 'public', $collectionId, $imageType);
                session()->flash('success', 'Immagine banner salvata con successo!');
            } catch (\Exception $e) {
                session()->flash('error', 'Errore nel salvataggio dell\'immagine banner.');
            }
        }
    }

    public function removeImage($type)
    {
        switch ($type) {
            case 'banner':
                $this->bannerImage = null;
                break;
            case 'card':
                $this->cardImage = null;
                break;
            case 'avatar':
                $this->avatarImage = null;
                break;
        }
    }

    public function render()
    {
        return view('livewire.collections.head-images-manager');
    }
}
