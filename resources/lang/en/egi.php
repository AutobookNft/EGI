<?php

return [

    /*
    |--------------------------------------------------------------------------
    | EGI (Ecological Goods Invent) - English Translations
    |--------------------------------------------------------------------------
    |
    | Translations for EGI CRUD system in FlorenceEGI
    | Version: 1.0.0 - Oracode System 2.0 Compliant
    |
    */

    // Meta and SEO
    'meta_description_default' => 'Details for EGI: :title',
    'image_alt_default' => 'EGI Artwork',
    'view_full' => 'View Full',
    'artwork_loading' => 'Artwork Loading...',

    // Basic Information
    'by_author' => 'by :name',
    'unknown_creator' => 'Unknown Creator',

    // Main Actions
    'like_button_title' => 'Add to Favorites',
    'share_button_title' => 'Share this EGI',
    'current_price' => 'Current Price',
    'not_currently_listed' => 'Not Currently Listed',
    'contact_owner_availability' => 'Contact owner for availability',
    'liked' => 'Liked',
    'add_to_favorites' => 'Add to Favorites',
    'reserve_this_piece' => 'Reserve This Piece',

    /*
    |--------------------------------------------------------------------------
    | NFT Card System - NFT Cards System
    |--------------------------------------------------------------------------
    */

    // Badges and Status
    'badge' => [
        'owned' => 'OWNED',
        'media_content' => 'Media Content',
    ],

    // Titles
    'title' => [
        'untitled' => '✨ Untitled EGI',
    ],

    // Platform
    'platform' => [
        'powered_by' => 'Powered by :platform',
    ],

    // Creator
    'creator' => [
        'created_by' => '👨‍🎨 Created by:',
    ],

    // Prices
    'price' => [
        'purchased_for' => '💳 Purchased for',
        'price' => '💰 Price',
        'floor' => '📊 Floor',
    ],

    // Status
    'status' => [
        'not_for_sale' => '🚫 Not for sale',
        'draft' => '⏳ Draft',
    ],

    // Actions
    'actions' => [
        'view' => 'View',
        'view_details' => 'View EGI details',
        'reserve' => 'Reserve',
    ],

    // Information Sections
    'properties' => 'Properties',
    'supports_epp' => 'Supports EPP',
    'asset_type' => 'Asset Type',
    'format' => 'Format',
    'about_this_piece' => 'About This Piece',
    'default_description' => 'This unique digital artwork represents a moment of creative expression, capturing the essence of digital artistry in the blockchain era.',
    'provenance' => 'Provenance',
    'view_full_collection' => 'View Full Collection',

    /*
    |--------------------------------------------------------------------------
    | CRUD System - Editing System
    |--------------------------------------------------------------------------
    */

    'crud' => [

        // Header and Navigation
        'edit_egi' => 'Edit EGI',
        'toggle_edit_mode' => 'Toggle Edit Mode',
        'start_editing' => 'Start Editing',
        'save_changes' => 'Save Changes',
        'cancel' => 'Cancel',

        // Title Field
        'title' => 'Title',
        'title_placeholder' => 'Enter artwork title...',
        'title_hint' => 'Maximum 60 characters',
        'characters_remaining' => 'characters remaining',

        // Description Field
        'description' => 'Description',
        'description_placeholder' => 'Describe your artwork, its story and meaning...',
        'description_hint' => 'Tell the story behind your creation',

        // Price Field
        'price' => 'Price',
        'price_placeholder' => '0.00',
        'price_hint' => 'Price in ALGO (leave empty if not for sale)',

        // Creation Date Field
        'creation_date' => 'Creation Date',
        'creation_date_hint' => 'When did you create this artwork?',

        // Published Field
        'is_published' => 'Published',
        'is_published_hint' => 'Make artwork publicly visible',

        // View Mode - Current State
        'current_title' => 'Current Title',
        'no_title' => 'No title set',
        'current_price' => 'Current Price',
        'price_not_set' => 'Price not set',
        'current_status' => 'Publication Status',
        'status_published' => 'Published',
        'status_draft' => 'Draft',

        // Delete System
        'delete_egi' => 'Delete EGI',
        'delete_confirmation_title' => 'Confirm Deletion',
        'delete_confirmation_message' => 'Are you sure you want to delete this EGI? This action cannot be undone.',
        'delete_confirm' => 'Delete Permanently',

        // Validation Messages
        'title_required' => 'Title is required',
        'title_max_length' => 'Title cannot exceed 60 characters',
        'price_numeric' => 'Price must be a valid number',
        'price_min' => 'Price cannot be negative',
        'creation_date_format' => 'Invalid date format',

        // Success Messages
        'update_success' => 'EGI updated successfully!',
        'delete_success' => 'EGI deleted successfully.',

        // Error Messages
        'update_error' => 'Error updating EGI.',
        'delete_error' => 'Error deleting EGI.',
        'permission_denied' => 'You do not have permission for this action.',
        'not_found' => 'EGI not found.',

        // General Messages
        'no_changes_detected' => 'No changes detected.',
        'unsaved_changes_warning' => 'You have unsaved changes. Are you sure you want to leave?',
    ],

    /*
    |--------------------------------------------------------------------------
    | Responsive Labels - Mobile/Tablet
    |--------------------------------------------------------------------------
    */

    'mobile' => [
        'edit_egi_short' => 'Edit',
        'save_short' => 'Save',
        'delete_short' => 'Delete',
        'cancel_short' => 'Cancel',
        'published_short' => 'Pub.',
        'draft_short' => 'Draft',
    ],

    /*
    |--------------------------------------------------------------------------
    | Accessibility Labels - Screen Readers
    |--------------------------------------------------------------------------
    */

    'a11y' => [
        'edit_form' => 'EGI edit form',
        'delete_button' => 'Delete EGI button',
        'toggle_edit' => 'Toggle edit mode',
        'save_form' => 'Save EGI changes',
        'close_modal' => 'Close confirmation dialog',
        'required_field' => 'Required field',
        'optional_field' => 'Optional field',
    ],

    /*
    |--------------------------------------------------------------------------
    | Homepage Multi-Content Carousel
    |--------------------------------------------------------------------------
    */

    'carousel' => [
        'title' => 'Discover the Renaissance',
        'subtitle' => 'Explore artworks, creators, collections, and collectors in the FlorenceEGI ecosystem',

        // Content Type Buttons
        'content_types' => [
            'egi_list' => 'EGI List View',
            'egi_card' => 'EGI Card View',
            'creators' => 'Featured Creators',
            'collections' => 'Art Collections',
            'collectors' => 'Top Collectors'
        ],

        // Content Labels
        'creators' => 'Creators',
        'collections' => 'Collections',
        'collectors' => 'Collectors',

        // Dynamic Headers
        'headers' => [
            'egi_list' => 'EGI',
            'egi_card' => 'EGI',
            'creators' => 'Artists',
            'collections' => 'Collections',
            'collectors' => 'Collectors'
        ],

        // Navigation
        'navigation' => [
            'previous' => 'Previous',
            'next' => 'Next',
            'slide' => 'Go to slide :number'
        ],

        // Empty States
        'empty_state' => [
            'title' => 'No Content Available',
            'subtitle' => 'Check back soon for new content!',
            'no_egis' => 'No EGI artworks available at the moment.',
            'no_creators' => 'No creators available at the moment.',
            'no_collections' => 'No collections available at the moment.',
            'no_collectors' => 'No collectors available at the moment.'
        ],

        // Legacy (for backwards compatibility)
        'two_columns' => 'List View',
        'three_columns' => 'Card View'
    ],

];
