<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @Oracode Model: User Profile Data
 * 🎯 Purpose: Manages public profile information and social links
 * 🛡️ Privacy: Contains non-sensitive, publicly shareable user data
 * 🧱 Core Logic: Handles social media integration and professional identity
 */
class UserProfile extends Model
{
    protected $fillable = [
        'user_id', 'title', 'job_role', 'site_url', 'facebook', 'social_x',
        'tiktok', 'instagram', 'snapchat', 'twitch', 'linkedin', 'discord',
        'telegram', 'other', 'profile_photo_path', 'annotation'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getSocialLinksAttribute(): array
    {
        return array_filter([
            'website' => $this->site_url,
            'facebook' => $this->facebook,
            'twitter' => $this->social_x,
            'tiktok' => $this->tiktok,
            'instagram' => $this->instagram,
            'snapchat' => $this->snapchat,
            'twitch' => $this->twitch,
            'linkedin' => $this->linkedin,
            'discord' => $this->discord,
            'telegram' => $this->telegram,
            'other' => $this->other
        ]);
    }

    public function hasCompletedProfile(): bool
    {
        return !empty($this->job_role) || !empty($this->annotation) || count($this->social_links) > 0;
    }
}