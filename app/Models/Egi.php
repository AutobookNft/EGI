<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Egi extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'collection_id', 'key_file', 'token_EGI', 'jsonMetadata', 'user_id',
        'auction_id', 'owner_id', 'drop_id', 'upload_id', 'creator', 'owner_wallet',
        'drop_title', 'title', 'description', 'extension', 'media', 'type', 'bind',
        'paired', 'price', 'floorDropPrice', 'position', 'creation_date', 'size',
        'dimension', 'show', 'mint', 'rebind', 'file_crypt', 'file_hash',
        'file_IPFS', 'file_mime', 'status', 'is_public', 'updated_by'
    ];

    public static function boot()
    {
        parent::boot();

        static::updated(function ($egi) {
            $egi->audits()->create([
                'user_id' => auth()->id(),
                'old_values' => $egi->getOriginal(),
                'new_values' => $egi->getChanges(),
                'action' => 'update',
            ]);
        });
    }

    public function audits()
    {
        return $this->hasMany(EgiAudit::class);
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
}
