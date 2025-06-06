<?php

/**
 * @Oracode Translation File: Invoice Preferences Management - Español
 * 🎯 Purpose: Complete Spanish translations for global invoice and billing preferences
 * 🛡️ Privacy: Fiscal data protection, billing address security, GDPR compliance
 * 🌐 i18n: Multi-country billing support with Spanish base translations
 * 🧱 Core Logic: Supports global invoicing, VAT handling, fiscal compliance
 * ⏰ MVP: Critical for marketplace transactions and fiscal compliance
 *
 * @package Lang\Es
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Global Billing Ready)
 * @deadline 2025-06-30
 */

return [
    // TÍTULOS Y ENCABEZADOS DE PÁGINA
    'management_title' => 'Preferencias de Facturación',
    'management_subtitle' => 'Gestiona tus preferencias de facturación y pagos',
    'billing_title' => 'Datos de Facturación',
    'billing_subtitle' => 'Configura tus datos para emitir facturas',
    'payment_title' => 'Métodos de Pago',
    'payment_subtitle' => 'Gestiona tus métodos de pago',
    'tax_title' => 'Configuración Fiscal',
    'tax_subtitle' => 'Define las preferencias fiscales para tu país',

    // TIPOS DE ENTIDAD DE FACTURACIÓN
    'entity_types' => [
        'individual' => 'Persona Física',
        'sole_proprietorship' => 'Autónomo',
        'corporation' => 'Sociedad',
        'partnership' => 'Sociedad de Personas',
        'non_profit' => 'Organización sin Fines de Lucro',
        'government' => 'Entidad Pública',
        'other' => 'Otro',
    ],

    'entity_descriptions' => [
        'individual' => 'Facturación como persona física',
        'sole_proprietorship' => 'Autónomo con número de IVA',
        'corporation' => 'S.L., S.A., etc.',
        'partnership' => 'Sociedad civil, comunidad de bienes, etc.',
        'non_profit' => 'Asociaciones, fundaciones, ONG',
        'government' => 'Administraciones y entidades públicas',
        'other' => 'Otras formas jurídicas',
    ],

    // SECCIONES DEL FORMULARIO DE FACTURACIÓN
    'billing_entity' => 'Entidad de Facturación',
    'billing_entity_desc' => 'Configura el tipo de entidad de facturación',
    'billing_address' => 'Dirección de Facturación',
    'billing_address_desc' => 'Dirección a la que se envían las facturas',
    'tax_information' => 'Información Fiscal',
    'tax_information_desc' => 'Datos fiscales para cumplimiento y facturación',
    'invoice_preferences' => 'Preferencias de Factura',
    'invoice_preferences_desc' => 'Formato, idioma y método de entrega de las facturas',
    'payment_terms' => 'Condiciones de Pago',
    'payment_terms_desc' => 'Preferencias sobre métodos y plazos de pago',

    // CAMPOS DEL FORMULARIO - ENTIDAD DE FACTURACIÓN
    'entity_type' => 'Tipo de Entidad',
    'entity_type_placeholder' => 'Selecciona el tipo de entidad',
    'legal_name' => 'Razón Social',
    'legal_name_placeholder' => 'Nombre legal para facturación',
    'trade_name' => 'Nombre Comercial',
    'trade_name_placeholder' => 'Nombre comercial (si es distinto)',
    'vat_number' => 'Número de IVA',
    'vat_number_placeholder' => 'ES12345678A',
    'tax_code' => 'Código Fiscal',
    'tax_code_placeholder' => 'Código fiscal de la entidad',
    'business_registration' => 'Número de Registro',
    'business_registration_placeholder' => 'Número de registro',
    'sdi_code' => 'Código SDI/PEC',
    'sdi_code_placeholder' => 'Código para facturación electrónica',
    'sdi_code_help' => 'Código SDI de 7 caracteres o dirección PEC para facturación electrónica',

    // CAMPOS DEL FORMULARIO - DIRECCIÓN DE FACTURACIÓN
    'billing_street' => 'Dirección',
    'billing_street_placeholder' => 'Calle, número, piso',
    'billing_city' => 'Ciudad',
    'billing_city_placeholder' => 'Nombre de la ciudad',
    'billing_postal_code' => 'Código Postal',
    'billing_postal_code_placeholder' => 'Código postal',
    'billing_province' => 'Provincia',
    'billing_province_placeholder' => 'Código provincia',
    'billing_region' => 'Región/Estado',
    'billing_region_placeholder' => 'Región o estado',
    'billing_country' => 'País',
    'billing_country_placeholder' => 'Selecciona el país',
    'same_as_personal' => 'Igual que la dirección personal',
    'different_billing_address' => 'Dirección de facturación diferente',

    // CAMPOS DEL FORMULARIO - CONFIGURACIÓN FISCAL
    'tax_regime' => 'Régimen Fiscal',
    'tax_regime_placeholder' => 'Selecciona el régimen fiscal',
    'tax_regimes' => [
        'ordinary' => 'Régimen General',
        'simplified' => 'Régimen Simplificado',
        'forfettario' => 'Régimen de Módulos',
        'agricultural' => 'Régimen Agrícola',
        'non_profit' => 'Sin Fines de Lucro',
        'exempt' => 'Exento de IVA',
        'foreign' => 'Entidad Extranjera',
    ],
    'vat_exempt' => 'Exento de IVA',
    'vat_exempt_reason' => 'Motivo de la Exención de IVA',
    'vat_exempt_reason_placeholder' => 'Especificar motivo de exención',
    'reverse_charge' => 'Inversión del Sujeto Pasivo',
    'tax_representative' => 'Representante Fiscal',
    'tax_representative_placeholder' => 'Nombre del representante fiscal (si aplica)',

    // CAMPOS DEL FORMULARIO - PREFERENCIAS DE FACTURA
    'invoice_format' => 'Formato de Factura',
    'invoice_formats' => [
        'electronic' => 'Factura Electrónica (XML)',
        'pdf' => 'PDF Estándar',
        'paper' => 'Papel (Correo Postal)',
    ],
    'invoice_language' => 'Idioma de la Factura',
    'invoice_languages' => [
        'it' => 'Italiano',
        'en' => 'Inglés',
        'de' => 'Alemán',
        'fr' => 'Francés',
        'es' => 'Español',
    ],
    'invoice_delivery' => 'Método de Entrega',
    'invoice_delivery_methods' => [
        'email' => 'Email',
        'pec' => 'PEC (Correo Certificado)',
        'sdi' => 'Sistema SDI',
        'portal' => 'Portal Web',
        'mail' => 'Correo Postal',
    ],
    'invoice_email' => 'Email para Facturas',
    'invoice_email_placeholder' => 'tu@email.com',
    'backup_delivery' => 'Entrega de Respaldo',
    'backup_delivery_desc' => 'Método alternativo si el principal falla',

    // CAMPOS DEL FORMULARIO - PREFERENCIAS DE PAGO
    'preferred_currency' => 'Moneda Preferida',
    'preferred_currencies' => [
        'EUR' => 'Euro (€)',
        'USD' => 'Dólar USA ($)',
        'GBP' => 'Libra Esterlina (£)',
        'CHF' => 'Franco Suizo (CHF)',
    ],
    'payment_terms_days' => 'Condiciones de Pago',
    'payment_terms_options' => [
        '0' => 'Pago Inmediato',
        '15' => '15 días',
        '30' => '30 días',
        '60' => '60 días',
        '90' => '90 días',
    ],
    'auto_payment' => 'Pago Automático',
    'auto_payment_desc' => 'Cobro automático en el método de pago predeterminado',
    'payment_reminder' => 'Recordatorio de Pago',
    'payment_reminder_desc' => 'Recibir recordatorios antes del vencimiento',
    'late_payment_interest' => 'Intereses de Mora',
    'late_payment_interest_desc' => 'Aplicar intereses por pagos atrasados',

    // ACCIONES Y BOTONES
    'save_preferences' => 'Guardar Preferencias',
    'test_invoice' => 'Generar Factura de Prueba',
    'reset_defaults' => 'Restablecer Valores',
    'export_settings' => 'Exportar Configuración',
    'import_settings' => 'Importar Configuración',
    'validate_tax_data' => 'Validar Datos Fiscales',
    'preview_invoice' => 'Vista Previa de la Factura',

    // MENSAJES DE ÉXITO Y ERROR
    'preferences_saved' => 'Preferencias de facturación guardadas correctamente',
    'preferences_error' => 'Error al guardar las preferencias de facturación',
    'tax_validation_success' => 'Datos fiscales validados correctamente',
    'tax_validation_error' => 'Error en la validación de los datos fiscales',
    'test_invoice_generated' => 'Factura de prueba generada y enviada',
    'sdi_code_verified' => 'Código SDI verificado correctamente',
    'vat_number_verified' => 'Número de IVA verificado en Hacienda',

    // MENSAJES DE VALIDACIÓN
    'validation' => [
        'entity_type_required' => 'El tipo de entidad es obligatorio',
        'legal_name_required' => 'La razón social es obligatoria',
        'vat_number_invalid' => 'El número de IVA no es válido',
        'vat_number_format' => 'Formato de número de IVA no válido para el país seleccionado',
        'tax_code_required' => 'El código fiscal es obligatorio para entidades italianas',
        'sdi_code_invalid' => 'El código SDI debe tener 7 caracteres o ser una dirección PEC válida',
        'billing_address_required' => 'La dirección de facturación es obligatoria',
        'invoice_email_required' => 'El email de facturación es obligatorio',
        'currency_unsupported' => 'Moneda no soportada para el país seleccionado',
    ],

    // AYUDA POR PAÍS
    'country_help' => [
        'IT' => [
            'vat_format' => 'Formato: IT + 11 dígitos (ej. IT12345678901)',
            'sdi_required' => 'Código SDI obligatorio para factura electrónica',
            'tax_code_format' => 'Código fiscal: 16 caracteres para personas, 11 para empresas',
        ],
        'DE' => [
            'vat_format' => 'Formato: DE + 9 dígitos (ej. DE123456789)',
            'tax_number' => 'Número fiscal alemán requerido',
        ],
        'FR' => [
            'vat_format' => 'Formato: FR + 2 letras/dígitos + 9 dígitos',
            'siret_required' => 'Número SIRET requerido para empresas francesas',
        ],
        'US' => [
            'ein_format' => 'Formato EIN: XX-XXXXXXX',
            'sales_tax' => 'Configura el impuesto por estado',
        ],
    ],

    // CUMPLIMIENTO Y PRIVACIDAD
    'compliance' => [
        'gdpr_notice' => 'Los datos fiscales se procesan según el RGPD para el cumplimiento legal',
        'data_retention' => 'Los datos de facturación se guardan durante 10 años según la ley',
        'third_party_sharing' => 'Los datos sólo se comparten con autoridades fiscales y procesadores autorizados',
        'encryption_notice' => 'Todos los datos fiscales están cifrados y almacenados de forma segura',
        'audit_trail' => 'Todos los cambios en los datos fiscales se registran para cumplimiento',
    ],
];

