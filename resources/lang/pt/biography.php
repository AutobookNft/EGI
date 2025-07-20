<?php

/**
 * @Oracode Translation: Biography System Portuguese Translations
 * 🎯 Purpose: Complete Portuguese translations for biography system
 * 📝 Content: User interface strings, validation messages, and system messages
 * 🧭 Navigation: Organized by component (manage, view, form, validation)
 *
 * @package Resources\Lang\Pt
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Biography System)
 * @date 2025-01-07
 */

return [
    // === GENERAL ===
    'biography' => 'Biografia',
    'biographies' => 'Biografias',
    'chapter' => 'Capítulo',
    'chapters' => 'Capítulos',
    'min_read' => 'min de leitura',
    'public' => 'Pública',
    'private' => 'Privada',
    'completed' => 'Concluída',
    'draft' => 'Rascunho',
    'save' => 'Salvar',
    'cancel' => 'Cancelar',
    'edit' => 'Editar',
    'delete' => 'Excluir',
    'view' => 'Visualizar',
    'create' => 'Criar',
    'gallery' => 'Galeria',
    'media' => 'Mídia',
    'video_not_supported' => 'Seu navegador não suporta vídeos HTML5',
    'link_copied' => 'Link copiado para a área de transferência',
    'share' => 'Compartilhar',
    'view_profile' => 'Ver Perfil',
    'discover_more' => 'Descobrir Mais',
    'discover_more_description' => 'Explore outras histórias extraordinárias de criadores e visionários',
    'media_label' => 'Mídia',

    // === MANAGE PAGE ===
    'manage' => [
        'title' => 'Gerencie suas Biografias',
        'subtitle' => 'Crie, edite e organize suas histórias pessoais',
        'your_biographies' => 'Suas Biografias',
        'description' => 'Gerencie suas histórias pessoais e compartilhe-as com o mundo',
        'create_new' => 'Criar Nova Biografia',
        'create_first' => 'Crie sua Primeira Biografia',
        'view_biography' => 'Ver Biografia',
        'edit' => 'Editar',
        'public' => 'Pública',
        'private' => 'Privada',
        'completed' => 'Concluída',
        'chapters' => 'Capítulos',
        'min_read' => 'min leitura',
        'confirm_delete' => 'Tem certeza de que deseja excluir esta biografia? Esta ação não pode ser desfeita.',
        'delete_error' => 'Erro ao excluir biografia. Tente novamente.',
        'no_biographies_title' => 'Ainda não há biografias',
        'no_biographies_description' => 'Comece a contar sua história criando sua primeira biografia. Compartilhe suas experiências, projetos e sua visão do mundo.',
        'empty_title' => 'Nenhuma biografia encontrada',
        'empty_description' => 'Comece a contar sua história criando sua primeira biografia',
    ],

    // === VIEW PAGE ===
    'view' => [
        'title' => 'Sua Biografia',
        'edit_biography' => 'Editar Biografia',
    ],

    // === SHOW PAGE ===
    'show' => [
        'title' => 'Biografia',
        'no_biography_title' => 'Nenhuma biografia disponível',
        'no_biography_description' => 'Este usuário ainda não criou uma biografia pública.',
    ],

    // === FORM ===
    'form' => [
        'title' => 'Título',
        'title_placeholder' => 'Digite o título da sua biografia',
        'type' => 'Tipo de Biografia',
        'content' => 'Conteúdo',
        'content_placeholder' => 'Conte sua história...',
        'excerpt' => 'Resumo',
        'excerpt_placeholder' => 'Breve descrição da sua biografia',
        'excerpt_help' => 'Máximo 500 caracteres. Usado para visualizações e compartilhamento.',
        'is_public' => 'Pública',
        'is_public_help' => 'Tornar a biografia visível para todos',
        'is_completed' => 'Concluída',
        'is_completed_help' => 'Marcar como concluída',
        'settings' => 'Configurações',
        'featured_image' => 'Imagem em Destaque',
        'featured_image_hint' => 'Faça upload de uma imagem representativa (JPEG, PNG, WebP, máx 2MB). Usada para visualizações e compartilhamento.',
        'save_biography' => 'Salvar Biografia',
        'create_biography' => 'Criar Biografia',
        'update_biography' => 'Atualizar Biografia',
    ],

    // === BIOGRAPHY TYPES ===
    'type' => [
        'single' => 'Única',
        'single_description' => 'Uma biografia em formato único, ideal para relatos breves',
        'chapters' => 'Capítulos',
        'chapters_description' => 'Biografia organizada em capítulos, perfeita para histórias longas e detalhadas',
    ],

    // === VALIDATION MESSAGES ===
    'validation' => [
        'title_required' => 'O título é obrigatório',
        'title_max' => 'O título não pode exceder 255 caracteres',
        'content_required' => 'O conteúdo é obrigatório',
        'type_required' => 'O tipo de biografia é obrigatório',
        'type_invalid' => 'O tipo de biografia não é válido',
        'excerpt_max' => 'O resumo não pode exceder 500 caracteres',
        'slug_unique' => 'Este slug já está em uso',
        'featured_image_max' => 'A imagem não pode exceder 2MB',
        'featured_image_mimes' => 'A imagem deve estar em formato JPEG, PNG ou WebP',
    ],

    // === SUCCESS MESSAGES ===
    'success' => [
        'created' => 'Biografia criada com sucesso',
        'updated' => 'Biografia atualizada com sucesso',
        'deleted' => 'Biografia excluída com sucesso',
        'published' => 'Biografia publicada com sucesso',
        'unpublished' => 'Biografia tornada privada com sucesso',
    ],

    // === ERROR MESSAGES ===
    'error' => [
        'not_found' => 'Biografia não encontrada',
        'unauthorized' => 'Não autorizado a acessar esta biografia',
        'create_failed' => 'Erro ao criar biografia',
        'update_failed' => 'Erro ao atualizar biografia',
        'delete_failed' => 'Erro ao excluir biografia',
        'generic' => 'Ocorreu um erro. Tente novamente.',
    ],

    // === CHAPTER SPECIFIC ===
    'chapter' => [
        'title' => 'Título do Capítulo',
        'content' => 'Conteúdo do Capítulo',
        'date_from' => 'Data de Início',
        'date_to' => 'Data de Fim',
        'is_ongoing' => 'Em Andamento',
        'sort_order' => 'Ordem',
        'is_published' => 'Publicado',
        'chapter_type' => 'Tipo de Capítulo',
        'add_chapter' => 'Adicionar Capítulo',
        'edit_chapter' => 'Editar Capítulo',
        'delete_chapter' => 'Excluir Capítulo',
        'reorder_chapters' => 'Reordenar Capítulos',
        'no_chapters' => 'Ainda não há capítulos',
        'no_chapters_description' => 'Comece a adicionar capítulos à sua biografia',
    ],

    // === MEDIA ===
    'media' => [
        'upload' => 'Fazer Upload de Mídia',
        'featured_image' => 'Imagem em Destaque',
        'gallery' => 'Galeria',
        'caption' => 'Legenda',
        'alt_text' => 'Texto Alternativo',
        'upload_failed' => 'Erro ao fazer upload do arquivo',
        'delete_media' => 'Excluir Mídia',
        'no_media' => 'Nenhuma mídia carregada',
    ],

    // === NAVIGATION ===
    'navigation' => [
        'all_biographies' => 'Todas as Biografias',
        'my_biographies' => 'Minhas Biografias',
        'public_biographies' => 'Biografias Públicas',
        'create_biography' => 'Criar Biografia',
        'manage_biographies' => 'Gerenciar Biografias',
    ],

    // === STATS ===
    'stats' => [
        'total_biographies' => 'Total de Biografias',
        'public_biographies' => 'Biografias Públicas',
        'total_chapters' => 'Total de Capítulos',
        'total_words' => 'Total de Palavras',
        'reading_time' => 'Tempo de Leitura',
        'last_updated' => 'Última Atualização',
    ],

    // === SHARING ===
    'sharing' => [
        'share_biography' => 'Compartilhar Biografia',
        'copy_link' => 'Copiar Link',
        'social_share' => 'Compartilhar nas Redes Sociais',
        'embed_code' => 'Código de Incorporação',
        'qr_code' => 'Código QR',
    ],

    // === PRIVACY ===
    'privacy' => [
        'public_description' => 'Visível para todos na internet',
        'private_description' => 'Visível apenas para você',
        'unlisted_description' => 'Visível apenas com link direto',
        'change_privacy' => 'Alterar Privacidade',
        'privacy_updated' => 'Configurações de privacidade atualizadas',
    ],

    // === TIMELINE ===
    'timeline' => [
        'show_timeline' => 'Mostrar Linha do Tempo',
        'hide_timeline' => 'Ocultar Linha do Tempo',
        'chronological' => 'Cronológico',
        'reverse_chronological' => 'Cronológico Inverso',
        'custom_order' => 'Ordem Personalizada',
        'date_range' => 'Período',
        'ongoing' => 'Em andamento',
        'present' => 'Presente',
    ],

    // === EXPORT ===
    'export' => [
        'export_biography' => 'Exportar Biografia',
        'export_pdf' => 'Exportar PDF',
        'export_word' => 'Exportar Word',
        'export_html' => 'Exportar HTML',
        'export_success' => 'Biografia exportada com sucesso',
        'export_failed' => 'Erro durante a exportação',
    ],

    // === SEARCH ===
    'search' => [
        'search_biographies' => 'Pesquisar Biografias',
        'search_placeholder' => 'Pesquisar por título, conteúdo ou autor...',
        'no_results' => 'Nenhum resultado encontrado',
        'results_found' => 'resultados encontrados',
        'filter_by' => 'Filtrar por',
        'filter_type' => 'Tipo',
        'filter_date' => 'Data',
        'filter_author' => 'Autor',
        'clear_filters' => 'Limpar Filtros',
    ],

    // === COMMENTS ===
    'comments' => [
        'comments' => 'Comentários',
        'add_comment' => 'Adicionar Comentário',
        'no_comments' => 'Ainda não há comentários',
        'comment_added' => 'Comentário adicionado com sucesso',
        'comment_deleted' => 'Comentário excluído com sucesso',
        'enable_comments' => 'Habilitar Comentários',
        'disable_comments' => 'Desabilitar Comentários',
    ],

    // === NOTIFICATIONS ===
    'notifications' => [
        'new_biography' => 'Nova biografia publicada',
        'biography_updated' => 'Biografia atualizada',
        'new_chapter' => 'Novo capítulo adicionado',
        'chapter_updated' => 'Capítulo atualizado',
        'new_comment' => 'Novo comentário recebido',
    ],

    // === EDIT PAGE SPECIFIC ===
    'edit_page' => [
        'edit_biography' => 'Editar Biografia',
        'create_new_biography' => 'Criar Nova Biografia',
        'tell_story_description' => 'Conte sua história e compartilhe-a com o mundo',
        'validation_errors' => 'Erros de Validação',
        'basic_info' => 'Informações Básicas',
        'media_management' => 'Gerenciamento de Mídia',
        'settings' => 'Configurações',
        'title_required' => 'Título *',
        'title_placeholder' => 'Digite o título da sua biografia',
        'content_required' => 'Conteúdo *',
        'content_placeholder' => 'Conte sua história...',
        'excerpt' => 'Resumo',
        'excerpt_placeholder' => 'Breve descrição da sua biografia...',
        'excerpt_help' => 'Descrição breve que aparecerá na visualização',
        'add_chapter' => 'Adicionar Capítulo',
        'edit_chapter' => 'Editar',
        'delete_chapter' => 'Excluir',
        'biography_images' => 'Imagens da Biografia',
        'upload_images_help' => 'Faça upload das imagens para sua biografia. Formatos suportados: JPG, PNG, WEBP (Máx 2MB cada)',
        'uploading_images' => 'Fazendo upload das imagens...',
        'uploaded_images' => 'Imagens Carregadas',
        'biography_public' => 'Biografia Pública',
        'biography_public_help' => 'Tornar sua biografia visível para todos os usuários',
        'go_back' => 'Voltar',
        'update_biography' => 'Atualizar Biografia',
        'create_biography' => 'Criar Biografia',
    ],
];
