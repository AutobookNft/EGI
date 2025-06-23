@props(['policyData'])

<div {{ $attributes->merge(['class' => 'p-6 border border-blue-200 shadow-lg bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl']) }}
     role="region"
     aria-label="{{ __('gdpr.policy_version_info') }}">
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">{{ __('gdpr.current_version') }}</h2>
            <p class="mt-1 text-gray-700">
                <strong>{{ __('gdpr.version') }}:</strong> {{ $policyData->version }}
            </p>
            <p class="text-gray-700">
                <strong>{{ __('gdpr.effective_date') }}:</strong>
                <time datetime="{{ $policyData->effective_date?->toISOString() }}">
                    {{ $policyData->effective_date?->format('d M Y') }}
                </time>
            </p>
            <p class="text-gray-700">
                <strong>{{ __('gdpr.last_updated') }}:</strong>
                <time datetime="{{ $policyData->updated_at?->toISOString() }}">
                    {{ $policyData->updated_at?->format('d M Y, H:i') }}
                </time>
            </p>
        </div>

        {{-- Policy Actions --}}
        <div class="flex flex-col space-y-2 no-print">
            <a href="{{ route('gdpr.privacy-policy.download') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-700 transition-colors bg-blue-100 rounded-lg hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
               aria-label="{{ __('gdpr.download_pdf_version') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                {{ __('gdpr.download_pdf') }}
            </a>
            <button type="button" onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                    aria-label="{{ __('gdpr.print_policy') }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                {{ __('gdpr.print') }}
            </button>
        </div>
    </div>
</div>
