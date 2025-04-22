<?php

namespace App\Models;

use App\Enums\NotificationStatus;
use App\Enums\WalletStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class NotificationPayloadWallet extends Model
{
    use HasFactory;

    /**
     * La tabella associata al modello.
     *
     * @var string
     */
    protected $table = 'notification_payload_wallets';

    /**
     * Gli attributi assegnabili in massa.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'collection_id',
        'proposer_id',
        'receiver_id',
        'wallet',
        'platform_role',
        'royalty_mint',
        'royalty_rebind',
        'status',
        'type',

    ];

    /**
     * Metodo per gestire la creazione
     */
    public function handleCreation()
    {
        // Logica specifica per la creazione
        $this->update([
            'status' => NotificationStatus::PENDING_CREATE->value,
            'type'   => NotificationStatus::CREATION->value

        ]);
    }

    /**
     * Metodo per gestire l'update
     */
    public function handleUpdate()
    {
        // Logica specifica per l'update
        $this->update([
            'status' =>  NotificationStatus::PENDING_UPDATE->value,
            'type'   =>  NotificationStatus::UPDATE->value
        ]);
    }

    /**
     * Metodo per gestire l'approvazione
     */
    public function handleAccepted()
    {
        // Logica specifica per l'approvazione
        $this->update([
            'status'    =>  NotificationStatus::ACCEPTED->value,
        ]);
    }

    /**
     * Metodo per gestire il rifiuto
     */
    public function handleRejection()
    {
        // Logica specifica per il rifiuto
        $this->update([
            'status' =>  NotificationStatus::REJECTED->value
        ]);
    }


    // Helper per determinare lo stato in base all'enum normalizzato
    public function isPending(): bool
    {
        return $this->status === NotificationStatus::PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status === NotificationStatus::ACCEPTED;
    }

    public function isRejected(): bool
    {
        return $this->status === NotificationStatus::REJECTED;
    }

    /**
     * Relazione con il modello Wallet.
     */
    public function walletModel()
    {
        return $this->hasOne(Wallet::class, 'notification_payload_wallets_id', 'id');
    }


    public function getModelType(): string
    {
        return static::class;
    }

    public function getModelId(): int
    {
        return $this->id;
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

    /**
     * Relazione con il modello User.
     */
    public function proposer()
    {
        return $this->belongsTo(User::class, 'proposer_id');
    }

    /**
     * Relazione con il modello User.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Relazione con CollectionUser
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function collectionUser()
    {
        return $this->belongsTo(CollectionUser::class, 'receiver_id', 'user_id');
    }

    public function collection(){
        return $this->belongsTo(Collection::class);
    }


}
