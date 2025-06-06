{{--
@Oracode View: Personal Data Management Interface (GDPR-Compliant)
🎯 Purpose: Provide secure personal data editing with country-specific validation
🛡️ Privacy: GDPR-native interface with consent management and audit awareness
🧱 Core Logic: FegiAuth integration + country adaptation + fiscal validation UI
🌍 Scale: Multi-country form support with intelligent field requirements
⏰ MVP: Critical user domain interface for FlorenceEGI platform

@package resources/views/user/domains/personal-data
@author Padmin D. Curtis (AI Partner OS1-Compliant)
@version 1.0.0 (FlorenceEGI MVP - GDPR Native)
@deadline 2025-06-30

@oracode-dimension communication (user interface)
@oracode-dimension governance (GDPR compliance)
@oracode-dimension impact (user data control)
@value-flow Enables secure personal data management with transparency
@community-impact Supports user data sovereignty and privacy rights
@transparency-level High - clear consent status and data usage
@narrative-coherence Empowers users with control over personal information
--}}

<x-app-layout>
    <x-slot name="title">{{ __('user_personal_data.page_title') }}</x-slot>

    <x-slot name="meta">
        <meta name="description" content="{{ __('user_personal_data.meta_description') }}">
        <meta name="robots" content="noindex, nofollow">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </x-slot>

    <x-slot name="styles">
        <link href="{{ asset('css/user-domains.css') }}" rel="stylesheet">
        @include('user.domains.personal-data.partials.styles')
    </x-slot>

    <x-container class="py-8">
        {{-- Page Header with Auth Status --}}
        <x-page-header>
            <x-slot name="title">
                <div class="flex items-center justify-between">
                    <h1 class="text-3xl font-bold text-gray-900">
                        {{ __('user_personal_data.page_title') }}
                    </h1>
                    <x-auth-badge :type="$authType" />
                </div>
            </x-slot>

            <x-slot name="description">
                {{ __('user_personal_data.page_description') }}
            </x-slot>
        </x-page-header>

        {{-- GDPR Compliance Status --}}
        <x-gdpr-status-card
            :status="$consentStatus"
            :compliant="$gdprCompliant"
            class="mb-6"
        />

        {{-- Error Display --}}
        @if(isset($error))
            <x-alert type="error" :dismissible="true" class="mb-6">
                {{ $error }}
            </x-alert>
        @endif

        {{-- Success Messages --}}
        @if(session('success'))
            <x-alert type="success" :dismissible="true" class="mb-6">
                {{ session('success') }}
                @if(session('changes_count'))
                    <span class="block mt-1 text-sm">
                        {{ __('user_personal_data.messages.fields_updated', ['count' => session('changes_count')]) }}
                    </span>
                @endif
            </x-alert>
        @endif

        {{-- Main Form Card --}}
        <x-card>
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">
                        {{ __('user_personal_data.form_title') }}
                    </h2>
                    <div class="flex items-center space-x-3">
                        @if($lastUpdated)
                            <span class="text-sm text-gray-500">
                                {{ __('user_personal_data.last_updated') }}:
                                <time datetime="{{ $lastUpdated->toISOString() }}">
                                    {{ $lastUpdated->diffForHumans() }}
                                </time>
                            </span>
                        @endif
                        <x-country-selector
                            :selected="$userCountry"
                            :countries="$supportedCountries"
                            wire:model="selectedCountry"
                        />
                    </div>
                </div>
            </x-slot>

            @if($canEdit)
                <form
                    action="{{ route('user.personal-data.update') }}"
                    method="POST"
                    id="personal-data-form"
                    class="space-y-6"
                    x-data="personalDataForm({{ json_encode($formRequirements) }}, '{{ $userCountry }}')"
                    x-init="initializeForm()"
                >
                    @csrf
                    @method('PUT')

                    {{-- Personal Identity Section --}}
                    <x-form-section>
                        <x-slot name="title">{{ __('user_personal_data.sections.identity') }}</x-slot>
                        <x-slot name="description">{{ __('user_personal_data.sections.identity_description') }}</x-slot>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <x-form-group>
                                <x-label for="birth_date">{{ __('user_personal_data.birth_date') }}</x-label>
                                <x-input
                                    type="date"
                                    id="birth_date"
                                    name="birth_date"
                                    :value="old('birth_date', $personalData->birth_date?->format('Y-m-d'))"
                                    :max="now()->subYears(13)->format('Y-m-d')"
                                    :min="now()->subYears(120)->format('Y-m-d')"
                                />
                                <x-input-error :messages="$errors->get('birth_date')" />
                            </x-form-group>

                            <x-form-group>
                                <x-label for="birth_place">{{ __('user_personal_data.birth_place') }}</x-label>
                                <x-input
                                    type="text"
                                    id="birth_place"
                                    name="birth_place"
                                    :value="old('birth_place', $personalData->birth_place)"
                                    :placeholder="__('user_personal_data.placeholders.birth_place')"
                                    maxlength="255"
                                />
                                <x-input-error :messages="$errors->get('birth_place')" />
                            </x-form-group>

                            <x-form-group>
                                <x-label for="gender">{{ __('user_personal_data.gender') }}</x-label>
                                <x-select id="gender" name="gender">
                                    <option value="">{{ __('user_personal_data.select_gender') }}</option>
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
                                </x-select>
                                <x-input-error :messages="$errors->get('gender')" />
                            </x-form-group>
                        </div>
                    </x-form-section>

                    {{-- Address Section --}}
                    <x-form-section>
                        <x-slot name="title">{{ __('user_personal_data.sections.address') }}</x-slot>
                        <x-slot name="description">{{ __('user_personal_data.sections.address_description') }}</x-slot>

                        <div class="space-y-4">
                            <x-form-group>
                                <x-label for="street">{{ __('user_personal_data.street_address') }}</x-label>
                                <x-input
                                    type="text"
                                    id="street"
                                    name="street"
                                    :value="old('street', $personalData->street)"
                                    :placeholder="__('user_personal_data.placeholders.street')"
                                    maxlength="255"
                                />
                                <x-input-error :messages="$errors->get('street')" />
                            </x-form-group>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <x-form-group>
                                    <x-label for="city">{{ __('user_personal_data.city') }}</x-label>
                                    <x-input
                                        type="text"
                                        id="city"
                                        name="city"
                                        :value="old('city', $personalData->city)"
                                        :placeholder="__('user_personal_data.placeholders.city')"
                                        maxlength="100"
                                    />
                                    <x-input-error :messages="$errors->get('city')" />
                                </x-form-group>

                                <x-form-group x-show="countryRequires('province')" x-transition>
                                    <x-label for="province">{{ __('user_personal_data.province') }}</x-label>
                                    <x-input
                                        type="text"
                                        id="province"
                                        name="province"
                                        :value="old('province', $personalData->province)"
                                        :placeholder="__('user_personal_data.placeholders.province')"
                                        maxlength="10"
                                        x-bind:required="countryRequires('province')"
                                    />
                                    <x-input-error :messages="$errors->get('province')" />
                                </x-form-group>

                                <x-form-group>
                                    <x-label for="zip" x-text="getPostalCodeLabel()">{{ __('user_personal_data.postal_code') }}</x-label>
                                    <x-input
                                        type="text"
                                        id="zip"
                                        name="zip"
                                        :value="old('zip', $personalData->zip)"
                                        x-bind:placeholder="getPostalCodePlaceholder()"
                                        maxlength="20"
                                    />
                                    <x-input-error :messages="$errors->get('zip')" />
                                </x-form-group>
                            </div>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <x-form-group x-show="countryRequires('region')" x-transition>
                                    <x-label for="region">{{ __('user_personal_data.region') }}</x-label>
                                    <x-input
                                        type="text"
                                        id="region"
                                        name="region"
                                        :value="old('region', $personalData->region)"
                                        :placeholder="__('user_personal_data.placeholders.region')"
                                        maxlength="100"
                                    />
                                    <x-input-error :messages="$errors->get('region')" />
                                </x-form-group>

                                <x-form-group x-show="countryRequires('state')" x-transition>
                                    <x-label for="state">{{ __('user_personal_data.state') }}</x-label>
                                    <x-input
                                        type="text"
                                        id="state"
                                        name="state"
                                        :value="old('state', $personalData->state)"
                                        :placeholder="__('user_personal_data.placeholders.state')"
                                        maxlength="100"
                                    />
                                    <x-input-error :messages="$errors->get('state')" />
                                </x-form-group>
                            </div>

                            <x-form-group>
                                <x-label for="country">{{ __('user_personal_data.country') }}</x-label>
                                <x-select
                                    id="country"
                                    name="country"
                                    x-model="selectedCountry"
                                    @change="updateCountryRequirements($event.target.value)"
                                >
                                    <option value="">{{ __('user_personal_data.select_country') }}</option>
                                    @foreach($supportedCountries as $code => $name)
                                        <option
                                            value="{{ $code }}"
                                            {{ old('country', $personalData->country) === $code ? 'selected' : '' }}
                                        >
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </x-select>
                                <x-input-error :messages="$errors->get('country')" />
                            </x-form-group>
                        </div>
                    </x-form-section>

                    {{-- Contact Information Section --}}
                    <x-form-section>
                        <x-slot name="title">{{ __('user_personal_data.sections.contact') }}</x-slot>
                        <x-slot name="description">{{ __('user_personal_data.sections.contact_description') }}</x-slot>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <x-form-group>
                                <x-label for="home_phone">{{ __('user_personal_data.phone') }}</x-label>
                                <x-input
                                    type="tel"
                                    id="home_phone"
                                    name="home_phone"
                                    :value="old('home_phone', $personalData->home_phone)"
                                    :placeholder="__('user_personal_data.placeholders.phone')"
                                    maxlength="20"
                                />
                                <x-input-error :messages="$errors->get('home_phone')" />
                            </x-form-group>

                            <x-form-group>
                                <x-label for="cell_phone">{{ __('user_personal_data.mobile') }}</x-label>
                                <x-input
                                    type="tel"
                                    id="cell_phone"
                                    name="cell_phone"
                                    :value="old('cell_phone', $personalData->cell_phone)"
                                    :placeholder="__('user_personal_data.placeholders.mobile')"
                                    maxlength="20"
                                />
                                <x-input-error :messages="$errors->get('cell_phone')" />
                            </x-form-group>

                            <x-form-group>
                                <x-label for="work_phone">{{ __('user_personal_data.work_phone') }}</x-label>
                                <x-input
                                    type="tel"
                                    id="work_phone"
                                    name="work_phone"
                                    :value="old('work_phone', $personalData->work_phone)"
                                    :placeholder="__('user_personal_data.placeholders.work_phone')"
                                    maxlength="20"
                                />
                                <x-input-error :messages="$errors->get('work_phone')" />
                            </x-form-group>
                        </div>

                        <x-form-group>
                            <x-label for="emergency_contact">{{ __('user_personal_data.emergency_contact') }}</x-label>
                            <x-textarea
                                id="emergency_contact"
                                name="emergency_contact"
                                rows="3"
                                :placeholder="__('user_personal_data.placeholders.emergency_contact')"
                                maxlength="500"
                            >{{ old('emergency_contact', $personalData->emergency_contact) }}</x-textarea>
                            <x-input-error :messages="$errors->get('emergency_contact')" />
                            <x-input-help>{{ __('user_personal_data.help.emergency_contact') }}</x-input-help>
                        </x-form-group>
                    </x-form-section>

                    {{-- Fiscal Information Section --}}
                    <x-form-section x-show="hasFiscalFields()" x-transition>
                        <x-slot name="title">{{ __('user_personal_data.sections.fiscal') }}</x-slot>
                        <x-slot name="description">{{ __('user_personal_data.sections.fiscal_description') }}</x-slot>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <x-form-group x-show="countryRequires('fiscal_code')" x-transition>
                                <x-label for="fiscal_code">{{ __('user_personal_data.tax_code') }}</x-label>
                                <x-input
                                    type="text"
                                    id="fiscal_code"
                                    name="fiscal_code"
                                    :value="old('fiscal_code', $personalData->fiscal_code)"
                                    x-bind:placeholder="getFiscalCodePlaceholder()"
                                    x-bind:maxlength="getFiscalCodeMaxLength()"
                                    x-bind:pattern="getFiscalCodePattern()"
                                    class="uppercase"
                                    x-bind:required="countryRequires('fiscal_code')"
                                    @input="validateFiscalCode($event)"
                                />
                                <x-input-error :messages="$errors->get('fiscal_code')" />
                                <div x-show="fiscalCodeFeedback" x-text="fiscalCodeFeedback" class="fiscal-validation-feedback"></div>
                            </x-form-group>

                            <x-form-group x-show="countryRequires('tax_id_number')" x-transition>
                                <x-label for="tax_id_number">{{ __('user_personal_data.id_card_number') }}</x-label>
                                <x-input
                                    type="text"
                                    id="tax_id_number"
                                    name="tax_id_number"
                                    :value="old('tax_id_number', $personalData->tax_id_number)"
                                    :placeholder="__('user_personal_data.placeholders.tax_id')"
                                    maxlength="30"
                                    @input="validateTaxId($event)"
                                />
                                <x-input-error :messages="$errors->get('tax_id_number')" />
                                <div x-show="taxIdFeedback" x-text="taxIdFeedback" class="fiscal-validation-feedback"></div>
                            </x-form-group>
                        </div>
                    </x-form-section>

                    {{-- GDPR Consent Section --}}
                    <x-form-section>
                        <x-slot name="title">{{ __('user_personal_data.sections.privacy') }}</x-slot>
                        <x-slot name="description">{{ __('user_personal_data.sections.privacy_description') }}</x-slot>

                        <div class="space-y-6">
                            <x-form-group>
                                <div class="flex items-start space-x-3">
                                    <x-checkbox
                                        id="allow_personal_data_processing"
                                        name="allow_personal_data_processing"
                                        value="1"
                                        :checked="old('allow_personal_data_processing', $personalData->allow_personal_data_processing)"
                                        required
                                        class="mt-1"
                                    />
                                    <div class="flex-1">
                                        <x-label for="allow_personal_data_processing" class="font-medium">
                                            {{ __('user_personal_data.consent_required') }}
                                        </x-label>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ __('user_personal_data.consent_description') }}
                                        </p>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('allow_personal_data_processing')" />
                            </x-form-group>

                            <x-form-group>
                                <x-label>{{ __('user_personal_data.processing_purposes_title') }}</x-label>
                                <div class="processing-purposes-grid">
                                    @foreach($processingPurposes as $purposeKey => $purposeLabel)
                                        <div class="flex items-start p-3 space-x-3 border border-gray-200 rounded-lg">
                                            <x-checkbox
                                                id="purpose_{{ $purposeKey }}"
                                                name="processing_purposes[]"
                                                value="{{ $purposeKey }}"
                                                :checked="in_array($purposeKey, old('processing_purposes', $personalData->processing_purposes ?? []))"
                                                class="mt-1"
                                            />
                                            <div class="flex-1">
                                                <x-label for="purpose_{{ $purposeKey }}" class="text-sm font-medium">
                                                    {{ $purposeLabel }}
                                                </x-label>
                                                <p class="mt-1 text-xs text-gray-500">
                                                    {{ __("user_personal_data.processing_purposes_description.{$purposeKey}") }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('processing_purposes')" />
                            </x-form-group>
                        </div>
                    </x-form-section>

                    {{-- Form Actions --}}
                    <x-form-actions>
                        <x-button
                            type="submit"
                            variant="primary"
                            :loading="true"
                            x-bind:disabled="!isFormValid()"
                        >
                            {{ __('user_personal_data.save_changes') }}
                        </x-button>

                        <x-button
                            type="button"
                            variant="secondary"
                            @click="resetForm()"
                        >
                            {{ __('user_personal_data.reset_form') }}
                        </x-button>

                        @if(FegiAuth::can('export_own_personal_data'))
                            <x-button
                                type="button"
                                variant="outline"
                                @click="exportData()"
                                x-bind:disabled="isExporting"
                            >
                                <span x-show="!isExporting">{{ __('user_personal_data.export_data') }}</span>
                                <span x-show="isExporting">{{ __('user_personal_data.exporting') }}...</span>
                            </x-button>
                        @endif
                    </x-form-actions>
                </form>
            @else
                {{-- Read-Only View for Users Without Edit Permission --}}
                <x-read-only-personal-data :data="$personalData" :consent="$consentStatus" />
            @endif
        </x-card>

        {{-- GDPR Actions Card --}}
        @if($canEdit && FegiAuth::can('delete_own_personal_data'))
            <x-gdpr-actions-card class="mt-6" />
        @endif
    </x-container>

    <x-slot name="scripts">
        @include('user.domains.personal-data.partials.scripts')
    </x-slot>
</x-app-layout>
