@extends('layouts.gdpr')

@section('title', __('gdpr.breach_report'))

@section('content')
<div class="min-h-screen px-4 py-6 sm:px-6 lg:px-8" role="main" aria-labelledby="breach-report-title">
    <div class="max-w-4xl mx-auto">
        {{-- Header Card with ARIA --}}
        <div class="p-6 mb-8 border shadow-xl bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
            <div class="flex items-center justify-between">
                <div>
                    <h1 id="breach-report-title" class="text-3xl font-bold text-gray-900">
                        {{ __('gdpr.breach_report') }}
                    </h1>
                    <p class="mt-2 text-gray-600" id="breach-report-desc">
                        {{ __('gdpr.breach_report_description') }}
                    </p>
                </div>
                <div class="hidden sm:block" aria-hidden="true">
                    <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Emergency Notice with ARIA alert --}}
        <div class="p-6 mb-8 border border-red-300 shadow-lg bg-gradient-to-br from-red-50 to-red-100 rounded-2xl"
             role="alert"
             aria-live="polite">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-red-900">{{ __('gdpr.urgent_notice') }}</h2>
                    <p class="mt-1 text-red-700">{{ __('gdpr.breach_report_notice') }}</p>
                    <p class="mt-2 text-sm text-red-600">
                        <strong>{{ __('gdpr.emergency_contact') }}:</strong>
                        <a href="mailto:{{ config('gdpr.breach_email') }}"
                           class="underline hover:text-red-800"
                           aria-label="{{ __('gdpr.email_security_team') }}">
                            {{ config('gdpr.breach_email') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Report Form with semantic structure --}}
        <form method="POST"
              action="{{ route('gdpr.breach-report.store') }}"
              class="space-y-6"
              aria-describedby="breach-report-desc"
              novalidate>
            @csrf

            {{-- Incident Details Section --}}
            <section class="p-6 border shadow-lg bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50"
                     aria-labelledby="incident-details-heading">
                <h2 id="incident-details-heading" class="flex items-center mb-6 text-xl font-semibold text-gray-900">
                    <svg class="w-6 h-6 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('gdpr.incident_details') }}
                </h2>

                {{-- Incident Type --}}
                <div class="mb-6">
                    <label for="incident_type" class="block mb-2 text-sm font-medium text-gray-700">
                        {{ __('gdpr.incident_type') }}
                        <span class="text-red-500" aria-label="{{ __('gdpr.required_field') }}">*</span>
                    </label>
                    <select name="incident_type"
                            id="incident_type"
                            required
                            aria-required="true"
                            aria-describedby="incident-type-error"
                            class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">{{ __('gdpr.select_incident_type') }}</option>
                        <option value="unauthorized_access">{{ __('gdpr.incident_types.unauthorized_access') }}</option>
                        <option value="data_leak">{{ __('gdpr.incident_types.data_leak') }}</option>
                        <option value="phishing">{{ __('gdpr.incident_types.phishing') }}</option>
                        <option value="malware">{{ __('gdpr.incident_types.malware') }}</option>
                        <option value="physical_breach">{{ __('gdpr.incident_types.physical_breach') }}</option>
                        <option value="other">{{ __('gdpr.incident_types.other') }}</option>
                    </select>
                    @error('incident_type')
                        <p class="mt-1 text-sm text-red-600" id="incident-type-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Discovery Date/Time --}}
                <div class="grid grid-cols-1 gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <label for="discovered_date" class="block mb-2 text-sm font-medium text-gray-700">
                            {{ __('gdpr.discovered_date') }}
                            <span class="text-red-500" aria-label="{{ __('gdpr.required_field') }}">*</span>
                        </label>
                        <input type="date"
                               name="discovered_date"
                               id="discovered_date"
                               required
                               aria-required="true"
                               max="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        @error('discovered_date')
                            <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="discovered_time" class="block mb-2 text-sm font-medium text-gray-700">
                            {{ __('gdpr.discovered_time') }}
                            <span class="text-red-500" aria-label="{{ __('gdpr.required_field') }}">*</span>
                        </label>
                        <input type="time"
                               name="discovered_time"
                               id="discovered_time"
                               required
                               aria-required="true"
                               class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        @error('discovered_time')
                            <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block mb-2 text-sm font-medium text-gray-700">
                        {{ __('gdpr.incident_description') }}
                        <span class="text-red-500" aria-label="{{ __('gdpr.required_field') }}">*</span>
                    </label>
                    <textarea name="description"
                              id="description"
                              rows="6"
                              required
                              aria-required="true"
                              aria-describedby="description-help"
                              class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="{{ __('gdpr.describe_incident_detail') }}">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500" id="description-help">
                        {{ __('gdpr.include_what_when_how') }}
                    </p>
                </div>
            </section>

            {{-- Affected Data Section --}}
            <section class="p-6 border shadow-lg bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50"
                     aria-labelledby="affected-data-heading">
                <h2 id="affected-data-heading" class="flex items-center mb-6 text-xl font-semibold text-gray-900">
                    <svg class="w-6 h-6 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    {{ __('gdpr.affected_data') }}
                </h2>

                {{-- Data Categories --}}
                <fieldset>
                    <legend class="block mb-3 text-sm font-medium text-gray-700">
                        {{ __('gdpr.select_affected_data_categories') }}
                    </legend>
                    <div class="space-y-3" role="group" aria-required="true">
                        @foreach($dataCategories as $key => $category)
                        <label class="flex items-start cursor-pointer group">
                            <input type="checkbox"
                                   name="affected_data[]"
                                   value="{{ $key }}"
                                   class="w-4 h-4 mt-1 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                   aria-describedby="{{ $key }}-desc">
                            <div class="ml-3">
                                <span class="block font-medium text-gray-900 transition-colors group-hover:text-red-600">
                                    {{ $category['name'] }}
                                </span>
                                <span class="block text-sm text-gray-500" id="{{ $key }}-desc">
                                    {{ $category['description'] }}
                                </span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('affected_data')
                        <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </fieldset>

                {{-- Estimated Records --}}
                <div class="mt-6">
                    <label for="estimated_records" class="block mb-2 text-sm font-medium text-gray-700">
                        {{ __('gdpr.estimated_records_affected') }}
                    </label>
                    <input type="number"
                           name="estimated_records"
                           id="estimated_records"
                           min="0"
                           aria-describedby="records-help"
                           class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                           placeholder="{{ __('gdpr.enter_number_or_unknown') }}">
                    @error('estimated_records')
                        <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500" id="records-help">
                        {{ __('gdpr.leave_blank_if_unknown') }}
                    </p>
                </div>
            </section>

            {{-- Actions Taken Section --}}
            <section class="p-6 border shadow-lg bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50"
                     aria-labelledby="actions-taken-heading">
                <h2 id="actions-taken-heading" class="flex items-center mb-6 text-xl font-semibold text-gray-900">
                    <svg class="w-6 h-6 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('gdpr.actions_taken') }}
                </h2>

                <div>
                    <label for="actions_taken" class="block mb-2 text-sm font-medium text-gray-700">
                        {{ __('gdpr.describe_immediate_actions') }}
                    </label>
                    <textarea name="actions_taken"
                              id="actions_taken"
                              rows="4"
                              aria-describedby="actions-help"
                              class="w-full px-4 py-2 transition-all duration-200 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                              placeholder="{{ __('gdpr.list_steps_taken') }}">{{ old('actions_taken') }}</textarea>
                    @error('actions_taken')
                        <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500" id="actions-help">
                        {{ __('gdpr.include_containment_measures') }}
                    </p>
                </div>
            </section>

            {{-- Consent & Submit with live region --}}
            <div class="p-6 border shadow-lg bg-gray-50/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
                <div class="mb-6">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox"
                               name="consent_to_investigation"
                               id="consent_to_investigation"
                               required
                               aria-required="true"
                               aria-describedby="consent-desc"
                               class="w-4 h-4 mt-1 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <div class="ml-3">
                            <span class="block text-sm font-medium text-gray-900">
                                {{ __('gdpr.consent_to_investigation') }}
                            </span>
                            <span class="block text-sm text-gray-500" id="consent-desc">
                                {{ __('gdpr.consent_investigation_text') }}
                            </span>
                        </div>
                    </label>
                    @error('consent_to_investigation')
                        <p class="mt-2 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Buttons --}}
                <div class="flex flex-col justify-between gap-4 sm:flex-row">
                    <a href="{{ route('gdpr.consent') }}"
                       class="inline-flex items-center justify-center px-6 py-3 text-gray-700 transition-all duration-200 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('gdpr.cancel') }}
                    </a>

                    <button type="submit"
                            class="inline-flex items-center justify-center px-6 py-3 text-white transition-all duration-200 bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        {{ __('gdpr.submit_breach_report') }}
                    </button>
                </div>

                {{-- Status message live region --}}
                <div aria-live="polite" aria-atomic="true" class="sr-only" id="form-status"></div>
            </div>
        </form>

        {{-- Previous Reports --}}
        @if($previousReports->isNotEmpty())
        <section class="p-6 mt-8 border shadow-lg bg-gray-50/80 backdrop-blur-lg rounded-2xl border-gray-200/50"
                 aria-labelledby="previous-reports-heading">
            <h2 id="previous-reports-heading" class="mb-6 text-xl font-semibold text-gray-900">
                {{ __('gdpr.your_previous_reports') }}
            </h2>

            <div class="overflow-x-auto" role="region" aria-label="{{ __('gdpr.reports_table') }}">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('gdpr.report_date') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('gdpr.type') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('gdpr.status') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('gdpr.reference') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($previousReports as $report)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                <time datetime="{{ $report->created_at->toISOString() }}">
                                    {{ $report->created_at->format('d M Y, H:i') }}
                                </time>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                {{ __('gdpr.incident_types.' . $report->incident_type) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($report->status === 'resolved') bg-green-100 text-green-800
                                    @elseif($report->status === 'investigating') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ __('gdpr.report_status.' . $report->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                <code>{{ $report->reference_number }}</code>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation with ARIA announcements
    const form = document.querySelector('form');
    const statusRegion = document.getElementById('form-status');

    // Real-time validation
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            validateField(this);
        });
    });

    function validateField(field) {
        const isValid = field.value.trim() !== '';

        if (!isValid && field.type !== 'checkbox') {
            field.setAttribute('aria-invalid', 'true');
            field.classList.add('border-red-500');
        } else if (field.type === 'checkbox' && !field.checked) {
            field.setAttribute('aria-invalid', 'true');
        } else {
            field.setAttribute('aria-invalid', 'false');
            field.classList.remove('border-red-500');
        }
    }

    // Incident type dynamic behavior
    const incidentType = document.getElementById('incident_type');
    const description = document.getElementById('description');

    incidentType.addEventListener('change', function() {
        if (this.value === 'other') {
            description.setAttribute('placeholder', '{{ __("gdpr.please_specify_incident_type") }}');
            description.focus();
        }
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        let isValid = true;

        // Validate all required fields
        requiredFields.forEach(field => {
            validateField(field);
            if (field.getAttribute('aria-invalid') === 'true') {
                isValid = false;
            }
        });

        // Check at least one affected data category
        const dataCheckboxes = document.querySelectorAll('input[name="affected_data[]"]:checked');
        if (dataCheckboxes.length === 0) {
            isValid = false;
            statusRegion.textContent = '{{ __("gdpr.select_affected_data_required") }}';
        }

        if (!isValid) {
            statusRegion.textContent = '{{ __("gdpr.please_fix_errors") }}';
            const firstInvalid = form.querySelector('[aria-invalid="true"]');
            if (firstInvalid) {
                firstInvalid.focus();
            }
            return false;
        }

        // Show loading state
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<svg class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> {{ __("gdpr.submitting") }}';
        submitButton.disabled = true;

        statusRegion.textContent = '{{ __("gdpr.submitting_report") }}';

        // Submit form
        form.submit();
    });

    // Date/time constraints
    const dateField = document.getElementById('discovered_date');
    const today = new Date().toISOString().split('T')[0];
    dateField.setAttribute('max', today);

    // Auto-focus on first field
    document.getElementById('incident_type').focus();
});
</script>
@endpush
@endsection
