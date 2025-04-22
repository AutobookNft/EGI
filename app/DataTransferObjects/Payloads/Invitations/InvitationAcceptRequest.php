<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Payloads\Invitations;

use App\Enums\NotificationStatus;
use App\Models\NotificationPayloadInvitation;
use Illuminate\Support\Facades\Log;

/**
 * DTO per la richiesta di accettazione di un wallet
 */
class InvitationAcceptRequest
{
    public function __construct(
        public readonly string $id,
        public readonly int $collection_id,
        public readonly int $wallet_payload_id,
        public readonly int $receiver_id,
        public readonly int $receiver_email,
        public readonly int $role,
        public readonly int $metadata,
        public readonly string $status
    ) {}

    public static function fromNotification(NotificationPayloadInvitation $notification): self
    {

        return new self(
            id: $notification->id,
            collection_id: $notification->collection_id,
            wallet_payload_id: $notification->id,
            receiver_id: $notification->receiver_id,
            receiver_email: $notification->email,
            role: $notification->role,
            metadata: $notification->metadata,
            status:NotificationStatus::ACCEPTED->value
        );
    }
}


