<x-app-layout>
    {{--
    @Oracode View: GDPR Personal Data Editor - App Layout Compatible
    🎯 Purpose: Allow users to edit their personal data with GDPR compliance
    🛡️ Privacy: Secure form for personal data modification
    🎨 Brand: FlorenceEGI design system with DaisyUI theme compatibility
    🔧 Accessibility: Full ARIA support, form validation, error handling
    🌐 i18n: Complete localization with profile.* translations

    @package FlorenceEGI
    @author Padmin D. Curtis (for Fabio Cherici)
    @version 1.0.0 - App layout compatible implementation
    @date 2025-05-25
    @seo-purpose Personal data editing interface for authenticated users
    @accessibility-trait Form validation, ARIA labels, semantic structure
    --}}

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {{ __('profile.edit_personal_data') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('profile.edit_personal_data_description') }}
                </p>
            </div>
            <div class="hidden sm:block">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
        </div>
    </x-slot>

    {{-- Main Content Container --}}
    <div class="py-6">
        <div class="max-w-4xl px-4 mx-auto sm:px-6 lg:px-8">

            {{-- Success/Error Messages --}}
            @if (session('success'))
                <div class="mb-6 alert alert-success" role="alert" aria-live="polite">
                    <svg class="w-6 h-6 stroke-current shrink-0" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 alert alert-error" role="alert" aria-live="assertive">
                    <svg class="w-6 h-6 stroke-current shrink-0" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Main Form --}}
            <form method="POST" action="{{ route('gdpr.edit-personal-data.update') }}"
                  class="space-y-6" id="edit-personal-data-form" novalidate>
                @csrf
                @method('PUT')

                {{-- Basic Information Section --}}
                <div class="shadow-xl card bg-base-100">
                    <div class="card-body">
                        <h2 class="flex items-center mb-6 text-2xl card-title">
                            <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{ __('profile.basic_information') }}
                        </h2>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            {{-- Name Field --}}
                            <div class="form-control">
                                <label class="label" for="name">
                                    <span class="font-medium label-text">
                                        {{ __('profile.name') }}
                                        <span class="text-error" aria-label="{{ __('profile.required_field') }}">*</span>
                                    </span>
                                </label>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       value="{{ old('name', $user->name) }}"
                                       class="input input-bordered w-full @error('name') input-error @enderror"
                                       required
                                       aria-describedby="name-error"
                                       autocomplete="name">
                                @error('name')
                                    <label class="label">
                                        <span class="label-text-alt text-error" id="name-error" role="alert">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            {{-- Email Field --}}
                            <div class="form-control">
                                <label class="label" for="email">
                                    <span class="font-medium label-text">
                                        {{ __('profile.email') }}
                                        <span class="text-error" aria-label="{{ __('profile.required_field') }}">*</span>
                                    </span>
                                </label>
                                <input type="email"
                                       name="email"
                                       id="email"
                                       value="{{ old('email', $user->email) }}"
                                       class="input input-bordered w-full @error('email') input-error @enderror"
                                       required
                                       aria-describedby="email-error"
                                       autocomplete="email">
                                @error('email')
                                    <label class="label">
                                        <span class="label-text-alt text-error" id="email-error" role="alert">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            {{-- Phone Field --}}
                            <div class="form-control">
                                <label class="label" for="phone">
                                    <span class="font-medium label-text">{{ __('profile.phone') }}</span>
                                </label>
                                <input type="tel"
                                       name="phone"
                                       id="phone"
                                       value="{{ old('phone', $user->phone) }}"
                                       class="input input-bordered w-full @error('phone') input-error @enderror"
                                       aria-describedby="phone-error phone-help"
                                       autocomplete="tel">
                                <label class="label">
                                    <span class="label-text-alt" id="phone-help">{{ __('profile.phone_help') }}</span>
                                </label>
                                @error('phone')
                                    <label class="label">
                                        <span class="label-text-alt text-error" id="phone-error" role="alert">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            {{-- Date of Birth Field --}}
                            <div class="form-control">
                                <label class="label" for="date_of_birth">
                                    <span class="font-medium label-text">{{ __('profile.date_of_birth') }}</span>
                                </label>
                                <input type="date"
                                       name="date_of_birth"
                                       id="date_of_birth"
                                       value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                                       class="input input-bordered w-full @error('date_of_birth') input-error @enderror"
                                       aria-describedby="date_of_birth-error"
                                       autocomplete="bday">
                                @error('date_of_birth')
                                    <label class="label">
                                        <span class="label-text-alt text-error" id="date_of_birth-error" role="alert">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Address Information Section --}}
                <div class="shadow-xl card bg-base-100">
                    <div class="card-body">
                        <h2 class="flex items-center mb-6 text-2xl card-title">
                            <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ __('profile.address_information') }}
                        </h2>

                        <div class="grid grid-cols-1 gap-6">
                            {{-- Street Address --}}
                            <div class="form-control">
                                <label class="label" for="address">
                                    <span class="font-medium label-text">{{ __('profile.street_address') }}</span>
                                </label>
                                <input type="text"
                                       name="address"
                                       id="address"
                                       value="{{ old('address', $user->address) }}"
                                       class="input input-bordered w-full @error('address') input-error @enderror"
                                       aria-describedby="address-error"
                                       autocomplete="street-address">
                                @error('address')
                                    <label class="label">
                                        <span class="label-text-alt text-error" id="address-error" role="alert">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                                {{-- City --}}
                                <div class="form-control">
                                    <label class="label" for="city">
                                        <span class="font-medium label-text">{{ __('profile.city') }}</span>
                                    </label>
                                    <input type="text"
                                           name="city"
                                           id="city"
                                           value="{{ old('city', $user->city) }}"
                                           class="input input-bordered w-full @error('city') input-error @enderror"
                                           aria-describedby="city-error"
                                           autocomplete="address-level2">
                                    @error('city')
                                        <label class="label">
                                            <span class="label-text-alt text-error" id="city-error" role="alert">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                {{-- State/Province --}}
                                <div class="form-control">
                                    <label class="label" for="state">
                                        <span class="font-medium label-text">{{ __('profile.state') }}</span>
                                    </label>
                                    <input type="text"
                                           name="state"
                                           id="state"
                                           value="{{ old('state', $user->state) }}"
                                           class="input input-bordered w-full @error('state') input-error @enderror"
                                           aria-describedby="state-error"
                                           autocomplete="address-level1">
                                    @error('state')
                                        <label class="label">
                                            <span class="label-text-alt text-error" id="state-error" role="alert">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>

                                {{-- Postal Code --}}
                                <div class="form-control">
                                    <label class="label" for="postal_code">
                                        <span class="font-medium label-text">{{ __('profile.postal_code') }}</span>
                                    </label>
                                    <input type="text"
                                           name="postal_code"
                                           id="postal_code"
                                           value="{{ old('postal_code', $user->postal_code) }}"
                                           class="input input-bordered w-full @error('postal_code') input-error @enderror"
                                           aria-describedby="postal_code-error"
                                           autocomplete="postal-code">
                                    @error('postal_code')
                                        <label class="label">
                                            <span class="label-text-alt text-error" id="postal_code-error" role="alert">{{ $message }}</span>
                                        </label>
                                    @enderror
                                </div>
                            </div>

                            {{-- Country --}}
                            <div class="form-control">
                                <label class="label" for="country">
                                    <span class="font-medium label-text">{{ __('profile.country') }}</span>
                                </label>
                                <select name="country"
                                        id="country"
                                        class="select select-bordered w-full @error('country') select-error @enderror"
                                        aria-describedby="country-error"
                                        autocomplete="country">
                                    <option value="">{{ __('profile.select_country') }}</option>
                                    @foreach($countries as $code => $name)
                                        <option value="{{ $code }}" {{ old('country', $user->country) == $code ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country')
                                    <label class="label">
                                        <span class="label-text-alt text-error" id="country-error" role="alert">{{ $message }}</span>
                                    </label>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Profile Information Section --}}
                <div class="shadow-xl card bg-base-100">
                    <div class="card-body">
                        <h2 class="flex items-center mb-6 text-2xl card-title">
                            <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ __('profile.profile_information') }}
                        </h2>

                        {{-- Bio --}}
                        <div class="form-control">
                            <label class="label" for="bio">
                                <span class="font-medium label-text">{{ __('profile.bio') }}</span>
                            </label>
                            <textarea name="bio"
                                      id="bio"
                                      rows="4"
                                      class="textarea textarea-bordered w-full @error('bio') textarea-error @enderror"
                                      placeholder="{{ __('profile.bio_placeholder') }}"
                                      aria-describedby="bio-error bio-help"
                                      maxlength="500">{{ old('bio', $user->bio) }}</textarea>
                            <label class="label">
                                <span class="label-text-alt" id="bio-help">{{ __('profile.bio_help') }}</span>
                                <span class="label-text-alt">
                                    <span id="bio-count">{{ strlen(old('bio', $user->bio ?? '')) }}</span>/500
                                </span>
                            </label>
                            @error('bio')
                                <label class="label">
                                    <span class="label-text-alt text-error" id="bio-error" role="alert">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Audit Information --}}
                <div class="shadow-lg card bg-base-200">
                    <div class="card-body">
                        <h3 class="mb-4 text-lg font-semibold text-base-content/70">{{ __('profile.data_information') }}</h3>
                        <div class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                            <div class="stat">
                                <div class="stat-title">{{ __('profile.last_updated') }}</div>
                                <div class="text-sm stat-value">{{ $user->updated_at->format('d M Y, H:i') }}</div>
                            </div>
                            <div class="stat">
                                <div class="stat-title">{{ __('profile.account_created') }}</div>
                                <div class="text-sm stat-value">{{ $user->created_at->format('d M Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col gap-4 sm:flex-row sm:justify-between">
                    <a href="{{ route('profile.show') }}"
                       class="btn btn-outline btn-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('profile.cancel') }}
                    </a>

                    <button type="submit"
                            class="btn btn-primary btn-lg"
                            id="submit-button">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 13l4 4L19 7" />
                        </svg>
                        {{ __('profile.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Enhanced Form Validation & UX JavaScript --}}
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('edit-personal-data-form');
        const submitButton = document.getElementById('submit-button');
        const bioTextarea = document.getElementById('bio');
        const bioCount = document.getElementById('bio-count');

        // Bio character counter
        if (bioTextarea && bioCount) {
            bioTextarea.addEventListener('input', function() {
                const count = this.value.length;
                bioCount.textContent = count;

                // Visual feedback for character limit
                if (count > 450) {
                    bioCount.parentElement.classList.add('text-warning');
                } else {
                    bioCount.parentElement.classList.remove('text-warning');
                }

                if (count >= 500) {
                    bioCount.parentElement.classList.add('text-error');
                    bioCount.parentElement.classList.remove('text-warning');
                } else {
                    bioCount.parentElement.classList.remove('text-error');
                }
            });
        }

        // Real-time validation for required fields
        const requiredInputs = form.querySelectorAll('input[required]');
        requiredInputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });

            input.addEventListener('input', function() {
                // Clear error state when user starts typing
                if (this.classList.contains('input-error')) {
                    validateField(this);
                }
            });
        });

        // Email validation
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('blur', function() {
                if (this.value && !isValidEmail(this.value)) {
                    this.classList.add('input-error');
                    showFieldError(this, '{{ __("profile.invalid_email") }}');
                } else {
                    clearFieldError(this);
                }
            });
        }

        // Phone number formatting
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                // Simple phone formatting (you can customize this based on locale)
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

        // Form submission with enhanced feedback
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate all required fields
            let isFormValid = true;
            requiredInputs.forEach(input => {
                if (!validateField(input)) {
                    isFormValid = false;
                }
            });

            // Email specific validation
            if (emailInput && emailInput.value && !isValidEmail(emailInput.value)) {
                isFormValid = false;
                emailInput.classList.add('input-error');
                showFieldError(emailInput, '{{ __("profile.invalid_email") }}');
            }

            if (isFormValid) {
                // Show loading state
                const originalHTML = submitButton.innerHTML;
                submitButton.innerHTML = `
                    <span class="loading loading-spinner loading-sm"></span>
                    {{ __('profile.saving') }}
                `;
                submitButton.disabled = true;

                // Submit form
                form.submit();
            } else {
                // Scroll to first error
                const firstError = form.querySelector('.input-error, .select-error, .textarea-error');
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstError.focus();
                }

                // Show toast notification for validation errors
                if (window.showToast) {
                    window.showToast('{{ __("profile.validation_errors") }}', 'error');
                }
            }
        });

        // Helper functions
        function validateField(field) {
            if (field.hasAttribute('required') && !field.value.trim()) {
                field.classList.add('input-error');
                showFieldError(field, '{{ __("profile.field_required") }}');
                return false;
            } else {
                field.classList.remove('input-error');
                clearFieldError(field);
                return true;
            }
        }

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function showFieldError(field, message) {
            const errorId = field.id + '-error';
            let errorElement = document.getElementById(errorId);

            if (!errorElement) {
                errorElement = document.createElement('span');
                errorElement.id = errorId;
                errorElement.className = 'label-text-alt text-error';
                errorElement.setAttribute('role', 'alert');

                const label = field.closest('.form-control').querySelector('.label:last-of-type') ||
                             field.closest('.form-control').appendChild(document.createElement('label'));
                label.className = 'label';
                label.appendChild(errorElement);
            }

            errorElement.textContent = message;
        }

        function clearFieldError(field) {
            const errorId = field.id + '-error';
            const errorElement = document.getElementById(errorId);
            if (errorElement) {
                errorElement.remove();
            }
        }

        // Auto-save indicator (optional enhancement)
        let saveTimeout;
        const autoSaveInputs = form.querySelectorAll('input, textarea, select');
        autoSaveInputs.forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(saveTimeout);

                // Show "unsaved changes" indicator
                if (submitButton) {
                    submitButton.classList.add('btn-warning');
                    submitButton.textContent = '{{ __("profile.unsaved_changes") }}';
                }

                // Clear indicator after user stops typing
                saveTimeout = setTimeout(() => {
                    if (submitButton) {
                        submitButton.classList.remove('btn-warning');
                        submitButton.innerHTML = `
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('profile.save_changes') }}
                        `;
                    }
                }, 2000);
            });
        });
    });
    </script>
    @endpush
</x-app-layout>
