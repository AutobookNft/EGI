<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Log;

class CustomDatabaseNotification extends DatabaseNotification
{
    protected $table = 'notifications';

    protected $fillable = [
        'id',
        'type',
        'view',
        'notifiable_type',
        'notifiable_id',
        'sender_id',
        'model_type',
        'model_id',
        'data',
        'read_at',
        'created_at',
        'updated_at',
        'outcome',
    ];

    public $incrementing = false; // se usi UUID

    /**
     * Summary of model
     * Relazione polimorfica la notifica viene associata a un modello specifico
     * questo modello specifico costituisce il payload della notifica
     * Esempio di utilizzo: model_type: App\Models\NotificationPayloadWallet - model_id: 30
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo('notifiable', 'notifiable_type', 'notifiable_id');
    }

    /**
     * Recupera il wallet associato alla notifica.
     *
     * @return string|null
     */
    public function getWalletAttribute(): ?string
    {
        return $this->model instanceof NotificationPayloadWallet ? $this->model->wallet : null;
    }

}
