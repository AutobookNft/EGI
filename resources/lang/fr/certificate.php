<?php

return [
    // Titoli e descrizioni pagine
    'page_title' => 'Certificat de Réservation #:uuid',
    'meta_description' => 'Certificat de Réservation :type pour EGI - FlorenceEGI',
    'verify_page_title' => 'Vérifier le Certificat #:uuid',
    'verify_meta_description' => 'Vérifiez l\'authenticité du certificat de réservation EGI #:uuid sur FlorenceEGI',
    'list_by_egi_title' => 'Certificats pour EGI #:egi_id',
    'list_by_egi_meta_description' => 'Voir tous les certificats de réservation pour EGI #:egi_id sur FlorenceEGI',
    'user_certificates_title' => 'Vos Certificats de Réservation',
    'user_certificates_meta_description' => 'Voir tous vos certificats de réservation EGI sur FlorenceEGI',

    // Messaggi errore
    'not_found' => 'Le certificat demandé n\'a pas été trouvé.',
    'download_failed' => 'Impossible de télécharger le PDF du certificat. Veuillez réessayer plus tard.',
    'verification_failed' => 'Impossible de vérifier le certificat. Il peut être invalide ou ne plus exister.',
    'list_failed' => 'Impossible de récupérer la liste des certificats.',
    'auth_required' => 'Veuillez vous connecter pour voir vos certificats.',

    // Dettagli certificato
    'details' => [
        'title' => 'Détails du Certificat',
        'egi_title' => 'Titre de l\'EGI',
        'collection' => 'Collection',
        'reservation_type' => 'Type de Réservation',
        'wallet_address' => 'Adresse du Portefeuille',
        'offer_amount_eur' => 'Montant de l\'Offre (EUR)',
        'offer_amount_algo' => 'Montant de l\'Offre (ALGO)',
        'certificate_uuid' => 'UUID du Certificat',
        'signature_hash' => 'Hash de la Signature',
        'created_at' => 'Créé le',
        'status' => 'Statut',
        'priority' => 'Priorité'
    ],

    // Azioni
    'actions' => [
        'download_pdf' => 'Télécharger le PDF',
        'verify' => 'Vérifier le Certificat',
        'view_egi' => 'Voir l\'EGI',
        'back_to_list' => 'Retour aux Certificats',
        'share' => 'Partager le Certificat'
    ],

    // Verifica
    'verification' => [
        'title' => 'Résultat de la Vérification du Certificat',
        'valid' => 'Ce certificat est valide et authentique.',
        'invalid' => 'Ce certificat semble invalide ou a été altéré.',
        'highest_priority' => 'Ce certificat représente la réservation de la plus haute priorité pour cet EGI.',
        'not_highest_priority' => 'Ce certificat a été dépassé par une réservation de priorité plus élevée.',
        'egi_available' => 'L\'EGI pour cette réservation est toujours disponible.',
        'egi_not_available' => 'L\'EGI pour cette réservation a été émis ou n\'est plus disponible.',
        'what_this_means' => 'Ce que cela signifie',
        'explanation_valid' => 'Ce certificat a été émis par FlorenceEGI et n\'a pas été modifié.',
        'explanation_invalid' => 'Les données du certificat ne correspondent pas à la signature. Il peut avoir été modifié.',
        'explanation_priority' => 'Une réservation de priorité plus élevée (type fort ou montant plus élevé) a été effectuée après celle-ci.',
        'explanation_not_available' => 'L\'EGI a été émis ou n\'est plus disponible pour la réservation.'
    ],

    // Altro
    'unknown_egi' => 'EGI Inconnu',
    'no_certificates' => 'Aucun certificat trouvé.',
    'success_message' => 'Réservation réussie ! Voici votre certificat.',
    'created_just_now' => 'Créé à l\'instant',
    'qr_code_alt' => 'Code QR pour la vérification du certificat'
];
