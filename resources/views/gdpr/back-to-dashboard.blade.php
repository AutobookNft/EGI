@extends('layouts.gdpr')

@section('title', __('gdpr.back_to_dashboard'))

@section('content')
<div class="flex items-center justify-center min-h-screen px-4 py-12 sm:px-6 lg:px-8" role="main" aria-labelledby="redirect-title">
    <div class="w-full max-w-md">
        {{-- Redirect Card with ARIA --}}
        <div class="p-8 text-center border shadow-xl bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
            {{-- Success Icon --}}
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 bg-green-100 rounded-full"
                 aria-hidden="true">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 13l4 4L19 7" />
                </svg>
            </div>

            {{-- Message --}}
            <h1 id="redirect-title" class="mb-4 text-2xl font-bold text-gray-900">
                {{ __('gdpr.returning_to_dashboard') }}
            </h1>

            <p class="mb-8 text-gray-600" role="status" aria-live="polite">
                {{ __('gdpr.redirect_message') }}
            </p>

            {{-- Loading Animation --}}
            <div class="flex justify-center mb-8" aria-label="{{ __('gdpr.loading') }}">
                <div class="flex space-x-2">
                    <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                    <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                    <div class="w-3 h-3 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>
            </div>

            {{-- Manual Link --}}
            <div class="text-sm">
                <p class="mb-2 text-gray-500">{{ __('gdpr.not_redirected') }}</p>
                <a href="{{ route('dashboard.index') }}"
                   class="inline-flex items-center justify-center px-6 py-3 text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    {{ __('gdpr.go_to_dashboard') }}
                </a>
            </div>

            {{-- Progress Bar (visual feedback) --}}
            <div class="w-full h-2 mt-8 overflow-hidden bg-gray-200 rounded-full"
                 role="progressbar"
                 aria-label="{{ __('gdpr.redirect_progress') }}"
                 aria-valuenow="0"
                 aria-valuemin="0"
                 aria-valuemax="100">
                <div id="progress-bar"
                     class="h-2 transition-all duration-300 ease-out bg-blue-600 rounded-full"
                     style="width: 0%"></div>
            </div>
        </div>

        {{-- Additional Actions --}}
        <div class="mt-8 text-center">
            <p class="mb-4 text-sm text-gray-600">{{ __('gdpr.while_you_wait') }}</p>

            <div class="flex flex-col justify-center gap-4 sm:flex-row">
                <a href="{{ route('gdpr.consent') }}"
                   class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    {{ __('gdpr.review_settings') }}
                </a>

                <a href="{{ route('gdpr.export-data') }}"
                   class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ __('gdpr.export_data') }}
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Success notification if coming from an action --}}
@if(session('gdpr_success'))
<div class="fixed max-w-sm p-4 border border-green-200 rounded-lg shadow-lg bottom-4 right-4 bg-green-50 no-print"
     role="alert"
     aria-live="assertive"
     aria-atomic="true"
     id="success-notification">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <svg class="w-5 h-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-green-800">
                {{ session('gdpr_success') }}
            </p>
        </div>
        <button type="button"
                onclick="document.getElementById('success-notification').remove()"
                class="flex-shrink-0 ml-auto text-green-400 hover:text-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                aria-label="{{ __('gdpr.close_notification') }}">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
@endif

@push('styles')
<style>
    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    .animate-bounce {
        animation: bounce 1.5s infinite;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const progressBar = document.getElementById('progress-bar');
    const redirectUrl = '{{ route("dashboard.index") }}';
    const redirectDelay = 5000; // 5 seconds
    const updateInterval = 50; // Update every 50ms

    let progress = 0;
    const increment = 100 / (redirectDelay / updateInterval);

    // Update progress bar
    const progressInterval = setInterval(function() {
        progress += increment;

        if (progress >= 100) {
            progress = 100;
            clearInterval(progressInterval);
        }

        progressBar.style.width = progress + '%';
        progressBar.parentElement.setAttribute('aria-valuenow', Math.round(progress));
    }, updateInterval);

    // Redirect after delay
    setTimeout(function() {
        // Announce redirect for screen readers
        const announcement = document.createElement('div');
        announcement.className = 'sr-only';
        announcement.setAttribute('role', 'status');
        announcement.setAttribute('aria-live', 'assertive');
        announcement.textContent = '{{ __("gdpr.redirecting_now") }}';
        document.body.appendChild(announcement);

        // Perform redirect
        window.location.href = redirectUrl;
    }, redirectDelay);

    // Auto-hide success notification after 10 seconds
    const notification = document.getElementById('success-notification');
    if (notification) {
        setTimeout(function() {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            notification.style.transition = 'all 0.3s ease-out';

            setTimeout(function() {
                notification.remove();
            }, 300);
        }, 10000);
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        // Press 'Enter' or 'Space' to go to dashboard immediately
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            window.location.href = redirectUrl;
        }

        // Press 'Escape' to stop redirect
        if (e.key === 'Escape') {
            clearInterval(progressInterval);
            progressBar.style.width = '0%';

            // Update message
            const title = document.getElementById('redirect-title');
            const status = document.querySelector('[role="status"]');

            title.textContent = '{{ __("gdpr.redirect_cancelled") }}';
            status.textContent = '{{ __("gdpr.use_button_below") }}';

            // Remove loading animation
            document.querySelector('.animate-bounce').parentElement.parentElement.remove();
        }
    });
});
</script>
@endpush
@endsection
