<?php

namespace App\Media;

use App\Models\Collection as EGICollection;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class CustomPathGenerator extends DefaultPathGenerator {
    /**
     * Base path for the original media file.
     */
    public function getPath(Media $media): string {
        if ($media->model_type === EGICollection::class) {
            $collection = EGICollection::find($media->model_id);
            if ($collection) {
                $path = sprintf(
                    'users_files/collections_%d/creator_%d/head/',
                    $collection->id,
                    $collection->creator_id
                );
                Log::channel('egi_upload')->info('CustomPathGenerator:getPath', [
                    'media_id' => $media->id,
                    'path' => $path,
                ]);
                return $path;
            }
        }

        return parent::getPath($media);
    }

    /**
     * Path for conversions: keep in a "conversions" subfolder inside head.
     */
    public function getPathForConversions(Media $media): string {
        if ($media->model_type === EGICollection::class) {
            $base = $this->getPath($media);
            $path = $base . 'conversions/';
            Log::channel('egi_upload')->info('CustomPathGenerator:getPathForConversions', [
                'media_id' => $media->id,
                'path' => $path,
            ]);
            return $path;
        }

        return parent::getPathForConversions($media);
    }
}
