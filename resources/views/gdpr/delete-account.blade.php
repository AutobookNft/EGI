{{-- resources/views/gdpr/delete-account.blade.php --}}
@extends('layouts.gdpr')

@section('page-title', __('gdpr.delete_account.title'))

@php
    $pageSubtitle = __('gdpr.delete_account.subtitle');
    $breadcrumbItems = [
        ['label' => __('gdpr.delete_account.breadcrumb'), 'url' => null]
    ];
@endphp

@push('styles')
<style>
    .danger-zone {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border: 2px solid #ef4444;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .danger-zone-title {
        color: #991b1b;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .danger-zone-description {
        color: #7f1d1d;
        line-height: 1.6;
        margin-bottom: 2rem;
    }

    .warning-list {
        background: rgba(239, 68, 68, 0.1);
        border-radius: 8px;
        padding: 1.5rem;
        margin: 1.5rem 0;
    }

    .warning-list-title {
        color: #991b1b;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .warning-list ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .warning-list li {
        color: #7f1d1d;
        padding: 0.5rem 0;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .warning-list li::before {
        content: "⚠️";
        flex-shrink: 0;
        margin-top: 0.1rem;
    }

    .deletion-options {
        background: #fff;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .deletion-option {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .deletion-option:hover {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .deletion-option.selected {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .deletion-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .deletion-option-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .deletion-option-description {
        color: #6b7280;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .deletion-form {
        background: #fff;
        border-radius: 12px;
        padding: 2rem;
        border: 2px solid #ef4444;
    }

    .confirmation-section {
        margin-bottom: 2rem;
    }

    .confirmation-checkbox {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin: 1rem 0;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .confirmation-checkbox input[type="checkbox"] {
        margin-top: 0.25rem;
        width: 16px;
        height: 16px;
    }

    .confirmation-checkbox label {
        flex: 1;
        color: #374151;
        font-size: 0.9rem;
        line-height: 1.5;
        cursor: pointer;
    }

    .password-confirmation {
        margin-bottom: 2rem;
    }

    .password-input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.2s ease;
    }

    .password-input:focus {
        outline: none;
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .delete-btn {
        background: #ef4444;
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
        justify-content: center;
    }

    .delete-btn:hover:not(:disabled) {
        background: #dc2626;
        transform: translateY(-1px);
    }

    .delete-btn:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
    }

    .account-value-section {
        background: #f0f9ff;
        border: 1px solid #0ea5e9;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .account-value-title {
        color: #0c4a6e;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .account-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .account-stat {
        text-align: center;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        border: 1px solid #bae6fd;
    }

    .account-stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0c4a6e;
    }

    .account-stat-label {
        font-size: 0.8rem;
        color: #0369a1;
        margin-top: 0.25rem;
    }

    .pending-requests-notice {
        background: #fef3c7;
        border: 1px solid #f59e0b;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 2rem;
    }

    .pending-requests-notice-title {
        color: #92400e;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .pending-requests-notice-text {
        color: #92400e;
        font-size: 0.9rem;
    }
</style>
@endpush

