<x-app-layout>
    {{--
    @Oracode View: GDPR Profile Management - FlorenceEGI Brand Compliant
    🎯 Purpose: Replace Jetstream profile with GDPR-compliant personal data management
    🛡️ Privacy: Full GDPR compliance with edit, export, restrict, delete capabilities
    🎨 Brand: FlorenceEGI Renaissance design system with dedicated GDPR layout
    🔧 Accessibility: Full ARIA support, semantic structure, keyboard navigation
    🌐 i18n: Complete localization support with profile.* translations

    @package FlorenceEGI
    @author Padmin D. Curtis (for Fabio Cherici)
    @version 2.0.0 - Complete rewrite with proper translations
    @date 2025-05-25
    @seo-purpose Provide comprehensive privacy control interface for users
    @accessibility-trait Full ARIA landmark structure with navigation
    --}}

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl gdpr-title">
                    {{ __('profile.management_title') }}
                </h1>
                <p class="mt-1 gdpr-subtitle">
                    {{ __('profile.management_subtitle') }}
                </p>
            </div>
            <div class="hidden sm:block">
                <svg class="w-8 h-8 text-oro-fiorentino" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        </div>
    </x-slot>

    {{-- Success/Error Messages --}}
    @if (session('success'))
        <div class="mb-6 gdpr-alert gdpr-alert-success" role="alert" aria-live="polite">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 gdpr-alert gdpr-alert-error" role="alert" aria-live="assertive">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif


    {{-- Navigation Tabs --}}
    <nav class="mb-8" role="tablist" aria-label="{{ __('profile.sections_navigation') }}">
        <div class="border-b border-gray-200/50">
            <ul class="flex -mb-px space-x-8" role="tablist">
                <li role="presentation">
                    <button id="personal-data-tab"
                            class="gdpr-tab-button active"
                            role="tab"
                            aria-controls="personal-data-panel"
                            aria-selected="true"
                            data-tab-target="personal-data-panel">
                        <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ __('profile.personal_data_tab') }}
                    </button>
                </li>
                <li role="presentation">
                    <button id="privacy-settings-tab"
                            class="gdpr-tab-button"
                            role="tab"
                            aria-controls="privacy-settings-panel"
                            aria-selected="false"
                            data-tab-target="privacy-settings-panel">
                        <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        {{ __('profile.privacy_settings_tab') }}
                    </button>
                </li>
                <li role="presentation">
                    <button id="security-tab"
                            class="gdpr-tab-button"
                            role="tab"
                            aria-controls="security-panel"
                            aria-selected="false"
                            data-tab-target="security-panel">
                        <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        {{ __('profile.security_tab') }}
                    </button>
                </li>
                <li role="presentation">
                    <button id="data-rights-tab"
                            class="gdpr-tab-button"
                            role="tab"
                            aria-controls="data-rights-panel"
                            aria-selected="false"
                            data-tab-target="data-rights-panel">
                        <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ __('profile.data_rights_tab') }}
                    </button>
                </li>
            </ul>
        </div>
    </nav>


    {{-- Tab Panels --}}
    <div class="space-y-8 tab-panels">

        {{-- Personal Data Panel --}}
        <div id="personal-data-panel"
             class="tab-panel"
             role="tabpanel"
             aria-labelledby="personal-data-tab"
             aria-hidden="false">

            <div class="space-y-6">
                {{-- Basic Information Card --}}
                <div class="p-6 gdpr-card rounded-2xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg gdpr-title">
                            {{ __('profile.basic_information') }}
                        </h3>
                        <a href="{{ route('gdpr.edit-personal-data') }}"
                           class="inline-flex items-center px-4 py-2 text-sm rounded-lg gdpr-btn-secondary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            {{ __('profile.edit_information') }}
                        </a>
                    </div>

                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium gdpr-subtitle">{{ __('profile.name') }}</dt>
                            <dd class="mt-1 gdpr-text">{{ auth()->user()->name ?? __('profile.not_provided') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium gdpr-subtitle">{{ __('profile.email') }}</dt>
                            <dd class="mt-1 gdpr-text">{{ auth()->user()->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium gdpr-subtitle">{{ __('profile.user_type') }}</dt>
                            <dd class="mt-1">
                                <span class="px-2 py-1 text-xs font-medium rounded-full gdpr-badge-success">
                                    {{ __('user_types.' . (auth()->user()->user_type ?? 'unknown')) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium gdpr-subtitle">{{ __('profile.member_since') }}</dt>
                            <dd class="mt-1 gdpr-text">{{ auth()->user()->created_at->format('d M Y') }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Address Information Card --}}
                @if(auth()->user()->address || auth()->user()->city || auth()->user()->country)
                <div class="p-6 gdpr-card rounded-2xl">
                    <h3 class="mb-6 text-lg gdpr-title">
                        {{ __('profile.address_information') }}
                    </h3>

                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        @if(auth()->user()->address)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium gdpr-subtitle">{{ __('profile.street_address') }}</dt>
                            <dd class="mt-1 gdpr-text">{{ auth()->user()->address }}</dd>
                        </div>
                        @endif
                        @if(auth()->user()->city)
                        <div>
                            <dt class="text-sm font-medium gdpr-subtitle">{{ __('profile.city') }}</dt>
                            <dd class="mt-1 gdpr-text">{{ auth()->user()->city }}</dd>
                        </div>
                        @endif
                        @if(auth()->user()->country)
                        <div>
                            <dt class="text-sm font-medium gdpr-subtitle">{{ __('profile.country') }}</dt>
                            <dd class="mt-1 gdpr-text">{{ auth()->user()->country }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
                @endif
            </div>
        </div>

        {{-- Privacy Settings Panel --}}
        <div id="privacy-settings-panel"
             class="tab-panel"
             role="tabpanel"
             aria-labelledby="privacy-settings-tab"
             aria-hidden="true">

            <div class="p-6 gdpr-card rounded-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg gdpr-title">
                        {{ __('profile.privacy_consents') }}
                    </h3>
                    <a href="{{ route('gdpr.consent') }}"
                       class="inline-flex items-center px-4 py-2 text-sm rounded-lg gdpr-btn-secondary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ __('profile.manage_consents') }}
                    </a>
                </div>

                <p class="mb-4 text-sm gdpr-subtitle">
                    {{ __('profile.consents_summary_description') }}
                </p>

                {{-- Consent Status Summary --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="p-4 rounded-lg gdpr-badge-success">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 mr-3" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium">{{ __('profile.required_consents') }}</p>
                                <p class="text-lg font-bold">{{ __('profile.all_given') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-lg gdpr-badge-warning">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium">{{ __('profile.optional_consents') }}</p>
                                <p class="text-lg font-bold">{{ __('profile.review_available') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-lg gdpr-badge-info">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium">{{ __('profile.last_updated') }}</p>
                                <p class="text-lg font-bold">
                                    {{ auth()->user()->gdpr_consents_given_at?->diffForHumans() ?? __('profile.never') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Security Panel --}}
        <div id="security-panel"
             class="hidden tab-panel"
             role="tabpanel"
             aria-labelledby="security-tab"
             aria-hidden="true">

            <div class="space-y-6">
                {{-- Password Management --}}
                @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="p-6 gdpr-card rounded-2xl">
                    <h3 class="mb-6 text-lg gdpr-title">
                        {{ __('profile.password_security') }}
                    </h3>
                    @livewire('profile.update-password-form')
                </div>
                @endif

                {{-- Two Factor Authentication --}}
                @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="p-6 gdpr-card rounded-2xl">
                    <h3 class="mb-6 text-lg gdpr-title">
                        {{ __('profile.two_factor_authentication') }}
                    </h3>
                    @livewire('profile.two-factor-authentication-form')
                </div>
                @endif

                {{-- Browser Sessions --}}
                <div class="p-6 gdpr-card rounded-2xl">
                    <h3 class="mb-6 text-lg gdpr-title">
                        {{ __('profile.browser_sessions') }}
                    </h3>
                    @livewire('profile.logout-other-browser-sessions-form')
                </div>
            </div>
        </div>

        {{-- Data Rights Panel --}}
        <div id="data-rights-panel"
             class="hidden tab-panel"
             role="tabpanel"
             aria-labelledby="data-rights-tab"
             aria-hidden="true">

            <div class="space-y-6">
                {{-- Data Export Card --}}
                <div class="p-6 gdpr-card rounded-2xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg gdpr-title">
                            {{ __('profile.data_export') }}
                        </h3>
                        <svg class="w-6 h-6 text-oro-fiorentino" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="mb-4 text-sm gdpr-subtitle">
                        {{ __('profile.data_export_description') }}
                    </p>
                    <a href="{{ route('gdpr.export-data') }}"
                       class="inline-flex items-center px-4 py-2 text-sm rounded-lg gdpr-btn-primary">
                        {{ __('profile.request_data_export') }}
                    </a>
                </div>

                {{-- Processing Restrictions Card --}}
                <div class="p-6 gdpr-card rounded-2xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg gdpr-title">
                            {{ __('profile.processing_restrictions') }}
                        </h3>
                        <svg class="w-6 h-6 text-oro-fiorentino" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                    <p class="mb-4 text-sm gdpr-subtitle">
                        {{ __('profile.processing_restrictions_description') }}
                    </p>
                    <a href="{{ route('gdpr.limit-processing') }}"
                       class="inline-flex items-center px-4 py-2 text-sm rounded-lg gdpr-btn-secondary">
                        {{ __('profile.manage_restrictions') }}
                    </a>
                </div>

                {{-- Account Deletion Card --}}
                <div class="p-6 border-red-200 gdpr-card rounded-2xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold" style="color: var(--rosso-urgenza);">
                            {{ __('profile.account_deletion') }}
                        </h3>
                        <svg class="w-6 h-6" style="color: var(--rosso-urgenza);" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="p-4 mb-4 rounded-lg gdpr-alert-error">
                        <div class="flex">
                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <h4 class="text-sm font-medium">{{ __('profile.deletion_warning_title') }}</h4>
                                <p class="mt-1 text-sm opacity-80">{{ __('profile.deletion_warning_description') }}</p>
                            </div>
                        </div>
                    </div>
                    <p class="mb-4 text-sm gdpr-subtitle">
                        {{ __('profile.account_deletion_description') }}
                    </p>
                    <a href="{{ route('gdpr.delete-account') }}"
                       class="inline-flex items-center px-4 py-2 text-sm rounded-lg gdpr-btn-danger">
                        {{ __('profile.request_account_deletion') }}
                    </a>
                </div>

                {{-- Activity Log Card --}}
                <div class="p-6 gdpr-card rounded-2xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg gdpr-title">
                            {{ __('profile.activity_log') }}
                        </h3>
                        <svg class="w-6 h-6 text-oro-fiorentino" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <p class="mb-4 text-sm gdpr-subtitle">
                        {{ __('profile.activity_log_description') }}
                    </p>
                    <a href="{{ route('gdpr.activity-log') }}"
                       class="inline-flex items-center px-4 py-2 text-sm rounded-lg gdpr-btn-secondary">
                        {{ __('profile.view_activity_log') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for Tab Navigation --}}
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab navigation functionality
        const tabButtons = document.querySelectorAll('.gdpr-tab-button');
        const tabPanels = document.querySelectorAll('.tab-panel');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetPanel = this.dataset.tabTarget;

                // Update ARIA attributes and visual state
                tabButtons.forEach(btn => {
                    const isActive = btn === this;
                    btn.setAttribute('aria-selected', isActive);
                    btn.classList.toggle('active', isActive);
                });

                // Show/hide panels
                tabPanels.forEach(panel => {
                    const isTarget = panel.id === targetPanel;
                    panel.classList.toggle('hidden', !isTarget);
                    panel.setAttribute('aria-hidden', !isTarget);
                });
            });
        });

        // Keyboard navigation for tabs
        tabButtons.forEach((button, index) => {
            button.addEventListener('keydown', function(e) {
                let targetIndex;

                switch(e.key) {
                    case 'ArrowLeft':
                        targetIndex = index > 0 ? index - 1 : tabButtons.length - 1;
                        break;
                    case 'ArrowRight':
                        targetIndex = index < tabButtons.length - 1 ? index + 1 : 0;
                        break;
                    case 'Home':
                        targetIndex = 0;
                        break;
                    case 'End':
                        targetIndex = tabButtons.length - 1;
                        break;
                    default:
                        return;
                }

                e.preventDefault();
                tabButtons[targetIndex].focus();
                tabButtons[targetIndex].click();
            });
        });
    });
    </script>

    <script>
            window.appConfig = @json(config('app'));

            // GDPR specific configuration
            window.gdprConfig = {
                locale: '{{ app()->getLocale() }}',
                csrfToken: '{{ csrf_token() }}',
                routes: {
                    consent: '{{ route("gdpr.consent") }}',
                    export: '{{ route("gdpr.export-data") }}',
                    restrict: '{{ route("gdpr.limit-processing") }}',
                    delete: '{{ route("gdpr.delete-account") }}'
                }
            };
        </script>
    @endpush
</x-app-layout>
