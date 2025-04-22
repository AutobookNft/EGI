<?php

namespace App\Notifications\Wallets;

use App\DataTransferObjects\Notifications\NotificationData;
use App\Notifications\Channels\CustomDatabaseChannel;
use Illuminate\Notifications\Notification;

class WalletRejection extends Notification
{
    private NotificationData $notificationData;

    public function __construct(NotificationData $notificationData)
    {
        $this->notificationData = $notificationData;
    }

    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class];
    }

    public function toCustomDatabase($notifiable)
    {
        return $this->notificationData->toNotificationData();
    }
}
