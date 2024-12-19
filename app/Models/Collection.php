<?php

namespace App\Models;

use App\Casts\EGIImageCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use HasFactory;
    use SoftDeletes; // Aggiungi il trait SoftDeletes

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_id',
        'creator_id',
        'owner_id',
        'collection_name',
        'description',
        'type',
        'is_published',
        'path_image_banner',
        'path_image_card',
        'path_image_avatar',
        'path_image_EGI',
        'url_collection_site',
        'position',
        'token',
        'EGI_number',
        'EGI_asset_roles',
        'floor_price',
        'path_image_to_ipfs',
        'url_image_ipfs',
        'epp_id',
        'EGI_asset_id',
        'owner_wallet',
        'address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'image_banner' => EGIImageCast::class,
        'image_card'   => EGIImageCast::class,
        'image_avatar' => EGIImageCast::class,
        'image_egi'    => EGIImageCast::class,
        'is_published' => 'boolean',
    ];
    // Relazione con il team
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    // Relazione con il creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    // Relazione con l'owner
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function egis()
    {
        return $this->hasMany(Egi::class);
    }


}
