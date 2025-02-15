<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Base;

/**
 * DTO che rappresenta i dati base di una notifica nel sistema
 */
class NotificationData
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $view,
        public readonly string $notifiable_type,
        public readonly int $notifiable_id,
        public readonly int $sender_id,
        public readonly string $model_type,
        public readonly int $model_id,
        public readonly ?string $outcome = null,
        public readonly ?string $read_at = null
    ) {}
}
