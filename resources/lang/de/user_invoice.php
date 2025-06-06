<?php

/**
 * @Oracode Translation File: Invoice Preferences Management - German
 * 🎯 Purpose: Complete German translations for global invoice and billing preferences
 * 🛡️ Privacy: Fiscal data protection, billing address security, GDPR compliance
 * 🌐 i18n: Multi-country billing support with German base translations
 * 🧱 Core Logic: Supports global invoicing, VAT handling, fiscal compliance
 * ⏰ MVP: Critical for marketplace transactions and fiscal compliance
 *
 * @package Lang\De
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Global Billing Ready)
 * @deadline 2025-06-30
 */

return [
    // SEITENTITEL UND KOPFZEILEN
    'management_title' => 'Rechnungspräferenzen',
    'management_subtitle' => 'Verwalte deine Rechnungs- und Zahlungspräferenzen',
    'billing_title' => 'Rechnungsdaten',
    'billing_subtitle' => 'Lege deine Daten für die Rechnungserstellung fest',
    'payment_title' => 'Zahlungsmethoden',
    'payment_subtitle' => 'Verwalte deine Zahlungsmethoden',
    'tax_title' => 'Steuereinstellungen',
    'tax_subtitle' => 'Lege die steuerlichen Präferenzen für dein Land fest',

    // RECHNUNGSEMPFÄNGER-TYPEN
    'entity_types' => [
        'individual' => 'Privatperson',
        'sole_proprietorship' => 'Einzelunternehmen',
        'corporation' => 'Kapitalgesellschaft',
        'partnership' => 'Personengesellschaft',
        'non_profit' => 'Gemeinnützige Organisation',
        'government' => 'Öffentliche Einrichtung',
        'other' => 'Andere',
    ],

    'entity_descriptions' => [
        'individual' => 'Rechnung auf private Person',
        'sole_proprietorship' => 'Einzelunternehmer mit USt-ID',
        'corporation' => 'GmbH, AG usw.',
        'partnership' => 'Personengesellschaft, GbR usw.',
        'non_profit' => 'Vereine, Stiftungen, NGOs',
        'government' => 'Behörden und öffentliche Einrichtungen',
        'other' => 'Andere Rechtsformen',
    ],

    // FORMULARABSCHNITTE RECHNUNG
    'billing_entity' => 'Rechnungsempfänger',
    'billing_entity_desc' => 'Lege den Typ des Rechnungsempfängers fest',
    'billing_address' => 'Rechnungsadresse',
    'billing_address_desc' => 'Adresse für den Versand der Rechnungen',
    'tax_information' => 'Steuerinformationen',
    'tax_information_desc' => 'Steuerdaten für Konformität und Rechnungsstellung',
    'invoice_preferences' => 'Rechnungspräferenzen',
    'invoice_preferences_desc' => 'Format, Sprache und Zustellungsart der Rechnungen',
    'payment_terms' => 'Zahlungsbedingungen',
    'payment_terms_desc' => 'Präferenzen für Zahlungsmethoden und Fristen',

    // FELDER – RECHNUNGSEMPFÄNGER
    'entity_type' => 'Empfängertyp',
    'entity_type_placeholder' => 'Empfängertyp auswählen',
    'legal_name' => 'Juristischer Name',
    'legal_name_placeholder' => 'Juristischer Name für Rechnungsstellung',
    'trade_name' => 'Handelsname',
    'trade_name_placeholder' => 'Handelsname (falls abweichend)',
    'vat_number' => 'USt-IdNr.',
    'vat_number_placeholder' => 'DE123456789',
    'tax_code' => 'Steuernummer',
    'tax_code_placeholder' => 'Steuernummer des Empfängers',
    'business_registration' => 'Handelsregisternummer',
    'business_registration_placeholder' => 'Registernummer',
    'sdi_code' => 'SDI-/PEC-Code',
    'sdi_code_placeholder' => 'Code für elektronische Rechnungsstellung',
    'sdi_code_help' => '7-stelliger SDI-Code oder gültige PEC-Adresse für E-Rechnungen',

    // FELDER – RECHNUNGSADRESSE
    'billing_street' => 'Straße',
    'billing_street_placeholder' => 'Straße, Hausnummer, Zusatz',
    'billing_city' => 'Stadt',
    'billing_city_placeholder' => 'Stadtname',
    'billing_postal_code' => 'PLZ',
    'billing_postal_code_placeholder' => 'Postleitzahl',
    'billing_province' => 'Bundesland',
    'billing_province_placeholder' => 'Bundeslandkürzel',
    'billing_region' => 'Region/Bundesland',
    'billing_region_placeholder' => 'Region oder Bundesland',
    'billing_country' => 'Land',
    'billing_country_placeholder' => 'Land auswählen',
    'same_as_personal' => 'Wie Privatadresse',
    'different_billing_address' => 'Abweichende Rechnungsadresse',

    // FELDER – STEUEREINSTELLUNGEN
    'tax_regime' => 'Steuerregime',
    'tax_regime_placeholder' => 'Steuerregime auswählen',
    'tax_regimes' => [
        'ordinary' => 'Reguläres Regime',
        'simplified' => 'Vereinfachtes Regime',
        'forfettario' => 'Pauschalregime',
        'agricultural' => 'Landwirtschaftliches Regime',
        'non_profit' => 'Gemeinnützig',
        'exempt' => 'USt-befreit',
        'foreign' => 'Ausländische Entität',
    ],
    'vat_exempt' => 'USt-befreit',
    'vat_exempt_reason' => 'Grund für USt-Befreiung',
    'vat_exempt_reason_placeholder' => 'Grund für Befreiung angeben',
    'reverse_charge' => 'Reverse Charge anwendbar',
    'tax_representative' => 'Steuervertreter',
    'tax_representative_placeholder' => 'Name des Steuervertreters (falls zutreffend)',

    // FELDER – RECHNUNGSPRÄFERENZEN
    'invoice_format' => 'Rechnungsformat',
    'invoice_formats' => [
        'electronic' => 'Elektronische Rechnung (XML)',
        'pdf' => 'Standard-PDF',
        'paper' => 'Papier (Post)',
    ],
    'invoice_language' => 'Rechnungssprache',
    'invoice_languages' => [
        'it' => 'Italienisch',
        'en' => 'Englisch',
        'de' => 'Deutsch',
        'fr' => 'Französisch',
        'es' => 'Spanisch',
    ],
    'invoice_delivery' => 'Zustellart',
    'invoice_delivery_methods' => [
        'email' => 'E-Mail',
        'pec' => 'PEC (zertifizierte E-Mail)',
        'sdi' => 'SDI-System',
        'portal' => 'Webportal',
        'mail' => 'Post',
    ],
    'invoice_email' => 'Rechnungs-E-Mail',
    'invoice_email_placeholder' => 'deine@email.de',
    'backup_delivery' => 'Backup-Zustellung',
    'backup_delivery_desc' => 'Alternative Methode, falls die Hauptmethode fehlschlägt',

    // FELDER – ZAHLUNGSPRÄFERENZEN
    'preferred_currency' => 'Bevorzugte Währung',
    'preferred_currencies' => [
        'EUR' => 'Euro (€)',
        'USD' => 'US-Dollar ($)',
        'GBP' => 'Britisches Pfund (£)',
        'CHF' => 'Schweizer Franken (CHF)',
    ],
    'payment_terms_days' => 'Zahlungsbedingungen',
    'payment_terms_options' => [
        '0' => 'Sofortzahlung',
        '15' => '15 Tage',
        '30' => '30 Tage',
        '60' => '60 Tage',
        '90' => '90 Tage',
    ],
    'auto_payment' => 'Automatische Zahlung',
    'auto_payment_desc' => 'Standardzahlungsmethode automatisch belasten',
    'payment_reminder' => 'Zahlungserinnerung',
    'payment_reminder_desc' => 'Erinnerungen vor Fälligkeit erhalten',
    'late_payment_interest' => 'Verzugszinsen',
    'late_payment_interest_desc' => 'Zinsen für verspätete Zahlungen berechnen',

    // AKTIONEN UND BUTTONS
    'save_preferences' => 'Präferenzen Speichern',
    'test_invoice' => 'Testrechnung Erstellen',
    'reset_defaults' => 'Zurücksetzen',
    'export_settings' => 'Einstellungen Exportieren',
    'import_settings' => 'Einstellungen Importieren',
    'validate_tax_data' => 'Steuerdaten Validieren',
    'preview_invoice' => 'Rechnungsvorschau',

    // ERFOLGS- UND FEHLERMELDUNGEN
    'preferences_saved' => 'Rechnungspräferenzen erfolgreich gespeichert',
    'preferences_error' => 'Fehler beim Speichern der Rechnungspräferenzen',
    'tax_validation_success' => 'Steuerdaten erfolgreich validiert',
    'tax_validation_error' => 'Fehler bei der Validierung der Steuerdaten',
    'test_invoice_generated' => 'Testrechnung erstellt und versendet',
    'sdi_code_verified' => 'SDI-Code erfolgreich überprüft',
    'vat_number_verified' => 'USt-IdNr. beim Finanzamt geprüft',

    // VALIDIERUNGSMELDUNGEN
    'validation' => [
        'entity_type_required' => 'Empfängertyp ist erforderlich',
        'legal_name_required' => 'Juristischer Name ist erforderlich',
        'vat_number_invalid' => 'USt-IdNr. ist ungültig',
        'vat_number_format' => 'Format der USt-IdNr. ist für das Land ungültig',
        'tax_code_required' => 'Steuernummer ist für italienische Empfänger erforderlich',
        'sdi_code_invalid' => 'SDI-Code muss 7 Zeichen oder eine gültige PEC-Adresse sein',
        'billing_address_required' => 'Rechnungsadresse ist erforderlich',
        'invoice_email_required' => 'Rechnungs-E-Mail ist erforderlich',
        'currency_unsupported' => 'Währung wird für das gewählte Land nicht unterstützt',
    ],

    // LÄNDERSPEZIFISCHE HINWEISE
    'country_help' => [
        'IT' => [
            'vat_format' => 'Format: IT + 11 Ziffern (z.B. IT12345678901)',
            'sdi_required' => 'SDI-Code für elektronische Rechnungen erforderlich',
            'tax_code_format' => 'Steuernummer: 16 Zeichen für Personen, 11 für Firmen',
        ],
        'DE' => [
            'vat_format' => 'Format: DE + 9 Ziffern (z.B. DE123456789)',
            'tax_number' => 'Deutsche Steuernummer erforderlich',
        ],
        'FR' => [
            'vat_format' => 'Format: FR + 2 Buchstaben/Ziffern + 9 Ziffern',
            'siret_required' => 'SIRET-Nummer für französische Firmen erforderlich',
        ],
        'US' => [
            'ein_format' => 'EIN-Format: XX-XXXXXXX',
            'sales_tax' => 'Umsatzsteuer nach Bundesstaat einrichten',
        ],
    ],

    // KONFORMITÄT UND DATENSCHUTZ
    'compliance' => [
        'gdpr_notice' => 'Steuerdaten werden gemäß DSGVO verarbeitet',
        'data_retention' => 'Rechnungsdaten werden laut Gesetz 10 Jahre aufbewahrt',
        'third_party_sharing' => 'Daten werden nur mit Finanzbehörden und autorisierten Zahlungsanbietern geteilt',
        'encryption_notice' => 'Alle Steuerdaten sind verschlüsselt und sicher gespeichert',
        'audit_trail' => 'Alle Änderungen werden zur Einhaltung protokolliert',
    ],
];

