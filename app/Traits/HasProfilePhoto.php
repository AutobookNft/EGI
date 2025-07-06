<?php

namespace App\Traits;

trait HasProfilePhoto
{
    /**
     * Get the URL to the user's profile photo.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path
            ? $this->profile_photo_path
            : $this->defaultProfilePhotoUrl();
    }

    /**
     * Get the default profile photo URL (fallback to DiceBear).
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        $name = urlencode($this->name ?? 'Anonymous');
        return "https://api.dicebear.com/7.x/bottts/png?seed={$name}&backgroundColor=transparent&size=512";
    }
}
