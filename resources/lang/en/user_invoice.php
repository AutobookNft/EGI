<?php

/**
 * @Oracode Translation File: Invoice Preferences Management - English
 * 🎯 Purpose: Complete English translations for global invoice and billing preferences
 * 🛡️ Privacy: Fiscal data protection, billing address security, GDPR compliance
 * 🌐 i18n: Multi-country billing support with English base translations
 * 🧱 Core Logic: Supports global invoicing, VAT handling, fiscal compliance
 * ⏰ MVP: Critical for marketplace transactions and fiscal compliance
 *
 * @package Lang\En
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Global Billing Ready)
 * @deadline 2025-06-30
 */

return [
    // PAGE TITLES AND HEADERS
    'management_title' => 'Billing Preferences',
    'management_subtitle' => 'Manage your billing and payment preferences',
    'billing_title' => 'Billing Details',
    'billing_subtitle' => 'Set up your invoice details',
    'payment_title' => 'Payment Methods',
    'payment_subtitle' => 'Manage your payment methods',
    'tax_title' => 'Tax Settings',
    'tax_subtitle' => 'Set tax preferences for your country',

    // BILLING ENTITY TYPES
    'entity_types' => [
        'individual' => 'Individual',
        'sole_proprietorship' => 'Sole Proprietorship',
        'corporation' => 'Corporation',
        'partnership' => 'Partnership',
        'non_profit' => 'Non-Profit Organization',
        'government' => 'Public Entity',
        'other' => 'Other',
    ],

    'entity_descriptions' => [
        'individual' => 'Billing as a private individual',
        'sole_proprietorship' => 'Sole proprietorship with VAT',
        'corporation' => 'LLC, PLC, and other corporations',
        'partnership' => 'General and limited partnerships',
        'non_profit' => 'Associations, foundations, NGOs',
        'government' => 'Public administrations and entities',
        'other' => 'Other legal forms',
    ],

    // BILLING FORM SECTIONS
    'billing_entity' => 'Billing Entity',
    'billing_entity_desc' => 'Configure the billing entity type',
    'billing_address' => 'Billing Address',
    'billing_address_desc' => 'Address where invoices are sent',
    'tax_information' => 'Tax Information',
    'tax_information_desc' => 'Tax data for compliance and invoicing',
    'invoice_preferences' => 'Invoice Preferences',
    'invoice_preferences_desc' => 'Format, language, and delivery method for invoices',
    'payment_terms' => 'Payment Terms',
    'payment_terms_desc' => 'Preferences for payment methods and deadlines',

    // FORM FIELDS - BILLING ENTITY
    'entity_type' => 'Entity Type',
    'entity_type_placeholder' => 'Select entity type',
    'legal_name' => 'Legal Name',
    'legal_name_placeholder' => 'Legal name for invoicing',
    'trade_name' => 'Trade Name',
    'trade_name_placeholder' => 'Trade name (if different)',
    'vat_number' => 'VAT Number',
    'vat_number_placeholder' => 'IT12345678901',
    'tax_code' => 'Tax Code',
    'tax_code_placeholder' => 'Tax code of the entity',
    'business_registration' => 'Business Registration Number',
    'business_registration_placeholder' => 'Registration number',
    'sdi_code' => 'SDI/PEC Code',
    'sdi_code_placeholder' => 'Electronic invoicing code',
    'sdi_code_help' => '7-character SDI code or PEC email for e-invoicing',

    // FORM FIELDS - BILLING ADDRESS
    'billing_street' => 'Street Address',
    'billing_street_placeholder' => 'Street, house number, unit',
    'billing_city' => 'City',
    'billing_city_placeholder' => 'City name',
    'billing_postal_code' => 'Postal Code',
    'billing_postal_code_placeholder' => 'Postal code/ZIP',
    'billing_province' => 'Province',
    'billing_province_placeholder' => 'Province/state code',
    'billing_region' => 'Region/State',
    'billing_region_placeholder' => 'Region or state',
    'billing_country' => 'Country',
    'billing_country_placeholder' => 'Select country',
    'same_as_personal' => 'Same as personal address',
    'different_billing_address' => 'Different billing address',

    // FORM FIELDS - TAX SETTINGS
    'tax_regime' => 'Tax Regime',
    'tax_regime_placeholder' => 'Select tax regime',
    'tax_regimes' => [
        'ordinary' => 'Ordinary Regime',
        'simplified' => 'Simplified Regime',
        'forfettario' => 'Flat Rate Regime',
        'agricultural' => 'Agricultural Regime',
        'non_profit' => 'Non-Profit',
        'exempt' => 'VAT Exempt',
        'foreign' => 'Foreign Entity',
    ],
    'vat_exempt' => 'VAT Exempt',
    'vat_exempt_reason' => 'Reason for VAT Exemption',
    'vat_exempt_reason_placeholder' => 'Specify reason for exemption',
    'reverse_charge' => 'Reverse Charge Applicable',
    'tax_representative' => 'Tax Representative',
    'tax_representative_placeholder' => 'Name of tax representative (if applicable)',

    // FORM FIELDS - INVOICE PREFERENCES
    'invoice_format' => 'Invoice Format',
    'invoice_formats' => [
        'electronic' => 'Electronic Invoice (XML)',
        'pdf' => 'Standard PDF',
        'paper' => 'Paper (Mail)',
    ],
    'invoice_language' => 'Invoice Language',
    'invoice_languages' => [
        'it' => 'Italian',
        'en' => 'English',
        'de' => 'German',
        'fr' => 'French',
        'es' => 'Spanish',
    ],
    'invoice_delivery' => 'Delivery Method',
    'invoice_delivery_methods' => [
        'email' => 'Email',
        'pec' => 'PEC (Certified Email)',
        'sdi' => 'SDI System',
        'portal' => 'Web Portal',
        'mail' => 'Postal Mail',
    ],
    'invoice_email' => 'Invoice Email',
    'invoice_email_placeholder' => 'your@email.com',
    'backup_delivery' => 'Backup Delivery',
    'backup_delivery_desc' => 'Alternative method if the primary one fails',

    // FORM FIELDS - PAYMENT PREFERENCES
    'preferred_currency' => 'Preferred Currency',
    'preferred_currencies' => [
        'EUR' => 'Euro (€)',
        'USD' => 'US Dollar ($)',
        'GBP' => 'British Pound (£)',
        'CHF' => 'Swiss Franc (CHF)',
    ],
    'payment_terms_days' => 'Payment Terms',
    'payment_terms_options' => [
        '0' => 'Immediate Payment',
        '15' => '15 days',
        '30' => '30 days',
        '60' => '60 days',
        '90' => '90 days',
    ],
    'auto_payment' => 'Automatic Payment',
    'auto_payment_desc' => 'Automatically charge the default payment method',
    'payment_reminder' => 'Payment Reminder',
    'payment_reminder_desc' => 'Receive reminders before due date',
    'late_payment_interest' => 'Late Payment Interest',
    'late_payment_interest_desc' => 'Apply interest for late payments',

    // ACTIONS AND BUTTONS
    'save_preferences' => 'Save Preferences',
    'test_invoice' => 'Generate Test Invoice',
    'reset_defaults' => 'Reset to Default',
    'export_settings' => 'Export Settings',
    'import_settings' => 'Import Settings',
    'validate_tax_data' => 'Validate Tax Data',
    'preview_invoice' => 'Invoice Preview',

    // SUCCESS AND ERROR MESSAGES
    'preferences_saved' => 'Billing preferences saved successfully',
    'preferences_error' => 'Error saving billing preferences',
    'tax_validation_success' => 'Tax data successfully validated',
    'tax_validation_error' => 'Error validating tax data',
    'test_invoice_generated' => 'Test invoice generated and sent',
    'sdi_code_verified' => 'SDI code successfully verified',
    'vat_number_verified' => 'VAT number verified in the Tax Registry',

    // VALIDATION MESSAGES
    'validation' => [
        'entity_type_required' => 'Entity type is required',
        'legal_name_required' => 'Legal name is required',
        'vat_number_invalid' => 'VAT number is not valid',
        'vat_number_format' => 'VAT number format is not valid for the selected country',
        'tax_code_required' => 'Tax code is required for Italian entities',
        'sdi_code_invalid' => 'SDI code must be 7 characters or a valid PEC address',
        'billing_address_required' => 'Billing address is required',
        'invoice_email_required' => 'Invoice email is required',
        'currency_unsupported' => 'Currency not supported for the selected country',
    ],

    // COUNTRY SPECIFIC HELP
    'country_help' => [
        'IT' => [
            'vat_format' => 'Format: IT + 11 digits (e.g. IT12345678901)',
            'sdi_required' => 'SDI code required for electronic invoicing',
            'tax_code_format' => 'Tax code: 16 characters for individuals, 11 for companies',
        ],
        'DE' => [
            'vat_format' => 'Format: DE + 9 digits (e.g. DE123456789)',
            'tax_number' => 'German tax number required',
        ],
        'FR' => [
            'vat_format' => 'Format: FR + 2 letters/digits + 9 digits',
            'siret_required' => 'SIRET number required for French companies',
        ],
        'US' => [
            'ein_format' => 'EIN Format: XX-XXXXXXX',
            'sales_tax' => 'Set up sales tax by state',
        ],
    ],

    // COMPLIANCE AND PRIVACY
    'compliance' => [
        'gdpr_notice' => 'Tax data is processed according to GDPR for legal compliance',
        'data_retention' => 'Billing data kept for 10 years as required by law',
        'third_party_sharing' => 'Data shared only with tax authorities and authorized payment processors',
        'encryption_notice' => 'All tax data is encrypted and securely stored',
        'audit_trail' => 'All changes to tax data are logged for compliance',
    ],
];

