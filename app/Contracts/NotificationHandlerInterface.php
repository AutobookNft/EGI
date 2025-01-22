<?php

namespace App\Contracts;

use Illuminate\Notifications\DatabaseNotification;
use App\Models\User;

interface NotificationHandlerInterface
{
    public function handle(User $message_to, DatabaseNotification $notification);
}
