<?php


return [
    'invitations' => [
        'request' => [
            'view'=>'notifications.invitations.request',
            'render'=>'livewire',
        ],

        'rejected' => [
            'view'=>'notifications.invitations.approval',
            'render'=>'livewire',
        ],
        'accepted' => [
            'view'=>'notifications.invitations.approval',
            'render'=>'livewire',
        ],
    ],
    'wallets' => [
        'creation' => [
            'view'=>'notifications.wallets.creation',
            'render'=>'livewire',
        ],
        'accepted' => [
            'view'=>'notifications.wallets.accepted',
            'render'=>'livewire',
        ],
        'change-request' => [
            'view'=>'livewire.notifications.wallets.change-request',
            'render'=>'include',
        ],
        'change-response-rejected' => [
            'view'=>'livewire.notifications.wallets.change-response-rejected',
            'render'=>'include',
        ],
    ],
];
