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
class EgiImageUpload extends Component
{
    use WithFileUploads, HasPermissionTrait;

    /**
     * The image being uploaded or managed.
     *
     * @var TemporaryUploadedFile|null
     */
    #[Modelable]
    public $image_EGI;

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
     * @param int $collectionId The ID of the collection.
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
        if ($collection->image_EGI) {
            // Retrieve the cached image path using the EGIImageService.
            $this->existingImageUrl = EGIImageService::getCachedEGIImagePath(
                $this->collectionId,
                $collection->image_EGI,
                $collection->is_published,
                null,
                'head.EGI_asset' // PathKey for the EGI image.
            );

            $this->existingImageUrl = $collection->image_EGI;
        }

    }

    /**
     * Save the uploaded image to storage and update the database.
     *
     * @return void
     */
    public function saveImage()
    {

        Log::channel('florenceegi')->info('EgiImageUpload, saveImage');

        // Save the image using the EGIImageService.

        try {

            $collection = Collection::findOrFail($this->collectionId);

            Log::channel('florenceegi')->info('EgiImageUpload, saveImage', ['collection' => $collection]);

            // Verifica il permesso "update_collection"
            $this->hasPermission($collection, 'update_collection_image_header');

            Log::channel('florenceegi')->info('EgiImageUpload, saveImage after permission', ['image_EGI' => $this->image_EGI]);

            // Check if an image has been uploaded.
            if (!$this->image_EGI) {
                throw new \Exception('No image to save.');
            }

            // Generate a unique filename with the appropriate prefix.
            $filename = 'EGI_asset_' . uniqid() . '.' . $this->image_EGI->getClientOriginalExtension();

            Log::channel('florenceegi')->info('EgiImageUpload, saveImage', ['filename' => $filename]);

            // Save the image using the EGIImageService.
            if (!EGIImageService::saveEGIImage($this->collectionId, $filename, $this->image_EGI, 'head.EGI_asset')) {
                throw new \Exception("Error saving the EGI image.");
            }

            Log::channel('florenceegi')->info('EgiImageUpload, saveImage', ['filename' => $filename]);

            // Update the corresponding database field with the new filename.
            $collection->image_EGI = $filename;
            $collection->save();

            Log::channel('florenceegi')->info('EgiImageUpload, saveImage', ['filename' => $filename]);

            // Reload the existing image URL to reflect the new upload.
            $this->loadExistingImage();

            // Clear the uploaded image from the component state.
            $this->image_EGI = null;

            // Flash a success message to the session.
            session()->flash('success', 'EGI image saved successfully!');
        } catch (\Exception $e) {
            // Log the error and flash an error message to the session.
            Log::error('Error saving the EGI image: ' . $e->getMessage());
            session()->flash('error', 'Error saving the EGI image.');
        }
    }

    #[On('egiImageRemove')]
    public function egiImageRemove(){


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

            $collection = Collection::findOrFail($this->collectionId);

            // Check if the collection has an image to remove.
            if ($collection->image_EGI) {
                // Remove the old image using the EGIImageService.
                EGIImageService::removeOldImage('EGI_asset_', $this->collectionId, 'head.EGI_asset');

                // Set the image field to null and save the collection.
                $collection->image_EGI = null;
                $collection->save();

                // Clear the image state in the component.
                $this->image_EGI = null;
                $this->existingImageUrl = null;

                // Flash a success message to the session.
                session()->flash('success', 'Egi image removed successfully!');
            }
        } catch (\Exception $e) {
            // Log the error and flash an error message to the session.
            Log::error('Error removing the EGI image: ' . $e->getMessage());
            session()->flash('error', 'Error removing the EGI image.');
        }
   }

    /**
     * Render the component's view with the appropriate image URL.
     *
     * @return \Illuminate\View\View The view for the image upload component.
     */
    public function render()
    {

        Log::channel('florenceegi')->info('EgiImageUpload, render', ['collectionId' => $this->collectionId]);

        // // Soluzione alternativa per il debug
        if ($this->image_EGI instanceof TemporaryUploadedFile) {
            // Usa il percorso locale invece dell'URL temporaneo
            // Solo per debug - NON usare in produzione
            $tmpPath = storage_path('app/livewire-tmp/' . $this->image_EGI->getFilename());
            Log::channel('florenceegi')->info('Temporary file path: ' . $tmpPath);
            if (file_exists($tmpPath)) {
                // Converti l'immagine in base64 per il test
                $imageData = base64_encode(file_get_contents($tmpPath));
                $mimeType = mime_content_type($tmpPath);
                $imageUrl = 'data:' . $mimeType . ';base64,' . $imageData;
                Log::channel('florenceegi')->info('Image URL: ' . $imageUrl);
            } else {
                $imageUrl = null;
            }
        } else {
            $imageUrl = $this->existingImageUrl;
            Log::channel('florenceegi')->info('Existing image URL: ' . $imageUrl);
        }

        return view('livewire.collections.images.egi-image-upload', [
            'imageUrl' => $imageUrl,
        ]);
    }
}
