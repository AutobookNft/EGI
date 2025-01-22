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
    ]
];
