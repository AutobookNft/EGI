<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class WalletChangeApprovalModel extends Model
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
        'id',
        'wallet_id',
        'proposer_id',
        'riceiver_id',
        'wallet',
        'platform_role',
        'royalty_mint',
        'royalty_rebind',
        'status',
        'type', // Nuovo campo

    ];

    /**
     * Metodo per gestire la creazione
     */
    public function handleCreation()
    {
        // Logica specifica per la creazione
        $this->update(['status' => 'created']);
    }

    /**
     * Metodo per gestire l'update
     */
    public function handleUpdate()
    {
        // Logica specifica per l'update
        $this->update(['status' => 'updated']);
    }

    /**
     * Metodo per gestire l'approvazione
     */
    public function handleApproval()
    {
        // Logica specifica per l'approvazione
        $this->update(['status' => 'approved']);
    }

    /**
     * Metodo per gestire il rifiuto
     */
    public function handleRejection()
    {
        // Logica specifica per il rifiuto
        $this->update(['status' => 'rejected']);
    }

    /**
     * Relazione con il modello Wallet.
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Relazione polimorfica con il modello CustomDatabaseNotification.
     * si tratta di un modello per peronsalizzare Notification
     *
     * Colonne implicite: model_type e model_id
    */
    public function notifications(): MorphMany
    {
        return $this->morphMany(CustomDatabaseNotification::class, 'model');
    }



}
