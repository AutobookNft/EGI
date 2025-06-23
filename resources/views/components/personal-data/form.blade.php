{{--
@Oracode Component: Personal Data Form (OS1-Compliant)
🎯 Purpose: Main form for personal data editing with country-specific validation
🛡️ Privacy: GDPR-compliant with consent management and audit trail
🧱 Core Logic: Dynamic validation based on user country and business type
🌍 Scale: 6 MVP countries support (IT, PT, FR, ES, EN, DE)

@props [
    'user' => \App\Models\User,
    'personalData' => \App\Models\UserPersonalData,
    'gdprConsents' => \App\Models\UserGDPRConsents,
    'userCountry' => string,
    'availableCountries' => array,
    'validationConfig' => array,
    'canEdit' => bool,
    'authType' => string
]
--}}

@props([
    'user',
    'personalData',
    'gdprConsents',
    'userCountry',
    'availableCountries',
    'validationConfig',
    'canEdit' => true,
    'authType' => 'strong'
])

<div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
    <div class="p-6">
        <form id="personal-data-form" method="POST" action="{{ route('user.domains.personal-data.update') }}">
            @csrf
            @method('PUT')

            {{-- Personal Identity Section --}}
            <div class="mb-8">
                <x-personal-data.section-header
                    title="{{ __('user_personal_data.basic_information') }}"
                    description="{{ __('user_personal_data.basic_description') }}"
                    icon="user" />

                <div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">
                    {{-- Birth Date --}}
                    <div>
                        <x-input-label for="birth_date" :value="__('user_personal_data.birth_date')" />
                        <x-text-input
                            id="birth_date"
                            name="birth_date"
                            type="date"
                            class="block w-full mt-1"
                            :value="old('birth_date', $personalData->birth_date?->format('Y-m-d'))"
                            :disabled="!$canEdit"
                            data-validation="birth_date" />
                        <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                    </div>

                    {{-- Birth Place --}}
                    <div>
                        <x-input-label for="birth_place" :value="__('user_personal_data.birth_place')" />
                        <x-text-input
                            id="birth_place"
                            name="birth_place"
                            type="text"
                            class="block w-full mt-1"
                            :value="old('birth_place', $personalData->birth_place)"
                            :placeholder="__('user_personal_data.birth_place_placeholder')"
                            :disabled="!$canEdit"
                            data-validation="birth_place" />
                        <x-input-error :messages="$errors->get('birth_place')" class="mt-2" />
                    </div>

                    {{-- Gender --}}
                    <div class="md:col-span-2">
                        <x-input-label for="gender" :value="__('user_personal_data.gender')" />
                        <select
                            id="gender"
                            name="gender"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            :disabled="!$canEdit"
                            data-validation="gender">
                            <option value="">{{ __('label.select_option') }}</option>
                            <option value="male" {{ old('gender', $personalData->gender) === 'male' ? 'selected' : '' }}>
                                {{ __('user_personal_data.gender_male') }}
                            </option>
                            <option value="female" {{ old('gender', $personalData->gender) === 'female' ? 'selected' : '' }}>
                                {{ __('user_personal_data.gender_female') }}
                            </option>
                            <option value="other" {{ old('gender', $personalData->gender) === 'other' ? 'selected' : '' }}>
                                {{ __('user_personal_data.gender_other') }}
                            </option>
                            <option value="prefer_not_say" {{ old('gender', $personalData->gender) === 'prefer_not_say' ? 'selected' : '' }}>
                                {{ __('user_personal_data.gender_prefer_not_say') }}
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                    </div>
                </div>
            </div>

            {{-- Address Information Section --}}
            <div class="mb-8">
                <x-personal-data.section-header
                    title="{{ __('user_personal_data.address_information') }}"
                    description="{{ __('user_personal_data.address_description') }}"
                    icon="map-pin" />

                <div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">
                    {{-- Street Address --}}
                    <div class="md:col-span-2">
                        <x-input-label for="street" :value="__('user_personal_data.street_address')" />
                        <x-text-input
                            id="street"
                            name="street"
                            type="text"
                            class="block w-full mt-1"
                            :value="old('street', $personalData->street)"
                            :placeholder="__('user_personal_data.street_address_placeholder')"
                            :disabled="!$canEdit"
                            data-validation="street" />
                        <x-input-error :messages="$errors->get('street')" class="mt-2" />
                    </div>

                    {{-- City --}}
                    <div>
                        <x-input-label for="city" :value="__('user_personal_data.city')" />
                        <x-text-input
                            id="city"
                            name="city"
                            type="text"
                            class="block w-full mt-1"
                            :value="old('city', $personalData->city)"
                            :placeholder="__('user_personal_data.city_placeholder')"
                            :disabled="!$canEdit"
                            data-validation="city" />
                        <x-input-error :messages="$errors->get('city')" class="mt-2" />
                    </div>

                    {{-- Postal Code --}}
                    <div>
                        <x-input-label for="zip" :value="__('user_personal_data.postal_code')" />
                        <x-text-input
                            id="zip"
                            name="zip"
                            type="text"
                            class="block w-full mt-1"
                            :value="old('zip', $personalData->zip)"
                            :placeholder="__('user_personal_data.postal_code_placeholder')"
                            :disabled="!$canEdit"
                            data-validation="zip"
                            data-country="{{ $userCountry }}" />
                        <x-input-error :messages="$errors->get('zip')" class="mt-2" />
                    </div>

                    {{-- Province --}}
                    <div>
                        <x-input-label for="province" :value="__('user_personal_data.province')" />
                        <x-text-input
                            id="province"
                            name="province"
                            type="text"
                            class="block w-full mt-1"
                            :value="old('province', $personalData->province)"
                            :placeholder="__('user_personal_data.province_placeholder')"
                            :disabled="!$canEdit"
                            data-validation="province" />
                        <x-input-error :messages="$errors->get('province')" class="mt-2" />
                    </div>

                    {{-- Country --}}
                    <div>
                        <x-input-label for="country" :value="__('user_personal_data.country')" />
                        <select
                            id="country"
                            name="country"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            :disabled="!$canEdit"
                            data-validation="country">
                            <option value="">{{ __('label.select_option') }}</option>
                            @foreach($availableCountries as $code => $name)
                                <option value="{{ $code }}" {{ old('country', $personalData->country) === $code ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('country')" class="mt-2" />
                    </div>
                </div>
            </div>

            {{-- Contact Information Section --}}
            <div class="mb-8">
                <x-personal-data.section-header
                    title="{{ __('user_personal_data.contact_information') }}"
                    description="{{ __('user_personal_data.contact_description') }}"
                    icon="phone" />

                <div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">
                    {{-- Home Phone --}}
                    <div>
                        <x-input-label for="home_phone" :value="__('user_personal_data.phone')" />
                        <x-text-input
                            id="home_phone"
                            name="home_phone"
                            type="tel"
                            class="block w-full mt-1"
                            :value="old('home_phone', $personalData->home_phone)"
                            :placeholder="__('user_personal_data.phone_placeholder')"
                            :disabled="!$canEdit"
                            data-validation="home_phone" />
                        <x-input-error :messages="$errors->get('home_phone')" class="mt-2" />
                    </div>

                    {{-- Cell Phone --}}
                    <div>
                        <x-input-label for="cell_phone" :value="__('user_personal_data.mobile')" />
                        <x-text-input
                            id="cell_phone"
                            name="cell_phone"
                            type="tel"
                            class="block w-full mt-1"
                            :value="old('cell_phone', $personalData->cell_phone)"
                            :placeholder="__('user_personal_data.phone_placeholder')"
                            :disabled="!$canEdit"
                            data-validation="cell_phone" />
                        <x-input-error :messages="$errors->get('cell_phone')" class="mt-2" />
                    </div>

                    {{-- Work Phone --}}
                    <div>
                        <x-input-label for="work_phone" :value="__('user_personal_data.work_phone')" />
                        <x-text-input
                            id="work_phone"
                            name="work_phone"
                            type="tel"
                            class="block w-full mt-1"
                            :value="old('work_phone', $personalData->work_phone)"
                            :placeholder="__('user_personal_data.phone_placeholder')"
                            :disabled="!$canEdit"
                            data-validation="work_phone" />
                        <x-input-error :messages="$errors->get('work_phone')" class="mt-2" />
                    </div>

                    {{-- Emergency Contact --}}
                    <div>
                        <x-input-label for="emergency_contact" :value="__('user_personal_data.emergency_contact')" />
                        <x-text-input
                            id="emergency_contact"
                            name="emergency_contact"
                            type="text"
                            class="block w-full mt-1"
                            :value="old('emergency_contact', $personalData->emergency_contact)"
                            :placeholder="__('user_personal_data.emergency_contact_placeholder')"
                            :disabled="!$canEdit"
                            data-validation="emergency_contact" />
                        <x-input-error :messages="$errors->get('emergency_contact')" class="mt-2" />
                    </div>
                </div>
            </div>

            {{-- Fiscal Information Section (Country-Specific) --}}
            <div class="mb-8" id="fiscal-section" data-country="{{ $userCountry }}">
                <x-personal-data.section-header
                    title="{{ __('user_personal_data.fiscal_information') }}"
                    description="{{ __('user_personal_data.fiscal_description') }}"
                    icon="credit-card" />

                <div class="grid grid-cols-1 gap-6 mt-4 md:grid-cols-2">
                    {{-- Fiscal Code (Country-Specific) --}}
                    <div>
                        <x-input-label for="fiscal_code" :value="__('user_personal_data.tax_code')" />
                        <x-text-input
                            id="fiscal_code"
                            name="fiscal_code"
                            type="text"
                            class="block w-full mt-1 font-mono"
                            :value="old('fiscal_code', $personalData->fiscal_code)"
                            :placeholder="__('user_personal_data.tax_code_placeholder')"
                            :disabled="!$canEdit"
                            data-validation="fiscal_code"
                            data-country="{{ $userCountry }}" />
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('user_personal_data.tax_code_help') }}
                        </p>
                        <x-input-error :messages="$errors->get('fiscal_code')" class="mt-2" />
                    </div>

                    {{-- Tax ID Number --}}
                    <div>
                        <x-input-label for="tax_id_number" :value="__('user_personal_data.id_card_number')" />
                        <x-text-input
                            id="tax_id_number"
                            name="tax_id_number"
                            type="text"
                            class="block w-full mt-1"
                            :value="old('tax_id_number', $personalData->tax_id_number)"
                            :placeholder="__('user_personal_data.id_card_number_placeholder')"
                            :disabled="!$canEdit"
                            data-validation="tax_id_number" />
                        <x-input-error :messages="$errors->get('tax_id_number')" class="mt-2" />
                    </div>
                </div>
            </div>

            {{-- GDPR Consent Management Section --}}
            <div class="mb-8">
                <x-personal-data.section-header
                    title="{{ __('user_personal_data.consent_management') }}"
                    description="{{ __('user_personal_data.consent_description') }}"
                    icon="shield-check" />

                {{-- Debug Information (only in development) --}}
                @if(config('app.debug'))
                    <div class="p-4 mb-4 text-xs border border-yellow-200 rounded bg-yellow-50">
                        <strong>🔍 Consent Debug Info (ConsentService Only):</strong><br>
                        ConsentService Says: {{ var_export($gdprConsents['allow_personal_data_processing'] ?? 'NOT_SET') }}<br>
                        Old Consents Array: {{ var_export(old('consents')) }}<br>
                        Checkbox Should Be: {{ ($gdprConsents['allow_personal_data_processing'] ?? false) ? 'CHECKED' : 'UNCHECKED' }}<br>
                        @if(isset($gdprConsents['_debug']))
                            User Consents Count: {{ $gdprConsents['_debug']['user_consents_count'] }}<br>
                            Available Consent Types: {{ implode(', ', $gdprConsents['_debug']['consent_types_defined'] ?? []) }}<br>
                        @endif
                    </div>
                @endif

                <div class="mt-4 space-y-4">
                    {{-- Data Processing Consent --}}
                    <div class="flex items-start space-x-3">
                        @php
                            // ✅ CORRETTO: Controllo consent dal ConsentService + old() su array consents
                            $currentConsentStatus = $gdprConsents['allow-personal-data-processing'] ?? false;
                            $oldConsentValue = old('consents.allow_personal_data_processing', $currentConsentStatus);
                            $isChecked = (bool) $oldConsentValue;
                        @endphp

                        <input
                            type="checkbox"
                            id="consent_allow_personal_data_processing"
                            name="consents[allow_personal_data_processing]"
                            value="1"
                            {{ $isChecked ? 'checked' : '' }}
                            @if(!$canEdit) disabled @endif
                            class="mt-1 text-indigo-600 border-gray-300 rounded shadow-sm focus:ring-indigo-500"
                            onchange="toggleProcessingPurposes(this.checked)" />

                        <div>
                            <label for="consent_allow_personal_data_processing" class="text-sm font-medium text-gray-700">
                                {{ __('user_personal_data.consent_required') }}
                                <span class="text-red-500">*</span>
                            </label>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __('user_personal_data.gdpr_notices.data_processing_info') }}
                            </p>
                        </div>
                    </div>


                    {{-- Processing Purposes - Corretto come lista informativa non interattiva --}}
                    <div id="processing-purposes"
                        class="pl-6 ml-6 space-y-1 border-l-2 border-gray-200">

                        <p class="mb-2 text-sm font-medium text-gray-700">{{ __('user_personal_data.processing_purposes') }}:</p>

                        {{-- Controlliamo che il nostro DTO esista e abbia dei purposes --}}
                        @if(isset($platformServicesConsent) && !empty($platformServicesConsent->processingPurposes))

                            {{-- Usiamo una lista non ordinata (<ul>) per la massima chiarezza semantica --}}
                            <ul class="text-sm text-gray-600 list-disc list-inside">

                                {{-- Cicliamo sulla lista di "slug" dei purposes --}}
                                @foreach($platformServicesConsent->processingPurposes as $purposeSlug)

                                    {{-- Ogni purpose è un semplice punto della lista (<li>) --}}
                                    <li>
                                        {{-- Usiamo lo slug per prendere la sua traduzione --}}
                                        {{ __('gdpr.consent_types.purposes.' . $purposeSlug) }}
                                    </li>

                                @endforeach
                            </ul>

                        @endif
                    </div>

                    {{-- Altri consensi GDPR --}}
                    <div class="grid grid-cols-1 gap-4 mt-6 md:grid-cols-2">
                        {{-- Marketing Consent --}}
                        <div class="flex items-start space-x-3">
                            @php
                                $marketingConsent = old('consents.marketing', $gdprConsents['marketing'] ?? false);
                            @endphp

                            <input
                                type="checkbox"
                                id="consent_marketing"
                                name="consents[marketing]"
                                value="1"
                                {{ $marketingConsent ? 'checked' : '' }}
                                @if(!$canEdit) disabled @endif
                                class="mt-1 text-indigo-600 border-gray-300 rounded shadow-sm focus:ring-indigo-500" />

                            <div>
                                <label for="consent_marketing" class="text-sm font-medium text-gray-700">
                                    {{ __('user_personal_data.consent_marketing') }}
                                </label>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('user_personal_data.consent_marketing_desc') }}
                                </p>
                            </div>
                        </div>

                        {{-- Analytics Consent --}}
                        <div class="flex items-start space-x-3">
                            @php
                                $analyticsConsent = old('consents.analytics', $gdprConsents['analytics'] ?? false);
                            @endphp

                            <input
                                type="checkbox"
                                id="consent_analytics"
                                name="consents[analytics]"
                                value="1"
                                {{ $analyticsConsent ? 'checked' : '' }}
                                @if(!$canEdit) disabled @endif
                                class="mt-1 text-indigo-600 border-gray-300 rounded shadow-sm focus:ring-indigo-500" />

                            <div>
                                <label for="consent_analytics" class="text-sm font-medium text-gray-700">
                                    {{ __('user_personal_data.consent_analytics') }}
                                </label>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('user_personal_data.consent_analytics_desc') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <x-input-error :messages="$errors->get('consents.allow_personal_data_processing')" class="mt-2" />
                    <x-input-error :messages="$errors->get('consent_metadata.processing_purposes')" class="mt-2" />
                </div>
            </div>

            {{-- Debug Information --}}

            {{-- Form Actions --}}
            @if($canEdit)
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-500">
                        @if($authType === 'weak')
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-800 bg-yellow-100 rounded-full">
                                {{ __('user_personal_data.weak_auth_notice') }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center space-x-3">
                        <x-secondary-button type="button" data-action="reset-form">
                            {{ __('user_personal_data.cancel_changes') }}
                        </x-secondary-button>

                        <x-primary-button type="submit" data-action="submit-form">
                            {{ __('user_personal_data.update_data') }}
                        </x-primary-button>
                    </div>
                </div>
            @else
                <div class="pt-6 border-t border-gray-200">
                    <div class="text-center">
                        <p class="text-sm text-gray-500">
                            {{ __('user_personal_data.read_only_notice') }}
                        </p>
                    </div>
                </div>
            @endif
        </form>
    </div>
</div>
{{-- JavaScript for Dynamic Show/Hide --}}
<script>
function toggleProcessingPurposes(isChecked) {
    const purposesDiv = document.getElementById('processing-purposes');
    if (purposesDiv) {
        purposesDiv.style.display = isChecked ? 'block' : 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('consent_allow_personal_data_processing');
    if (checkbox) {
        toggleProcessingPurposes(checkbox.checked);
    }
});
</script>
