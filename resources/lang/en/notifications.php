<?php

return [
    // Notification Badge Component
    'badge' => [
        'title' => 'Notifications',
        'aria_label' => 'View notifications',
        'view_all' => 'View all notifications',
        'empty' => [
            'title' => 'No notifications',
            'message' => 'You have not received any notifications yet.'
        ]
    ],

    // Notification Types
    'types' => [
        'reservations' => 'Reservations',
        'gdpr' => 'Privacy',
        'collections' => 'Collections',
        'egis' => 'EGI',
        'wallets' => 'Wallets',
        'invitations' => 'Invitations',
        'general' => 'General',
        'system' => 'System'
    ],

    // Notification Status
    'status' => [
        'read' => 'Read',
        'unread' => 'Unread',
        'archived' => 'Archived'
    ],

    // Notification Actions
    'actions' => [
        'mark_as_read' => 'Mark as read',
        'mark_as_unread' => 'Mark as unread',
        'delete' => 'Delete',
        'archive' => 'Archive'
    ],

    // Time formatting
    'time' => [
        'now' => 'Now',
        'minutes_ago' => ':count minutes ago',
        'hours_ago' => ':count hours ago',
        'days_ago' => ':count days ago',
        'weeks_ago' => ':count weeks ago'
    ]
];
