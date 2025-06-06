<?php

/**
 * @Oracode Translation File: Invoice Preferences Management - French
 * 🎯 Purpose: Complete French translations for global invoice and billing preferences
 * 🛡️ Privacy: Fiscal data protection, billing address security, GDPR compliance
 * 🌐 i18n: Multi-country billing support with French base translations
 * 🧱 Core Logic: Supports global invoicing, VAT handling, fiscal compliance
 * ⏰ MVP: Critical for marketplace transactions and fiscal compliance
 *
 * @package Lang\Fr
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Global Billing Ready)
 * @deadline 2025-06-30
 */

return [
    // TITRES ET EN-TÊTES DE PAGE
    'management_title' => 'Préférences de Facturation',
    'management_subtitle' => 'Gérez vos préférences de facturation et de paiement',
    'billing_title' => 'Détails de Facturation',
    'billing_subtitle' => 'Configurez vos informations pour l\'émission des factures',
    'payment_title' => 'Moyens de Paiement',
    'payment_subtitle' => 'Gérez vos moyens de paiement',
    'tax_title' => 'Paramètres Fiscaux',
    'tax_subtitle' => 'Définissez les préférences fiscales pour votre pays',

    // TYPES D’ENTITÉ DE FACTURATION
    'entity_types' => [
        'individual' => 'Personne Physique',
        'sole_proprietorship' => 'Entreprise Individuelle',
        'corporation' => 'Société',
        'partnership' => 'Société de Personnes',
        'non_profit' => 'Organisation à But Non Lucratif',
        'government' => 'Entité Publique',
        'other' => 'Autre',
    ],

    'entity_descriptions' => [
        'individual' => 'Facturation en tant que personne physique',
        'sole_proprietorship' => 'Entreprise individuelle avec numéro de TVA',
        'corporation' => 'SARL, SA, SAS, etc.',
        'partnership' => 'SNC, société de personnes, etc.',
        'non_profit' => 'Associations, fondations, ONG',
        'government' => 'Administrations et entités publiques',
        'other' => 'Autres formes juridiques',
    ],

    // SECTIONS DU FORMULAIRE DE FACTURATION
    'billing_entity' => 'Entité de Facturation',
    'billing_entity_desc' => 'Configurez le type d\'entité de facturation',
    'billing_address' => 'Adresse de Facturation',
    'billing_address_desc' => 'Adresse où les factures sont envoyées',
    'tax_information' => 'Informations Fiscales',
    'tax_information_desc' => 'Données fiscales pour la conformité et la facturation',
    'invoice_preferences' => 'Préférences de Facture',
    'invoice_preferences_desc' => 'Format, langue et mode d\'envoi des factures',
    'payment_terms' => 'Conditions de Paiement',
    'payment_terms_desc' => 'Préférences pour les modes et délais de paiement',

    // CHAMPS DU FORMULAIRE - ENTITÉ DE FACTURATION
    'entity_type' => 'Type d\'Entité',
    'entity_type_placeholder' => 'Sélectionnez le type d\'entité',
    'legal_name' => 'Raison Sociale',
    'legal_name_placeholder' => 'Nom légal pour la facturation',
    'trade_name' => 'Nom Commercial',
    'trade_name_placeholder' => 'Nom commercial (si différent)',
    'vat_number' => 'Numéro de TVA',
    'vat_number_placeholder' => 'FR12345678901',
    'tax_code' => 'Code Fiscal',
    'tax_code_placeholder' => 'Code fiscal de l\'entité',
    'business_registration' => 'Numéro d\'Enregistrement',
    'business_registration_placeholder' => 'Numéro d\'enregistrement',
    'sdi_code' => 'Code SDI/PEC',
    'sdi_code_placeholder' => 'Code pour la facturation électronique',
    'sdi_code_help' => 'Code SDI à 7 caractères ou email PEC pour facturation électronique',

    // CHAMPS DU FORMULAIRE - ADRESSE DE FACTURATION
    'billing_street' => 'Adresse',
    'billing_street_placeholder' => 'Rue, numéro, complément',
    'billing_city' => 'Ville',
    'billing_city_placeholder' => 'Nom de la ville',
    'billing_postal_code' => 'Code Postal',
    'billing_postal_code_placeholder' => 'Code postal',
    'billing_province' => 'Département',
    'billing_province_placeholder' => 'Code département',
    'billing_region' => 'Région/État',
    'billing_region_placeholder' => 'Région ou État',
    'billing_country' => 'Pays',
    'billing_country_placeholder' => 'Sélectionnez le pays',
    'same_as_personal' => 'Identique à l\'adresse personnelle',
    'different_billing_address' => 'Adresse de facturation différente',

    // CHAMPS DU FORMULAIRE - PARAMÈTRES FISCAUX
    'tax_regime' => 'Régime Fiscal',
    'tax_regime_placeholder' => 'Sélectionnez le régime fiscal',
    'tax_regimes' => [
        'ordinary' => 'Régime Ordinaire',
        'simplified' => 'Régime Simplifié',
        'forfettario' => 'Régime Forfaitaire',
        'agricultural' => 'Régime Agricole',
        'non_profit' => 'Non Lucratif',
        'exempt' => 'Exonéré de TVA',
        'foreign' => 'Entité Étrangère',
    ],
    'vat_exempt' => 'Exonéré de TVA',
    'vat_exempt_reason' => 'Raison de l\'Exonération',
    'vat_exempt_reason_placeholder' => 'Précisez la raison de l\'exonération',
    'reverse_charge' => 'Autoliquidation Applicable',
    'tax_representative' => 'Représentant Fiscal',
    'tax_representative_placeholder' => 'Nom du représentant fiscal (si applicable)',

    // CHAMPS DU FORMULAIRE - PRÉFÉRENCES DE FACTURE
    'invoice_format' => 'Format de Facture',
    'invoice_formats' => [
        'electronic' => 'Facture Électronique (XML)',
        'pdf' => 'PDF Standard',
        'paper' => 'Papier (Poste)',
    ],
    'invoice_language' => 'Langue de la Facture',
    'invoice_languages' => [
        'it' => 'Italien',
        'en' => 'Anglais',
        'de' => 'Allemand',
        'fr' => 'Français',
        'es' => 'Espagnol',
    ],
    'invoice_delivery' => 'Mode d\'Envoi',
    'invoice_delivery_methods' => [
        'email' => 'Email',
        'pec' => 'PEC (Email Certifiée)',
        'sdi' => 'Système SDI',
        'portal' => 'Portail Web',
        'mail' => 'Courrier Postal',
    ],
    'invoice_email' => 'Email de Facture',
    'invoice_email_placeholder' => 'votre@email.com',
    'backup_delivery' => 'Envoi de Secours',
    'backup_delivery_desc' => 'Méthode alternative si la principale échoue',

    // CHAMPS DU FORMULAIRE - PRÉFÉRENCES DE PAIEMENT
    'preferred_currency' => 'Devise Préférée',
    'preferred_currencies' => [
        'EUR' => 'Euro (€)',
        'USD' => 'Dollar US ($)',
        'GBP' => 'Livre Sterling (£)',
        'CHF' => 'Franc Suisse (CHF)',
    ],
    'payment_terms_days' => 'Conditions de Paiement',
    'payment_terms_options' => [
        '0' => 'Paiement Immédiat',
        '15' => '15 jours',
        '30' => '30 jours',
        '60' => '60 jours',
        '90' => '90 jours',
    ],
    'auto_payment' => 'Paiement Automatique',
    'auto_payment_desc' => 'Débiter automatiquement le moyen de paiement par défaut',
    'payment_reminder' => 'Rappel de Paiement',
    'payment_reminder_desc' => 'Recevez des rappels avant échéance',
    'late_payment_interest' => 'Intérêts de Retard',
    'late_payment_interest_desc' => 'Appliquer des intérêts pour retard de paiement',

    // ACTIONS ET BOUTONS
    'save_preferences' => 'Enregistrer les Préférences',
    'test_invoice' => 'Générer une Facture Test',
    'reset_defaults' => 'Réinitialiser',
    'export_settings' => 'Exporter les Paramètres',
    'import_settings' => 'Importer les Paramètres',
    'validate_tax_data' => 'Valider les Données Fiscales',
    'preview_invoice' => 'Aperçu de la Facture',

    // MESSAGES DE SUCCÈS ET D’ERREUR
    'preferences_saved' => 'Préférences de facturation enregistrées avec succès',
    'preferences_error' => 'Erreur lors de l\'enregistrement des préférences',
    'tax_validation_success' => 'Données fiscales validées avec succès',
    'tax_validation_error' => 'Erreur de validation des données fiscales',
    'test_invoice_generated' => 'Facture test générée et envoyée',
    'sdi_code_verified' => 'Code SDI vérifié avec succès',
    'vat_number_verified' => 'Numéro de TVA vérifié auprès de l\'administration fiscale',

    // MESSAGES DE VALIDATION
    'validation' => [
        'entity_type_required' => 'Le type d\'entité est obligatoire',
        'legal_name_required' => 'La raison sociale est obligatoire',
        'vat_number_invalid' => 'Le numéro de TVA n\'est pas valide',
        'vat_number_format' => 'Format de TVA invalide pour le pays sélectionné',
        'tax_code_required' => 'Le code fiscal est obligatoire pour les entités italiennes',
        'sdi_code_invalid' => 'Le code SDI doit comporter 7 caractères ou une adresse PEC valide',
        'billing_address_required' => 'L\'adresse de facturation est obligatoire',
        'invoice_email_required' => 'L\'email de facturation est obligatoire',
        'currency_unsupported' => 'Devise non prise en charge pour le pays sélectionné',
    ],

    // AIDES PAYS SPÉCIFIQUES
    'country_help' => [
        'IT' => [
            'vat_format' => 'Format : IT + 11 chiffres (ex : IT12345678901)',
            'sdi_required' => 'Code SDI requis pour la facturation électronique',
            'tax_code_format' => 'Code fiscal : 16 caractères pour les particuliers, 11 pour les sociétés',
        ],
        'DE' => [
            'vat_format' => 'Format : DE + 9 chiffres (ex : DE123456789)',
            'tax_number' => 'Numéro fiscal allemand requis',
        ],
        'FR' => [
            'vat_format' => 'Format : FR + 2 lettres/chiffres + 9 chiffres',
            'siret_required' => 'Numéro SIRET requis pour les entreprises françaises',
        ],
        'US' => [
            'ein_format' => 'Format EIN : XX-XXXXXXX',
            'sales_tax' => 'Configurer la taxe de vente par État',
        ],
    ],

    // CONFORMITÉ ET CONFIDENTIALITÉ
    'compliance' => [
        'gdpr_notice' => 'Les données fiscales sont traitées selon le RGPD pour conformité légale',
        'data_retention' => 'Les données de facturation sont conservées 10 ans selon la loi',
        'third_party_sharing' => 'Les données sont partagées uniquement avec les autorités fiscales et les prestataires autorisés',
        'encryption_notice' => 'Toutes les données fiscales sont cryptées et stockées en toute sécurité',
        'audit_trail' => 'Toutes les modifications sont enregistrées pour conformité',
    ],
];
