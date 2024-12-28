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

    // const STATUS_PENDING = 'pending';
    // const STATUS_ACCEPTED = 'accepted';
    // const STATUS_REJECTED = 'rejected';

    // public static function statuses()
    // {
    //     return [
    //         self::STATUS_PENDING,
    //         self::STATUS_ACCEPTED,
    //         self::STATUS_REJECTED,
    //     ];
    // }

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
}

