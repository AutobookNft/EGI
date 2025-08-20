<?php

return [
    // Tipi Utente
    'user_types' => [
        'weak' => 'Utenti Autenticazione Debole',
        'creator' => 'Creatori di Contenuti',
        'collector' => 'Collezionisti Privati',
        'commissioner' => 'Collezionisti Pubblici',
        'company' => 'Entità Aziendali',
        'epp' => 'Progetti Protezione Ambientale',
        'trader_pro' => 'Trader Professionali',
        'vip' => 'Utenti VIP',
    ],

    // Descrizioni Tipi Utente
    'user_types_desc' => [
        'weak' => 'Utenti con autenticazione solo wallet',
        'creator' => 'Artisti e creatori di contenuti',
        'collector' => 'Collezionisti privati e appassionati',
        'commissioner' => 'Collezionisti pubblici con visibilità',
        'company' => 'Entità aziendali e organizzazioni',
        'epp' => 'Progetti di protezione ambientale',
        'trader_pro' => 'Operatori professionali del mercato',
        'vip' => 'Utenti con status privilegiato',
    ],

    // Stato Distribuzione
    'status' => [
        'pending' => 'In Attesa di Elaborazione',
        'processed' => 'Elaborato con Successo',
        'confirmed' => 'Confermato su Blockchain',
        'failed' => 'Elaborazione Fallita',
    ],

    // Descrizioni Stato Distribuzione
    'status_desc' => [
        'pending' => 'Distribuzione creata ma non ancora elaborata',
        'processed' => 'Distribuzione elaborata con successo off-chain',
        'confirmed' => 'Distribuzione confermata su blockchain',
        'failed' => 'Elaborazione della distribuzione fallita',
    ],
];
