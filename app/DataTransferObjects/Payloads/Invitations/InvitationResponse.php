<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Payloads\Invitations;

/**
 * DTO per la risposta alle operazioni sui wallet
 */
class InvitationResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $status,
        public readonly ?string $message = null
    ) {}

    public static function success(
        string $status
    ): self {
        return new self(
            success: true,
            status: $status
        );
    }

    public static function error(
        string $message
    ): self {
        return new self(
            success: false,
            status: 'error',
            message: $message
        );
    }

    /**
     * Converte il DTO in un array per la risposta JSON
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'status' => $this->status,
            'message' => $this->message
        ];

    }
}
