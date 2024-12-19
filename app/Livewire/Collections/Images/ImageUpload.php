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
 * Class ImageUpload
 *
 * This Livewire component manages the upload, display, and removal of
 * different types of images (card, EGI asset, or default) associated with a collection.
 */
class ImageUpload extends Component
{
    use WithFileUploads;

    /**
     * The image being uploaded or managed.
     *
     * @var TemporaryUploadedFile|null
     */
    #[Modelable]
    public $image;

    /**
     * The unique identifier for the collection.
     *
     * @var int
     */
    public $collectionId;

    /**
     * The type of the image (e.g., 'card', 'EGI', or default).
     *
     * @var string
     */
    public $imageType;

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
     * @param string $imageType    The type of the image (e.g., 'card' or 'EGI').
     *
     * @return void
     */
    public function mount($collectionId, $imageType)
    {
        // Store the collection ID and image type passed as parameters.
        $this->collectionId = $collectionId;
        $this->imageType = $imageType;

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

        // Get the corresponding database field for the image type.
        $field = $this->getDatabaseField();

        // Check if the collection has an image for the specified field.
        if ($collection->$field) {
            // Retrieve the cached image path using the EGIImageService.
            $this->existingImageUrl = EGIImageService::getCachedEGIImagePath(
                $this->collectionId,
                $collection->$field,
                $collection->is_published,
                null,
                $this->getKeyInPathsFile()
            );
        }

        // Log the image loading process for debugging purposes.
        Log::channel('florenceegi')->info('Rendering ImageUpload', [
            'collectionId' => $this->collectionId,
            'existingImageUrl' => $this->existingImageUrl,
            'imageType' => $this->imageType,
            'field' => $field,
        ]);
    }

    /**
     * Save the uploaded image to storage and update the database.
     *
     * @return void
     */
    public function saveImage()
    {
        try {
            // Check if an image has been uploaded.
            if (!$this->image) {
                throw new \Exception('No image to save.');
            }

            // Generate a unique filename with the appropriate prefix.
            $prefix = $this->getPrefixSavedFile();
            $filename = $prefix . uniqid() . '.' . $this->image->getClientOriginalExtension();

            // Get the storage path key for the image type.
            $pathKey = $this->getKeyInPathsFile();

            // Save the image using the EGIImageService.
            if (!EGIImageService::saveEGIImage($this->collectionId, $filename, $this->image, $pathKey)) {
                throw new \Exception("Error saving the {$this->imageType} image.");
            }

            // Update the corresponding database field with the new filename.
            $collection = Collection::findOrFail($this->collectionId);
            $collection->{$this->getDatabaseField()} = $filename;
            $collection->save();

            // Reload the existing image URL to reflect the new upload.
            $this->loadExistingImage();

            // Clear the uploaded image from the component state.
            $this->image = null;

            // Flash a success message to the session.
            session()->flash('success', ucfirst($this->imageType) . ' image saved successfully!');
        } catch (\Exception $e) {
            // Log the error and flash an error message to the session.
            Log::error('Error saving the ' . $this->imageType . ' image: ' . $e->getMessage());
            session()->flash('error', 'Error saving the ' . $this->imageType . ' image.');
        }
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

            // Get the corresponding database field for the image type.
            $field = $this->getDatabaseField();

            // Check if the collection has an image to remove.
            if ($collection->$field) {
                // Remove the old image using the EGIImageService.
                EGIImageService::removeOldImage($this->getPrefixSavedFile(), $this->collectionId, $this->getKeyInPathsFile());

                // Set the image field to null and save the collection.
                $collection->$field = null;
                $collection->save();

                // Clear the image state in the component.
                $this->image = null;
                $this->existingImageUrl = null;

                // Flash a success message to the session.
                session()->flash('success', ucfirst($this->imageType) . ' image removed successfully!');
            }
        } catch (\Exception $e) {
            // Log the error and flash an error message to the session.
            Log::error('Error removing the ' . $this->imageType . ' image: ' . $e->getMessage());
            session()->flash('error', 'Error removing the ' . $this->imageType . ' image.');
        }
    }

    /**
     * Get the corresponding database field for the image type.
     *
     * @return string The database field name.
     */
    protected function getDatabaseField()
    {
        return match ($this->imageType) {
            'card' => 'image_card',
            'EGI' => 'image_EGI',
            default => 'image',
        };
    }

    /**
     * Get the file prefix for the image type.
     *
     * @return string The file prefix.
     */
    protected function getPrefixSavedFile()
    {
        return match ($this->imageType) {
            'card' => 'card_image_',
            'EGI' => 'egi_asset_',
            default => 'image_',
        };
    }

    /**
     * Get the storage path key for the image type.
     *
     * @return string The path key in the configuration file.
     */
    protected function getKeyInPathsFile()
    {
        return match ($this->imageType) {
            'card' => 'head.card',
            'EGI' => 'head.EGI_asset',
            default => 'head.root',
        };
    }

    /**
     * Render the component's view with the appropriate image URL.
     *
     * @return \Illuminate\View\View The view for the image upload component.
     */
    public function render()
    {
        // Determine the image URL: temporary URL if the image is in preview, otherwise the existing URL.
        $imageUrl = ($this->image instanceof TemporaryUploadedFile)
            ? $this->image->temporaryUrl()
            : $this->existingImageUrl;

        // Return the view with the image URL.
        return view('livewire.collections.images.image-upload', [
            'imageUrl' => $imageUrl,
        ]);
    }
}
