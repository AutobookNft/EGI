<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Notifications\Gdpr;

/**
 * Value Object che rappresenta il payload specifico del consenso GDPR.
 */
class GdprNotificationPayloadData
{
    public function __construct(
        private readonly ?string $consent_type = '',
        private readonly ?string $gdpr_notification_type = null,
        private readonly mixed $previous_value = null,
        private readonly mixed $new_value = null,
        private readonly ?string $message = '',
        private readonly ?string $email = null,
        private readonly ?string $role = 'creator',
        private readonly ?string $ip_address = null,
        private readonly ?string $user_agent = null,
        private readonly ?string $payload_status = null,
    ) {}

    public function toArray(): array
    {
        return [
            'consent_type' => $this->consent_type,
            'gdpr_notification_type' => $this->gdpr_notification_type,
            'previous_value' => $this->previous_value,
            'new_value' => $this->new_value,
            'message' => $this->message,
            'email' => $this->email,
            'role' => $this->role,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
        ];
    }
}


