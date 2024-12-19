<?php

namespace App\Livewire\Collections\Images;

use App\Models\Collection;
use App\Services\EGIImageService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Modelable;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Class BannerImageUpload
 *
 * This Livewire component manages the upload, display, and removal of
 * the banner image associated with a specific collection.
 */
class BannerImageUpload extends Component
{
    use WithFileUploads;

    /**
     * The banner image being uploaded or managed.
     *
     * @var TemporaryUploadedFile|null
     */
    #[Modelable]
    public $bannerImage;

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
        }

        // Log the existing image URL for debugging purposes.
        Log::channel('florenceegi')->info('Rendering BannerImageUpload', [
            'collectionId' => $this->collectionId,
            'existingImageUrl' => $this->existingImageUrl,
        ]);
    }

    /**
     * Save the uploaded banner image to the storage and update the database.
     *
     * @return void
     */
    public function saveImage()
    {
        try {
            // Check if an image has been uploaded.
            if (!$this->bannerImage) {
                throw new \Exception('No image to save.');
            }

            // Generate a unique filename with the 'banner_image_' prefix.
            $filename = 'banner_image_' . uniqid() . '.' . $this->bannerImage->getClientOriginalExtension();

            // Save the image using the EGIImageService.
            if (!EGIImageService::saveEGIImage($this->collectionId, $filename, $this->bannerImage, 'head.banner')) {
                throw new \Exception('Error saving the banner image.');
            }

            // Retrieve the collection and update the image_banner field.
            $collection = Collection::findOrFail($this->collectionId);
            $collection->image_banner = $filename;
            $collection->save();

            // Reload the existing image URL to reflect the new upload.
            $this->loadExistingImage();

            // Clear the uploaded image from the component state.
            $this->bannerImage = null;

            // Flash a success message to the session.
            session()->flash('success', 'Banner image saved successfully!');
        } catch (\Exception $e) {
            // Log the error and flash an error message to the session.
            Log::error('Error saving the banner image: ' . $e->getMessage());
            session()->flash('error', 'Error saving the banner image.');
        }
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
                $this->bannerImage = null;
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
     * @return \Illuminate\View\View The view for the banner image upload component.
     */
    public function render()
    {
        // Determine the image URL: temporary URL if the image is in preview, otherwise the existing URL.
        $imageUrl = ($this->bannerImage instanceof TemporaryUploadedFile)
            ? $this->bannerImage->temporaryUrl()
            : $this->existingImageUrl;

        // Return the view with the image URL.
        return view('livewire.collections.images.banner-image-upload', [
            'imageUrl' => $imageUrl,
        ]);
    }
}
