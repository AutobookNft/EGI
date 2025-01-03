<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UsesUuid;

class Notification extends Model
{
    use UsesUuid;

    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'outcome',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array', // Questo converte automaticamente il JSON in un array associativo
    ];

}
