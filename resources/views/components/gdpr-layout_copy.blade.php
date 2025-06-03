<x-app-layout>
    {{-- Propaga gli stili delle viste GDPR --}}
    @stack('styles')

    <x-slot name="header">
        {{-- Breadcrumb Navigation --}}
        <nav class="text-sm breadcrumbs">
            <ul>
                <li><a href="{{ route('dashboard') }}">{{ __('gdpr.breadcrumb.dashboard') }}</a></li>
                <li><a href="{{ route('gdpr.consent') }}">{{ __('gdpr.breadcrumb.gdpr') }}</a></li>
                @if(isset($breadcrumbItems) && count($breadcrumbItems) > 0)
                    @foreach($breadcrumbItems as $item)
                        @if($loop->last)
                            <li>{{ $item['label'] }}</li>
                        @else
                            <li><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                        @endif
                    @endforeach
                @endif
            </ul>
        </nav>

        {{-- Page Title and Info --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $pageTitle }}
                </h2>
                @if($pageSubtitle)
                    <p class="mt-1 text-sm text-gray-600">{{ $pageSubtitle }}</p>
                @endif
            </div>

            {{-- GDPR Compliance Badge --}}
            <div class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-green-800 bg-green-100 rounded-lg">
                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001z" clip-rule="evenodd"/>
                    <path fill-rule="evenodd" d="M13.707 7.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ __('gdpr.compliance_badge') }}
            </div>
        </div>
    </x-slot>

    <div class="gdpr-layout">
        {{-- Mobile sidebar toggle --}}
        <button class="gdpr-mobile-toggle" onclick="toggleGdprSidebar()">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <div class="gdpr-container">
            {{-- Main GDPR Content Area --}}
            <div class="gdpr-main-content">
                {{-- Flash Messages & Alerts --}}
                @if(session('gdpr_success'))
                    <div class="gdpr-alert success">
                        <strong>{{ __('gdpr.alerts.success') }}</strong>
                        {{ session('gdpr_success') }}
                    </div>
                @endif

                @if(session('gdpr_error'))
                    <div class="gdpr-alert error">
                        <strong>{{ __('gdpr.alerts.error') }}</strong>
                        {{ session('gdpr_error') }}
                    </div>
                @endif

                @if(session('gdpr_warning'))
                    <div class="gdpr-alert warning">
                        <strong>{{ __('gdpr.alerts.warning') }}</strong>
                        {{ session('gdpr_warning') }}
                    </div>
                @endif

                @if(session('gdpr_info'))
                    <div class="gdpr-alert info">
                        <strong>{{ __('gdpr.alerts.info') }}</strong>
                        {{ session('gdpr_info') }}
                    </div>
                @endif

                {{-- Main Content Card --}}
                <div class="gdpr-content-card">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>

    {{-- Propaga gli script delle viste GDPR --}}
    @stack('scripts')

    @push('scripts')
        {{-- GDPR Layout JavaScript --}}
        <script>
            /**
             * Toggle mobile GDPR sidebar
             */
            function toggleGdprSidebar() {
                const sidebar = document.getElementById('gdprSidebar');
                if (sidebar) {
                    sidebar.classList.toggle('open');
                }
            }

            /**
             * Close sidebar when clicking outside on mobile
             */
            document.addEventListener('click', function(event) {
                const sidebar = document.getElementById('gdprSidebar');
                const toggle = document.querySelector('.gdpr-mobile-toggle');

                if (window.innerWidth <= 768 && sidebar && sidebar.classList.contains('open')) {
                    if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                        sidebar.classList.remove('open');
                    }
                }
            });

            /**
             * Handle window resize
             */
            window.addEventListener('resize', function() {
                const sidebar = document.getElementById('gdprSidebar');
                if (sidebar && window.innerWidth > 768) {
                    sidebar.classList.remove('open');
                }
            });

            /**
             * Initialize GDPR layout
             */
            document.addEventListener('DOMContentLoaded', function() {
                console.log('[GDPR Layout] Initialized successfully');

                // Auto-hide alerts after 5 seconds
                const alerts = document.querySelectorAll('.gdpr-alert');
                alerts.forEach(alert => {
                    setTimeout(() => {
                        alert.style.opacity = '0';
                        alert.style.transform = 'translateY(-10px)';
                        setTimeout(() => {
                            alert.remove();
                        }, 300);
                    }, 5000);
                });
            });
        </script>
    @endpush

</x-app-layout>
