<?php

/**
 * Reservation messages
 * @package FlorenceEGI
 * @subpackage Translations
 * @language en
 * @version 1.0.0
 */

return [
    // Success messages
    'success' => 'Your reservation was successful! The certificate has been generated.',
    'cancel_success' => 'Your reservation has been canceled successfully.',
    'success_title' => 'Reservation Successful!',
    'view_certificate' => 'View Certificate',
    'close' => 'Close',

    // Error messages
    'unauthorized' => 'You need to connect your wallet or log in to make a reservation.',
    'validation_failed' => 'Please check your input and try again.',
    'auth_required' => 'Authentication required to view your reservations.',
    'list_failed' => 'Could not retrieve your reservations. Please try again later.',
    'status_failed' => 'Could not retrieve the reservation status. Please try again later.',
    'unauthorized_cancel' => 'You don\'t have permission to cancel this reservation.',
    'cancel_failed' => 'Could not cancel the reservation. Please try again later.',

    // UI buttons
    'button' => [
        'reserve' => 'Reserve',
        'reserved' => 'Reserved',
        'make_offer' => 'Make Offer'
    ],

    // Badge
    'badge' => [
        'highest' => 'Highest Priority',
        'superseded' => 'Lower Priority',
        'has_offers' => 'Reserved'
    ],

    // Reservation details
    'already_reserved' => [
        'title' => 'Already Reserved',
        'text' => 'You already have a reservation for this EGI.',
        'details' => 'Your reservation details:',
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
        'no_entries' => 'No reservation entries found.',
        'be_first' => 'Be the first to reserve this EGI!'

    ],

    // Error messages
    'errors' => [
        'button_click_error' => 'An error occurred while processing your request.',
        'form_validation' => 'Please check your input and try again.',
        'api_error' => 'An error occurred while communicating with the server.',
        'unauthorized' => 'You need to connect your wallet or log in to make a reservation.'
    ],

    // Form
    'form' => [
        'title' => 'Reserve this EGI',
        'offer_amount_label' => 'Your Offer (EUR)',
        'offer_amount_placeholder' => 'Enter amount in EUR',
        'algo_equivalent' => 'Approximately :amount ALGO',
        'terms_accepted' => 'I accept the terms and conditions for EGI reservations',
        'contact_info' => 'Additional Contact Information (Optional)',
        'submit_button' => 'Make Reservation',
        'cancel_button' => 'Cancel'
    ],

    // Type of reservation
    'type' => [
        'strong' => 'Strong Reservation',
        'weak' => 'Basic Reservation'
    ],

    // Priority levels
    'priority' => [
        'highest' => 'Highest Priority',
        'superseded' => 'Lower Priority'
    ],

    // Status of the reservation
    'status' => [
        'active' => 'Active',
        'pending' => 'Pending',
        'cancelled' => 'Cancelled',
        'expired' => 'Expired'
    ]
];