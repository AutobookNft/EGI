<?php

namespace App\Services\Notifications;

use App\Contracts\NotificationHandlerInterface;
use App\Enums\NotificationStatus;
use App\Notifications\Invitations\InvitationAccepted;
use App\Models\User;
use App\Notifications\Invitations\InvitationRejection;
use App\Notifications\Invitations\InvitationRequest;
use Illuminate\Support\Facades\Notification;


class InvitationNotificationHandler implements NotificationHandlerInterface
{
    public function handle(User $message_to, $notification)
    {

        $status = NotificationStatus::fromDatabase($notification->status);

        try {
            if (NotificationStatus::ACCEPTED->value === $status->value) {
                Notification::send($message_to, new InvitationAccepted($notification));
            } elseif (NotificationStatus::REJECTED->value === $status->value) {
                Notification::send($message_to, new InvitationRejection($notification));
            } elseif (NotificationStatus::PENDING->value === $status->value) {
                Notification::send($message_to, new InvitationRequest($notification));
            }
        } catch (\Exception $e) {

            throw $e;
        }
    }
}

