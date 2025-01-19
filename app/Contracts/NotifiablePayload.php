<?php

namespace App\Contracts;

use App\Models\User;

interface NotifiablePayload {
    public function getNotificationData(): array;
    public function getRecipient(): User;
    public function getModelType(): string;
    public function getModelId(): int;
}
