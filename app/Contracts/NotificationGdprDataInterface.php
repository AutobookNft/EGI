<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DataTransferObjects\Notifications\Gdpr\GdprNotificationPayloadData;
use App\Enums\NotificationStatus;

/**
 * Interface NotificationDataInterface
 *
 * Definisce il contratto per i DTO delle notifiche
 */
interface NotificationGdprDataInterface
{
    public function getType(): string;
    public function getOutcome(): NotificationStatus;
    public function getPayload(): GdprNotificationPayloadData;

}
