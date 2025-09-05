<?php

namespace App\DataTransferObjects\Notifications\Invitations;

use App\Contracts\InvitationNotificationDataInterface;
use Illuminate\Support\Facades\Log;

class InvitationNotificationData implements InvitationNotificationDataInterface
{
    public function __construct(
        private readonly int $collection_id,
        private readonly ?int $proposerId = null,
        private readonly int $receiverId,
        private readonly ?string $email = '',
        private readonly string $role,
        private readonly ?bool $is_owner = null,
        private readonly ?string $joined_at = null,
        private readonly string $status,
        private readonly ?array $metadata = null
    ) {}

    public function toPayloadInArray(): array
    {

        Log::channel('florenceegi')->info('DTO: InvitationNotificationData:toPayloadInArray', [
            'collection_id' => $this->collection_id,
            'proposer_id' => $this->proposerId,
            'receiver_id' => $this->receiverId,
            'email' => $this->email,
            'role' => $this->role,
            'is_owner' => $this->is_owner ?? false,
            'joined_at' => now(),
            'status' => $this->status,
            'metadata' => $this->metadata,
        ]);

        return [
            'collection_id' => $this->collection_id,
            'proposer_id' => $this->proposerId,
            'receiver_id' => $this->receiverId,
            'email' => $this->email,
            'role' => $this->role,
            'status' => $this->status,
            'metadata' => $this->metadata,
        ];
    }

    public function toCollectionUser(): array
    {
        return [
            'collection_id' => $this->collection_id,
            'user_id' => $this->receiverId,
            'role' => $this->role,
            'is_owner' => $this->is_owner ?? false,
            'joined_at' => now(),
            'status' => $this->status,
            'metadata' => $this->metadata
        ];
    }


    public function getCollectionId(): int
    {
        return $this->collection_id;
    }

    public function getProposerId(): ?int
    {
        return $this->proposerId;
    }

    public function getReceiverId(): int
    {
        return $this->receiverId;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function getIsOwner(): ?bool
    {
        return $this->is_owner;
    }

    public function getJoinedAt(): ?string
    {
        return $this->joined_at;
    }
}