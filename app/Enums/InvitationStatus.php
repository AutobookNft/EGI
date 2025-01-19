<?php

namespace App\Enums;

enum InvitationStatus: string {
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    public static function fromDatabase(string $value): self
    {
        return match($value) {
            'pending' => self::PENDING,
            'accepted' => self::ACCEPTED,
            'rejected' => self::REJECTED,
            default => throw new \ValueError("Status '$value' non valido")
        };
    }
}
