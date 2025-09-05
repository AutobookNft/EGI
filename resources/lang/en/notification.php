<?php

/*
|--------------------------------------------------------------------------
| Translation to English of all notification data
|--------------------------------------------------------------------------
|
 */

return [
    'proposal_declined_subject' => 'Your proposal has been declined',
    'proposal_declined_line' => 'Your proposal has been declined.',
    'proposal_declined_reason' => 'Reason:',
    'proposal_declined_id' => 'Proposal ID:',
    'view_details' => 'View details',
    'thank_you' => 'Thank you for using our platform.',
    'proposal_declined' => 'Proposal declined',
    'proposal_declined_message' => 'Your proposal has been declined.',
    'reply' => 'Reply',
    'wallet_changes_approved' => 'Wallet changes have been approved',
    'no_notifications' => 'No notifications',
    'select_notification' => 'Select a notification to view its details',
    'hide_processed_notifications' => 'Hide processed notifications.',
    'show_processed_notifications' => 'Show processed notifications.',
    'confirm_delete' => 'Are you sure you want to delete this notification?',
    'proposer' => 'Proposer',
    'receiver' => 'Recipient',
    'proposed_creation_new_wallet' => 'You have proposed the creation of a new wallet',
    'proposed_change_to_a_wallet' => 'You have proposed a change to a wallet',
    'no_historical_notifications' => 'No historical notifications',
    'notification_list_error' => 'Error retrieving notifications',
    'invitation_received' => 'You have been invited to participate in a collection',
    'not_found' => 'Notification not found',

    // Badge Notifications
    'badge' => [
        'title' => 'Notifications',
        'aria_label' => 'Open notifications menu',
        'view_all' => 'View all notifications',
        'empty' => [
            'title' => 'No notifications',
            'message' => 'You haven’t received any notifications yet.',
        ],
    ],

    'types' => [
        'reservations' => 'Reservation',
        'gdpr' => 'GDPR',
        'collections' => 'Collection',
        'egis' => 'EGI',
        'wallets' => 'Wallet',
        'invitations' => 'Invitation',
        'general' => 'General',
    ],

    'status' => [
        'read' => 'Read',
        'pending_ack' => 'Pending read',
    ],

    'label' => [
        'status' => 'Status',
        'from' => 'From',
        'created_at' => 'Created at',
        'archived' => 'Archive',
        'additional_details' => 'Additional Details',
    ],

    'actions' => [
        'done' => 'Done',
        'learn_more' => 'Learn more',
    ],

    'aria' => [
        'details_label' => 'Notification details',
        'actions_label' => 'Actions for the notification',
        'mark_as_read' => 'Mark the notification as read',
        'learn_more' => 'Open the link for more information',
    ],

    'gdpr' => [
        'disavow_button_label' => 'I do not recognize this action',
        'confirm_button_label' => 'Confirm this action',
        'confirm_action_prompt' => 'Are you sure you want to confirm this action?',
        'unknown' => [
            'content' => 'You have received an unknown notification.',
            'title' => 'Unknown notification',
        ],
        'consent_updated' => [
            'content' => 'Your consent has been updated.',
            'title' => 'Consent updated',
        ],
        'breach_report_received' => [
            'content' => 'You have received a data breach report.',
            'title' => 'Data breach report received',
        ],
        'data_deletion_request' => [
            'content' => 'You have received a data deletion request.',
            'title' => 'Data deletion request received',
        ],
        'data_access_request' => [
            'content' => 'You have received a data access request.',
            'title' => 'Data access request received',
        ],
        'data_portability_request' => [
            'content' => 'You have received a data portability request.',
            'title' => 'Data portability request received',
        ],
        'data_processing_objection' => [
            'content' => 'You have received an objection to data processing.',
            'title' => 'Objection to data processing received',
        ],
        'data_processing_restriction' => [
            'content' => 'You have received a request for restriction of data processing.',
            'title' => 'Request for restriction of data processing received',
        ],
        'data_processing_notification' => [
            'content' => 'You have received a data processing notification.',
            'title' => 'Data processing notification received',
        ],
        'data_processing_consent' => [
            'content' => 'You have received a request for consent to data processing.',
            'title' => 'Request for consent to data processing received',
        ],
    ],

    // Notification Type Labels
    'Wallet' => 'Wallet',
    'Highest Bid' => 'Highest Bid',
    'Superseded' => 'Superseded',
    'Invitation' => 'Invitation',
    'Alert' => 'Alert',
    'Urgent' => 'Urgent',
    'New High Bid' => 'New High Bid',
    'Recent' => 'Recent',
    'Today' => 'Today',
    'Older' => 'Older',
];