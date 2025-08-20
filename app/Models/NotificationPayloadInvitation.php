<?php

namespace App\Models;

use App\Contracts\NotifiablePayload;
use App\Enums\InvitationStatus;
use App\Enums\NotificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Log;

class NotificationPayloadInvitation extends Model implements NotifiablePayload {
    use HasFactory;

    /**
     * La tabella associata al modello.
     *
     * @var string
     */
    protected $table = 'notification_payload_invitations';

    /**
     * I campi che possono essere assegnati in massa.
     *
     * @var array
     */
    protected $fillable = [
        'collection_id',
        'proposer_id',
        'receiver_id',
        'email',
        'role',
        'status',
        'metadata',
    ];

    // protected $casts = [
    //     'status' => InvitationStatus::class
    // ];

    public function getNotificationData(): array {
        return [
            'message' => __('Sei stato invitato a partecipare ad una collezione.'),
            'collection_name' => $this->collection->name,
        ];
    }

    public function getRecipient(): User {
        return User::where('email', $this->email)->firstOrFail();
    }

    public function getModelType(): string {
        return static::class;
    }

    public function getModelId(): int {
        return $this->id;
    }

    /**
     * Relazione con il modello Collection.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function collection() {
        return $this->belongsTo(Collection::class);
    }

    // Helper per determinare lo stato in base all'enum normalizzato
    public function isPending(): bool {
        return $this->status === NotificationStatus::PENDING;
    }

    public function isAccepted(): bool {
        return $this->status === NotificationStatus::ACCEPTED;
    }

    public function isRejected(): bool {
        return $this->status === NotificationStatus::REJECTED;
    }

    /**
     * Ottiene lo status dell'invito.
     *
     * @return string
     */
    public function getStatus(): string {
        return $this->status;
    }

    /**
     * Metodo per gestire l'update
     */
    public function handlePending() {
        // Logica specifica per l'update
        $this->update(['status' => $this->isPending]);
    }

    /**
     * Metodo per gestire l'approvazione
     */
    public function handleApproval() {
        // Logica specifica per l'approvazione
        $this->update(['status' => NotificationStatus::ACCEPTED]);
    }

    /**
     * Metodo per gestire il rifiuto
     */
    public function handleRejection() {
        // Logica specifica per il rifiuto
        $this->update(['status' =>  NotificationStatus::REJECTED]);
    }

    /**
     * Summary of proposer
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function proposer() {
        return $this->belongsTo(User::class, 'proposer_id');
    }

    /**
     * Summary of receiver
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver() {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function notifications(): MorphMany {
        return $this->morphMany(CustomDatabaseNotification::class, 'model');
    }
}
