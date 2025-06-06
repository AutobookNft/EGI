<?php

/**
 * @Oracode Translation File: Document Management - Portuguese
 * 🎯 Purpose: Complete Portuguese translations for document upload and verification system
 * 🛡️ Privacy: Document security, verification status, GDPR compliance
 * 🌐 i18n: Document management translations for Portuguese users
 * 🧱 Core Logic: Supports document upload, verification, and identity confirmation
 * ⏰ MVP: Critical for KYC compliance and user verification
 *
 * @package Lang\Pt
 * @author Padmin D. Curtis (AI Partner OS1-Compliant)
 * @version 1.0.0 (FlorenceEGI MVP - KYC Ready)
 * @deadline 2025-06-30
 */

return [
    // TÍTULOS E CABEÇALHOS DA PÁGINA
    'management_title' => 'Gestão de Documentos',
    'management_subtitle' => 'Carregue e gerencie seus documentos de identidade',
    'upload_title' => 'Enviar Documento',
    'upload_subtitle' => 'Envie um novo documento para verificação',
    'verification_title' => 'Status da Verificação',
    'verification_subtitle' => 'Verifique o status de verificação dos seus documentos',

    // TIPOS DE DOCUMENTO
    'types' => [
        'identity_card' => 'Cartão de Identidade',
        'passport' => 'Passaporte',
        'driving_license' => 'Carta de Condução',
        'fiscal_code_card' => 'Cartão de Código Fiscal',
        'residence_certificate' => 'Certificado de Residência',
        'birth_certificate' => 'Certidão de Nascimento',
        'business_registration' => 'Registro Empresarial',
        'vat_certificate' => 'Certificado de IVA',
        'bank_statement' => 'Extrato Bancário',
        'utility_bill' => 'Fatura de Serviço (Comprovante de Morada)',
        'other' => 'Outro Documento',
    ],

    // STATUS DE VERIFICAÇÃO
    'status' => [
        'pending' => 'Pendente',
        'under_review' => 'Em Análise',
        'approved' => 'Aprovado',
        'rejected' => 'Rejeitado',
        'expired' => 'Expirado',
        'requires_reupload' => 'Necessita Novo Envio',
    ],

    'status_descriptions' => [
        'pending' => 'Documento enviado, aguardando verificação',
        'under_review' => 'O documento está sendo verificado pela nossa equipe',
        'approved' => 'Documento verificado e aprovado',
        'rejected' => 'Documento rejeitado. Confira os motivos e envie novamente',
        'expired' => 'Documento expirado. Envie uma versão atualizada',
        'requires_reupload' => 'Necessário reenviar o documento com melhor qualidade',
    ],

    // FORMULÁRIO DE ENVIO
    'upload_form' => [
        'document_type' => 'Tipo de Documento',
        'document_type_placeholder' => 'Selecione o tipo de documento',
        'document_file' => 'Arquivo do Documento',
        'document_file_help' => 'Formatos suportados: PDF, JPG, PNG. Tamanho máximo: 10MB',
        'document_notes' => 'Notas (Opcional)',
        'document_notes_placeholder' => 'Adicione notas ou informações adicionais...',
        'expiry_date' => 'Data de Validade',
        'expiry_date_placeholder' => 'Insira a data de validade do documento',
        'expiry_date_help' => 'Insira a data de validade se aplicável',
        'upload_button' => 'Enviar Documento',
        'replace_button' => 'Substituir Documento',
    ],

    // LISTA DE DOCUMENTOS
    'list' => [
        'your_documents' => 'Seus Documentos',
        'no_documents' => 'Nenhum documento enviado',
        'no_documents_desc' => 'Envie seus documentos para concluir a verificação de identidade',
        'document_name' => 'Nome do Documento',
        'upload_date' => 'Data de Envio',
        'status' => 'Status',
        'actions' => 'Ações',
        'download' => 'Baixar',
        'replace' => 'Substituir',
        'delete' => 'Excluir',
        'view_details' => 'Ver Detalhes',
    ],

    // AÇÕES E BOTÕES
    'upload_new' => 'Enviar Novo Documento',
    'view_document' => 'Ver Documento',
    'download_document' => 'Baixar Documento',
    'delete_document' => 'Excluir Documento',
    'replace_document' => 'Substituir Documento',
    'request_verification' => 'Solicitar Verificação',
    'back_to_list' => 'Voltar à Lista',

    // MENSAGENS DE SUCESSO E ERRO
    'upload_success' => 'Documento enviado com sucesso',
    'upload_error' => 'Erro ao enviar o documento',
    'delete_success' => 'Documento excluído com sucesso',
    'delete_error' => 'Erro ao excluir o documento',
    'verification_requested' => 'Verificação solicitada. Você receberá atualizações por e-mail.',
    'verification_completed' => 'Verificação do documento concluída',

    // MENSAGENS DE VALIDAÇÃO
    'validation' => [
        'document_type_required' => 'O tipo de documento é obrigatório',
        'document_file_required' => 'O arquivo do documento é obrigatório',
        'document_file_mimes' => 'O documento deve ser em PDF, JPG ou PNG',
        'document_file_max' => 'O documento não pode exceder 10MB',
        'expiry_date_future' => 'A data de validade deve ser futura',
        'document_already_exists' => 'Você já enviou um documento desse tipo',
    ],

    // SEGURANÇA E PRIVACIDADE
    'security' => [
        'encryption_notice' => 'Todos os documentos são criptografados e armazenados com segurança',
        'access_log' => 'Todos os acessos aos documentos são registrados por segurança',
        'retention_policy' => 'Os documentos são mantidos conforme a legislação vigente',
        'delete_warning' => 'A exclusão de um documento é irreversível',
        'verification_required' => 'Os documentos são verificados manualmente pela nossa equipe',
        'processing_time' => 'A verificação geralmente leva de 2 a 5 dias úteis',
    ],

    // REQUISITOS DE ARQUIVO
    'requirements' => [
        'title' => 'Requisitos do Documento',
        'quality' => 'Imagem nítida e bem iluminada',
        'completeness' => 'Documento completo, não recortado',
        'readability' => 'Texto claramente legível',
        'validity' => 'Documento válido e não expirado',
        'authenticity' => 'Documento original, não fotocópias de fotocópias',
        'format' => 'Formato suportado: PDF, JPG, PNG',
        'size' => 'Tamanho máximo: 10MB',
    ],

    // DETALHES DE VERIFICAÇÃO
    'verification' => [
        'process_title' => 'Processo de Verificação',
        'step1' => '1. Envio do documento',
        'step2' => '2. Verificação automática de qualidade',
        'step3' => '3. Verificação manual da equipe',
        'step4' => '4. Notificação do resultado',
        'rejection_reasons' => 'Motivos Comuns de Rejeição',
        'poor_quality' => 'Imagem de baixa qualidade',
        'incomplete' => 'Documento incompleto ou recortado',
        'expired' => 'Documento expirado',
        'unreadable' => 'Texto ilegível',
        'wrong_type' => 'Tipo de documento incorreto',
        'suspected_fraud' => 'Suspeita de falsificação',
    ],
];

