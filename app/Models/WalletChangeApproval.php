<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletChangeApproval extends Model
{
    use HasFactory;

    /**
     * La tabella associata al modello.
     *
     * @var string
     */
    protected $table = 'wallet_change_approvals';

    /**
     * Gli attributi assegnabili in massa.
     *
     * @var array
     */
    protected $fillable = [
        'wallet_id',
        'requested_by_user_id',
        'approver_user_id',
        'change_type',
        'change_details',
        'status',
        'rejection_reason',
    ];

    /**
     * Gli attributi che devono essere castati.
     *
     * @var array
     */
    protected $casts = [
        'change_details' => 'array', // Il campo JSON viene automaticamente convertito in array
    ];

    /**
     * Relazione con il modello Wallet.
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Relazione con il modello User per l'utente che ha richiesto la modifica.
     */
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    /**
     * Relazione con il modello User per l'utente che approva la modifica.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }
}
