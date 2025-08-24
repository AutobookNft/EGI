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

                {{-- Notice: Basic and Address Information managed by main Personal Data form --}}
                <div class="p-4 mb-6 rounded-lg alert alert-info">
                    <svg class="w-6 h-6 stroke-current shrink-0" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="font-bold">{{ __('profile.main_form_notice') }}</h3>
                        <div class="text-xs">
                            {{ __('profile.main_form_description') }}
                            <a href="{{ route('user.domains.personal-data.index') }}" class="link link-primary">
                                {{ __('profile.go_to_main_form') }}
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Profile Bio Section (Unique to this form) --}}
                <div class="shadow-xl card bg-base-100">
                    <div class="card-body">
                        <h2 class="flex items-center mb-6 text-2xl card-title">
                            <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            {{ __('profile.bio_section') }}
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

    {{-- Simplified Form Validation & UX JavaScript --}}
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

        // Form submission with enhanced feedback
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Show loading state
            const originalHTML = submitButton.innerHTML;
            submitButton.innerHTML = `
                <span class="loading loading-spinner loading-sm"></span>
                {{ __('profile.saving') }}
            `;
            submitButton.disabled = true;

            // Submit form
            form.submit();
        });

        // Auto-save indicator for bio changes
        let saveTimeout;
        if (bioTextarea) {
            bioTextarea.addEventListener('input', function() {
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
        }
    });
    </script>
    @endpush
</x-app-layout>
