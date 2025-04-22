<?php


return [
    'invitations' => [
        'accepted' => [
            'view'=>'notifications.invitations.approval',
            'render'=>'livewire',
        ],

        'request' => [
            'view'=>'notifications.invitations.request',
            'render'=>'livewire',
        ],

        'rejected' => [
            'view'=>'notifications.invitations.rejected',
            'render'=>'livewire',
        ],
    ],
    'wallets' => [
        'creation' => [
            'view' => 'notifications.wallets.creation',
            'render' => 'controller',
            'controller' => 'App\\Http\\Controllers\\Notifications\Wallets\\NotificationWalletResponseController'
        ],
        'accepted' => [
            'view'=>'notifications.wallets.accepted',
            'render'=>'livewire',
        ],
        'rejected' => [
            'view'=>'notifications.wallets.rejected',
            'render' => 'controller',
            'controller' => 'App\\Http\\Controllers\\Notifications\Wallets\\NotificationWalletResponseController'
        ],
        'pending_create' => [
            'view'=>'notifications.wallets.creation',
            'render' => 'controller',
            'controller' => 'App\\Http\\Controllers\\Notifications\Wallets\\NotificationWalletResponseController'
        ],
        'expired' => [
            'view'=>'notifications.wallets.update',
            'render' => 'controller',
            'controller' => 'App\\Http\\Controllers\\Notifications\Wallets\\NotificationWalletResponseController'
        ],
        'pending_update' => [
            'view'=>'notifications.wallets.creation',
            'render' => 'controller',
            'controller' => 'App\\Http\\Controllers\\Notifications\Wallets\\NotificationWalletResponseController'
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
