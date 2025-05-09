<?php

namespace App\Models;

use App\Casts\EGIImageCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
        'id',
        'creator_id',
        'owner_id',
        'collection_name',
        'is_default',
        'description',
        'type',
        'status',
        'is_published',
        'image_banner',
        'image_card',
        'image_avatar',
        'image_egi',
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
        'image_EGI'    => EGIImageCast::class,
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
        $pendingApprovals = NotificationPayloadWallet::whereHas('wallet', function ($query) {
            $query->where('collection_id', $this->id);
        })->where('status', 'pending')->exists();

        return !$pendingApprovals && $this->status === 'published';
    }

    public function epp()
    {
        return $this->belongsTo(Epp::class, 'epp_id');
    }

/**
     * Definisce la relazione polimorfica: una Collection può avere molti Like.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function likes(): MorphMany
    {
        // Il secondo argomento 'likeable' deve corrispondere al nome usato
        // nel metodo morphs() nella migration della tabella likes.
        return $this->morphMany(Like::class, 'likeable');
    }

     /**
     * Definisce la relazione: una Collection ha molte Reservations ATTRAVERSO i suoi Egi.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function reservations(): HasManyThrough
    {
        // Spiegazione parametri:
        // 1°: Modello finale che vogliamo ottenere (Reservation)
        // 2°: Modello intermedio attraverso cui passiamo (Egi)
        // 3°: Chiave esterna sul modello intermedio (Egi) che si riferisce a questo (Collection) -> 'collection_id'
        // 4°: Chiave esterna sul modello finale (Reservation) che si riferisce al modello intermedio (Egi) -> 'egi_id'
        // 5°: Chiave locale di questo modello (Collection) -> 'id' (usata per matchare il 3° parametro)
        // 6°: Chiave locale del modello intermedio (Egi) -> 'id' (usata per matchare il 4° parametro)
        return $this->hasManyThrough(
            Reservation::class,
            Egi::class,
            'collection_id', // Foreign key on the intermediate table (egis)
            'egi_id',        // Foreign key on the final table (reservations)
            'id',            // Local key on this table (collections)
            'id'             // Local key on the intermediate table (egis)
        );
    }

}
