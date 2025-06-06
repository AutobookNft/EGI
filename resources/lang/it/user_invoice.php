<?php

/**
 * @Oracode Translation File: Invoice Preferences Management - Italian
 * 🎯 Purpose: Complete Italian translations for global invoice and billing preferences
 * 🛡️ Privacy: Fiscal data protection, billing address security, GDPR compliance
 * 🌐 i18n: Multi-country billing support with Italian base translations
 * 🧱 Core Logic: Supports global invoicing, VAT handling, fiscal compliance
 * ⏰ MVP: Critical for marketplace transactions and fiscal compliance
 *
 * @package Lang\It
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Global Billing Ready)
 * @deadline 2025-06-30
 */

return [
    // ===================================================================
    // PAGE TITLES AND HEADERS
    // ===================================================================
    'management_title' => 'Preferenze Fatturazione',
    'management_subtitle' => 'Gestisci le tue preferenze per fatturazione e pagamenti',
    'billing_title' => 'Dati di Fatturazione',
    'billing_subtitle' => 'Configura i dati per l\'emissione delle fatture',
    'payment_title' => 'Metodi di Pagamento',
    'payment_subtitle' => 'Gestisci i tuoi metodi di pagamento',
    'tax_title' => 'Configurazione Fiscale',
    'tax_subtitle' => 'Imposta le preferenze fiscali per il tuo paese',

    // ===================================================================
    // BILLING ENTITY TYPES
    // ===================================================================
    'entity_types' => [
        'individual' => 'Persona Fisica',
        'sole_proprietorship' => 'Ditta Individuale',
        'corporation' => 'Società di Capitali',
        'partnership' => 'Società di Persone',
        'non_profit' => 'Organizzazione Non Profit',
        'government' => 'Ente Pubblico',
        'other' => 'Altro',
    ],

    'entity_descriptions' => [
        'individual' => 'Fatturazione come persona fisica privata',
        'sole_proprietorship' => 'Ditta individuale con partita IVA',
        'corporation' => 'S.r.l., S.p.A., S.r.l.s. e società di capitali',
        'partnership' => 'S.n.c., S.a.s. e società di persone',
        'non_profit' => 'Associazioni, fondazioni, ONG',
        'government' => 'Enti pubblici, amministrazioni, comuni',
        'other' => 'Altre forme giuridiche',
    ],

    // ===================================================================
    // BILLING FORM SECTIONS
    // ===================================================================
    'billing_entity' => 'Soggetto Fatturazione',
    'billing_entity_desc' => 'Configura il tipo di soggetto per la fatturazione',
    'billing_address' => 'Indirizzo di Fatturazione',
    'billing_address_desc' => 'Indirizzo dove devono essere inviate le fatture',
    'tax_information' => 'Informazioni Fiscali',
    'tax_information_desc' => 'Dati fiscali per compliance e fatturazione',
    'invoice_preferences' => 'Preferenze Fatture',
    'invoice_preferences_desc' => 'Formato, lingua e modalità di ricezione fatture',
    'payment_terms' => 'Termini di Pagamento',
    'payment_terms_desc' => 'Preferenze per modalità e tempistiche di pagamento',

    // ===================================================================
    // FORM FIELDS - BILLING ENTITY
    // ===================================================================
    'entity_type' => 'Tipo Soggetto',
    'entity_type_placeholder' => 'Seleziona il tipo di soggetto',
    'legal_name' => 'Ragione Sociale / Nome Completo',
    'legal_name_placeholder' => 'Nome legale per fatturazione',
    'trade_name' => 'Nome Commerciale',
    'trade_name_placeholder' => 'Nome commerciale (se diverso)',
    'vat_number' => 'Partita IVA',
    'vat_number_placeholder' => 'IT12345678901',
    'tax_code' => 'Codice Fiscale',
    'tax_code_placeholder' => 'Codice fiscale del soggetto',
    'business_registration' => 'Numero REA / Registro Imprese',
    'business_registration_placeholder' => 'Numero di registrazione',
    'sdi_code' => 'Codice SDI / PEC',
    'sdi_code_placeholder' => 'Codice per fatturazione elettronica',
    'sdi_code_help' => 'Codice SDI a 7 caratteri o indirizzo PEC per fatturazione elettronica',

    // ===================================================================
    // FORM FIELDS - BILLING ADDRESS
    // ===================================================================
    'billing_street' => 'Indirizzo',
    'billing_street_placeholder' => 'Via, numero civico, interno',
    'billing_city' => 'Città',
    'billing_city_placeholder' => 'Nome della città',
    'billing_postal_code' => 'Codice Postale',
    'billing_postal_code_placeholder' => 'CAP o codice postale',
    'billing_province' => 'Provincia',
    'billing_province_placeholder' => 'Sigla provincia',
    'billing_region' => 'Regione / Stato',
    'billing_region_placeholder' => 'Regione o stato',
    'billing_country' => 'Paese',
    'billing_country_placeholder' => 'Seleziona il paese',
    'same_as_personal' => 'Uguale all\'indirizzo personale',
    'different_billing_address' => 'Indirizzo di fatturazione diverso',

    // ===================================================================
    // FORM FIELDS - TAX SETTINGS
    // ===================================================================
    'tax_regime' => 'Regime Fiscale',
    'tax_regime_placeholder' => 'Seleziona il regime fiscale',
    'tax_regimes' => [
        'ordinary' => 'Regime Ordinario',
        'simplified' => 'Regime Semplificato',
        'forfettario' => 'Regime Forfettario',
        'agricultural' => 'Regime Agricolo',
        'non_profit' => 'Non Profit',
        'exempt' => 'Esente IVA',
        'foreign' => 'Soggetto Estero',
    ],
    'vat_exempt' => 'Esente IVA',
    'vat_exempt_reason' => 'Motivo Esenzione IVA',
    'vat_exempt_reason_placeholder' => 'Specificare motivo esenzione',
    'reverse_charge' => 'Reverse Charge Applicabile',
    'tax_representative' => 'Rappresentante Fiscale',
    'tax_representative_placeholder' => 'Nome rappresentante fiscale (se applicabile)',

    // ===================================================================
    // FORM FIELDS - INVOICE PREFERENCES
    // ===================================================================
    'invoice_format' => 'Formato Fattura',
    'invoice_formats' => [
        'electronic' => 'Fattura Elettronica (XML)',
        'pdf' => 'PDF Standard',
        'paper' => 'Cartaceo (Spedizione)',
    ],
    'invoice_language' => 'Lingua Fattura',
    'invoice_languages' => [
        'it' => 'Italiano',
        'en' => 'English',
        'de' => 'Deutsch',
        'fr' => 'Français',
        'es' => 'Español',
    ],
    'invoice_delivery' => 'Modalità Consegna',
    'invoice_delivery_methods' => [
        'email' => 'Email',
        'pec' => 'PEC (Posta Certificata)',
        'sdi' => 'Sistema di Interscambio (SDI)',
        'portal' => 'Portale Web',
        'mail' => 'Posta Ordinaria',
    ],
    'invoice_email' => 'Email per Fatture',
    'invoice_email_placeholder' => 'indirizzo@email.com',
    'backup_delivery' => 'Consegna di Backup',
    'backup_delivery_desc' => 'Metodo alternativo se quello principale fallisce',

    // ===================================================================
    // FORM FIELDS - PAYMENT PREFERENCES
    // ===================================================================
    'preferred_currency' => 'Valuta Preferita',
    'preferred_currencies' => [
        'EUR' => 'Euro (€)',
        'USD' => 'US Dollar ($)',
        'GBP' => 'British Pound (£)',
        'CHF' => 'Swiss Franc (CHF)',
    ],
    'payment_terms_days' => 'Termini di Pagamento',
    'payment_terms_options' => [
        '0' => 'Pagamento Immediato',
        '15' => '15 giorni',
        '30' => '30 giorni',
        '60' => '60 giorni',
        '90' => '90 giorni',
    ],
    'auto_payment' => 'Pagamento Automatico',
    'auto_payment_desc' => 'Addebita automaticamente il metodo di pagamento predefinito',
    'payment_reminder' => 'Promemoria Pagamento',
    'payment_reminder_desc' => 'Ricevi notifiche prima della scadenza',
    'late_payment_interest' => 'Interessi di Mora',
    'late_payment_interest_desc' => 'Applica interessi per pagamenti in ritardo',

    // ===================================================================
    // ACTIONS AND BUTTONS
    // ===================================================================
    'save_preferences' => 'Salva Preferenze',
    'test_invoice' => 'Genera Fattura di Test',
    'reset_defaults' => 'Ripristina Predefiniti',
    'export_settings' => 'Esporta Configurazione',
    'import_settings' => 'Importa Configurazione',
    'validate_tax_data' => 'Valida Dati Fiscali',
    'preview_invoice' => 'Anteprima Fattura',

    // ===================================================================
    // SUCCESS AND ERROR MESSAGES
    // ===================================================================
    'preferences_saved' => 'Preferenze di fatturazione salvate con successo',
    'preferences_error' => 'Errore durante il salvataggio delle preferenze',
    'tax_validation_success' => 'Dati fiscali validati correttamente',
    'tax_validation_error' => 'Errore nella validazione dei dati fiscali',
    'test_invoice_generated' => 'Fattura di test generata e inviata',
    'sdi_code_verified' => 'Codice SDI verificato correttamente',
    'vat_number_verified' => 'Partita IVA verificata nell\'Anagrafe Tributaria',

    // ===================================================================
    // VALIDATION MESSAGES
    // ===================================================================
    'validation' => [
        'entity_type_required' => 'Il tipo di soggetto è obbligatorio',
        'legal_name_required' => 'La ragione sociale è obbligatoria',
        'vat_number_invalid' => 'La partita IVA non è valida',
        'vat_number_format' => 'Formato partita IVA non corretto per il paese selezionato',
        'tax_code_required' => 'Il codice fiscale è obbligatorio per soggetti italiani',
        'sdi_code_invalid' => 'Il codice SDI deve essere di 7 caratteri o un indirizzo PEC valido',
        'billing_address_required' => 'L\'indirizzo di fatturazione è obbligatorio',
        'invoice_email_required' => 'L\'email per le fatture è obbligatoria',
        'currency_unsupported' => 'Valuta non supportata per il paese selezionato',
    ],

    // ===================================================================
    // COUNTRY SPECIFIC HELP
    // ===================================================================
    'country_help' => [
        'IT' => [
            'vat_format' => 'Formato: IT + 11 cifre (es. IT12345678901)',
            'sdi_required' => 'Codice SDI obbligatorio per fatturazione elettronica',
            'tax_code_format' => 'Codice fiscale: 16 caratteri per persone fisiche, 11 per aziende',
        ],
        'DE' => [
            'vat_format' => 'Formato: DE + 9 cifre (es. DE123456789)',
            'tax_number' => 'Numero fiscale tedesco richiesto',
        ],
        'FR' => [
            'vat_format' => 'Formato: FR + 2 lettere/cifre + 9 cifre',
            'siret_required' => 'Numero SIRET richiesto per aziende francesi',
        ],
        'US' => [
            'ein_format' => 'Formato EIN: XX-XXXXXXX',
            'sales_tax' => 'Configura sales tax per stato',
        ],
    ],

    // ===================================================================
    // COMPLIANCE AND PRIVACY
    // ===================================================================
    'compliance' => [
        'gdpr_notice' => 'I dati fiscali sono trattati secondo GDPR per adempimenti fiscali',
        'data_retention' => 'I dati di fatturazione sono conservati per 10 anni come richiesto dalla legge',
        'third_party_sharing' => 'I dati sono condivisi solo con autorità fiscali e processori di pagamento autorizzati',
        'encryption_notice' => 'Tutti i dati fiscali sono crittografati e conservati in sicurezza',
        'audit_trail' => 'Tutte le modifiche ai dati fiscali sono registrate per compliance',
    ],
];
