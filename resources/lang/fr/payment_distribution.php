<?php

return [
    // Types d'Utilisateur
    'user_types' => [
        'weak' => 'Utilisateurs Authentification Faible',
        'creator' => 'Créateurs de Contenu',
        'collector' => 'Collectionneurs Privés',
        'commissioner' => 'Collectionneurs Publics',
        'company' => 'Entités Commerciales',
        'epp' => 'Projets Protection Environnementale',
        'trader_pro' => 'Traders Professionnels',
        'vip' => 'Utilisateurs VIP',
    ],

    // Descriptions Types d'Utilisateur
    'user_types_desc' => [
        'weak' => 'Utilisateurs avec authentification wallet uniquement',
        'creator' => 'Artistes et créateurs de contenu',
        'collector' => 'Collectionneurs privés et passionnés',
        'commissioner' => 'Collectionneurs publics avec visibilité',
        'company' => 'Entités commerciales et organisations',
        'epp' => 'Projets de protection environnementale',
        'trader_pro' => 'Opérateurs professionnels du marché',
        'vip' => 'Utilisateurs avec statut privilégié',
    ],

    // Statut de Distribution
    'status' => [
        'pending' => 'En Attente de Traitement',
        'processed' => 'Traité avec Succès',
        'confirmed' => 'Confirmé sur Blockchain',
        'failed' => 'Traitement Échoué',
    ],

    // Descriptions Statut de Distribution
    'status_desc' => [
        'pending' => 'Distribution créée mais pas encore traitée',
        'processed' => 'Distribution traitée avec succès hors-chaîne',
        'confirmed' => 'Distribution confirmée sur blockchain',
        'failed' => 'Traitement de distribution échoué',
    ],
];
