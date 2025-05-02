<?php

namespace App\Livewire\Collections\Images;

use App\Models\Collection;
use App\Services\EGIImageService;
use App\Traits\HasPermissionTrait;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Modelable;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Attributes\On;

/**
 * Class ImageUpload
 *
 * This Livewire component manages the upload, display, and removal of
 * different types of images (card, EGI asset, or default) associated with a collection.
 */
class CardImageUpload extends Component
{
    use WithFileUploads, HasPermissionTrait;

    /**
     * The image being uploaded or managed.
     *
     * @var TemporaryUploadedFile|null
     */
    #[Modelable]
    public $image_card;

    /**
     * The unique identifier for the collection.
     *
     * @var int
     */
    public $collectionId;

    /**
     * The URL of the existing image.
     *
     * @var string|null
     */
    public $existingImageUrl;

    /**
     * Mount the component and initialize the collection ID and image type.
     *
     * @param int    $collectionId The ID of the collection.
     *
     * @return void
     */
    public function mount($collectionId)
    {
        // Store the collection ID and image type passed as parameters.
        $this->collectionId = $collectionId;

        // Load the existing image URL.
        $this->loadExistingImage();
    }

    /**
     * Load the existing image URL from the database.
     *
     * @return void
     */
    public function loadExistingImage()
    {
        // Retrieve the collection or fail with a 404 error if not found.
        $collection = Collection::findOrFail($this->collectionId);

        // Check if the collection has an image for the specified field.
        if ($collection->image_card) {
            // Retrieve the cached image path using the EGIImageService.
            $this->existingImageUrl = EGIImageService::getCachedEGIImagePath(
                $this->collectionId,
                $collection->image_card,
                $collection->is_published,
                null,
                'head.card' // PathKey for the card image.
            );

            $this->existingImageUrl = $collection->image_card;
        }
    }

    /**
     * Save the uploaded image to storage and update the database.
     *
     * @return void
     */
    public function saveImage()
    {
        try {
            $collection = Collection::findOrFail($this->collectionId);

            // Verifica il permesso "update_collection"
            $this->hasPermission($collection, 'update_collection_image_header');

            // Check if an image has been uploaded.
            if (!$this->image_card) {
                throw new \Exception('No image to save.');
            }

            $filename = 'card_image_' . uniqid() . '.' . $this->image_card->getClientOriginalExtension();

            // Save the image using the EGIImageService.
            if (!EGIImageService::saveEGIImage($this->collectionId, $filename, $this->image_card, 'head.card')) {
                throw new \Exception("Error saving the card image.");
            }

            // Update the corresponding database field with the new filename.
            $collection->image_card = $filename;
            $collection->save();

            // Reload the existing image URL to reflect the new upload.
            $this->loadExistingImage();

            // Clear the uploaded image from the component state.
            $this->image_card = null;

            // Flash a success message to the session.
            session()->flash('success', 'card image saved successfully!');
        } catch (\Exception $e) {
            // Log the error and flash an error message to the session.
            Log::error('Error saving the card image: ' . $e->getMessage());
            session()->flash('error', 'Error saving the card image.');
        }
    }

    #[On('cardImageRemove')]
    public function cardImageRemove()
    {
        $this->removeImage();
    }

    /**
     * Remove the existing image from storage and update the database.
     *
     * @return void
     */
    public function removeImage()
    {
        try {
            // Retrieve the collection or fail if not found.
            $collection = Collection::findOrFail($this->collectionId);

            // Check if the collection has an image to remove.
            if ($collection->image_card) {
                // Remove the old image using the EGIImageService.
                EGIImageService::removeOldImage('card_image_', $this->collectionId, 'head.card');

                // Set the image field to null and save the collection.
                $collection->image_card = null;
                $collection->save();

                // Clear the image state in the component.
                $this->image_card = null;
                $this->existingImageUrl = null;

                // Flash a success message to the session.
                session()->flash('success', 'card image removed successfully!');
            }
        } catch (\Exception $e) {
            // Log the error and flash an error message to the session.
            Log::error('Error removing the card image: ' . $e->getMessage());
            session()->flash('error', 'Error removing the card image.');
        }
    }

    /**
     * Render the component's view with the appropriate image URL.
     *
     * @return \Illuminate\View\View The view for the image upload component.
     */
    public function render()
    {

        // Soluzione alternativa per l'anteprima usando base64 invece di temporaryUrl()
        if ($this->image_card instanceof TemporaryUploadedFile) {
            // Usa il percorso locale invece dell'URL temporaneo
            $tmpPath = storage_path('app/livewire-tmp/' . $this->image_card->getFilename());
            if (file_exists($tmpPath)) {
                // Converti l'immagine in base64 per il test
                $imageData = base64_encode(file_get_contents($tmpPath));
                $mimeType = mime_content_type($tmpPath);
                $imageUrl = 'data:' . $mimeType . ';base64,' . $imageData;
            } else {
                $imageUrl = null;
            }
            } else {
                $imageUrl = $this->existingImageUrl;
        }

        // Return the view with the image URL.
        return view('livewire.collections.images.card-image-upload', [
            'imageUrl' => $imageUrl,
        ]);
    }
}
