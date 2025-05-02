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
 * Class BannerImageUpload
 *
 * This Livewire component manages the upload, display, and removal of
 * the banner image associated with a specific collection.
 */
class BannerImageUpload extends Component
{
    use WithFileUploads, HasPermissionTrait;

    /**
     * The banner image being uploaded or managed.
     *
     * @var TemporaryUploadedFile|null
     */
    #[Modelable]
    public $image_banner;

    /**
     * The unique identifier for the collection.
     *
     * @var int
     */
    public $collectionId;

    /**
     * The URL of the existing banner image.
     *
     * @var string|null
     */
    public $existingImageUrl;

    /**
     * Mount the component and initialize the collection ID.
     *
     * @param int $collectionId The ID of the collection.
     *
     * @return void
     */
    public function mount($collectionId)
    {
        // Store the collection ID passed as a parameter.
        $this->collectionId = $collectionId;

        // Load the existing banner image.
        $this->loadExistingImage();
    }

    /**
     * Load the existing banner image URL from the database.
     *
     * @return void
     */
    public function loadExistingImage()
    {
        // Retrieve the collection or fail with a 404 error if not found.
        $collection = Collection::findOrFail($this->collectionId);

        // Check if the collection has a banner image.
        if ($collection->image_banner) {
            // Retrieve the cached image path for the banner.
            $this->existingImageUrl = EGIImageService::getCachedEGIImagePath(
                $this->collectionId,
                $collection->image_banner,
                $collection->is_published,
                null,
                'head.banner' // PathKey for the banner image.
            );
            $this->existingImageUrl = $collection->image_banner;
        }

    }

    /**
     * Save the uploaded banner image to the storage and update the database.
     *
     * @return void
     */
    public function saveImage()
    {

        $collection = Collection::findOrFail($this->collectionId);

        // Verifica il permesso "update_collection"
        $this->hasPermission($collection, 'update_collection_image_header');

        try {
            // Check if an image has been uploaded.
            if (!$this->image_banner) {
                throw new \Exception('No image to save.');
            }

            // Generate a unique filename with the 'banner_image_' prefix.
            $filename = 'banner_image_' . uniqid() . '.' . $this->image_banner->getClientOriginalExtension();

            // Save the image using the EGIImageService.
            if (!EGIImageService::saveEGIImage($this->collectionId, $filename, $this->image_banner, 'head.banner')) {
                throw new \Exception('Error saving the banner image.');
            }

            // Retrieve the collection and update the image_banner field.
            $collection->image_banner = $filename;
            $collection->save();

            // Reload the existing image URL to reflect the new upload.
            $this->loadExistingImage();

            // Clear the uploaded image from the component state.
            $this->image_banner = null;

            // Flash a success message to the session.
            session()->flash('success', 'Banner image saved successfully!');
        } catch (\Exception $e) {
            // Log the error and flash an error message to the session.
            Log::error('Error saving the banner image: ' . $e->getMessage());
            session()->flash('error', 'Error saving the banner image.');
        }
    }

    #[On('bannerImageRemove')]
    public function bannerImageRemove(){
        $this->removeImage();
    }

    /**
     * Remove the existing banner image from storage and update the database.
     *
     * @return void
     */
    public function removeImage()
    {

        try {
            // Retrieve the collection or fail if not found.
            $collection = Collection::findOrFail($this->collectionId);

            // Check if the collection has a banner image.
            if ($collection->image_banner) {
                // Remove the old image using the EGIImageService.
                EGIImageService::removeOldImage('banner_image_', $this->collectionId, 'head.banner');

                // Set the image_banner field to null and save the collection.
                $collection->image_banner = null;
                $collection->save();

                // Clear the image state in the component.
                $this->image_banner = null;
                $this->existingImageUrl = null;

                // Flash a success message to the session.
                session()->flash('success', 'Banner image removed successfully!');
            }
        } catch (\Exception $e) {
            // Log the error and flash an error message to the session.
            Log::error('Error removing the banner image: ' . $e->getMessage());
            session()->flash('error', 'Error removing the banner image.');
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
        if ($this->image_banner instanceof TemporaryUploadedFile) {
            // Usa il percorso locale invece dell'URL temporaneo
            $tmpPath = storage_path('app/livewire-tmp/' . $this->image_banner->getFilename());
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
        return view('livewire.collections.images.banner-image-upload', [
            'imageUrl' => $imageUrl,
        ]);
    }
}
