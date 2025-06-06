<?php

/**
 * @Oracode Translation File: Invoice Preferences Management - Portuguese
 * 🎯 Purpose: Complete Portuguese translations for global invoice and billing preferences
 * 🛡️ Privacy: Fiscal data protection, billing address security, GDPR compliance
 * 🌐 i18n: Multi-country billing support with Portuguese base translations
 * 🧱 Core Logic: Supports global invoicing, VAT handling, fiscal compliance
 * ⏰ MVP: Critical for marketplace transactions and fiscal compliance
 *
 * @package Lang\Pt
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - Global Billing Ready)
 * @deadline 2025-06-30
 */

return [
    // TÍTULOS E CABEÇALHOS DA PÁGINA
    'management_title' => 'Preferências de Faturamento',
    'management_subtitle' => 'Gerencie suas preferências de faturamento e pagamentos',
    'billing_title' => 'Dados de Faturamento',
    'billing_subtitle' => 'Configure os dados para emissão de faturas',
    'payment_title' => 'Métodos de Pagamento',
    'payment_subtitle' => 'Gerencie seus métodos de pagamento',
    'tax_title' => 'Configurações Fiscais',
    'tax_subtitle' => 'Defina as preferências fiscais para seu país',

    // TIPOS DE ENTIDADE DE FATURAMENTO
    'entity_types' => [
        'individual' => 'Pessoa Física',
        'sole_proprietorship' => 'Empresário Individual',
        'corporation' => 'Sociedade',
        'partnership' => 'Sociedade de Pessoas',
        'non_profit' => 'Organização Sem Fins Lucrativos',
        'government' => 'Entidade Pública',
        'other' => 'Outro',
    ],

    'entity_descriptions' => [
        'individual' => 'Faturamento como pessoa física',
        'sole_proprietorship' => 'Empresário individual com CNPJ',
        'corporation' => 'LTDA, S.A., etc.',
        'partnership' => 'Sociedade de pessoas, etc.',
        'non_profit' => 'Associações, fundações, ONGs',
        'government' => 'Administrações e entidades públicas',
        'other' => 'Outras formas jurídicas',
    ],

    // SEÇÕES DO FORMULÁRIO DE FATURAMENTO
    'billing_entity' => 'Entidade de Faturamento',
    'billing_entity_desc' => 'Configure o tipo de entidade para faturamento',
    'billing_address' => 'Endereço de Faturamento',
    'billing_address_desc' => 'Endereço para o envio das faturas',
    'tax_information' => 'Informações Fiscais',
    'tax_information_desc' => 'Dados fiscais para compliance e faturamento',
    'invoice_preferences' => 'Preferências de Fatura',
    'invoice_preferences_desc' => 'Formato, idioma e forma de recebimento das faturas',
    'payment_terms' => 'Condições de Pagamento',
    'payment_terms_desc' => 'Preferências sobre métodos e prazos de pagamento',

    // CAMPOS DO FORMULÁRIO - ENTIDADE DE FATURAMENTO
    'entity_type' => 'Tipo de Entidade',
    'entity_type_placeholder' => 'Selecione o tipo de entidade',
    'legal_name' => 'Razão Social / Nome Completo',
    'legal_name_placeholder' => 'Nome legal para faturamento',
    'trade_name' => 'Nome Fantasia',
    'trade_name_placeholder' => 'Nome fantasia (se diferente)',
    'vat_number' => 'CNPJ / NIF',
    'vat_number_placeholder' => 'BR12345678000101',
    'tax_code' => 'CPF / Código Fiscal',
    'tax_code_placeholder' => 'Código fiscal da entidade',
    'business_registration' => 'Número de Registro',
    'business_registration_placeholder' => 'Número de registro',
    'sdi_code' => 'Código SDI/PEC',
    'sdi_code_placeholder' => 'Código para faturamento eletrônico',
    'sdi_code_help' => 'Código SDI de 7 caracteres ou email PEC para faturamento eletrônico',

    // CAMPOS DO FORMULÁRIO - ENDEREÇO DE FATURAMENTO
    'billing_street' => 'Endereço',
    'billing_street_placeholder' => 'Rua, número, complemento',
    'billing_city' => 'Cidade',
    'billing_city_placeholder' => 'Nome da cidade',
    'billing_postal_code' => 'CEP',
    'billing_postal_code_placeholder' => 'CEP',
    'billing_province' => 'Estado',
    'billing_province_placeholder' => 'UF',
    'billing_region' => 'Região/Estado',
    'billing_region_placeholder' => 'Região ou estado',
    'billing_country' => 'País',
    'billing_country_placeholder' => 'Selecione o país',
    'same_as_personal' => 'Igual ao endereço pessoal',
    'different_billing_address' => 'Endereço de faturamento diferente',

    // CAMPOS DO FORMULÁRIO - CONFIGURAÇÕES FISCAIS
    'tax_regime' => 'Regime Fiscal',
    'tax_regime_placeholder' => 'Selecione o regime fiscal',
    'tax_regimes' => [
        'ordinary' => 'Regime Normal',
        'simplified' => 'Regime Simplificado',
        'forfettario' => 'Regime de Lucro Presumido',
        'agricultural' => 'Regime Agrícola',
        'non_profit' => 'Sem Fins Lucrativos',
        'exempt' => 'Isento de IVA',
        'foreign' => 'Entidade Estrangeira',
    ],
    'vat_exempt' => 'Isento de IVA',
    'vat_exempt_reason' => 'Motivo da Isenção de IVA',
    'vat_exempt_reason_placeholder' => 'Especifique o motivo da isenção',
    'reverse_charge' => 'Reverse Charge Aplicável',
    'tax_representative' => 'Representante Fiscal',
    'tax_representative_placeholder' => 'Nome do representante fiscal (se aplicável)',

    // CAMPOS DO FORMULÁRIO - PREFERÊNCIAS DE FATURA
    'invoice_format' => 'Formato da Fatura',
    'invoice_formats' => [
        'electronic' => 'Fatura Eletrônica (XML)',
        'pdf' => 'PDF Padrão',
        'paper' => 'Impresso (Correio)',
    ],
    'invoice_language' => 'Idioma da Fatura',
    'invoice_languages' => [
        'it' => 'Italiano',
        'en' => 'Inglês',
        'de' => 'Alemão',
        'fr' => 'Francês',
        'es' => 'Espanhol',
    ],
    'invoice_delivery' => 'Forma de Envio',
    'invoice_delivery_methods' => [
        'email' => 'Email',
        'pec' => 'PEC (Email Certificada)',
        'sdi' => 'Sistema SDI',
        'portal' => 'Portal Web',
        'mail' => 'Correios',
    ],
    'invoice_email' => 'Email para Faturas',
    'invoice_email_placeholder' => 'seu@email.com',
    'backup_delivery' => 'Envio de Backup',
    'backup_delivery_desc' => 'Método alternativo caso o principal falhe',

    // CAMPOS DO FORMULÁRIO - PREFERÊNCIAS DE PAGAMENTO
    'preferred_currency' => 'Moeda Preferida',
    'preferred_currencies' => [
        'EUR' => 'Euro (€)',
        'USD' => 'Dólar Americano ($)',
        'GBP' => 'Libra Esterlina (£)',
        'CHF' => 'Franco Suíço (CHF)',
    ],
    'payment_terms_days' => 'Condições de Pagamento',
    'payment_terms_options' => [
        '0' => 'Pagamento Imediato',
        '15' => '15 dias',
        '30' => '30 dias',
        '60' => '60 dias',
        '90' => '90 dias',
    ],
    'auto_payment' => 'Pagamento Automático',
    'auto_payment_desc' => 'Debitar automaticamente o método de pagamento padrão',
    'payment_reminder' => 'Lembrete de Pagamento',
    'payment_reminder_desc' => 'Receba lembretes antes do vencimento',
    'late_payment_interest' => 'Juros por Atraso',
    'late_payment_interest_desc' => 'Aplicar juros para pagamentos em atraso',

    // AÇÕES E BOTÕES
    'save_preferences' => 'Salvar Preferências',
    'test_invoice' => 'Gerar Fatura de Teste',
    'reset_defaults' => 'Restaurar Padrões',
    'export_settings' => 'Exportar Configuração',
    'import_settings' => 'Importar Configuração',
    'validate_tax_data' => 'Validar Dados Fiscais',
    'preview_invoice' => 'Prévia da Fatura',

    // MENSAGENS DE SUCESSO E ERRO
    'preferences_saved' => 'Preferências de faturamento salvas com sucesso',
    'preferences_error' => 'Erro ao salvar as preferências de faturamento',
    'tax_validation_success' => 'Dados fiscais validados corretamente',
    'tax_validation_error' => 'Erro na validação dos dados fiscais',
    'test_invoice_generated' => 'Fatura de teste gerada e enviada',
    'sdi_code_verified' => 'Código SDI verificado com sucesso',
    'vat_number_verified' => 'CNPJ/NIF verificado na Receita Federal',

    // MENSAGENS DE VALIDAÇÃO
    'validation' => [
        'entity_type_required' => 'O tipo de entidade é obrigatório',
        'legal_name_required' => 'A razão social é obrigatória',
        'vat_number_invalid' => 'O CNPJ/NIF não é válido',
        'vat_number_format' => 'Formato de CNPJ/NIF inválido para o país selecionado',
        'tax_code_required' => 'O código fiscal é obrigatório para entidades italianas',
        'sdi_code_invalid' => 'O código SDI deve ter 7 caracteres ou ser um endereço PEC válido',
        'billing_address_required' => 'O endereço de faturamento é obrigatório',
        'invoice_email_required' => 'O email de faturamento é obrigatório',
        'currency_unsupported' => 'Moeda não suportada para o país selecionado',
    ],

    // AJUDA POR PAÍS
    'country_help' => [
        'IT' => [
            'vat_format' => 'Formato: IT + 11 dígitos (ex: IT12345678901)',
            'sdi_required' => 'Código SDI obrigatório para faturamento eletrônico',
            'tax_code_format' => 'Código fiscal: 16 caracteres para pessoas, 11 para empresas',
        ],
        'DE' => [
            'vat_format' => 'Formato: DE + 9 dígitos (ex: DE123456789)',
            'tax_number' => 'Número fiscal alemão obrigatório',
        ],
        'FR' => [
            'vat_format' => 'Formato: FR + 2 letras/dígitos + 9 dígitos',
            'siret_required' => 'Número SIRET obrigatório para empresas francesas',
        ],
        'US' => [
            'ein_format' => 'Formato EIN: XX-XXXXXXX',
            'sales_tax' => 'Configurar imposto estadual',
        ],
    ],

    // CONFORMIDADE E PRIVACIDADE
    'compliance' => [
        'gdpr_notice' => 'Os dados fiscais são tratados conforme o RGPD para conformidade legal',
        'data_retention' => 'Os dados de faturamento são mantidos por 10 anos conforme a lei',
        'third_party_sharing' => 'Os dados são compartilhados apenas com autoridades fiscais e processadores autorizados',
        'encryption_notice' => 'Todos os dados fiscais são criptografados e armazenados com segurança',
        'audit_trail' => 'Todas as alterações nos dados fiscais são registradas para conformidade',
    ],
];

