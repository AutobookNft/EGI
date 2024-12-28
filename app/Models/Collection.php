<?php

namespace App\Models;

use App\Casts\EGIImageCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use HasFactory;
    use SoftDeletes; // Gestione SoftDeletes

    /**
     * Gli attributi assegnabili in massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'creator_id',
        'owner_id',
        'collection_name',
        'description',
        'type',
        'status',
        'is_published',
        'image_banner',
        'image_card',
        'image_avatar',
        'url_collection_site',
        'position',
        'EGI_number',
        'EGI_asset_roles',
        'floor_price',
        'path_image_to_ipfs',
        'url_image_ipfs',
        'epp_id',
        'EGI_asset_id',
    ];

    /**
     * Gli attributi che devono essere castati.
     *
     * @var array
     */
    protected $casts = [
        'image_banner' => EGIImageCast::class,
        'image_card'   => EGIImageCast::class,
        'image_avatar' => EGIImageCast::class,
        'is_published' => 'boolean',
    ];

    /**
     * Relazione con il creator.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Relazione con l'owner.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relazione con gli EGI.
     */
    public function egis()
    {
        return $this->hasMany(Egi::class);
    }

    /**
     * Relazione con gli utenti tramite la tabella pivot collection_user.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'collection_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Relazione con i wallet.
     */
    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    /**
     * Verifica se la collection è pubblicata.
     *
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Verifica se la collection può essere pubblicata.
     *
     * @return bool
     */
    public function canBePublished(): bool
    {
        $pendingApprovals = WalletChangeApproval::whereHas('wallet', function ($query) {
            $query->where('collection_id', $this->id);
        })->where('status', 'pending')->exists();

        return !$pendingApprovals && $this->status === 'draft';
    }
}
