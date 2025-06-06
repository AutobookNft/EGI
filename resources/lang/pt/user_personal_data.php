<?php

/**
 * @Oracode Translation File: Personal Data Management - Português
 * 🎯 Purpose: Complete Portuguese translations for GDPR-compliant personal data management
 * 🛡️ Privacy: GDPR-compliant notices, consent language, data subject rights
 * 🌐 i18n: Base language file for FlorenceEGI personal data domain
 * 🧱 Core Logic: Supports all personal data CRUD operations with privacy notices
 * ⏰ MVP: Critical for Portuguese market compliance and user trust
 *
 * @package Lang\Pt
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - GDPR Native)
 * @deadline 2025-06-30
 */

return [
    // TÍTULOS E CABEÇALHOS DA PÁGINA
    'management_title' => 'Gestão de Dados Pessoais',
    'management_subtitle' => 'Gerencie seus dados pessoais em conformidade com o RGPD',
    'edit_title' => 'Editar Dados Pessoais',
    'edit_subtitle' => 'Atualize suas informações pessoais com segurança',
    'export_title' => 'Exportar Dados Pessoais',
    'export_subtitle' => 'Baixe uma cópia completa dos seus dados pessoais',
    'deletion_title' => 'Solicitação de Eliminação de Dados',
    'deletion_subtitle' => 'Solicite a exclusão permanente dos seus dados pessoais',

    // SEÇÕES DO FORMULÁRIO
    'basic_information' => 'Informações Básicas',
    'basic_description' => 'Dados essenciais para identificação',
    'fiscal_information' => 'Informações Fiscais',
    'fiscal_description' => 'Código fiscal e dados para obrigações fiscais',
    'address_information' => 'Informações de Endereço',
    'address_description' => 'Endereço de residência e domicílio',
    'contact_information' => 'Informações de Contato',
    'contact_description' => 'Telefone e outros contatos',
    'identity_verification' => 'Verificação de Identidade',
    'identity_description' => 'Verifique sua identidade para alterações sensíveis',

    // CAMPOS DO FORMULÁRIO
    'first_name' => 'Nome',
    'first_name_placeholder' => 'Digite seu nome',
    'last_name' => 'Sobrenome',
    'last_name_placeholder' => 'Digite seu sobrenome',
    'birth_date' => 'Data de Nascimento',
    'birth_date_placeholder' => 'Selecione sua data de nascimento',
    'birth_place' => 'Local de Nascimento',
    'birth_place_placeholder' => 'Cidade e estado de nascimento',
    'gender' => 'Gênero',
    'gender_male' => 'Masculino',
    'gender_female' => 'Feminino',
    'gender_other' => 'Outro',
    'gender_prefer_not_say' => 'Prefiro não informar',

    // Campos fiscais
    'tax_code' => 'Código Fiscal',
    'tax_code_placeholder' => 'RSSMRA80A01H501X',
    'tax_code_help' => 'Seu código fiscal italiano (16 caracteres)',
    'id_card_number' => 'Número do Documento de Identidade',
    'id_card_number_placeholder' => 'Número do documento de identidade',
    'passport_number' => 'Número do Passaporte',
    'passport_number_placeholder' => 'Número do passaporte (se houver)',
    'driving_license' => 'Carteira de Motorista',
    'driving_license_placeholder' => 'Número da carteira de motorista',

    // Campos de endereço
    'street_address' => 'Endereço',
    'street_address_placeholder' => 'Rua, número',
    'city' => 'Cidade',
    'city_placeholder' => 'Nome da cidade',
    'postal_code' => 'CEP',
    'postal_code_placeholder' => '00100',
    'province' => 'Estado',
    'province_placeholder' => 'Sigla do estado (ex: SP)',
    'region' => 'Região',
    'region_placeholder' => 'Nome da região',
    'country' => 'País',
    'country_placeholder' => 'Selecione o país',

    // Campos de contato
    'phone' => 'Telefone',
    'phone_placeholder' => '+55 11 91234-5678',
    'mobile' => 'Celular',
    'mobile_placeholder' => '+55 11 91234-5678',
    'emergency_contact' => 'Contato de Emergência',
    'emergency_contact_placeholder' => 'Nome e telefone',

    // PRIVACIDADE E CONSENTIMENTO
    'consent_management' => 'Gestão de Consentimentos',
    'consent_description' => 'Gerencie seus consentimentos para tratamento de dados',
    'consent_required' => 'Consentimento Obrigatório',
    'consent_optional' => 'Consentimento Opcional',
    'consent_marketing' => 'Marketing e Comunicações',
    'consent_marketing_desc' => 'Consentimento para receber comunicações de marketing',
    'consent_profiling' => 'Perfilamento',
    'consent_profiling_desc' => 'Consentimento para atividades de perfilamento e análise',
    'consent_analytics' => 'Análises',
    'consent_analytics_desc' => 'Consentimento para análises estatísticas anonimizadas',
    'consent_third_party' => 'Terceiros',
    'consent_third_party_desc' => 'Consentimento para compartilhamento com parceiros selecionados',

    // AÇÕES E BOTÕES
    'update_data' => 'Atualizar Dados',
    'save_changes' => 'Salvar Alterações',
    'cancel_changes' => 'Cancelar',
    'export_data' => 'Exportar Dados',
    'request_deletion' => 'Solicitar Exclusão',
    'verify_identity' => 'Verificar Identidade',
    'confirm_changes' => 'Confirmar Alterações',
    'back_to_profile' => 'Voltar ao Perfil',

    // MENSAGENS DE SUCESSO E ERRO
    'update_success' => 'Dados pessoais atualizados com sucesso',
    'update_error' => 'Erro ao atualizar dados pessoais',
    'validation_error' => 'Alguns campos contêm erros. Verifique e tente novamente.',
    'identity_verification_required' => 'Verificação de identidade necessária para esta operação',
    'identity_verification_failed' => 'Falha na verificação de identidade. Tente novamente.',
    'export_started' => 'Exportação de dados iniciada. Você receberá um e-mail quando estiver pronta.',
    'export_ready' => 'Sua exportação de dados está pronta para download',
    'deletion_requested' => 'Solicitação de exclusão enviada. Será processada em até 30 dias.',

    // MENSAGENS DE VALIDAÇÃO
    'validation' => [
        'first_name_required' => 'O nome é obrigatório',
        'last_name_required' => 'O sobrenome é obrigatório',
        'birth_date_required' => 'A data de nascimento é obrigatória',
        'birth_date_valid' => 'A data de nascimento deve ser válida',
        'birth_date_age' => 'Você deve ter pelo menos 13 anos para se cadastrar',
        'tax_code_invalid' => 'O código fiscal não é válido',
        'tax_code_format' => 'O código fiscal deve ter 16 caracteres',
        'phone_invalid' => 'O número de telefone não é válido',
        'postal_code_invalid' => 'O CEP não é válido para o país selecionado',
        'country_required' => 'O país é obrigatório',
    ],

    // AVISOS RGPD
    'gdpr_notices' => [
        'data_processing_info' => 'Seus dados pessoais são tratados de acordo com o RGPD (UE) 2016/679',
        'data_controller' => 'Controlador dos dados: FlorenceEGI S.r.l.',
        'data_purpose' => 'Finalidade: Gestão da conta do usuário e serviços da plataforma',
        'data_retention' => 'Retenção: Os dados são mantidos pelo tempo necessário aos serviços solicitados',
        'data_rights' => 'Direitos: Você pode acessar, retificar, excluir ou limitar o tratamento dos seus dados',
        'data_contact' => 'Para exercer seus direitos, contate: privacy@florenceegi.com',
        'sensitive_data_warning' => 'Atenção: você está editando dados sensíveis. Verificação de identidade necessária.',
        'audit_notice' => 'Todas as alterações nos dados pessoais são registradas por segurança',
    ],

    // FUNCIONALIDADE DE EXPORTAÇÃO
    'export' => [
        'formats' => [
            'json' => 'JSON (Legível por máquina)',
            'pdf' => 'PDF (Legível por humanos)',
            'csv' => 'CSV (Planilha)',
        ],
        'categories' => [
            'basic' => 'Informações Básicas',
            'fiscal' => 'Dados Fiscais',
            'address' => 'Endereço',
            'contact' => 'Informações de Contato',
            'consents' => 'Consensos e Preferências',
            'audit' => 'Registro de Alterações',
        ],
        'select_format' => 'Selecione o formato de exportação',
        'select_categories' => 'Selecione as categorias para exportar',
        'generate_export' => 'Gerar Exportação',
        'download_ready' => 'Download Pronto',
        'download_expires' => 'O link de download expira em 7 dias',
    ],

    // FLUXO DE EXCLUSÃO
    'deletion' => [
        'confirm_title' => 'Confirmar Exclusão de Dados',
        'warning_irreversible' => 'ATENÇÃO: Esta operação é irreversível',
        'warning_account' => 'A exclusão dos dados encerrará permanentemente sua conta',
        'warning_backup' => 'Os dados podem ser mantidos em backups por até 90 dias',
        'reason_required' => 'Motivo da solicitação (opcional)',
        'reason_placeholder' => 'Você pode especificar o motivo da exclusão...',
        'final_confirmation' => 'Confirmo que desejo excluir permanentemente meus dados pessoais',
        'type_delete' => 'Digite "EXCLUIR" para confirmar',
        'submit_request' => 'Enviar Solicitação de Exclusão',
        'request_submitted' => 'Solicitação de exclusão enviada com sucesso',
        'processing_time' => 'A solicitação será processada em até 30 dias úteis',
    ],
];

