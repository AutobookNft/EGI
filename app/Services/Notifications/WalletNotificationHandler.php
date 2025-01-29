<?php


namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;
use App\Enums\WalletStatus;
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

        $action = WalletStatus::fromDatabase($notification->type);

        try {
            if (WalletStatus::CREATION->value === $action->value) {
                Notification::send($message_to, new WalletCreation($notification));
            } elseif (WalletStatus::UPDATE->value === $action->value) {
                Notification::send($message_to, new WalletUpdate($notification));
            } elseif (WalletStatus::ACCEPTED->value === $action->value) {
                Notification::send($message_to, new WalletAccepted($notification));
            } elseif (WalletStatus::REJECTED->value === $action->value) {
                Notification::send($message_to, new WalletRejection($notification));
            }
        } catch (\Exception $e) {

            throw $e;
        }
    }

}
