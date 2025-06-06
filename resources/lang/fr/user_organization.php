<?php

/**
 * @Oracode Translation File: Organization Data Management - French
 * 🎯 Purpose: Complete French translations for business/organization data management
 * 🛡️ Privacy: Corporate data protection, business information security
 * 🌐 i18n: Multi-country business data support with French base
 * 🧱 Core Logic: Supports creator/enterprise/epp_entity organization management
 * ⏰ MVP: Critical for business users and EPP entity onboarding
 *
 * @package Lang\Fr
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Business Ready)
 * @deadline 2025-06-30
 */

return [
    // TITRES ET EN-TÊTES DE PAGE
    'management_title' => 'Données de l’Organisation',
    'management_subtitle' => 'Gérez les données de votre entreprise ou organisation',
    'company_title' => 'Informations sur l’Entreprise',
    'company_subtitle' => 'Détails juridiques et opérationnels',
    'contacts_title' => 'Contacts de l’Entreprise',
    'contacts_subtitle' => 'Contacts et références',
    'certifications_title' => 'Certifications',
    'certifications_subtitle' => 'Certifications environnementales et de qualité',

    // TYPES D’ORGANISATION
    'organization_types' => [
        'corporation' => 'Société',
        'partnership' => 'Société de personnes',
        'sole_proprietorship' => 'Entreprise individuelle',
        'cooperative' => 'Coopérative',
        'non_profit' => 'Organisation à but non lucratif',
        'foundation' => 'Fondation',
        'association' => 'Association',
        'government' => 'Entité publique',
        'educational' => 'Établissement d’enseignement',
        'research' => 'Institut de recherche',
        'startup' => 'Start-up innovante',
        'other' => 'Autre',
    ],

    'legal_forms' => [
        'srl' => 'SARL - Société à Responsabilité Limitée',
        'spa' => 'SA - Société Anonyme',
        'srls' => 'SARL Simplifiée',
        'snc' => 'SNC - Société en Nom Collectif',
        'sas' => 'Société en Commandite Simple',
        'ditta_individuale' => 'Entreprise individuelle',
        'cooperativa' => 'Coopérative',
        'onlus' => 'Organisation à but non lucratif (ONLUS)',
        'aps' => 'Association de Promotion Sociale',
        'ets' => 'Entité du Tiers Secteur',
        'fondazione' => 'Fondation',
        'ente_pubblico' => 'Entité Publique',
    ],

    // SECTIONS DU FORMULAIRE
    'legal_information' => 'Informations Juridiques',
    'legal_information_desc' => 'Données juridiques et d’enregistrement de l’organisation',
    'operational_information' => 'Informations Opérationnelles',
    'operational_information_desc' => 'Données sur l’activité et l’organisation',
    'contact_information' => 'Informations de Contact',
    'contact_information_desc' => 'Contacts et références de l’entreprise',
    'sustainability_info' => 'Informations de Durabilité',
    'sustainability_info_desc' => 'Certifications et démarches durables',
    'epp_information' => 'Informations EPP',
    'epp_information_desc' => 'Données spécifiques pour les entités EPP (Points de Protection Environnementale)',

    // CHAMPS - INFORMATIONS JURIDIQUES
    'legal_name' => 'Raison Sociale',
    'legal_name_placeholder' => 'Nom légal complet de l’organisation',
    'trade_name' => 'Nom Commercial',
    'trade_name_placeholder' => 'Nom commercial ou marque (si différent)',
    'legal_form' => 'Forme Juridique',
    'legal_form_placeholder' => 'Sélectionnez la forme juridique',
    'vat_number' => 'Numéro de TVA',
    'vat_number_placeholder' => 'FR12345678901',
    'tax_code' => 'Code Fiscal',
    'tax_code_placeholder' => 'Code fiscal de l’organisation',
    'registration_number' => 'Numéro d’Enregistrement',
    'registration_number_placeholder' => 'Numéro au registre du commerce',
    'chamber_of_commerce' => 'Chambre de Commerce',
    'chamber_of_commerce_placeholder' => 'Chambre de commerce d’enregistrement',
    'incorporation_date' => 'Date de Création',
    'incorporation_date_placeholder' => 'Date de création de l’organisation',
    'share_capital' => 'Capital Social',
    'share_capital_placeholder' => 'Capital social en euros',

    // CHAMPS - OPÉRATIONNEL
    'business_sector' => 'Secteur d’Activité',
    'business_sectors' => [
        'technology' => 'Technologies & Informatique',
        'manufacturing' => 'Industrie',
        'services' => 'Services',
        'retail' => 'Commerce de Détail',
        'wholesale' => 'Commerce de Gros',
        'construction' => 'Construction',
        'agriculture' => 'Agriculture',
        'food_beverage' => 'Agroalimentaire',
        'fashion' => 'Mode & Habillement',
        'tourism' => 'Tourisme & Hôtellerie',
        'healthcare' => 'Santé',
        'education' => 'Éducation',
        'finance' => 'Finance & Assurance',
        'transport' => 'Transports & Logistique',
        'energy' => 'Énergie & Services Publics',
        'creative' => 'Industries Créatives',
        'environmental' => 'Environnement & Durabilité',
        'research' => 'Recherche & Développement',
        'other' => 'Autre',
    ],
    'primary_activity' => 'Activité Principale',
    'primary_activity_placeholder' => 'Décrivez l’activité principale de l’organisation',
    'employee_count' => 'Nombre d’Employés',
    'employee_ranges' => [
        '1' => '1 employé',
        '2-9' => '2-9 employés',
        '10-49' => '10-49 employés',
        '50-249' => '50-249 employés',
        '250-999' => '250-999 employés',
        '1000+' => '1000+ employés',
    ],
    'annual_revenue' => 'Chiffre d’Affaires Annuel',
    'revenue_ranges' => [
        'under_100k' => 'Moins de 100 000 €',
        '100k_500k' => '100 000 € - 500 000 €',
        '500k_2m' => '500 000 € - 2 000 000 €',
        '2m_10m' => '2 000 000 € - 10 000 000 €',
        '10m_50m' => '10 000 000 € - 50 000 000 €',
        'over_50m' => 'Plus de 50 000 000 €',
    ],

    // CHAMPS - CONTACT
    'headquarters_address' => 'Siège Social',
    'headquarters_street' => 'Adresse du Siège',
    'headquarters_street_placeholder' => 'Rue, numéro',
    'headquarters_city' => 'Ville',
    'headquarters_postal_code' => 'Code Postal',
    'headquarters_province' => 'Département',
    'headquarters_country' => 'Pays',
    'operational_address' => 'Adresse Opérationnelle',
    'same_as_headquarters' => 'Identique au siège social',
    'operational_street' => 'Adresse Opérationnelle',
    'phone_main' => 'Téléphone Principal',
    'phone_main_placeholder' => '+33 1 23 45 67 89',
    'phone_secondary' => 'Téléphone Secondaire',
    'fax' => 'Fax',
    'email_general' => 'Email Général',
    'email_general_placeholder' => 'info@entreprise.com',
    'email_admin' => 'Email Administratif',
    'email_admin_placeholder' => 'admin@entreprise.com',
    'pec' => 'PEC (Email Certifiée)',
    'pec_placeholder' => 'entreprise@pec.fr',
    'website' => 'Site Web',
    'website_placeholder' => 'https://www.entreprise.com',

    // CHAMPS - DURABILITÉ & EPP
    'sustainability_commitment' => 'Engagement de Durabilité',
    'sustainability_commitment_desc' => 'Décrivez l’engagement environnemental de votre organisation',
    'environmental_certifications' => 'Certifications Environnementales',
    'certifications' => [
        'iso_14001' => 'ISO 14001 - Système de Management Environnemental',
        'emas' => 'EMAS - Système européen d’audit et de management environnemental',
        'carbon_neutral' => 'Certification Neutralité Carbone',
        'leed' => 'LEED - Leadership in Energy and Environmental Design',
        'ecolabel' => 'Écolabel Européen',
        'fsc' => 'FSC - Conseil de Soutien Forestier',
        'cradle_to_cradle' => 'Cradle to Cradle Certified',
        'b_corp' => 'Certification B-Corp',
        'organic' => 'Certification Biologique',
        'fair_trade' => 'Certification Commerce Équitable',
        'other' => 'Autres Certifications',
    ],
    'epp_entity_type' => 'Type d’Entité EPP',
    'epp_entity_types' => [
        'environmental_ngo' => 'ONG Environnementale',
        'research_institute' => 'Institut de Recherche',
        'green_tech_company' => 'Entreprise Green Tech',
        'renewable_energy' => 'Énergie Renouvelable',
        'waste_management' => 'Gestion des Déchets',
        'conservation_org' => 'Organisation de Conservation',
        'sustainable_agriculture' => 'Agriculture Durable',
        'environmental_consulting' => 'Consulting Environnemental',
        'carbon_offset' => 'Compensation Carbone',
        'biodiversity_protection' => 'Protection de la Biodiversité',
    ],
    'epp_certification_level' => 'Niveau de Certification EPP',
    'epp_levels' => [
        'bronze' => 'Bronze - Engagement de base',
        'silver' => 'Argent - Engagement moyen',
        'gold' => 'Or - Engagement avancé',
        'platinum' => 'Platine - Engagement excellent',
    ],
    'sustainability_projects' => 'Projets de Durabilité',
    'sustainability_projects_placeholder' => 'Décrivez vos principaux projets environnementaux',

    // ACTIONS ET BOUTONS
    'save_organization' => 'Enregistrer les Données de l’Organisation',
    'verify_legal_data' => 'Vérifier les Données Légales',
    'upload_certificate' => 'Téléverser un Certificat',
    'request_epp_verification' => 'Demander la Vérification EPP',
    'export_organization_data' => 'Exporter les Données de l’Organisation',
    'validate_vat' => 'Valider le Numéro de TVA',
    'check_chamber_registration' => 'Vérifier l’Enregistrement à la Chambre de Commerce',

    // MESSAGES DE SUCCÈS ET D’ERREUR
    'organization_saved' => 'Données de l’organisation enregistrées avec succès',
    'organization_error' => 'Erreur lors de l’enregistrement des données de l’organisation',
    'legal_verification_success' => 'Données légales vérifiées avec succès',
    'legal_verification_error' => 'Erreur lors de la vérification des données légales',
    'vat_verified' => 'Numéro de TVA vérifié avec succès',
    'chamber_verified' => 'Enregistrement à la chambre de commerce vérifié',
    'epp_verification_requested' => 'Demande de vérification EPP envoyée avec succès',
    'certificate_uploaded' => 'Certificat téléversé avec succès',

    // MESSAGES DE VALIDATION
    'validation' => [
        'legal_name_required' => 'La raison sociale est obligatoire',
        'legal_form_required' => 'La forme juridique est obligatoire',
        'vat_number_invalid' => 'Le numéro de TVA n’est pas valide',
        'tax_code_invalid' => 'Le code fiscal n’est pas valide',
        'incorporation_date_valid' => 'La date de création doit être valide',
        'share_capital_numeric' => 'Le capital social doit être un nombre',
        'employee_count_required' => 'Le nombre d’employés est obligatoire',
        'business_sector_required' => 'Le secteur d’activité est obligatoire',
        'headquarters_address_required' => 'L’adresse du siège social est obligatoire',
        'phone_main_required' => 'Le téléphone principal est obligatoire',
        'email_general_required' => 'L’email général est obligatoire',
        'email_valid' => 'L’adresse email doit être valide',
        'website_url' => 'Le site web doit être une URL valide',
        'pec_email' => 'La PEC doit être une adresse email valide',
    ],

    // AIDES ET DESCRIPTIONS
    'help' => [
        'legal_name' => 'Nom complet de l’organisation tel qu’enregistré',
        'trade_name' => 'Nom commercial ou marque utilisée',
        'vat_number' => 'Numéro de TVA pour transactions et facturation',
        'rea_number' => 'Numéro d’enregistrement à la chambre de commerce',
        'share_capital' => 'Capital social libéré de l’organisation',
        'epp_entity' => 'Les entités EPP peuvent attribuer des points environnementaux sur la plateforme',
        'sustainability_projects' => 'Projets démontrant l’engagement environnemental',
        'certifications' => 'Certifications attestant des pratiques durables',
    ],

    // CONFIDENTIALITÉ ET CONFORMITÉ
    'privacy' => [
        'data_usage' => 'Les données de l’organisation sont utilisées pour :',
        'usage_verification' => 'Vérification de l’identité de l’entreprise',
        'usage_compliance' => 'Conformité fiscale et légale',
        'usage_epp' => 'Gestion EPP et attribution de points',
        'usage_marketplace' => 'Opérations sur le marketplace FlorenceEGI',
        'data_sharing' => 'Les données peuvent être partagées avec :',
        'sharing_authorities' => 'Autorités fiscales et de contrôle',
        'sharing_partners' => 'Partenaires technologiques autorisés',
        'sharing_verification' => 'Organismes de certification',
        'retention_period' => 'Données conservées 10 ans après la fin de la relation',
        'gdpr_rights' => 'L’organisation a le droit d’accéder, de rectifier ou de supprimer ses données',
    ],
];

