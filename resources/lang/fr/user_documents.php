<?php

/**
 * @Oracode Translation File: Document Management - French
 * 🎯 Purpose: Complete Fr translations for document upload and verification system
 * 🛡️ Privacy: Document security, verification status, GDPR compliance
 * 🌐 i18n: Document management translations for French users
 * 🧱 Core Logic: Supports document upload, verification, and identity confirmation
 * ⏰ MVP: Critical for KYC compliance and user verification
 *
 * @package Lang\Fr
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - KYC Ready)
 * @deadline 2025-06-30
 */

return [
    // TITRES ET EN-TÊTES DE PAGE
    'management_title' => 'Gestion des Documents',
    'management_subtitle' => 'Téléchargez et gérez vos documents d\'identité',
    'upload_title' => 'Téléverser un Document',
    'upload_subtitle' => 'Ajoutez un nouveau document pour vérification',
    'verification_title' => 'Statut de Vérification',
    'verification_subtitle' => 'Vérifiez le statut de vos documents',

    // TYPES DE DOCUMENTS
    'types' => [
        'identity_card' => 'Carte d\'Identité',
        'passport' => 'Passeport',
        'driving_license' => 'Permis de Conduire',
        'fiscal_code_card' => 'Carte de Code Fiscal',
        'residence_certificate' => 'Certificat de Résidence',
        'birth_certificate' => 'Acte de Naissance',
        'business_registration' => 'Extrait K-Bis',
        'vat_certificate' => 'Certificat de TVA',
        'bank_statement' => 'Relevé Bancaire',
        'utility_bill' => 'Facture (Justificatif de Domicile)',
        'other' => 'Autre Document',
    ],

    // STATUTS DE VÉRIFICATION
    'status' => [
        'pending' => 'En Attente',
        'under_review' => 'En Cours d\'Examen',
        'approved' => 'Approuvé',
        'rejected' => 'Refusé',
        'expired' => 'Expiré',
        'requires_reupload' => 'Nécessite un Nouveau Téléversement',
    ],

    'status_descriptions' => [
        'pending' => 'Document téléchargé, en attente de vérification',
        'under_review' => 'Le document est en cours de vérification par notre équipe',
        'approved' => 'Document vérifié et approuvé',
        'rejected' => 'Document refusé. Consultez les motifs et retéléversez-le',
        'expired' => 'Le document est expiré. Veuillez téléverser une version à jour',
        'requires_reupload' => 'Un document de meilleure qualité est requis',
    ],

    // FORMULAIRE DE TÉLÉVERSEMENT
    'upload_form' => [
        'document_type' => 'Type de Document',
        'document_type_placeholder' => 'Sélectionnez le type de document',
        'document_file' => 'Fichier du Document',
        'document_file_help' => 'Formats acceptés : PDF, JPG, PNG. Taille max : 10 Mo',
        'document_notes' => 'Notes (Optionnel)',
        'document_notes_placeholder' => 'Ajoutez des notes ou informations complémentaires...',
        'expiry_date' => 'Date d\'Expiration',
        'expiry_date_placeholder' => 'Indiquez la date d\'expiration',
        'expiry_date_help' => 'Indiquez la date d\'expiration si applicable',
        'upload_button' => 'Téléverser Document',
        'replace_button' => 'Remplacer Document',
    ],

    // LISTE DES DOCUMENTS
    'list' => [
        'your_documents' => 'Vos Documents',
        'no_documents' => 'Aucun document téléchargé',
        'no_documents_desc' => 'Téléchargez vos documents pour compléter la vérification d\'identité',
        'document_name' => 'Nom du Document',
        'upload_date' => 'Date de Téléversement',
        'status' => 'Statut',
        'actions' => 'Actions',
        'download' => 'Télécharger',
        'replace' => 'Remplacer',
        'delete' => 'Supprimer',
        'view_details' => 'Voir Détails',
    ],

    // ACTIONS ET BOUTONS
    'upload_new' => 'Téléverser un Nouveau Document',
    'view_document' => 'Voir le Document',
    'download_document' => 'Télécharger le Document',
    'delete_document' => 'Supprimer le Document',
    'replace_document' => 'Remplacer le Document',
    'request_verification' => 'Demander Vérification',
    'back_to_list' => 'Retour à la Liste',

    // MESSAGES DE SUCCÈS ET D’ERREUR
    'upload_success' => 'Document téléchargé avec succès',
    'upload_error' => 'Erreur lors du téléchargement du document',
    'delete_success' => 'Document supprimé avec succès',
    'delete_error' => 'Erreur lors de la suppression du document',
    'verification_requested' => 'Vérification demandée. Vous recevrez des mises à jour par email.',
    'verification_completed' => 'Vérification du document terminée',

    // MESSAGES DE VALIDATION
    'validation' => [
        'document_type_required' => 'Le type de document est obligatoire',
        'document_file_required' => 'Le fichier du document est obligatoire',
        'document_file_mimes' => 'Le document doit être au format PDF, JPG ou PNG',
        'document_file_max' => 'Le document ne doit pas dépasser 10 Mo',
        'expiry_date_future' => 'La date d\'expiration doit être ultérieure',
        'document_already_exists' => 'Vous avez déjà téléchargé un document de ce type',
    ],

    // SÉCURITÉ ET CONFIDENTIALITÉ
    'security' => [
        'encryption_notice' => 'Tous les documents sont cryptés et stockés en toute sécurité',
        'access_log' => 'Chaque accès aux documents est enregistré pour des raisons de sécurité',
        'retention_policy' => 'Les documents sont conservés conformément à la législation',
        'delete_warning' => 'La suppression d\'un document est irréversible',
        'verification_required' => 'Les documents sont vérifiés manuellement par notre équipe',
        'processing_time' => 'La vérification prend généralement 2 à 5 jours ouvrables',
    ],

    // EXIGENCES POUR LES FICHIERS
    'requirements' => [
        'title' => 'Exigences du Document',
        'quality' => 'Image nette et bien éclairée',
        'completeness' => 'Document complet, non coupé',
        'readability' => 'Texte clairement lisible',
        'validity' => 'Document valide et non expiré',
        'authenticity' => 'Document original, pas des photocopies de photocopies',
        'format' => 'Format accepté : PDF, JPG, PNG',
        'size' => 'Taille max : 10 Mo',
    ],

    // DÉTAILS DE LA VÉRIFICATION
    'verification' => [
        'process_title' => 'Processus de Vérification',
        'step1' => '1. Téléversement du document',
        'step2' => '2. Contrôle automatique de la qualité',
        'step3' => '3. Vérification manuelle par l\'équipe',
        'step4' => '4. Notification du résultat',
        'rejection_reasons' => 'Motifs de Refus Courants',
        'poor_quality' => 'Qualité d\'image insuffisante',
        'incomplete' => 'Document incomplet ou coupé',
        'expired' => 'Document expiré',
        'unreadable' => 'Texte illisible',
        'wrong_type' => 'Type de document incorrect',
        'suspected_fraud' => 'Falsification suspectée',
    ],
];

