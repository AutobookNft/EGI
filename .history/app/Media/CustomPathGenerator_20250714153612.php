<?php

namespace App\Media;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class CustomPathGenerator extends DefaultPathGenerator
{
    public function getPathForConversions(Media $media): string
    {
        \Log::info('Creating conversions path for media', ['media_id' => $media->id]);
        return parent::getPathForConversions($media);
    }
}
