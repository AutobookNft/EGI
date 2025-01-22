<?php

namespace App\Services\Notifications;


class NotificationRegistry {
    public static function getComponent($notificationType) {
        return [
            'App\Notifications\WalletChangeRequestCreation' => [
                'view'=>'notifications.wallet-change-request',
                'render_type'=>'include',
            ],
            'App\Notifications\WalletChangeResponseRejection' => [
                'view'=>'notifications.wallet-change-response-rejected',
                'render_type'=>'include',
            ],
            'notifications.wallet-change-response-rejected' => [
                'view'=>'notifications.wallet-change-response-rejected',
                'render_type'=>'include',
            ],
            'App\Notifications\InvitationProposal' => [
                'view'=>'notifications.invitation',
                'render_type'=>'livewire',
            ],
            'App\Notifications\InvitationApproval' => [
                'view'=>'livewire.notifications.invitations.approval',
                'render_type'=>'include',
            ],
            'App\Notifications\InvitationRejection' => [
                'view'=>'livewire.notifications.invitations.approval',
                'render_type'=>'include',
            ],
            'App\Livewire\Proposals\ProposalDeclinedNotification' => [
                'view'=>'notifications.proposa-declined-notification',
                'render_type'=>'include',
            ],

        ][$notificationType] ?? null;
    }
}
