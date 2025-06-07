{{--
@Oracode Component: Personal Data Summary (OS1-Compliant)
🎯 Purpose: Quick overview of personal data completeness and status
🛡️ Privacy: Privacy-safe data status indicators
🧱 Core Logic: Data completeness tracking and country-specific info

@props [
    'personalData' => \App\Models\UserPersonalData,
    'lastUpdate' => \Carbon\Carbon|null,
    'userCountry' => string
]
--}}

@props([
    'personalData',
    'lastUpdate',
    'userCountry'
])

@php
    // Calculate data completeness
    $requiredFields = [
        'birth_date' => $personalData->birth_date,
        'street' => $personalData->street,
        'city' => $personalData->city,
        'zip' => $personalData->zip,
        'country' => $personalData->country,
    ];

    $filledFields = array_filter($requiredFields, fn($value) => !empty($value));
    $completeness = count($requiredFields) > 0 ? (count($filledFields) / count($requiredFields)) * 100 : 0;

    // Country-specific required fields
    $fiscalComplete = false;
    if ($userCountry === 'IT' && $personalData->fiscal_code) {
        $fiscalComplete = true;
    } elseif (in_array($userCountry, ['DE', 'FR', 'ES']) && $personalData->tax_id_number) {
        $fiscalComplete = true;
    }
@endphp

<div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
    <div class="p-4">
        <h3 class="mb-4 text-lg font-medium text-gray-900">
            {{ __('user_personal_data.data_summary_title') }}
        </h3>

        {{-- Data Completeness Progress --}}
        <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">
                    {{ __('user_personal_data.data_completeness') }}
                </span>
                <span class="text-sm text-gray-500">{{ round($completeness) }}%</span>
            </div>
            <div class="w-full h-2 bg-gray-200 rounded-full">
                <div class="h-2 rounded-full transition-all duration-300 {{ $completeness >= 80 ? 'bg-green-500' : ($completeness >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                     style="width: {{ $completeness }}%"></div>
            </div>
            <p class="mt-1 text-xs text-gray-500">
                @if($completeness >= 80)
                    {{ __('user_personal_data.profile_complete') }}
                @elseif($completeness >= 50)
                    {{ __('user_personal_data.profile_partial') }}
                @else
                    {{ __('user_personal_data.profile_incomplete') }}
                @endif
            </p>
        </div>

        {{-- Data Status Indicators --}}
        <div class="space-y-3">
            {{-- Basic Information --}}
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">{{ __('user_personal_data.basic_information') }}</span>
                @if($personalData->birth_date)
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        {{ __('user_personal_data.complete') }}
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-800 bg-gray-100 rounded-full">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        {{ __('user_personal_data.missing') }}
                    </span>
                @endif
            </div>

            {{-- Address Information --}}
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">{{ __('user_personal_data.address_information') }}</span>
                @if($personalData->hasCompleteAddress())
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        {{ __('user_personal_data.complete') }}
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-800 bg-yellow-100 rounded-full">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ __('user_personal_data.partial') }}
                    </span>
                @endif
            </div>

            {{-- Contact Information --}}
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">{{ __('user_personal_data.contact_information') }}</span>
                @if($personalData->cell_phone || $personalData->home_phone)
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        {{ __('user_personal_data.available') }}
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-800 bg-gray-100 rounded-full">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        {{ __('user_personal_data.not_provided') }}
                    </span>
                @endif
            </div>

            {{-- Fiscal Information (Country-Specific) --}}
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">
                    {{ __('user_personal_data.fiscal_information') }}
                    <span class="text-xs text-gray-400">({{ $userCountry }})</span>
                </span>
                @if($fiscalComplete)
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        {{ __('user_personal_data.validated') }}
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ __('user_personal_data.pending') }}
                    </span>
                @endif
            </div>

            {{-- GDPR Consent --}}
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600">{{ __('user_personal_data.gdpr_consent') }}</span>
                @if($personalData->allow_personal_data_processing)
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        {{ __('user_personal_data.consent_given') }}
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-800 bg-red-100 rounded-full">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        {{ __('user_personal_data.consent_required') }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Last Update Info --}}
        @if($lastUpdate)
            <div class="pt-3 mt-4 border-t border-gray-200">
                <div class="flex items-center text-xs text-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('user_personal_data.last_updated') }}: {{ $lastUpdate->format('d/m/Y H:i') }}
                </div>
            </div>
        @endif
    </div>
</div>
