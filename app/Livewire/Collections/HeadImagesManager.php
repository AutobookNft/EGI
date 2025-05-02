<?php

namespace App\Livewire\Collections;

use App\Models\Collection;
use App\Services\EGIImageService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Modelable;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

/**
 * Class HeadImagesManager
 *
 * This Livewire component manages the header images (banner, card, and avatar)
 * for a specific collection. It initializes the collection and provides a render
 * method to display the associated view.
 */
#[Layout('layouts.coll')]
class HeadImagesManager extends Component

{

    use WithFileUploads;

    /**
     * The collection instance associated with the header images.
     *
     * @var Collection
     */
    public $collection;

    //  /**
    //  * The image being uploaded or managed.
    //  *
    //  * @var TemporaryUploadedFile|null
    //  */
    // #[Modelable]
    // public $image_card;

    /**
     * The unique identifier for the collection.
     *
     * @var int
     */
    public $collectionId;

    /**
     * Mount the component and initialize the collection.
     *
     * @param int $id The ID of the collection to be managed.
     *
     * @return void
     */
    public function mount($id)
    {
        // Store the collection ID passed as a parameter.
        $this->collectionId = $id;

        // Retrieve the collection from the database or fail with a 404 error if not found.
        $this->collection = Collection::findOrFail($this->collectionId);
    }

    /**
     * Remove the existing banner image from storage and update the database.
     *
     * @return void
     */
    public function removeImage($type)
    {

        Log::channel('florenceegi')->info('HeadImagesManager, removeImage', ['type' => $type]);


        // Clear the image state in the component.
        switch ($type) {
            case 'banner':
                $this->dispatch('bannerImageRemove');
                break;
            case 'card':
                $this->dispatch('cardImageRemove');
                break;
            case 'EGI':
                $this->dispatch('egiImageRemove');
                break;
            case 'avatar':
                $this->dispatch('avatarImageRemove');
                break;
        }

    }

    /**
     * Render the component's view.
     *
     * @return \Illuminate\View\View The view associated with the component.
     */
    public function render()
    {

        log::channel('florenceegi')->info('HeadImagesManager, render', ['collectionId' => $this->collectionId]);

        // Return the Livewire view for managing head images.
        return view('livewire.collections.head-images-manager');
    }
}
