<?php

namespace App\Casts;

use App\Services\EGIImageService;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EGIImageCast
 *
 * Custom cast to transform image filenames into full URLs using EGIImageService.
 */
class EGIImageCast implements CastsAttributes
{
    /**
     * Cast the given value to an image URL.
     *
     * @param  Model  $model
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string|null
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if (!$value) {
            return null;
        }

        // Determine the path key based on the field name.
        $pathKey = match ($key) {
            'image_banner' => 'head.banner',
            'image_card'   => 'head.card',
            'image_avatar' => 'head.avatar',
            'image_EGI'    => 'head.EGI_asset',
            default        => 'head.root',
        };

        // Retrieve the image URL using EGIImageService.
        return EGIImageService::getCachedEGIImagePath(
            $model->id,
            $value,
            $model->is_published,
            null,
            $pathKey
        );
    }

    /**
     * Prepare the given value for storage (no transformation).
     *
     * @param  Model  $model
     * @param  string $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        // Return the value as is for storage.
        return $value;
    }
}
