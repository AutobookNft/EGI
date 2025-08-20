<?php

return [
    // Benutzertypen
    'user_types' => [
        'weak' => 'Schwache Authentifizierung Benutzer',
        'creator' => 'Content-Ersteller',
        'collector' => 'Private Sammler',
        'commissioner' => 'Öffentliche Sammler',
        'company' => 'Geschäftseinheiten',
        'epp' => 'Umweltschutzprojekte',
        'trader_pro' => 'Professionelle Händler',
        'vip' => 'VIP-Benutzer',
    ],

    // Beschreibungen Benutzertypen
    'user_types_desc' => [
        'weak' => 'Benutzer mit nur Wallet-Authentifizierung',
        'creator' => 'Künstler und Content-Ersteller',
        'collector' => 'Private Sammler und Enthusiasten',
        'commissioner' => 'Öffentliche Sammler mit Sichtbarkeit',
        'company' => 'Geschäftseinheiten und Organisationen',
        'epp' => 'Umweltschutzprojekte',
        'trader_pro' => 'Professionelle Marktoperateure',
        'vip' => 'Benutzer mit privilegiertem Status',
    ],

    // Verteilungsstatus
    'status' => [
        'pending' => 'Verarbeitung Ausstehend',
        'processed' => 'Erfolgreich Verarbeitet',
        'confirmed' => 'Blockchain Bestätigt',
        'failed' => 'Verarbeitung Fehlgeschlagen',
    ],

    // Beschreibungen Verteilungsstatus
    'status_desc' => [
        'pending' => 'Verteilung erstellt aber noch nicht verarbeitet',
        'processed' => 'Verteilung erfolgreich off-chain verarbeitet',
        'confirmed' => 'Verteilung auf Blockchain bestätigt',
        'failed' => 'Verteilungsverarbeitung fehlgeschlagen',
    ],
];
