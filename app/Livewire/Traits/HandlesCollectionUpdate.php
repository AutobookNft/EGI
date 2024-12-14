<?php

namespace App\Livewire\Traits;

use App\Services\FileStorageService;
use App\Models\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

trait HandlesCollectionUpdate
{
    public function collectionUpdate()
    {
        try {
            $this->validate();

            $fileStorageService = app(FileStorageService::class);

            $collection = Collection::findOrFail($this->collection['id']);

            $path = $this->createPathImage();

            if (is_object($this->path_image_banner)) {
                $filename = 'banner_' . $this->collection['id']. '.' . $this->path_image_banner->extension();
                $this->collection['path_image_banner'] = $fileStorageService->saveFile($this->path_image_banner, $path, $filename);
            }

            if (is_object($this->path_image_card)) {
                $filename = 'card_' . $this->collection['id'] . '.' . $this->path_image_card->extension();
                $this->collection['path_image_card'] = $fileStorageService->saveFile($this->path_image_card, $path, $filename);
            }

            if (is_object($this->path_image_avatar)) {
                $filename = 'avatar_' . $this->collection['id'] . '.' . $this->path_image_avatar->extension();
                $this->collection['path_image_avatar'] = $fileStorageService->saveFile($this->path_image_avatar, $path, $filename);
            }

            $collection->update($this->collection);

            $this->path_image_banner = $collection['path_image_banner'];
            $this->path_image_card = $collection['path_image_card'];
            $this->path_image_avatar = $collection['path_image_avatar'];

            session()->flash('message', __('collection.updated_successfully'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::channel('florenceegi')->error('Validation error during update', $e->errors());
            session()->flash('error', __('collection.update.validation_error'));
            throw $e;

        } catch (ModelNotFoundException $e) {
            Log::channel('florenceegi')->error('Collection not found during update', ['collection_id' => $this->collection['id']]);
            session()->flash('error', __('collection.not_found'));

        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('Failed to update collection', [
                'error' => $e->getMessage(),
                'collection_data' => $this->collection,
                'stack_trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', __('collection.update_failed'));
        }
    }

    private function createPathImage()
    {
        $filename =  config('app.bucket_root_file_folder') . "/creator_" . Auth::id() . "/collections_".$this->collection['id'];
        return $filename;
    }
}
