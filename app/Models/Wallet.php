<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'collection_id',
        'user_id',
        'notification_payload_wallets_id',
        'wallet',
        'royalty_mint',
        'royalty_rebind',
        'is_anonymous',
        'metadata',
        'platform_role',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
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

    // /**
    //  * Relazione uno a molti con NotificationPayloadWallet
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\HasMany
    //  */
    // public function notificationPayloadWallets()
    // {
    //     return $this->hasMany(NotificationPayloadWallet::class, 'id', 'notification_payload_wallets_id');
    // }

    public function notificationPayloadWallet()
    {
        return $this->belongsTo(NotificationPayloadWallet::class, 'notification_payload_wallets_id', 'id');
    }

    public function notificationPayload()
    {
        return $this->belongsTo(NotificationPayloadWallet::class, 'notification_payload_wallets_id', 'id');
    }

}
