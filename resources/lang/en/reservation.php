<?php

/**
 * Reservation Messages
 * @package FlorenceEGI
 * @subpackage Translations
 * @language en
 * @version 2.0.0
 */

return [
    // Success messages
    'success' => 'Your reservation has been successfully placed! The certificate has been generated.',
    'cancel_success' => 'Your reservation has been successfully cancelled.',
    'success_title' => 'Reservation placed!',
    'view_certificate' => 'View Certificate',
    'close' => 'Close',

    // Error messages
    'unauthorized' => 'You must connect your wallet or sign in to make a reservation.',
    'validation_failed' => 'Please check the entered data and try again.',
    'auth_required' => 'Authentication is required to view your reservations.',
    'list_failed' => 'Unable to retrieve your reservations. Please try again later.',
    'status_failed' => 'Unable to retrieve reservation status. Please try again later.',
    'unauthorized_cancel' => 'You do not have permission to cancel this reservation.',
    'cancel_failed' => 'Unable to cancel the reservation. Please try again later.',

    // UI Buttons
    'button' => [
        'reserve' => 'Reserve',
        'reserved' => 'Reserved',
        'make_offer' => 'Make an offer'
    ],

    // Badges
    'badge' => [
        'highest' => 'Highest Priority',
        'superseded' => 'Lower Priority',
        'has_offers' => 'Reserved'
    ],

    // Reservation details
    'already_reserved' => [
        'title' => 'Already Reserved',
        'text' => 'You already have a reservation for this EGI.',
        'details' => 'Details of your reservation:',
        'type' => 'Type',
        'amount' => 'Amount',
        'status' => 'Status',
        'view_certificate' => 'View Certificate',
        'ok' => 'OK',
        'new_reservation' => 'New Reservation',
        'confirm_new' => 'Do you want to make a new reservation?'
    ],

    // Reservation history
    'history' => [
        'title' => 'Reservation History',
        'entries' => 'Reservation Entries',
        'view_certificate' => 'View Certificate',
        'no_entries' => 'No reservations found.',
        'be_first' => 'Be the first to reserve this EGI!'
    ],

    // Error messages
    'errors' => [
        'button_click_error' => 'An error occurred while processing your request.',
        'form_validation' => 'Please check the entered data and try again.',
        'api_error' => 'A communication error occurred with the server.',
        'unauthorized' => 'You must connect your wallet or sign in to make a reservation.'
    ],

    // Form
    'form' => [
        'title' => 'Reserve this EGI',
        'offer_amount_label' => 'Your Offer (EUR)',
        'offer_amount_placeholder' => 'Enter amount in EUR',
        'algo_equivalent' => 'Approximately :amount ALGO',
        'terms_accepted' => 'I accept the terms and conditions for EGI reservations',
        'contact_info' => 'Additional Contact Information (Optional)',
        'submit_button' => 'Place Reservation',
        'cancel_button' => 'Cancel'
    ],

    // Reservation type
    'type' => [
        'strong' => 'Strong Reservation',
        'weak' => 'Weak Reservation'
    ],

    // Priority levels
    'priority' => [
        'highest' => 'Active Reservation',
        'superseded' => 'Superseded',
    ],

    // Reservation status
    'status' => [
        'active' => 'Active',
        'pending' => 'Pending',
        'cancelled' => 'Cancelled',
        'expired' => 'Expired'
    ],

    // === NEW SECTION: NOTIFICATIONS ===
    'notifications' => [
        'reservation_expired' => 'Your reservation of €:amount for :egi_title has expired.',
        'superseded' => 'Your offer for :egi_title has been superseded. New highest offer: €:new_highest_amount',
        'highest' => 'Congratulations! Your offer of €:amount for :egi_title is now the highest!',
        'rank_changed' => 'Your position for :egi_title has changed: you are now in position #:new_rank',
        'competitor_withdrew' => 'A competitor has withdrawn. You have moved up to position #:new_rank for :egi_title',
        'pre_launch_reminder' => 'The on-chain mint will start soon! Confirm your reservation for :egi_title.',
        'mint_window_open' => 'It\'s your turn! You have 48 hours to complete the mint of :egi_title.',
        'mint_window_closing' => 'Attention! Only :hours_remaining hours remaining to complete the mint of :egi_title.',
        'default' => 'Update on your reservation for :egi_title',
        'archived_success' => 'Notification archived successfully.'
    ],
];