<?php

/**
 * @Oracode Translation: Biography System French Translations
 * 🎯 Purpose: Complete French translations for biography system
 * 📝 Content: User interface strings, validation messages, and system messages
 * 🧭 Navigation: Organized by component (manage, view, form, validation)
 *
 * @package Resources\Lang\Fr
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Biography System)
 * @date 2025-01-07
 */

return [
    // === GENERAL ===
    'biography' => 'Biographie',
    'biographies' => 'Biographies',
    'chapter' => 'Chapitre',
    'chapters' => 'Chapitres',
    'min_read' => 'min de lecture',
    'public' => 'Publique',
    'private' => 'Privée',
    'completed' => 'Terminée',
    'draft' => 'Brouillon',
    'save' => 'Enregistrer',
    'cancel' => 'Annuler',
    'edit' => 'Modifier',
    'delete' => 'Supprimer',
    'view' => 'Voir',
    'create' => 'Créer',
    'gallery' => 'Galerie',
    'media' => 'Médias',
    'video_not_supported' => 'Votre navigateur ne prend pas en charge les vidéos HTML5',
    'link_copied' => 'Lien copié dans le presse-papiers',
    'share' => 'Partager',
    'view_profile' => 'Voir le Profil',
    'discover_more' => 'Découvrir Plus',
    'discover_more_description' => 'Explorez d\'autres histoires extraordinaires de créateurs et visionnaires',

    // === MANAGE PAGE ===
    'manage' => [
        'title' => 'Gérez vos Biographies',
        'subtitle' => 'Créez, modifiez et organisez vos histoires personnelles',
        'your_biographies' => 'Vos Biographies',
        'description' => 'Gérez vos histoires personnelles et partagez-les avec le monde',
        'create_new' => 'Créer une Nouvelle Biographie',
        'create_first' => 'Créez votre Première Biographie',
        'view_biography' => 'Voir la Biographie',
        'edit' => 'Modifier',
        'public' => 'Publique',
        'private' => 'Privée',
        'completed' => 'Terminée',
        'chapters' => 'Chapitres',
        'min_read' => 'min lecture',
        'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer cette biographie ? Cette action ne peut pas être annulée.',
        'delete_error' => 'Erreur lors de la suppression de la biographie. Veuillez réessayer.',
        'no_biographies_title' => 'Aucune biographie pour le moment',
        'no_biographies_description' => 'Commencez à raconter votre histoire en créant votre première biographie. Partagez vos expériences, projets et votre vision du monde.',
        'empty_title' => 'Aucune biographie trouvée',
        'empty_description' => 'Commencez à raconter votre histoire en créant votre première biographie',
    ],

    // === VIEW PAGE ===
    'view' => [
        'title' => 'Votre Biographie',
        'edit_biography' => 'Modifier la Biographie',
    ],

    // === SHOW PAGE ===
    'show' => [
        'title' => 'Biographie',
        'no_biography_title' => 'Aucune biographie disponible',
        'no_biography_description' => 'Cet utilisateur n\'a pas encore créé de biographie publique.',
    ],

    // === FORM ===
    'form' => [
        'title' => 'Titre',
        'title_placeholder' => 'Entrez le titre de votre biographie',
        'type' => 'Type de Biographie',
        'content' => 'Contenu',
        'content_placeholder' => 'Racontez votre histoire...',
        'excerpt' => 'Extrait',
        'excerpt_placeholder' => 'Brève description de votre biographie',
        'excerpt_help' => 'Maximum 500 caractères. Utilisé pour les aperçus et le partage.',
        'is_public' => 'Publique',
        'is_public_help' => 'Rendre la biographie visible pour tous',
        'is_completed' => 'Terminée',
        'is_completed_help' => 'Marquer comme terminée',
        'settings' => 'Paramètres',
        'featured_image' => 'Image à la Une',
        'featured_image_hint' => 'Téléchargez une image représentative (JPEG, PNG, WebP, max 2MB). Utilisée pour les aperçus et le partage.',
        'save_biography' => 'Enregistrer la Biographie',
        'create_biography' => 'Créer une Biographie',
        'update_biography' => 'Mettre à Jour la Biographie',
    ],

    // === BIOGRAPHY TYPES ===
    'type' => [
        'single' => 'Unique',
        'single_description' => 'Une biographie en format unique, idéale pour les récits courts',
        'chapters' => 'Chapitres',
        'chapters_description' => 'Biographie organisée en chapitres, parfaite pour les histoires longues et détaillées',
    ],

    // === VALIDATION MESSAGES ===
    'validation' => [
        'title_required' => 'Le titre est obligatoire',
        'title_max' => 'Le titre ne peut pas dépasser 255 caractères',
        'content_required' => 'Le contenu est obligatoire',
        'type_required' => 'Le type de biographie est obligatoire',
        'type_invalid' => 'Le type de biographie n\'est pas valide',
        'excerpt_max' => 'L\'extrait ne peut pas dépasser 500 caractères',
        'slug_unique' => 'Ce slug est déjà utilisé',
        'featured_image_max' => 'L\'image ne peut pas dépasser 2MB',
        'featured_image_mimes' => 'L\'image doit être au format JPEG, PNG ou WebP',
    ],

    // === SUCCESS MESSAGES ===
    'success' => [
        'created' => 'Biographie créée avec succès',
        'updated' => 'Biographie mise à jour avec succès',
        'deleted' => 'Biographie supprimée avec succès',
        'published' => 'Biographie publiée avec succès',
        'unpublished' => 'Biographie rendue privée avec succès',
    ],

    // === ERROR MESSAGES ===
    'error' => [
        'not_found' => 'Biographie non trouvée',
        'unauthorized' => 'Non autorisé à accéder à cette biographie',
        'create_failed' => 'Erreur lors de la création de la biographie',
        'update_failed' => 'Erreur lors de la mise à jour de la biographie',
        'delete_failed' => 'Erreur lors de la suppression de la biographie',
        'generic' => 'Une erreur s\'est produite. Veuillez réessayer.',
    ],

    // === CHAPTER SPECIFIC ===
    'chapter' => [
        'title' => 'Titre du Chapitre',
        'content' => 'Contenu du Chapitre',
        'date_from' => 'Date de Début',
        'date_to' => 'Date de Fin',
        'is_ongoing' => 'En Cours',
        'sort_order' => 'Ordre',
        'is_published' => 'Publié',
        'chapter_type' => 'Type de Chapitre',
        'add_chapter' => 'Ajouter un Chapitre',
        'edit_chapter' => 'Modifier le Chapitre',
        'delete_chapter' => 'Supprimer le Chapitre',
        'reorder_chapters' => 'Réorganiser les Chapitres',
        'no_chapters' => 'Aucun chapitre pour le moment',
        'no_chapters_description' => 'Commencez à ajouter des chapitres à votre biographie',
    ],

    // === MEDIA ===
    'media' => [
        'upload' => 'Télécharger des Médias',
        'featured_image' => 'Image à la Une',
        'gallery' => 'Galerie',
        'caption' => 'Légende',
        'alt_text' => 'Texte Alternatif',
        'upload_failed' => 'Erreur lors du téléchargement du fichier',
        'delete_media' => 'Supprimer les Médias',
        'no_media' => 'Aucun média téléchargé',
    ],

    // === NAVIGATION ===
    'navigation' => [
        'all_biographies' => 'Toutes les Biographies',
        'my_biographies' => 'Mes Biographies',
        'public_biographies' => 'Biographies Publiques',
        'create_biography' => 'Créer une Biographie',
        'manage_biographies' => 'Gérer les Biographies',
    ],

    // === STATS ===
    'stats' => [
        'total_biographies' => 'Total des Biographies',
        'public_biographies' => 'Biographies Publiques',
        'total_chapters' => 'Total des Chapitres',
        'total_words' => 'Total des Mots',
        'reading_time' => 'Temps de Lecture',
        'last_updated' => 'Dernière Mise à Jour',
    ],

    // === SHARING ===
    'sharing' => [
        'share_biography' => 'Partager la Biographie',
        'copy_link' => 'Copier le Lien',
        'social_share' => 'Partager sur les Réseaux Sociaux',
        'embed_code' => 'Code d\'Intégration',
        'qr_code' => 'Code QR',
    ],

    // === PRIVACY ===
    'privacy' => [
        'public_description' => 'Visible pour tous sur internet',
        'private_description' => 'Visible uniquement pour vous',
        'unlisted_description' => 'Visible uniquement avec un lien direct',
        'change_privacy' => 'Changer la Confidentialité',
        'privacy_updated' => 'Paramètres de confidentialité mis à jour',
    ],

    // === TIMELINE ===
    'timeline' => [
        'show_timeline' => 'Afficher la Chronologie',
        'hide_timeline' => 'Masquer la Chronologie',
        'chronological' => 'Chronologique',
        'reverse_chronological' => 'Chronologique Inverse',
        'custom_order' => 'Ordre Personnalisé',
        'date_range' => 'Période',
        'ongoing' => 'En cours',
        'present' => 'Présent',
    ],

    // === EXPORT ===
    'export' => [
        'export_biography' => 'Exporter la Biographie',
        'export_pdf' => 'Exporter en PDF',
        'export_word' => 'Exporter en Word',
        'export_html' => 'Exporter en HTML',
        'export_success' => 'Biographie exportée avec succès',
        'export_failed' => 'Erreur lors de l\'exportation',
    ],

    // === SEARCH ===
    'search' => [
        'search_biographies' => 'Rechercher des Biographies',
        'search_placeholder' => 'Rechercher par titre, contenu ou auteur...',
        'no_results' => 'Aucun résultat trouvé',
        'results_found' => 'résultats trouvés',
        'filter_by' => 'Filtrer par',
        'filter_type' => 'Type',
        'filter_date' => 'Date',
        'filter_author' => 'Auteur',
        'clear_filters' => 'Effacer les Filtres',
    ],

    // === COMMENTS ===
    'comments' => [
        'comments' => 'Commentaires',
        'add_comment' => 'Ajouter un Commentaire',
        'no_comments' => 'Aucun commentaire pour le moment',
        'comment_added' => 'Commentaire ajouté avec succès',
        'comment_deleted' => 'Commentaire supprimé avec succès',
        'enable_comments' => 'Activer les Commentaires',
        'disable_comments' => 'Désactiver les Commentaires',
    ],

    // === NOTIFICATIONS ===
    'notifications' => [
        'new_biography' => 'Nouvelle biographie publiée',
        'biography_updated' => 'Biographie mise à jour',
        'new_chapter' => 'Nouveau chapitre ajouté',
        'chapter_updated' => 'Chapitre mis à jour',
        'new_comment' => 'Nouveau commentaire reçu',
    ],

    // === EDIT PAGE SPECIFIC ===
    'edit_page' => [
        'edit_biography' => 'Modifier la Biographie',
        'create_new_biography' => 'Créer une Nouvelle Biographie',
        'tell_story_description' => 'Racontez votre histoire et partagez-la avec le monde',
        'validation_errors' => 'Erreurs de Validation',
        'basic_info' => 'Informations de Base',
        'media_management' => 'Gestion des Médias',
        'settings' => 'Paramètres',
        'title_required' => 'Titre *',
        'title_placeholder' => 'Entrez le titre de votre biographie',
        'content_required' => 'Contenu *',
        'content_placeholder' => 'Racontez votre histoire...',
        'excerpt' => 'Extrait',
        'excerpt_placeholder' => 'Brève description de votre biographie...',
        'excerpt_help' => 'Description brève qui apparaîtra en aperçu',
        'add_chapter' => 'Ajouter un Chapitre',
        'edit_chapter' => 'Modifier',
        'delete_chapter' => 'Supprimer',
        'biography_images' => 'Images de la Biographie',
        'upload_images_help' => 'Téléchargez les images pour votre biographie. Formats supportés : JPG, PNG, WEBP (Max 2MB chacune)',
        'uploading_images' => 'Téléchargement des images...',
        'uploaded_images' => 'Images Téléchargées',
        'biography_public' => 'Biographie Publique',
        'biography_public_help' => 'Rendre votre biographie visible pour tous les utilisateurs',
        'go_back' => 'Retour',
        'update_biography' => 'Mettre à Jour la Biographie',
        'create_biography' => 'Créer une Biographie',
    ],
];
