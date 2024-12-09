<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Collection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'collection_name',
        'show',
        'personal_team',
        'creator',
        'owner_wallet',
        'address',
        'epp_id',
        'EGI_asset_id',
        'description',
        'type',
        'path_image_banner',
        'path_image_card',
        'path_image_avatar',
        'path_image_EGI',
        'url_collection_site',
        'position',
        'token',
        'owner_id',
        'EGI_number',
        'EGI_asset_roles',
        'floor_price',
        'path_image_to_ipfs',
        'url_image_ipfs',
    ];

    protected $appends = [
        'verified_image_card_path',
        'verified_image_banner_path',
        'verified_image_avatar_path',
    ];

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_collection');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, Team::class, 'id', 'id', 'team_id', 'user_id');
    }

    /**
     * Accessor per verificare se il file esiste fisicamente e restituire il percorso.
     */
    public function getVerifiedImageCardPathAttribute()
    {
        $path = $this->path_image_card;

        // Verifica se il file esiste nel disco pubblico
        if ($path && Storage::disk('public')->exists($path)) {
            return $path; // Restituisci l'URL accessibile pubblicamente
        }

        return null; // Restituisci null se il file non esiste
    }

    public function getVerifiedImageBannerPathAttribute()
    {
        $path = $this->path_image_banner;

        // Verifica se il file esiste nel disco pubblico
        if ($path && Storage::disk('public')->exists($path)) {
            return $path; // Restituisci l'URL accessibile pubblicamente
        }

        return null; // Restituisci null se il file non esiste
    }

    public function getVerifiedImageAvatarPathAttribute()
    {
        $path = $this->path_image_avatar;

        // Verifica se il file esiste nel disco pubblico
        if ($path && Storage::disk('public')->exists($path)) {
            return $path; // Restituisci l'URL accessibile pubblicamente
        }

        return null; // Restituisci null se il file non esiste
    }

}
