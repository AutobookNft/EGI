<?php

namespace App\Notifications\Wallets;

use App\DataTransferObjects\Notifications\Wallets\WalletNotificationData;
use App\Notifications\Channels\CustomDatabaseChannel;
use Illuminate\Notifications\Notification;

class WalletRejection extends Notification
{
    private WalletNotificationData $notificationData;

    public function __construct(WalletNotificationData $notificationData)
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
