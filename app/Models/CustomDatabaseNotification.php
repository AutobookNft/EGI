<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CustomDatabaseNotification extends DatabaseNotification
{
    protected $table = 'notifications';

    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'model_type',
        'model_id',
        'data',
        'read_at',
        'created_at',
        'updated_at',
    ];

    public $incrementing = false; // se usi UUID


    /**
     * Summary of model
     * Relazione polimorfica con il modello associato alla notifica
     * questo modello a cui può essere asscioata la notifica costituisce il payload della notifica
     * Esempio di utilizzo: model_type: App\Notifications\WalletChangeRequest model_id: 30
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }
}
