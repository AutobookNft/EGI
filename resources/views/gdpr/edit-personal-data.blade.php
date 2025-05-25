@extends('layouts.gdpr')

@section('title', __('gdpr.edit_personal_data'))

@section('content')
<div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        {{-- Header Card --}}
        <div class="p-6 mb-8 border shadow-xl bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ __('gdpr.edit_personal_data') }}</h1>
                    <p class="mt-2 text-gray-600">{{ __('gdpr.edit_personal_data_description') }}</p>
                </div>
                <div class="hidden sm:block">
                    <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Main Form --}}
        <form method="POST" action="{{ route('gdpr.edit-personal-data.update') }}"
              class="space-y-6" id="edit-personal-data-form">
            @csrf
            @method('PUT')

            {{-- Basic Information Section --}}
            <div class="p-6 border shadow-lg bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
                <h2 class="flex items-center mb-6 text-xl font-semibold text-gray-900">
                    <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ __('gdpr.basic_information') }}
                </h2>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    {{-- Name --}}
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-700">
                            {{ __('gdpr.name') }}
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               value="{{ old('name', $user->name) }}"
                               class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-700">
                            {{ __('gdpr.email') }}
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               value="{{ old('email', $user->email) }}"
                               class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="block mb-2 text-sm font-medium text-gray-700">
                            {{ __('gdpr.phone') }}
                        </label>
                        <input type="tel"
                               name="phone"
                               id="phone"
                               value="{{ old('phone', $user->phone) }}"
                               class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date of Birth --}}
                    <div>
                        <label for="date_of_birth" class="block mb-2 text-sm font-medium text-gray-700">
                            {{ __('gdpr.date_of_birth') }}
                        </label>
                        <input type="date"
                               name="date_of_birth"
                               id="date_of_birth"
                               value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                               class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Address Information Section --}}
            <div class="p-6 border shadow-lg bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
                <h2 class="flex items-center mb-6 text-xl font-semibold text-gray-900">
                    <svg class="w-6 h-6 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ __('gdpr.address_information') }}
                </h2>

                <div class="grid grid-cols-1 gap-6">
                    {{-- Street Address --}}
                    <div>
                        <label for="address" class="block mb-2 text-sm font-medium text-gray-700">
                            {{ __('gdpr.street_address') }}
                        </label>
                        <input type="text"
                               name="address"
                               id="address"
                               value="{{ old('address', $user->address) }}"
                               class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        {{-- City --}}
                        <div>
                            <label for="city" class="block mb-2 text-sm font-medium text-gray-700">
                                {{ __('gdpr.city') }}
                            </label>
                            <input type="text"
                                   name="city"
                                   id="city"
                                   value="{{ old('city', $user->city) }}"
                                   class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- State/Province --}}
                        <div>
                            <label for="state" class="block mb-2 text-sm font-medium text-gray-700">
                                {{ __('gdpr.state') }}
                            </label>
                            <input type="text"
                                   name="state"
                                   id="state"
                                   value="{{ old('state', $user->state) }}"
                                   class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Postal Code --}}
                        <div>
                            <label for="postal_code" class="block mb-2 text-sm font-medium text-gray-700">
                                {{ __('gdpr.postal_code') }}
                            </label>
                            <input type="text"
                                   name="postal_code"
                                   id="postal_code"
                                   value="{{ old('postal_code', $user->postal_code) }}"
                                   class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('postal_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Country --}}
                    <div>
                        <label for="country" class="block mb-2 text-sm font-medium text-gray-700">
                            {{ __('gdpr.country') }}
                        </label>
                        <select name="country"
                                id="country"
                                class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">{{ __('gdpr.select_country') }}</option>
                            @foreach($countries as $code => $name)
                                <option value="{{ $code }}" {{ old('country', $user->country) == $code ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Profile Information Section --}}
            <div class="p-6 border shadow-lg bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
                <h2 class="flex items-center mb-6 text-xl font-semibold text-gray-900">
                    <svg class="w-6 h-6 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ __('gdpr.profile_information') }}
                </h2>

                {{-- Bio --}}
                <div>
                    <label for="bio" class="block mb-2 text-sm font-medium text-gray-700">
                        {{ __('gdpr.bio') }}
                    </label>
                    <textarea name="bio"
                              id="bio"
                              rows="4"
                              class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="{{ __('gdpr.bio_placeholder') }}">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">{{ __('gdpr.bio_help') }}</p>
                </div>
            </div>

            {{-- Audit Information --}}
            <div class="p-6 border shadow-lg bg-gray-50/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
                <h3 class="mb-3 text-sm font-medium text-gray-700">{{ __('gdpr.data_information') }}</h3>
                <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-gray-500">{{ __('gdpr.last_updated') }}</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->updated_at->format('d M Y, H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('gdpr.account_created') }}</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->created_at->format('d M Y') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col justify-between gap-4 sm:flex-row">
                <a href="{{ route('gdpr.consent') }}"
                   class="inline-flex items-center justify-center px-6 py-3 text-gray-700 transition-all duration-200 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('gdpr.cancel') }}
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center px-6 py-3 text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('gdpr.save_changes') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('edit-personal-data-form');
    const inputs = form.querySelectorAll('input[required]');

    // Real-time validation
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateInput(this);
        });

        input.addEventListener('input', function() {
            if (this.classList.contains('border-red-500')) {
                validateInput(this);
            }
        });
    });

    // Validate single input
    function validateInput(input) {
        const isValid = input.checkValidity();

        if (!isValid) {
            input.classList.add('border-red-500');
            input.classList.remove('border-gray-300');
        } else {
            input.classList.remove('border-red-500');
            input.classList.add('border-gray-300');
        }
    }

    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                if (value.length <= 3) {
                    value = value;
                } else if (value.length <= 6) {
                    value = value.slice(0, 3) + ' ' + value.slice(3);
                } else if (value.length <= 10) {
                    value = value.slice(0, 3) + ' ' + value.slice(3, 6) + ' ' + value.slice(6);
                } else {
                    value = value.slice(0, 3) + ' ' + value.slice(3, 6) + ' ' + value.slice(6, 10);
                }
            }
            e.target.value = value;
        });
    }

    // Form submission confirmation
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate all required fields
        let isFormValid = true;
        inputs.forEach(input => {
            if (!input.checkValidity()) {
                validateInput(input);
                isFormValid = false;
            }
        });

        if (isFormValid) {
            // Show loading state
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<svg class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> {{ __("gdpr.saving") }}';
            submitButton.disabled = true;

            // Submit form
            form.submit();
        } else {
            // Scroll to first error
            const firstError = form.querySelector('.border-red-500');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });
});
</script>
@endpush
@endsection

