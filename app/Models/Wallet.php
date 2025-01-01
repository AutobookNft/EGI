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
        'wallet',
        'royalty_mint',
        'royalty_rebind',
        'is_anonymous',
        'metadata',
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


}
