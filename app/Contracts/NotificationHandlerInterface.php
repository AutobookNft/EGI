<?php

namespace App\Contracts;

use Illuminate\Notifications\DatabaseNotification;

interface NotificationHandlerInterface
{
    public function handle(DatabaseNotification $notification, string $action);
}
