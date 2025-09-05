<?php

/*
|--------------------------------------------------------------------------
| Traduction en français de toutes les données des notifications
|--------------------------------------------------------------------------
|
 */

return [
    'proposal_declined_subject' => 'Votre proposition a été rejetée',
    'proposal_declined_line' => 'Votre proposition a été rejetée.',
    'proposal_declined_reason' => 'Raison :',
    'proposal_declined_id' => 'ID de la proposition :',
    'view_details' => 'Voir les détails',
    'thank_you' => 'Merci d’utiliser notre plateforme.',
    'proposal_declined' => 'Proposition rejetée',
    'proposal_declined_message' => 'Votre proposition a été rejetée.',
    'reply' => 'Répondre',
    'wallet_changes_approved' => 'Les modifications du portefeuille ont été approuvées',
    'no_notifications' => 'Aucune notification',
    'select_notification' => 'Sélectionnez une notification pour voir ses détails',
    'hide_processed_notifications' => 'Masquer les notifications traitées.',
    'show_processed_notifications' => 'Afficher les notifications traitées.',
    'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer cette notification ?',
    'proposer' => 'Proposant',
    'receiver' => 'Destinataire',
    'proposed_creation_new_wallet' => 'Vous avez proposé la création d’un nouveau portefeuille',
    'proposed_change_to_a_wallet' => 'Vous avez proposé une modification à un portefeuille',
    'no_historical_notifications' => 'Aucune notification historique',
    'notification_list_error' => 'Erreur lors de la récupération des notifications',
    'invitation_received' => 'Vous avez été invité à participer à une collection',
    'not_found' => 'Notification non trouvée',

    // Notifications de Badge
    'badge' => [
        'title' => 'Notifications',
        'aria_label' => 'Ouvrir le menu des notifications',
        'view_all' => 'Voir toutes les notifications',
        'empty' => [
            'title' => 'Aucune notification',
            'message' => 'Vous n’avez pas encore reçu de notifications.',
        ],
    ],

    'types' => [
        'reservations' => 'Réservation',
        'gdpr' => 'RGPD',
        'collections' => 'Collection',
        'egis' => 'EGI',
        'wallets' => 'Portefeuille',
        'invitations' => 'Invitation',
        'general' => 'Général',
    ],

    'status' => [
        'read' => 'Lu',
        'pending_ack' => 'En attente de lecture',
    ],

    'label' => [
        'status' => 'Statut',
        'from' => 'De',
        'created_at' => 'Créé le',
        'archived' => 'Archiver',
        'additional_details' => 'Détails supplémentaires',
    ],

    'actions' => [
        'done' => 'Terminé',
        'learn_more' => 'En savoir plus',
    ],

    'aria' => [
        'details_label' => 'Détails de la notification',
        'actions_label' => 'Actions pour la notification',
        'mark_as_read' => 'Marquer la notification comme lue',
        'learn_more' => 'Ouvrir le lien pour plus d’informations',
    ],

    'gdpr' => [
        'disavow_button_label' => 'Je ne reconnais pas cette action',
        'confirm_button_label' => 'Confirmer cette action',
        'confirm_action_prompt' => 'Êtes-vous sûr de vouloir confirmer cette action ?',
        'unknown' => [
            'content' => 'Vous avez reçu une notification inconnue.',
            'title' => 'Notification inconnue',
        ],
        'consent_updated' => [
            'content' => 'Votre consentement a été mis à jour.',
            'title' => 'Consentement mis à jour',
        ],
        'breach_report_received' => [
            'content' => 'Vous avez reçu un rapport de violation de données.',
            'title' => 'Rapport de violation de données reçu',
        ],
        'data_deletion_request' => [
            'content' => 'Vous avez reçu une demande de suppression de données.',
            'title' => 'Demande de suppression de données reçue',
        ],
        'data_access_request' => [
            'content' => 'Vous avez reçu une demande d’accès aux données.',
            'title' => 'Demande d’accès aux données reçue',
        ],
        'data_portability_request' => [
            'content' => 'Vous avez reçu une demande de portabilité des données.',
            'title' => 'Demande de portabilité des données reçue',
        ],
        'data_processing_objection' => [
            'content' => 'Vous avez reçu une objection au traitement des données.',
            'title' => 'Objection au traitement des données reçue',
        ],
        'data_processing_restriction' => [
            'content' => 'Vous avez reçu une demande de restriction du traitement des données.',
            'title' => 'Demande de restriction du traitement des données reçue',
        ],
        'data_processing_notification' => [
            'content' => 'Vous avez reçu une notification de traitement des données.',
            'title' => 'Notification de traitement des données reçue',
        ],
        'data_processing_consent' => [
            'content' => 'Vous avez reçu une demande de consentement au traitement des données.',
            'title' => 'Demande de consentement au traitement des données reçue',
        ],
    ],

    // Étiquettes des Types de Notification
    'Wallet' => 'Portefeuille',
    'Highest Bid' => 'Offre la plus élevée',
    'Superseded' => 'Remplacé',
    'Invitation' => 'Invitation',
    'Alert' => 'Alerte',
    'Urgent' => 'Urgent',
    'New High Bid' => 'Nouvelle offre élevée',
    'Recent' => 'Récent',
    'Today' => 'Aujourd’hui',
    'Older' => 'Ancien',
];