@section('gdpr-content')
    {{-- Pending Requests Notice --}}
    @if($hasPendingRequests)
        <div class="pending-requests-notice">
            <div class="pending-requests-notice-title">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.98-.833-2.75 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                {{ __('gdpr.delete_account.pending_requests_title') }}
            </div>
            <div class="pending-requests-notice-text">
                {{ __('gdpr.delete_account.pending_requests_message') }}
                <a href="{{ route('gdpr.activity-log') }}" style="color: #92400e; text-decoration: underline;">
                    {{ __('gdpr.delete_account.view_requests') }}
                </a>
            </div>
        </div>
    @endif

    {{-- Account Value Section --}}
    <div class="account-value-section">
        <div class="account-value-title">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
            {{ __('gdpr.delete_account.account_value_title') }}
        </div>
        <p style="color: #0369a1; margin-bottom: 1rem;">
            {{ __('gdpr.delete_account.account_value_description') }}
        </p>

        <div class="account-stats">
            <div class="account-stat">
                <div class="account-stat-number">{{ $accountStats['collections_count'] ?? 0 }}</div>
                <div class="account-stat-label">{{ __('gdpr.delete_account.stats.collections') }}</div>
            </div>
            <div class="account-stat">
                <div class="account-stat-number">{{ $accountStats['egis_count'] ?? 0 }}</div>
                <div class="account-stat-label">{{ __('gdpr.delete_account.stats.egis') }}</div>
            </div>
            <div class="account-stat">
                <div class="account-stat-number">{{ $accountStats['connections_count'] ?? 0 }}</div>
                <div class="account-stat-label">{{ __('gdpr.delete_account.stats.connections') }}</div>
            </div>
            <div class="account-stat">
                <div class="account-stat-number">{{ $accountStats['days_active'] ?? 0 }}</div>
                <div class="account-stat-label">{{ __('gdpr.delete_account.stats.days_active') }}</div>
            </div>
        </div>
    </div>

    {{-- Danger Zone --}}
    <div class="danger-zone">
        <div class="danger-zone-title">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.98-.833-2.75 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            {{ __('gdpr.delete_account.danger_zone_title') }}
        </div>

        <p class="danger-zone-description">
            {{ __('gdpr.delete_account.danger_zone_description') }}
        </p>

        <div class="warning-list">
            <div class="warning-list-title">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.98-.833-2.75 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                {{ __('gdpr.delete_account.what_will_be_deleted') }}
            </div>
            <ul>
                <li>{{ __('gdpr.delete_account.deletion_items.personal_data') }}</li>
                <li>{{ __('gdpr.delete_account.deletion_items.account_settings') }}</li>
                <li>{{ __('gdpr.delete_account.deletion_items.collections') }}</li>
                <li>{{ __('gdpr.delete_account.deletion_items.egis') }}</li>
                <li>{{ __('gdpr.delete_account.deletion_items.connections') }}</li>
                <li>{{ __('gdpr.delete_account.deletion_items.messages') }}</li>
                <li>{{ __('gdpr.delete_account.deletion_items.activity_history') }}</li>
            </ul>
        </div>
    </div>

    {{-- Deletion Options --}}
    <div class="deletion-options">
        <h3 style="margin-bottom: 1.5rem; color: #1f2937; font-size: 1.3rem;">
            {{ __('gdpr.delete_account.deletion_options_title') }}
        </h3>

        <form method="POST" action="{{ route('gdpr.delete-account.request') }}" class="deletion-form" id="deletionForm">
            @csrf

            {{-- Deletion Type Selection --}}
            <div style="margin-bottom: 2rem;">
                <label class="deletion-option" onclick="selectDeletionType(this)">
                    <input type="radio" name="deletion_type" value="immediate" required>
                    <div class="deletion-option-title">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        {{ __('gdpr.delete_account.immediate_deletion') }}
                    </div>
                    <div class="deletion-option-description">
                        {{ __('gdpr.delete_account.immediate_deletion_description') }}
                    </div>
                </label>

                <label class="deletion-option" onclick="selectDeletionType(this)">
                    <input type="radio" name="deletion_type" value="scheduled" required>
                    <div class="deletion-option-title">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ __('gdpr.delete_account.scheduled_deletion') }}
                    </div>
                    <div class="deletion-option-description">
                        {{ __('gdpr.delete_account.scheduled_deletion_description') }}
                    </div>
                </label>
            </div>

            {{-- Confirmation Checkboxes --}}
            <div class="confirmation-section">
                <h4 style="color: #1f2937; font-weight: 600; margin-bottom: 1rem;">
                    {{ __('gdpr.delete_account.confirmations_required') }}
                </h4>

                <div class="confirmation-checkbox">
                    <input type="checkbox" id="confirm_data_loss" name="confirmations[]" value="data_loss" required>
                    <label for="confirm_data_loss">
                        {{ __('gdpr.delete_account.confirm_data_loss') }}
                    </label>
                </div>

                <div class="confirmation-checkbox">
                    <input type="checkbox" id="confirm_irreversible" name="confirmations[]" value="irreversible" required>
                    <label for="confirm_irreversible">
                        {{ __('gdpr.delete_account.confirm_irreversible') }}
                    </label>
                </div>

                <div class="confirmation-checkbox">
                    <input type="checkbox" id="confirm_export" name="confirmations[]" value="export_complete">
                    <label for="confirm_export">
                        {{ __('gdpr.delete_account.confirm_data_export') }}
                        <a href="{{ route('gdpr.export-data') }}" style="color: #3b82f6; text-decoration: underline;">
                            {{ __('gdpr.delete_account.export_link') }}
                        </a>
                    </label>
                </div>

                <div class="confirmation-checkbox">
                    <input type="checkbox" id="confirm_legal" name="confirmations[]" value="legal_understanding" required>
                    <label for="confirm_legal">
                        {{ __('gdpr.delete_account.confirm_legal_rights') }}
                    </label>
                </div>
            </div>

            {{-- Password Confirmation --}}
            <div class="password-confirmation">
                <label for="password" style="display: block; font-weight: 600; color: #1f2937; margin-bottom: 0.5rem;">
                    {{ __('gdpr.delete_account.confirm_password') }}
                </label>
                <input type="password"
                       id="password"
                       name="password"
                       class="password-input"
                       placeholder="{{ __('gdpr.delete_account.enter_password') }}"
                       required>
            </div>

            {{-- Deletion Reason (Optional) --}}
            <div style="margin-bottom: 2rem;">
                <label for="deletion_reason" style="display: block; font-weight: 600; color: #1f2937; margin-bottom: 0.5rem;">
                    {{ __('gdpr.delete_account.deletion_reason') }} <span style="color: #6b7280;">({{ __('gdpr.delete_account.optional') }})</span>
                </label>
                <textarea id="deletion_reason"
                          name="deletion_reason"
                          rows="3"
                          class="password-input"
                          style="resize: vertical;"
                          placeholder="{{ __('gdpr.delete_account.deletion_reason_placeholder') }}"></textarea>
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="delete-btn" id="deleteButton" disabled>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                {{ __('gdpr.delete_account.delete_my_account') }}
            </button>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    /**
     * Select deletion type option
     */
    function selectDeletionType(element) {
        document.querySelectorAll('.deletion-option').forEach(option => {
            option.classList.remove('selected');
        });
        element.classList.add('selected');

        const radio = element.querySelector('input[type="radio"]');
        if (radio) {
            radio.checked = true;
        }

        validateForm();
    }

    /**
     * Validate form and enable/disable submit button
     */
    function validateForm() {
        const form = document.getElementById('deletionForm');
        const deleteButton = document.getElementById('deleteButton');

        const deletionType = form.querySelector('input[name="deletion_type"]:checked');
        const requiredCheckboxes = form.querySelectorAll('input[name="confirmations[]"][required]');
        const password = form.querySelector('#password');

        let allRequiredChecked = true;
        requiredCheckboxes.forEach(checkbox => {
            if (!checkbox.checked) {
                allRequiredChecked = false;
            }
        });

        const isValid = deletionType &&
                       allRequiredChecked &&
                       password.value.length >= 8;

        deleteButton.disabled = !isValid;
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('[GDPR Delete Account] Page initialized');

        // Add event listeners for form validation
        const form = document.getElementById('deletionForm');
        const inputs = form.querySelectorAll('input, textarea');

        inputs.forEach(input => {
            input.addEventListener('change', validateForm);
            input.addEventListener('input', validateForm);
        });

        // Double confirmation on submit
        form.addEventListener('submit', function(e) {
            const confirmMessage = `{{ __('gdpr.delete_account.final_confirmation') }}\n\n{{ __('gdpr.delete_account.type_delete_to_confirm') }}`;
            const userInput = prompt(confirmMessage);

            if (userInput !== 'DELETE' && userInput !== 'delete') {
                e.preventDefault();
                alert('{{ __('gdpr.delete_account.confirmation_failed') }}');
                return false;
            }

            // Final warning
            if (!confirm('{{ __('gdpr.delete_account.final_warning') }}')) {
                e.preventDefault();
                return false;
            }
        });

        // Initial validation
        validateForm();
    });
</script>
@endpush
