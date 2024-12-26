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

            $collection = Collection::findOrFail($this->collectionId);

            Log::channel('florenceegi')->info('Current collection', [
                'collection' => json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            ]);

            Log::channel('florenceegi')->info('Modified collection', [
                'collection' => json_encode($this->collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            ]);

            $collection->update($this->collection);

            session()->flash('message', __('collection.updated_successfully'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::channel('florenceegi')->error('Validation error during update', $e->errors());
            session()->flash('error', __('collection.update.validation_error'));
            throw $e;

        } catch (ModelNotFoundException $e) {
            Log::channel('florenceegi')->error('Collection not found during update', ['collection_id' => $this->collectionId]);
            session()->flash('error', __('collection.not_found'));

        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('Failed to update collection', [
                'error' => $e->getMessage(),
                'collection_data' => $this->collection,
                'stack_trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', __('collection.save_failed'));
        }
    }

}
