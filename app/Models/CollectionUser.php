<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionUser extends Model
{
    use HasFactory;

    protected $table = 'collection_user';

    protected $fillable = [
        'collection_id',
        'user_id',
        'role',
        'is_owner',
        'joined_at',
        'removed_at',
        'metadata',
        'status',
    ];


    protected $casts = [
        'is_owner' => 'boolean',
        'joined_at' => 'datetime',
        'removed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relazione con il modello Wallet.
     * Supponendo che la tabella `wallets` abbia una colonna `collection_user_id`
     * per identificare il wallet associato al CollectionUser.
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'user_id', 'id');
    }

    /**
     * Relazione uno a molti con NotificationPayloadWallet
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notificationPayloadWallets()
    {
        return $this->hasMany(NotificationPayloadWallet::class, 'receiver_id', 'user_id');
    }
}

