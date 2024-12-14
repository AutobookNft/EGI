<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Jetstream\Team as JetstreamTeam;

class TeamWallet extends JetstreamTeam
{
    /** @use HasFactory<\Database\Factories\TeamWallet> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'wallet',
        'royalty_mint',
        'royalty_rebind',
        'status',
    ];

    protected $appends = ['short_wallet'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    // Accessor per mostrare solo una parte dell'indirizzo
    public function getShortWalletAttribute()
    {

        if (strlen($this->wallet) == 0) {
            return null;
        }

        return substr($this->wallet, 0, 10) . '...';
    }

}
