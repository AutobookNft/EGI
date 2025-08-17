<?php

return [
    // Notification Badge Component
    'badge' => [
        'title' => 'Notifiche',
        'aria_label' => 'Visualizza notifiche',
        'view_all' => 'Visualizza tutte le notifiche',
        'empty' => [
            'title' => 'Nessuna notifica',
            'message' => 'Non hai ancora ricevuto notifiche.'
        ]
    ],

    // Notification Types
    'types' => [
        'reservations' => 'Prenotazioni',
        'gdpr' => 'Privacy',
        'collections' => 'Collezioni',
        'egis' => 'EGI',
        'wallets' => 'Portafogli',
        'invitations' => 'Inviti',
        'general' => 'Generale',
        'system' => 'Sistema'
    ],

    // Notification Status
    'status' => [
        'read' => 'Letta',
        'unread' => 'Non letta',
        'archived' => 'Archiviata'
    ],

    // Notification Actions
    'actions' => [
        'mark_as_read' => 'Segna come letta',
        'mark_as_unread' => 'Segna come non letta',
        'delete' => 'Elimina',
        'archive' => 'Archivia'
    ],

    // Time formatting
    'time' => [
        'now' => 'Ora',
        'minutes_ago' => ':count minuti fa',
        'hours_ago' => ':count ore fa',
        'days_ago' => ':count giorni fa',
        'weeks_ago' => ':count settimane fa'
    ]
];
