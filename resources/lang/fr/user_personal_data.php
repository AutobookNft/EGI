<?php

/**
 * @Oracode Translation File: Personal Data Management - French
 * 🎯 Purpose: Complete French translations for GDPR-compliant personal data management
 * 🛡️ Privacy: GDPR-compliant notices, consent language, data subject rights
 * 🌐 i18n: Base language file for FlorenceEGI personal data domain
 * 🧱 Core Logic: Supports all personal data CRUD operations with privacy notices
 * ⏰ MVP: Critical for French market compliance and user trust
 *
 * @package Lang\Fr
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - GDPR Native)
 * @deadline 2025-06-30
 */

return [
    // TITRES ET EN-TÊTES DE PAGE
    'management_title' => 'Gestion des Données Personnelles',
    'management_subtitle' => 'Gérez vos données personnelles conformément au RGPD',
    'edit_title' => 'Modifier les Données Personnelles',
    'edit_subtitle' => 'Mettez à jour vos informations personnelles en toute sécurité',
    'export_title' => 'Exporter les Données Personnelles',
    'export_subtitle' => 'Téléchargez une copie complète de vos données personnelles',
    'deletion_title' => 'Demande de Suppression des Données',
    'deletion_subtitle' => 'Demandez la suppression permanente de vos données personnelles',

    // SECTIONS DU FORMULAIRE
    'basic_information' => 'Informations de Base',
    'basic_description' => 'Données essentielles pour l\'identification',
    'fiscal_information' => 'Informations Fiscales',
    'fiscal_description' => 'Code fiscal et informations pour la conformité',
    'address_information' => 'Informations de Résidence',
    'address_description' => 'Adresse de résidence et de domicile',
    'contact_information' => 'Informations de Contact',
    'contact_description' => 'Téléphone et autres coordonnées',
    'identity_verification' => 'Vérification d\'Identité',
    'identity_description' => 'Vérifiez votre identité pour des modifications sensibles',

    // CHAMPS DU FORMULAIRE
    'first_name' => 'Prénom',
    'first_name_placeholder' => 'Entrez votre prénom',
    'last_name' => 'Nom',
    'last_name_placeholder' => 'Entrez votre nom',
    'birth_date' => 'Date de Naissance',
    'birth_date_placeholder' => 'Sélectionnez votre date de naissance',
    'birth_place' => 'Lieu de Naissance',
    'birth_place_placeholder' => 'Ville et département de naissance',
    'gender' => 'Genre',
    'gender_male' => 'Homme',
    'gender_female' => 'Femme',
    'gender_other' => 'Autre',
    'gender_prefer_not_say' => 'Préférer ne pas dire',

    // Champs fiscaux
    'tax_code' => 'Code Fiscal',
    'tax_code_placeholder' => 'RSSMRA80A01H501X',
    'tax_code_help' => 'Votre code fiscal italien (16 caractères)',
    'id_card_number' => 'Numéro de Carte d\'Identité',
    'id_card_number_placeholder' => 'Numéro de pièce d\'identité',
    'passport_number' => 'Numéro de Passeport',
    'passport_number_placeholder' => 'Numéro de passeport (si disponible)',
    'driving_license' => 'Permis de Conduire',
    'driving_license_placeholder' => 'Numéro du permis de conduire',

    // Champs d’adresse
    'street_address' => 'Adresse',
    'street_address_placeholder' => 'Rue, numéro',
    'city' => 'Ville',
    'city_placeholder' => 'Nom de la ville',
    'postal_code' => 'Code Postal',
    'postal_code_placeholder' => '00100',
    'province' => 'Département',
    'province_placeholder' => 'Code département (ex. 75)',
    'region' => 'Région',
    'region_placeholder' => 'Nom de la région',
    'country' => 'Pays',
    'country_placeholder' => 'Sélectionnez le pays',

    // Champs de contact
    'phone' => 'Téléphone',
    'phone_placeholder' => '+33 6 12 34 56 78',
    'mobile' => 'Portable',
    'mobile_placeholder' => '+33 6 12 34 56 78',
    'emergency_contact' => 'Contact d\'Urgence',
    'emergency_contact_placeholder' => 'Nom et téléphone',

    // CONFIDENTIALITÉ ET CONSENTEMENT
    'consent_management' => 'Gestion des Consentements',
    'consent_description' => 'Gérez vos consentements pour le traitement des données',
    'consent_required' => 'Consentement Obligatoire',
    'consent_optional' => 'Consentement Facultatif',
    'consent_marketing' => 'Marketing et Communications',
    'consent_marketing_desc' => 'Consentement pour recevoir des communications commerciales',
    'consent_profiling' => 'Profilage',
    'consent_profiling_desc' => 'Consentement pour le profilage et l\'analyse',
    'consent_analytics' => 'Analytics',
    'consent_analytics_desc' => 'Consentement pour l\'analyse statistique anonymisée',
    'consent_third_party' => 'Partenaires Tiers',
    'consent_third_party_desc' => 'Consentement pour le partage avec des partenaires sélectionnés',

    // ACTIONS ET BOUTONS
    'update_data' => 'Mettre à Jour les Données',
    'save_changes' => 'Enregistrer les Modifications',
    'cancel_changes' => 'Annuler',
    'export_data' => 'Exporter les Données',
    'request_deletion' => 'Demander la Suppression',
    'verify_identity' => 'Vérifier l\'Identité',
    'confirm_changes' => 'Confirmer les Modifications',
    'back_to_profile' => 'Retour au Profil',

    // MESSAGES DE SUCCÈS ET D’ERREUR
    'update_success' => 'Données personnelles mises à jour avec succès',
    'update_error' => 'Erreur lors de la mise à jour des données personnelles',
    'validation_error' => 'Certains champs comportent des erreurs. Veuillez vérifier et réessayer.',
    'identity_verification_required' => 'Vérification d\'identité requise pour cette opération',
    'identity_verification_failed' => 'Échec de la vérification d\'identité. Veuillez réessayer.',
    'export_started' => 'Exportation des données lancée. Vous recevrez un email lorsqu\'elle sera prête.',
    'export_ready' => 'Votre exportation de données est prête au téléchargement',
    'deletion_requested' => 'Demande de suppression envoyée. Elle sera traitée sous 30 jours.',

    // MESSAGES DE VALIDATION
    'validation' => [
        'first_name_required' => 'Le prénom est obligatoire',
        'last_name_required' => 'Le nom est obligatoire',
        'birth_date_required' => 'La date de naissance est obligatoire',
        'birth_date_valid' => 'La date de naissance doit être valide',
        'birth_date_age' => 'Vous devez avoir au moins 13 ans pour vous inscrire',
        'tax_code_invalid' => 'Code fiscal non valide',
        'tax_code_format' => 'Le code fiscal doit comporter 16 caractères',
        'phone_invalid' => 'Numéro de téléphone invalide',
        'postal_code_invalid' => 'Code postal invalide pour le pays sélectionné',
        'country_required' => 'Le pays est obligatoire',
    ],

    // AVIS RGPD
    'gdpr_notices' => [
        'data_processing_info' => 'Vos données personnelles sont traitées conformément au RGPD (UE) 2016/679',
        'data_controller' => 'Responsable du traitement : FlorenceEGI S.r.l.',
        'data_purpose' => 'Finalité : Gestion du compte utilisateur et des services de la plateforme',
        'data_retention' => 'Conservation : Les données sont conservées aussi longtemps que nécessaire aux services demandés',
        'data_rights' => 'Droits : Vous pouvez accéder, rectifier, supprimer ou limiter le traitement de vos données',
        'data_contact' => 'Pour exercer vos droits, contactez : privacy@florenceegi.com',
        'sensitive_data_warning' => 'Attention : vous modifiez des données sensibles. Vérification d\'identité requise.',
        'audit_notice' => 'Toutes les modifications des données personnelles sont enregistrées pour sécurité',
    ],

    // FONCTION EXPORT
    'export' => [
        'formats' => [
            'json' => 'JSON (Lecture machine)',
            'pdf' => 'PDF (Lecture humaine)',
            'csv' => 'CSV (Tableur)',
        ],
        'categories' => [
            'basic' => 'Informations de Base',
            'fiscal' => 'Données Fiscales',
            'address' => 'Adresse',
            'contact' => 'Contact',
            'consents' => 'Consentements et Préférences',
            'audit' => 'Historique des Modifications',
        ],
        'select_format' => 'Sélectionnez le format d\'export',
        'select_categories' => 'Sélectionnez les catégories à exporter',
        'generate_export' => 'Générer l\'Export',
        'download_ready' => 'Téléchargement Prêt',
        'download_expires' => 'Le lien de téléchargement expire dans 7 jours',
    ],

    // FLUX DE SUPPRESSION
    'deletion' => [
        'confirm_title' => 'Confirmer la Suppression des Données',
        'warning_irreversible' => 'ATTENTION : Cette opération est irréversible',
        'warning_account' => 'La suppression des données entraînera la clôture définitive du compte',
        'warning_backup' => 'Les données peuvent être conservées dans des sauvegardes jusqu\'à 90 jours',
        'reason_required' => 'Motif de la demande (optionnel)',
        'reason_placeholder' => 'Vous pouvez préciser le motif de la suppression...',
        'final_confirmation' => 'Je confirme vouloir supprimer définitivement mes données personnelles',
        'type_delete' => 'Tapez "SUPPRIMER" pour confirmer',
        'submit_request' => 'Envoyer la Demande de Suppression',
        'request_submitted' => 'Demande de suppression envoyée avec succès',
        'processing_time' => 'La demande sera traitée sous 30 jours ouvrés',
    ],
];
