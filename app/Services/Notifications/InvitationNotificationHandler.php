<?php


namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;
use App\Enums\InvitationStatus;
use App\Notifications\Channels\CustomDatabaseChannel;
use App\Notifications\InvitationAccepted;
use App\Models\User;
use App\Notifications\InvitationRejection;
use App\Notifications\InvitationRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;

class InvitationNotificationHandler implements NotificationHandlerInterface
{
    public function handle(User $message_to, $notification)
    {

        $status = InvitationStatus::fromDatabase($notification->status);

        try {
            if (InvitationStatus::ACCEPTED->value === $status->value) {
                Notification::send($message_to, new InvitationAccepted($notification));
            } elseif (InvitationStatus::REJECTED->value === $status->value) {
                Notification::send($message_to, new InvitationRejection($notification));
            } elseif (InvitationStatus::PENDING->value === $status->value) {
                Notification::send($message_to, new InvitationRequest($notification));
            }
        } catch (\Exception $e) {

            throw $e;
        }
    }


}

