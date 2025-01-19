<?php

namespace App\Contracts;

use Illuminate\Notifications\DatabaseNotification;
use App\Models\User;

interface NotificationHandlerInterface
{
    public function handle(User $receiver, DatabaseNotification $notification);
}
