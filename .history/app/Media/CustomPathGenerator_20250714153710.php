<?php

namespace App\Media;

use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class CustomPathGenerator extends DefaultPathGenerator
{
    public function getPathForConversions(Media $media): string
    {
        Log::channel('egi_upload')->info('Creating conversions path for media', ['media_id' => $media->id]);
        return parent::getPathForConversions($media);
    }
}
