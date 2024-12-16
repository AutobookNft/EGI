<?php

namespace App\Livewire\Collections;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class HeadImagesManager extends Component
{
    use WithFileUploads;

    public $bannerImage;
    public $cardImage;
    public $avatarImage;

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
