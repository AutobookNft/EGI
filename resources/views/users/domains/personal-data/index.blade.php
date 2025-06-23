{{--
@Oracode View: Personal Data Management (OS1-Compliant)
🎯 Purpose: Complete personal data management with GDPR compliance and fiscal validation
🛡️ Privacy: Full audit trail, consent management, data subject rights
🧱 Core Logic: FegiAuth integration, country-specific validation, UEM error handling
🌍 Scale: 6 MVP countries support with enterprise-grade validation
⏰ MVP: Critical Personal Data Domain for 30 June deadline

@package resources/views/user/domains/personal-data
@author Padmin D. Curtis (AI Partner OS1-Compliant)
@version 1.0.0 (FlorenceEGI MVP - Personal Data Domain)
@deadline 2025-06-30
--}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('user_personal_data.management_title') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('user_personal_data.management_subtitle') }}
                </p>
            </div>

            {{-- Auth Type Indicator --}}
            <div class="flex items-center space-x-3">
                <x-auth-type-badge :type="$authType" />

                @if($canEdit)
                    <x-button
                        type="button"
                        data-action="save-personal-data"
                        class="hidden"
                        id="save-button">
                        {{ __('user_personal_data.save_changes') }}
                    </x-button>
                @endif
            </div>
        </div>
    </x-slot>

    {{-- Vite Assets --}}
    @vite(['resources/css/domains/personal-data.css', 'resources/ts/domains/personal-data.ts'])

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">

                {{-- Main Content --}}
                <div class="space-y-6 lg:col-span-3">

                    {{-- GDPR Notice Section --}}
                    <x-personal-data.gdpr-notice :gdpr-summary="$gdprSummary" />

                    {{-- Personal Data Form --}}
                    <x-personal-data.form
                        :user="$user"
                        :personal-data="$personalData"
                        :gdprConsents="$gdprConsents"
                        :user-country="$userCountry"
                        :available-countries="$availableCountries"
                        :validation-config="$validationConfig"
                        :can-edit="$canEdit"
                        :auth-type="$authType"
                        :platform-services-consent="$platformServicesConsent" />

                </div>

                {{-- Sidebar --}}
                <div class="space-y-6 lg:col-span-1">

                    {{-- GDPR Quick Actions --}}
                    <x-personal-data.gdpr-actions
                        :gdpr-summary="$gdprSummary"
                        :can-edit="$canEdit"
                        :auth-type="$authType" />

                    {{-- Data Summary --}}
                    <x-personal-data.data-summary
                        :personal-data="$personalData"
                        :last-update="$lastUpdate"
                        :user-country="$userCountry" />

                    {{-- Validation Info --}}
                    <x-personal-data.validation-info
                        :user-country="$userCountry"
                        :validation-config="$validationConfig" />

                </div>
            </div>
        </div>
    </div>

    {{-- Error Display Container --}}
    <div id="error-container" class="fixed z-50 top-4 right-4"></div>

    {{-- Loading Overlay --}}
    <div id="loading-overlay" class="fixed inset-0 z-40 flex items-center justify-center hidden bg-black bg-opacity-50">
        <div class="flex items-center p-6 space-x-3 bg-white rounded-lg">
            <x-loading-spinner />
            <span class="text-gray-700">{{ __('user_personal_data.processing_update') }}</span>
        </div>
    </div>

    {{-- Pass data to TypeScript --}}
    @php
        $personalDataConfig = [
            'canEdit' => $canEdit,
            'authType' => $authType,
            'userCountry' => $userCountry,
            'availableCountries' => $availableCountries,
            'validationConfig' => $validationConfig,
            'csrfToken' => csrf_token(),
            'updateUrl' => route('user.domains.personal-data.update'),
            'exportUrl' => route('user.domains.personal-data.export'),
            'translations' => [
                'confirmChanges' => __('user_personal_data.confirm_changes'),
                'updateSuccess' => __('user_personal_data.update_success'),
                'updateError' => __('user_personal_data.update_error'),
                'validationError' => __('user_personal_data.validation_error'),
                'exportStarted' => __('user_personal_data.export_started'),
                'processing' => __('user_personal_data.processing_update'),
            ]
        ];
    @endphp

    <script>
        window.personalDataConfig = @json($personalDataConfig);
    </script>
</x-app-layout>
