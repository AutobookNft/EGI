<?php

/**
 * @Oracode Translation File: Organization Data Management - Spanish
 * 🎯 Purpose: Complete Spanish translations for business/organization data management
 * 🛡️ Privacy: Corporate data protection, business information security
 * 🌐 i18n: Multi-country business data support with Spanish base
 * 🧱 Core Logic: Supports creator/enterprise/epp_entity organization management
 * ⏰ MVP: Critical for business users and EPP entity onboarding
 *
 * @package Lang\Es
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Business Ready)
 * @deadline 2025-06-30
 */

return [
    // TÍTULOS Y ENCABEZADOS DE PÁGINA
    'management_title' => 'Datos de la Organización',
    'management_subtitle' => 'Gestiona los datos de tu empresa u organización',
    'company_title' => 'Información Empresarial',
    'company_subtitle' => 'Datos legales y operativos',
    'contacts_title' => 'Contactos Empresariales',
    'contacts_subtitle' => 'Contactos y referencias',
    'certifications_title' => 'Certificaciones',
    'certifications_subtitle' => 'Certificaciones ambientales y de calidad',

    // TIPOS DE ORGANIZACIÓN
    'organization_types' => [
        'corporation' => 'Sociedad',
        'partnership' => 'Sociedad de personas',
        'sole_proprietorship' => 'Autónomo',
        'cooperative' => 'Cooperativa',
        'non_profit' => 'Organización sin ánimo de lucro',
        'foundation' => 'Fundación',
        'association' => 'Asociación',
        'government' => 'Entidad pública',
        'educational' => 'Institución educativa',
        'research' => 'Instituto de investigación',
        'startup' => 'Start-up innovadora',
        'other' => 'Otro',
    ],

    'legal_forms' => [
        'srl' => 'SL - Sociedad de Responsabilidad Limitada',
        'spa' => 'SA - Sociedad Anónima',
        'srls' => 'SL Simplificada',
        'snc' => 'SC - Sociedad Colectiva',
        'sas' => 'Sociedad Comanditaria',
        'ditta_individuale' => 'Autónomo',
        'cooperativa' => 'Cooperativa',
        'onlus' => 'Organización sin ánimo de lucro (ONLUS)',
        'aps' => 'Asociación de Promoción Social',
        'ets' => 'Entidad del Tercer Sector',
        'fondazione' => 'Fundación',
        'ente_pubblico' => 'Entidad Pública',
    ],

    // SECCIONES DEL FORMULARIO
    'legal_information' => 'Información Legal',
    'legal_information_desc' => 'Datos legales y de registro de la organización',
    'operational_information' => 'Información Operativa',
    'operational_information_desc' => 'Datos de actividad y operaciones',
    'contact_information' => 'Información de Contacto',
    'contact_information_desc' => 'Contactos y referencias empresariales',
    'sustainability_info' => 'Información de Sostenibilidad',
    'sustainability_info_desc' => 'Certificaciones y acciones sostenibles',
    'epp_information' => 'Información EPP',
    'epp_information_desc' => 'Datos específicos para entidades EPP (Puntos de Protección Ambiental)',

    // CAMPOS DEL FORMULARIO - INFORMACIÓN LEGAL
    'legal_name' => 'Razón Social',
    'legal_name_placeholder' => 'Nombre legal completo de la organización',
    'trade_name' => 'Nombre Comercial',
    'trade_name_placeholder' => 'Marca o nombre comercial (si es diferente)',
    'legal_form' => 'Forma Jurídica',
    'legal_form_placeholder' => 'Selecciona la forma jurídica',
    'vat_number' => 'NIF / CIF',
    'vat_number_placeholder' => 'ES12345678A',
    'tax_code' => 'Código Fiscal',
    'tax_code_placeholder' => 'Código fiscal de la organización',
    'registration_number' => 'Número de Registro',
    'registration_number_placeholder' => 'Número en el registro mercantil',
    'chamber_of_commerce' => 'Cámara de Comercio',
    'chamber_of_commerce_placeholder' => 'Cámara de comercio de registro',
    'incorporation_date' => 'Fecha de Constitución',
    'incorporation_date_placeholder' => 'Fecha de constitución de la organización',
    'share_capital' => 'Capital Social',
    'share_capital_placeholder' => 'Capital social en euros',

    // CAMPOS DEL FORMULARIO - OPERATIVO
    'business_sector' => 'Sector de Actividad',
    'business_sectors' => [
        'technology' => 'Tecnología & IT',
        'manufacturing' => 'Industria',
        'services' => 'Servicios',
        'retail' => 'Comercio Minorista',
        'wholesale' => 'Comercio Mayorista',
        'construction' => 'Construcción',
        'agriculture' => 'Agricultura',
        'food_beverage' => 'Alimentación & Bebidas',
        'fashion' => 'Moda & Textil',
        'tourism' => 'Turismo & Hotelería',
        'healthcare' => 'Salud',
        'education' => 'Educación',
        'finance' => 'Finanzas & Seguros',
        'transport' => 'Transporte & Logística',
        'energy' => 'Energía & Servicios',
        'creative' => 'Industrias Creativas',
        'environmental' => 'Medioambiente & Sostenibilidad',
        'research' => 'Investigación & Desarrollo',
        'other' => 'Otro',
    ],
    'primary_activity' => 'Actividad Principal',
    'primary_activity_placeholder' => 'Describe la actividad principal de la organización',
    'employee_count' => 'Número de Empleados',
    'employee_ranges' => [
        '1' => '1 empleado',
        '2-9' => '2-9 empleados',
        '10-49' => '10-49 empleados',
        '50-249' => '50-249 empleados',
        '250-999' => '250-999 empleados',
        '1000+' => 'Más de 1000 empleados',
    ],
    'annual_revenue' => 'Ingresos Anuales',
    'revenue_ranges' => [
        'under_100k' => 'Menos de 100.000 €',
        '100k_500k' => '100.000 € - 500.000 €',
        '500k_2m' => '500.000 € - 2.000.000 €',
        '2m_10m' => '2.000.000 € - 10.000.000 €',
        '10m_50m' => '10.000.000 € - 50.000.000 €',
        'over_50m' => 'Más de 50.000.000 €',
    ],

    // CAMPOS DEL FORMULARIO - CONTACTO
    'headquarters_address' => 'Sede Social',
    'headquarters_street' => 'Dirección de la Sede',
    'headquarters_street_placeholder' => 'Calle, número',
    'headquarters_city' => 'Ciudad',
    'headquarters_postal_code' => 'Código Postal',
    'headquarters_province' => 'Provincia',
    'headquarters_country' => 'País',
    'operational_address' => 'Sede Operativa',
    'same_as_headquarters' => 'Igual que la sede social',
    'operational_street' => 'Dirección Operativa',
    'phone_main' => 'Teléfono Principal',
    'phone_main_placeholder' => '+34 912 345 678',
    'phone_secondary' => 'Teléfono Secundario',
    'fax' => 'Fax',
    'email_general' => 'Email General',
    'email_general_placeholder' => 'info@empresa.com',
    'email_admin' => 'Email Administrativo',
    'email_admin_placeholder' => 'admin@empresa.com',
    'pec' => 'PEC (Correo Certificado)',
    'pec_placeholder' => 'empresa@pec.es',
    'website' => 'Sitio Web',
    'website_placeholder' => 'https://www.empresa.com',

    // CAMPOS - SOSTENIBILIDAD & EPP
    'sustainability_commitment' => 'Compromiso de Sostenibilidad',
    'sustainability_commitment_desc' => 'Describe el compromiso ambiental de tu organización',
    'environmental_certifications' => 'Certificaciones Ambientales',
    'certifications' => [
        'iso_14001' => 'ISO 14001 - Sistema de Gestión Ambiental',
        'emas' => 'EMAS - Sistema Comunitario de Gestión y Auditoría Medioambiental',
        'carbon_neutral' => 'Certificado de Carbono Neutro',
        'leed' => 'LEED - Liderazgo en Energía y Diseño Ambiental',
        'ecolabel' => 'Etiqueta Ecológica Europea',
        'fsc' => 'FSC - Consejo de Administración Forestal',
        'cradle_to_cradle' => 'Cradle to Cradle Certified',
        'b_corp' => 'Certificación B-Corp',
        'organic' => 'Certificación Orgánica',
        'fair_trade' => 'Certificación de Comercio Justo',
        'other' => 'Otras Certificaciones',
    ],
    'epp_entity_type' => 'Tipo de Entidad EPP',
    'epp_entity_types' => [
        'environmental_ngo' => 'ONG Medioambiental',
        'research_institute' => 'Instituto de Investigación',
        'green_tech_company' => 'Empresa de Tecnología Verde',
        'renewable_energy' => 'Energía Renovable',
        'waste_management' => 'Gestión de Residuos',
        'conservation_org' => 'Organización de Conservación',
        'sustainable_agriculture' => 'Agricultura Sostenible',
        'environmental_consulting' => 'Consultoría Medioambiental',
        'carbon_offset' => 'Compensación de Carbono',
        'biodiversity_protection' => 'Protección de la Biodiversidad',
    ],
    'epp_certification_level' => 'Nivel de Certificación EPP',
    'epp_levels' => [
        'bronze' => 'Bronce - Compromiso básico',
        'silver' => 'Plata - Compromiso medio',
        'gold' => 'Oro - Compromiso avanzado',
        'platinum' => 'Platino - Compromiso excelente',
    ],
    'sustainability_projects' => 'Proyectos de Sostenibilidad',
    'sustainability_projects_placeholder' => 'Describe los principales proyectos medioambientales',

    // ACCIONES Y BOTONES
    'save_organization' => 'Guardar Datos de la Organización',
    'verify_legal_data' => 'Verificar Datos Legales',
    'upload_certificate' => 'Subir Certificado',
    'request_epp_verification' => 'Solicitar Verificación EPP',
    'export_organization_data' => 'Exportar Datos de la Organización',
    'validate_vat' => 'Validar NIF / CIF',
    'check_chamber_registration' => 'Comprobar Inscripción en la Cámara de Comercio',

    // MENSAJES DE ÉXITO Y ERROR
    'organization_saved' => 'Datos de la organización guardados correctamente',
    'organization_error' => 'Error al guardar los datos de la organización',
    'legal_verification_success' => 'Datos legales verificados correctamente',
    'legal_verification_error' => 'Error al verificar los datos legales',
    'vat_verified' => 'NIF / CIF verificado correctamente',
    'chamber_verified' => 'Registro en la cámara de comercio verificado',
    'epp_verification_requested' => 'Solicitud de verificación EPP enviada correctamente',
    'certificate_uploaded' => 'Certificado subido correctamente',

    // MENSAJES DE VALIDACIÓN
    'validation' => [
        'legal_name_required' => 'La razón social es obligatoria',
        'legal_form_required' => 'La forma jurídica es obligatoria',
        'vat_number_invalid' => 'El NIF / CIF no es válido',
        'tax_code_invalid' => 'El código fiscal no es válido',
        'incorporation_date_valid' => 'La fecha de constitución debe ser válida',
        'share_capital_numeric' => 'El capital social debe ser un número',
        'employee_count_required' => 'El número de empleados es obligatorio',
        'business_sector_required' => 'El sector de actividad es obligatorio',
        'headquarters_address_required' => 'La dirección de la sede social es obligatoria',
        'phone_main_required' => 'El teléfono principal es obligatorio',
        'email_general_required' => 'El email general es obligatorio',
        'email_valid' => 'El email debe ser válido',
        'website_url' => 'El sitio web debe ser una URL válida',
        'pec_email' => 'El PEC debe ser una dirección de correo válida',
    ],

    // AYUDA Y DESCRIPCIONES
    'help' => [
        'legal_name' => 'Nombre completo de la organización según registro legal',
        'trade_name' => 'Nombre comercial o marca utilizada en operaciones',
        'vat_number' => 'NIF / CIF para transacciones y facturación',
        'rea_number' => 'Número de inscripción en la cámara de comercio',
        'share_capital' => 'Capital social desembolsado',
        'epp_entity' => 'Las entidades EPP pueden asignar puntos medioambientales en la plataforma',
        'sustainability_projects' => 'Proyectos que demuestran el compromiso ambiental',
        'certifications' => 'Certificaciones que avalan prácticas sostenibles',
    ],

    // PRIVACIDAD Y CUMPLIMIENTO
    'privacy' => [
        'data_usage' => 'Los datos de la organización se usan para:',
        'usage_verification' => 'Verificación de la identidad empresarial',
        'usage_compliance' => 'Cumplimiento fiscal y legal',
        'usage_epp' => 'Gestión EPP y asignación de puntos',
        'usage_marketplace' => 'Operaciones en el marketplace FlorenceEGI',
        'data_sharing' => 'Los datos pueden ser compartidos con:',
        'sharing_authorities' => 'Autoridades fiscales y de control',
        'sharing_partners' => 'Socios tecnológicos autorizados',
        'sharing_verification' => 'Entidades certificadoras',
        'retention_period' => 'Datos conservados durante 10 años tras el fin de la relación',
        'gdpr_rights' => 'La organización tiene derecho a acceder, rectificar o eliminar los datos',
    ],
];
