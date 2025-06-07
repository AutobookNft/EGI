{{--
@Oracode Component: GDPR Notice (OS1-Compliant)
🎯 Purpose: GDPR compliance notice and transparency information
🛡️ Privacy: Data subject rights information and processor transparency
🧱 Core Logic: Dynamic notice based on consent status and data processing

@props [
    'gdprSummary' => array
]
--}}

@props([
    'gdprSummary'
])

<div class="p-4 mb-6 border border-blue-200 rounded-lg bg-blue-50">
    <div class="flex items-start space-x-3">
        {{-- GDPR Icon --}}
        <div class="flex-shrink-0">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>

        <div class="flex-1 min-w-0">
            <h4 class="mb-2 text-sm font-medium text-blue-900">
                {{ __('user_personal_data.gdpr_notices.data_processing_info') }}
            </h4>

            <div class="space-y-2 text-xs text-blue-800">
                <p>
                    <strong>{{ __('user_personal_data.gdpr_notices.data_controller') }}</strong>
                </p>
                <p>
                    {{ __('user_personal_data.gdpr_notices.data_purpose') }}
                </p>
                <p>
                    {{ __('user_personal_data.gdpr_notices.data_retention') }}
                </p>
            </div>

            {{-- Consent Status Indicator --}}
            <div class="flex items-center mt-3 space-x-2">
                @if($gdprSummary['consent_status'])
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        {{ __('user_personal_data.consent_given') }}
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ __('user_personal_data.consent_review_required') }}
                    </span>
                @endif

                @if($gdprSummary['last_data_update'])
                    <span class="text-xs text-blue-600">
                        {{ __('user_personal_data.last_updated') }}: {{ $gdprSummary['last_data_update']->format('d/m/Y H:i') }}
                    </span>
                @endif
            </div>

            {{-- Data Rights Links --}}
            <div class="pt-2 mt-3 border-t border-blue-200">
                <p class="mb-1 text-xs text-blue-700">
                    <strong>{{ __('user_personal_data.gdpr_notices.data_rights') }}</strong>
                </p>
                <div class="flex flex-wrap gap-2 text-xs">
                    <a href="{{ route('gdpr.export-data') }}" class="text-blue-600 underline hover:text-blue-800">
                        {{ __('user_personal_data.export_data') }}
                    </a>
                    <span class="text-blue-400">•</span>
                    <a href="{{ route('gdpr.consent') }}" class="text-blue-600 underline hover:text-blue-800">
                        {{ __('user_personal_data.manage_consent') }}
                    </a>
                    <span class="text-blue-400">•</span>
                    <a href="{{ route('gdpr.delete-account') }}" class="text-blue-600 underline hover:text-blue-800">
                        {{ __('user_personal_data.delete_account') }}
                    </a>
                </div>
            </div>

            {{-- Contact DPO --}}
            <div class="pt-2 mt-2 border-t border-blue-200">
                <p class="text-xs text-blue-700">
                    {{ __('user_personal_data.gdpr_notices.data_contact') }}
                </p>
            </div>
        </div>
    </div>
</div>
