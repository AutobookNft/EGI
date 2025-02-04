<?php


namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;
use App\Enums\NotificationStatus;
use App\Models\User;
use App\Notifications\Wallets\WalletAccepted;
use App\Notifications\Wallets\WalletCreation;
use App\Notifications\Wallets\WalletUpdate;
use App\Notifications\Wallets\WalletRejection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;

class WalletNotificationHandler implements NotificationHandlerInterface
{
    public function handle(User $message_to, $notification)
    {

        $action = NotificationStatus::fromDatabase(value: $notification->status);

        Log::channel('florenceegi')->info('WalletNotificationHandler', [
            '$action' =>$action
        ]);


        try {
            if (NotificationStatus::PENDING_CREATE->value === $action->value) {
                Notification::send($message_to, new WalletCreation($notification));
            } elseif (NotificationStatus::PENDING_UPDATE->value === $action->value) {
                Notification::send($message_to, new WalletUpdate($notification));
            } elseif (NotificationStatus::ACCEPTED->value === $action->value) {
                Notification::send($message_to, new WalletAccepted($notification));
            } elseif (NotificationStatus::REJECTED->value === $action->value) {
                Notification::send($message_to, new WalletRejection($notification));
            }
        } catch (\Exception $e) {

            throw $e;
        }
    }

}
