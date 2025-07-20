<?php

/**
 * @Oracode Translation: Biography System English Translations
 * 🎯 Purpose: Complete English translations for biography system
 * 📝 Content: User interface strings, validation messages, and system messages
 * 🧭 Navigation: Organized by component (manage, view, form, validation)
 *
 * @package Resources\Lang\En
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Biography System)
 * @date 2025-01-07
 */

return [
    // === GENERAL ===
    'biography' => 'Biography',
    'biographies' => 'Biographies',
    'chapter' => 'Chapter',
    'chapters' => 'Chapters',
    'min_read' => 'min read',
    'public' => 'Public',
    'private' => 'Private',
    'completed' => 'Completed',
    'draft' => 'Draft',
    'save' => 'Save',
    'cancel' => 'Cancel',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'view' => 'View',
    'create' => 'Create',
    'gallery' => 'Gallery',
    'media' => 'Media',
    'video_not_supported' => 'Your browser does not support HTML5 video',
    'link_copied' => 'Link copied to clipboard',
    'share' => 'Share',
    'view_profile' => 'View Profile',
    'discover_more' => 'Discover More',
    'discover_more_description' => 'Explore other extraordinary stories of creators and visionaries',
    'media_label' => 'Media',

    // === MANAGE PAGE ===
    'manage' => [
        'title' => 'Manage your Biographies',
        'subtitle' => 'Create, edit and organize your personal stories',
        'your_biographies' => 'Your Biographies',
        'description' => 'Manage your personal stories and share them with the world',
        'create_new' => 'Create New Biography',
        'create_first' => 'Create your First Biography',
        'view_biography' => 'View Biography',
        'edit' => 'Edit',
        'public' => 'Public',
        'private' => 'Private',
        'completed' => 'Completed',
        'chapters' => 'Chapters',
        'min_read' => 'min read',
        'confirm_delete' => 'Are you sure you want to delete this biography? This action cannot be undone.',
        'delete_error' => 'Error deleting biography. Please try again.',
        'no_biographies_title' => 'No biographies yet',
        'no_biographies_description' => 'Start telling your story by creating your first biography. Share your experiences, projects and your vision of the world.',
        'empty_title' => 'No biographies found',
        'empty_description' => 'Start telling your story by creating your first biography',
    ],

    // === VIEW PAGE ===
    'view' => [
        'title' => 'Your Biography',
        'edit_biography' => 'Edit Biography',
    ],

    // === SHOW PAGE ===
    'show' => [
        'title' => 'Biography',
        'no_biography_title' => 'No biography available',
        'no_biography_description' => 'This user has not yet created a public biography.',
    ],

    // === FORM ===
    'form' => [
        'title' => 'Title',
        'title_placeholder' => 'Enter your biography title',
        'type' => 'Biography Type',
        'content' => 'Content',
        'content_placeholder' => 'Tell your story...',
        'excerpt' => 'Excerpt',
        'excerpt_placeholder' => 'Brief description of your biography',
        'excerpt_help' => 'Maximum 500 characters. Used for previews and sharing.',
        'is_public' => 'Public',
        'is_public_help' => 'Make biography visible to everyone',
        'is_completed' => 'Completed',
        'is_completed_help' => 'Mark as completed',
        'settings' => 'Settings',
        'featured_image' => 'Featured Image',
        'featured_image_hint' => 'Upload a representative image (JPEG, PNG, WebP, max 2MB). Used for previews and sharing.',
        'save_biography' => 'Save Biography',
        'create_biography' => 'Create Biography',
        'update_biography' => 'Update Biography',
    ],

    // === BIOGRAPHY TYPES ===
    'type' => [
        'single' => 'Single',
        'single_description' => 'A single format biography, ideal for short stories',
        'chapters' => 'Chapters',
        'chapters_description' => 'Biography organized in chapters, perfect for long and detailed stories',
    ],

    // === VALIDATION MESSAGES ===
    'validation' => [
        'title_required' => 'Title is required',
        'title_max' => 'Title cannot exceed 255 characters',
        'content_required' => 'Content is required',
        'type_required' => 'Biography type is required',
        'type_invalid' => 'Biography type is not valid',
        'excerpt_max' => 'Excerpt cannot exceed 500 characters',
        'slug_unique' => 'This slug is already in use',
        'featured_image_max' => 'Image cannot exceed 2MB',
        'featured_image_mimes' => 'Image must be in JPEG, PNG or WebP format',
    ],

    // === SUCCESS MESSAGES ===
    'success' => [
        'created' => 'Biography created successfully',
        'updated' => 'Biography updated successfully',
        'deleted' => 'Biography deleted successfully',
        'published' => 'Biography published successfully',
        'unpublished' => 'Biography made private successfully',
    ],

    // === ERROR MESSAGES ===
    'error' => [
        'not_found' => 'Biography not found',
        'unauthorized' => 'Unauthorized to access this biography',
        'create_failed' => 'Error creating biography',
        'update_failed' => 'Error updating biography',
        'delete_failed' => 'Error deleting biography',
        'generic' => 'An error occurred. Please try again.',
    ],

    // === CHAPTER SPECIFIC ===
    'chapter' => [
        'title' => 'Chapter Title',
        'content' => 'Chapter Content',
        'date_from' => 'Start Date',
        'date_to' => 'End Date',
        'is_ongoing' => 'Ongoing',
        'sort_order' => 'Order',
        'is_published' => 'Published',
        'chapter_type' => 'Chapter Type',
        'add_chapter' => 'Add Chapter',
        'edit_chapter' => 'Edit Chapter',
        'delete_chapter' => 'Delete Chapter',
        'reorder_chapters' => 'Reorder Chapters',
        'no_chapters' => 'No chapters yet',
        'no_chapters_description' => 'Start adding chapters to your biography',
    ],

    // === MEDIA ===
    'media' => [
        'upload' => 'Upload Media',
        'featured_image' => 'Featured Image',
        'gallery' => 'Gallery',
        'caption' => 'Caption',
        'alt_text' => 'Alt Text',
        'upload_failed' => 'Error uploading file',
        'delete_media' => 'Delete Media',
        'no_media' => 'No media uploaded',
    ],

    // === NAVIGATION ===
    'navigation' => [
        'all_biographies' => 'All Biographies',
        'my_biographies' => 'My Biographies',
        'public_biographies' => 'Public Biographies',
        'create_biography' => 'Create Biography',
        'manage_biographies' => 'Manage Biographies',
    ],

    // === STATS ===
    'stats' => [
        'total_biographies' => 'Total Biographies',
        'public_biographies' => 'Public Biographies',
        'total_chapters' => 'Total Chapters',
        'total_words' => 'Total Words',
        'reading_time' => 'Reading Time',
        'last_updated' => 'Last Updated',
    ],

    // === SHARING ===
    'sharing' => [
        'share_biography' => 'Share Biography',
        'copy_link' => 'Copy Link',
        'social_share' => 'Share on Social',
        'embed_code' => 'Embed Code',
        'qr_code' => 'QR Code',
    ],

    // === PRIVACY ===
    'privacy' => [
        'public_description' => 'Visible to everyone on the internet',
        'private_description' => 'Visible only to you',
        'unlisted_description' => 'Visible only with direct link',
        'change_privacy' => 'Change Privacy',
        'privacy_updated' => 'Privacy settings updated',
    ],

    // === TIMELINE ===
    'timeline' => [
        'show_timeline' => 'Show Timeline',
        'hide_timeline' => 'Hide Timeline',
        'chronological' => 'Chronological',
        'reverse_chronological' => 'Reverse Chronological',
        'custom_order' => 'Custom Order',
        'date_range' => 'Period',
        'ongoing' => 'Ongoing',
        'present' => 'Present',
    ],

    // === EXPORT ===
    'export' => [
        'export_biography' => 'Export Biography',
        'export_pdf' => 'Export PDF',
        'export_word' => 'Export Word',
        'export_html' => 'Export HTML',
        'export_success' => 'Biography exported successfully',
        'export_failed' => 'Error during export',
    ],

    // === SEARCH ===
    'search' => [
        'search_biographies' => 'Search Biographies',
        'search_placeholder' => 'Search by title, content or author...',
        'no_results' => 'No results found',
        'results_found' => 'results found',
        'filter_by' => 'Filter by',
        'filter_type' => 'Type',
        'filter_date' => 'Date',
        'filter_author' => 'Author',
        'clear_filters' => 'Clear Filters',
    ],

    // === COMMENTS ===
    'comments' => [
        'comments' => 'Comments',
        'add_comment' => 'Add Comment',
        'no_comments' => 'No comments yet',
        'comment_added' => 'Comment added successfully',
        'comment_deleted' => 'Comment deleted successfully',
        'enable_comments' => 'Enable Comments',
        'disable_comments' => 'Disable Comments',
    ],

    // === NOTIFICATIONS ===
    'notifications' => [
        'new_biography' => 'New biography published',
        'biography_updated' => 'Biography updated',
        'new_chapter' => 'New chapter added',
        'chapter_updated' => 'Chapter updated',
        'new_comment' => 'New comment received',
    ],

    // === EDIT PAGE SPECIFIC ===
    'edit_page' => [
        'edit_biography' => 'Edit Biography',
        'create_new_biography' => 'Create New Biography',
        'tell_story_description' => 'Tell your story and share it with the world',
        'validation_errors' => 'Validation Errors',
        'basic_info' => 'Basic Information',
        'media_management' => 'Media Management',
        'settings' => 'Settings',
        'title_required' => 'Title *',
        'title_placeholder' => 'Enter your biography title',
        'content_required' => 'Content *',
        'content_placeholder' => 'Tell your story...',
        'excerpt' => 'Excerpt',
        'excerpt_placeholder' => 'Brief description of your biography...',
        'excerpt_help' => 'Brief description that will appear in preview',
        'add_chapter' => 'Add Chapter',
        'edit_chapter' => 'Edit',
        'delete_chapter' => 'Delete',
        'biography_images' => 'Biography Images',
        'upload_images_help' => 'Upload images for your biography. Supported formats: JPG, PNG, WEBP (Max 2MB each)',
        'uploading_images' => 'Uploading images...',
        'uploaded_images' => 'Uploaded Images',
        'biography_public' => 'Public Biography',
        'biography_public_help' => 'Make your biography visible to all users',
        'go_back' => 'Go Back',
        'update_biography' => 'Update Biography',
        'create_biography' => 'Create Biography',
    ],

    // Legacy success messages (keeping for backward compatibility)
    'biographies_retrieved_successfully' => 'Biographies retrieved successfully',
    'biography_created_successfully' => 'Biography created successfully',
    'biography_retrieved_successfully' => 'Biography retrieved successfully',
    'biography_updated_successfully' => 'Biography updated successfully',
    'biography_deleted_successfully' => 'Biography ":title" deleted successfully',
    'chapters_retrieved_successfully' => 'Chapters retrieved successfully',
    'chapter_retrieved_successfully' => 'Chapter retrieved successfully',
    'chapter_created_successfully' => 'Chapter created successfully',
    'chapter_updated_successfully' => 'Chapter updated successfully',
    'chapter_deleted_successfully' => 'Chapter ":title" deleted successfully',
    'chapters_reordered_successfully' => 'Chapters order updated successfully',
    'biography_published_successfully' => 'Biography published successfully',
    'biography_unpublished_successfully' => 'Biography made private successfully',
    'biography_completed_successfully' => 'Biography marked as completed',
    'biography_media_uploaded_successfully' => 'Image uploaded successfully',
    'biography_media_deleted_successfully' => 'Image deleted successfully',
    'chapter_published_successfully' => 'Chapter published successfully',
    'chapter_unpublished_successfully' => 'Chapter made private successfully',
    'chapter_media_uploaded_successfully' => 'Chapter image uploaded successfully',
    'chapter_media_deleted_successfully' => 'Chapter image deleted successfully',

    'categories' => [
        'profile' => 'Profile Information',
        'account' => 'Account Details',
        'preferences' => 'Preferences and Settings',
        'activity' => 'Activity History',
        'consents' => 'Consent History',
        'collections' => 'Collections and Content',
        'purchases' => 'Purchases and Transactions',
        'comments' => 'Comments and Reviews',
        'messages' => 'Messages and Communications',
        'biography' => 'Biographies and Chapters',
    ],
    'manage.chapters' => 'Chapters',
];
