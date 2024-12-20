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
 * Class AvatarImageUpload
 *
 * This Livewire component manages the upload, display, and removal of
 * the avatar image associated with a specific collection.
 */
class AvatarImageUpload extends Component
{
    use WithFileUploads;

    /**
     * The avatar image being uploaded or managed.
     *
     * @var TemporaryUploadedFile|null
     */
    #[Modelable]
    public $avatarImage;

    /**
     * The unique identifier for the collection.
     *
     * @var int
     */
    public $collectionId;

    /**
     * The URL of the existing avatar image.
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

        // Load the existing avatar image.
        $this->loadExistingImage();
    }

    /**
     * Load the existing avatar image URL from the database.
     *
     * @return void
     */
    public function loadExistingImage()
    {
        // Retrieve the collection or fail with a 404 error if not found.
        $collection = Collection::findOrFail($this->collectionId);

        // Check if the collection has an avatar image.
        if ($collection->image_avatar) {
            // Retrieve the cached image path for the avatar.
            // $this->existingImageUrl = EGIImageService::getCachedEGIImagePath(
            //     $this->collectionId,
            //     $collection->image_avatar,
            //     $collection->is_published,
            //     null,
            //     'head.avatar' // PathKey for the avatar image.
            // );
            $this->existingImageUrl = $collection->image_avatar;
        }

        // Log the existing image URL for debugging purposes.
        Log::channel('florenceegi')->info('Rendering AvatarImageUpload', [
            'collectionId' => $this->collectionId,
            'existingImageUrl' => $this->existingImageUrl,
        ]);
    }

    /**
     * Save the uploaded avatar image to the storage and update the database.
     *
     * @return void
     */
    public function saveImage()
    {
        try {
            // Check if an image has been uploaded.
            if (!$this->avatarImage) {
                throw new \Exception('No image to save.');
            }

            // Generate a unique filename with the 'avatar_image_' prefix.
            $filename = 'avatar_image_' . uniqid() . '.' . $this->avatarImage->getClientOriginalExtension();

            // Save the image using the EGIImageService.
            if (!EGIImageService::saveEGIImage($this->collectionId, $filename, $this->avatarImage, 'head.avatar')) {
                throw new \Exception('Error saving the avatar image.');
            }

            // Retrieve the collection and update the image_avatar field.
            $collection = Collection::findOrFail($this->collectionId);
            $collection->image_avatar = $filename;
            $collection->save();

            // Reload the existing image URL to reflect the new upload.
            $this->loadExistingImage();

            // Clear the uploaded image from the component state.
            $this->avatarImage = null;

            // Flash a success message to the session.
            session()->flash('success', 'Avatar image saved successfully!');
        } catch (\Exception $e) {
            // Log the error and flash an error message to the session.
            Log::error('Error saving the avatar image: ' . $e->getMessage());
            session()->flash('error', 'Error saving the avatar image.');
        }
    }

    /**
     * Remove the existing avatar image from storage and update the database.
     *
     * @return void
     */
    public function removeImage()
    {
        try {
            // Retrieve the collection or fail if not found.
            $collection = Collection::findOrFail($this->collectionId);

            // Check if the collection has an avatar image.
            if ($collection->image_avatar) {
                // Remove the old image using the EGIImageService.
                EGIImageService::removeOldImage('avatar_image_', $this->collectionId, 'head.root');

                // Set the image_avatar field to null and save the collection.
                $collection->image_avatar = null;
                $collection->save();

                // Clear the image state in the component.
                $this->avatarImage = null;
                $this->existingImageUrl = null;

                // Flash a success message to the session.
                session()->flash('success', 'Avatar image removed successfully!');
            }
        } catch (\Exception $e) {
            // Log the error and flash an error message to the session.
            Log::error('Error removing the avatar image: ' . $e->getMessage());
            session()->flash('error', 'Error removing the avatar image.');
        }
    }

    /**
     * Render the component's view with the appropriate image URL.
     *
     * @return \Illuminate\View\View The view for the avatar image upload component.
     */
    public function render()
    {
        // Determine the image URL: temporary URL if the image is in preview, otherwise the existing URL.
        $imageUrl = ($this->avatarImage instanceof TemporaryUploadedFile)
            ? $this->avatarImage->temporaryUrl()
            : $this->existingImageUrl;

        // Return the view with the image URL.
        return view('livewire.collections.images.avatar-image-upload', [
            'imageUrl' => $imageUrl,
        ]);
    }
}
