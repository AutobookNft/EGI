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
    'gdpr' => [
        // Interactive notifications (require user action)
        'consent_updated' => [
            'view' => 'notifications.gdpr.generic_alert',
            'render' => 'include',
            'type' => 'interactive', // ← Extra metadata
        ],

        // Informational notifications (read-only)
        'data_exported' => [
            'view' => 'notifications.gdpr.informational',
            'render' => 'include',
            'type' => 'informational',
        ],
        'processing_restricted' => [
            'view' => 'notifications.gdpr.informational',
            'render' => 'include',
            'type' => 'informational',
        ],
        'account_deletion_requested' => [
            'view' => 'notifications.gdpr.informational',
            'render' => 'include',
            'type' => 'informational',
        ],
        'account_deletion_processed' => [
            'view' => 'notifications.gdpr.informational',
            'render' => 'include',
            'type' => 'informational',
        ],
        'breach_report_received' => [
            'view' => 'notifications.gdpr.informational',
            'render' => 'include',
            'type' => 'informational',
        ],
        'default' => [
            'view' => 'notifications.gdpr.default',
            'render' => 'include',
            'type' => 'informational',
        ],
    ],
];
