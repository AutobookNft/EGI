<?php

namespace App\Models;

use App\Contracts\NotifiablePayload;
use App\Enums\InvitationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Collection;
use App\Models\User;

class CollectionInvitation extends Model implements NotifiablePayload
{
    use HasFactory;

    /**
     * La tabella associata al modello.
     *
     * @var string
     */
    protected $table = 'collection_invitations';

    /**
     * I campi che possono essere assegnati in massa.
     *
     * @var array
     */
    protected $fillable = [
        'collection_id',
        'proposal_name',
        'email',
        'role',
        'status',
    ];

    protected $casts = [
        'status' => InvitationStatus::class
    ];

    public function getNotificationData(): array
    {
        return [
            'message' => __('Sei stato invitato a partecipare ad una collezione.'),
            'collection_name' => $this->collection->name,
        ];
    }

    public function getRecipient(): User
    {
        return User::where('email', $this->email)->firstOrFail();
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
     * Relazione con il modello Collection.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

   // Normalizza lo stato direttamente nel modello
   public function getNormalizedStatusAttribute(): string
   {
       // Se status è un enum, estrai il valore string
       $currentStatus = $this->status instanceof InvitationStatus
           ? $this->status->value
           : $this->status;

       // Se lo stato è "proposal" o "pending" (in formato string),
       // lo normalizziamo a "pending", altrimenti usiamo la stringa di default
       return $currentStatus === InvitationStatus::PENDING->value || $currentStatus === 'proposal'
           ? InvitationStatus::PENDING->value
           : $currentStatus;
   }

    // Ritorna l'enum normalizzato
    public function getStatusEnumAttribute(): InvitationStatus
    {
        return InvitationStatus::fromDatabase($this->normalized_status);
    }

    // Helper per determinare lo stato in base all'enum normalizzato
    public function isPending(): bool
    {
        return $this->status_enum === InvitationStatus::PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status_enum === InvitationStatus::ACCEPTED;
    }

    public function isRejected(): bool
    {
        return $this->status_enum === InvitationStatus::REJECTED;
    }
}
