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
                    {{ __('profile.security_management') }}
                </h1>
                <p class="mt-1 gdpr-subtitle">
                    {{ __('profile.security_subtitle') }}
                </p>
            </div>
            <div class="hidden sm:block">
                <svg class="w-8 h-8 text-oro-fiorentino" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
        </div>
    </x-slot>

    {{-- Success/Error Messages --}}
    @if (session('success'))
        <div class="mb-6 gdpr-alert gdpr-alert-success" role="alert" aria-live="polite">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 gdpr-alert gdpr-alert-error" role="alert" aria-live="assertive">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Security Content --}}
    <div class="space-y-8">
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

    {{-- JavaScript for single panel (Security only) --}}
    @push('scripts')
        <script>
            window.appConfig = @json(config('app'));

            // GDPR specific configuration
            window.gdprConfig = {
                locale: '{{ app()->getLocale() }}',
                csrfToken: '{{ csrf_token() }}',
                routes: {
                    consent: '{{ route('gdpr.consent') }}',
                    export: '{{ route('gdpr.export-data') }}',
                    restrict: '{{ route('gdpr.limit-processing') }}',
                    delete: '{{ route('gdpr.delete-account') }}'
                }
            };
        </script>
    @endpush
</x-app-layout>
