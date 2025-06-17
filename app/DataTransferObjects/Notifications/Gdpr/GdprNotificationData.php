<?php

// declare(strict_types=1);

namespace App\DataTransferObjects\Notifications\Gdpr;

use App\Contracts\NotificationGdprDataInterface;
use App\Enums\NotificationStatus;

/**
 * DTO principale per le notifiche GDPR, include dati generali e payload specifico.
 */
final class GdprNotificationData implements NotificationGdprDataInterface
{
    public function __construct(
        private readonly ?string $type = null,
        private readonly ?NotificationStatus $outcome = null,
        private readonly ?GdprNotificationPayloadData $payload = null,
    ) {}

    // Implementazione dei metodi dell'interfaccia NotificationGdprDataInterface
    public function getType(): string
    {
        return $this->type;
    }

    public function getOutcome(): NotificationStatus
    {
        return $this->outcome;
    }
    public function getPayload(): GdprNotificationPayloadData
    {
        return $this->payload;
    }


    // Conversione in array per la notifica (dati generali)
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'outcome' => $this->outcome,
        ];
    }

    // Conversione in array per il payload specifico
    // public function toPayloadArray(): array
    // {
    //     return $this->payload->toArray();
    // }
}
