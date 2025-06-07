{{--
@Oracode Component: Validation Info (OS1-Compliant)
🎯 Purpose: Country-specific validation information and help
🛡️ Privacy: Clear validation requirements for data quality
🧱 Core Logic: Dynamic validation rules based on user country

@props [
    'userCountry' => string,
    'validationConfig' => array
]
--}}

@props([
    'userCountry',
    'validationConfig'
])

@php
    // Country-specific information
    $countryInfo = [
        'IT' => [
            'name' => 'Italia',
            'fiscal_code_format' => 'RSSMRA80A01H501X',
            'fiscal_code_length' => '16',
            'postal_code_format' => '00100',
            'phone_format' => '+39 123 456 7890'
        ],
        'PT' => [
            'name' => 'Portugal',
            'fiscal_code_format' => '123456789',
            'fiscal_code_length' => '9',
            'postal_code_format' => '1000-001',
            'phone_format' => '+351 12 345 6789'
        ],
        'FR' => [
            'name' => 'France',
            'fiscal_code_format' => '1234567890123',
            'fiscal_code_length' => '13',
            'postal_code_format' => '75001',
            'phone_format' => '+33 1 23 45 67 89'
        ],
        'ES' => [
            'name' => 'España',
            'fiscal_code_format' => '12345678Z',
            'fiscal_code_length' => '9',
            'postal_code_format' => '28001',
            'phone_format' => '+34 123 456 789'
        ],
        'EN' => [
            'name' => 'England',
            'fiscal_code_format' => 'AB123456C',
            'fiscal_code_length' => '9',
            'postal_code_format' => 'SW1A 1AA',
            'phone_format' => '+44 20 1234 5678'
        ],
        'DE' => [
            'name' => 'Deutschland',
            'fiscal_code_format' => '12345678901',
            'fiscal_code_length' => '11',
            'postal_code_format' => '10115',
            'phone_format' => '+49 30 12345678'
        ]
    ];

    $info = $countryInfo[$userCountry] ?? $countryInfo['IT'];
@endphp

<div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
    <div class="p-4">
        <h3 class="mb-4 text-lg font-medium text-gray-900">
            {{ __('user_personal_data.validation_info_title') }}
        </h3>

        {{-- Country Indicator --}}
        <div class="p-3 mb-4 border border-indigo-200 rounded-lg bg-indigo-50">
            <div class="flex items-center space-x-2">
                <div class="flex items-center justify-center w-6 h-6 bg-indigo-100 rounded-full">
                    <span class="text-xs font-bold text-indigo-600">{{ $userCountry }}</span>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-indigo-900">
                        {{ $info['name'] }}
                    </h4>
                    <p class="text-xs text-indigo-700">
                        {{ __('user_personal_data.validation_country_detected') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Validation Rules --}}
        <div class="space-y-4">
            {{-- Fiscal Code Format --}}
            <div class="pl-3 border-l-4 border-blue-400">
                <h4 class="text-sm font-medium text-gray-900">
                    {{ __('user_personal_data.tax_code') }}
                </h4>
                <p class="mt-1 text-xs text-gray-600">
                    {{ __('user_personal_data.format') }}:
                    <code class="bg-gray-100 px-1 py-0.5 rounded text-xs font-mono">{{ $info['fiscal_code_format'] }}</code>
                </p>
                <p class="text-xs text-gray-500">
                    {{ __('user_personal_data.length') }}: {{ $info['fiscal_code_length'] }} {{ __('user_personal_data.characters') }}
                </p>
            </div>

            {{-- Postal Code Format --}}
            <div class="pl-3 border-l-4 border-green-400">
                <h4 class="text-sm font-medium text-gray-900">
                    {{ __('user_personal_data.postal_code') }}
                </h4>
                <p class="mt-1 text-xs text-gray-600">
                    {{ __('user_personal_data.format') }}:
                    <code class="bg-gray-100 px-1 py-0.5 rounded text-xs font-mono">{{ $info['postal_code_format'] }}</code>
                </p>
            </div>

            {{-- Phone Format --}}
            <div class="pl-3 border-l-4 border-purple-400">
                <h4 class="text-sm font-medium text-gray-900">
                    {{ __('user_personal_data.phone') }}
                </h4>
                <p class="mt-1 text-xs text-gray-600">
                    {{ __('user_personal_data.format') }}:
                    <code class="bg-gray-100 px-1 py-0.5 rounded text-xs font-mono">{{ $info['phone_format'] }}</code>
                </p>
            </div>

            {{-- GDPR Requirements --}}
            <div class="pl-3 border-l-4 border-amber-400">
                <h4 class="text-sm font-medium text-gray-900">
                    {{ __('user_personal_data.gdpr_requirements') }}
                </h4>
                <ul class="mt-1 space-y-1 text-xs text-gray-600">
                    <li>• {{ __('user_personal_data.gdpr_req_consent') }}</li>
                    <li>• {{ __('user_personal_data.gdpr_req_purposes') }}</li>
                    <li>• {{ __('user_personal_data.gdpr_req_accuracy') }}</li>
                </ul>
            </div>
        </div>

        {{-- Real-time Validation Status --}}
        <div class="pt-3 mt-4 border-t border-gray-200">
            <h4 class="mb-2 text-sm font-medium text-gray-900">
                {{ __('user_personal_data.validation_status') }}
            </h4>

            <div id="validation-status" class="space-y-2">
                {{-- Will be populated by TypeScript --}}
                <div class="text-xs text-gray-500">
                    {{ __('user_personal_data.validation_checking') }}
                </div>
            </div>
        </div>

        {{-- Help Links --}}
        <div class="pt-3 mt-4 border-t border-gray-200">
            <h4 class="mb-2 text-sm font-medium text-gray-900">
                {{ __('user_personal_data.help_resources') }}
            </h4>

            <div class="flex flex-wrap gap-2 text-xs">
                <a href="{{ route('documentation.index') }}#personal-data"
                   class="text-blue-600 underline hover:text-blue-800">
                    {{ __('user_personal_data.documentation') }}
                </a>
                <span class="text-gray-400">•</span>
                <a href="{{ route('gdpr.privacy-policy') }}"
                   class="text-blue-600 underline hover:text-blue-800">
                    {{ __('user_personal_data.privacy_policy') }}
                </a>
                <span class="text-gray-400">•</span>
                <a href="{{ route('gdpr.contact-dpo') }}"
                   class="text-blue-600 underline hover:text-blue-800">
                    {{ __('user_personal_data.contact_support') }}
                </a>
            </div>
        </div>
    </div>
</div>
