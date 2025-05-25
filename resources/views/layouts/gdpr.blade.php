{{-- resources/views/layouts/gdpr.blade.php --}}
@extends('layouts.app')

@section('title', $pageTitle ?? __('gdpr.title'))

@push('styles')
    {{-- GDPR-specific styles --}}
    <style>
        .gdpr-layout {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .gdpr-container {
            display: flex;
            min-height: 100vh;
        }

        .gdpr-sidebar {
            width: 280px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.1);
        }

        .gdpr-main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        .gdpr-header {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .gdpr-content-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            min-height: 400px;
        }

        .gdpr-page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .gdpr-page-subtitle {
            font-size: 1.1rem;
            color: #718096;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .gdpr-breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: #718096;
        }

        .gdpr-breadcrumb a {
            color: #667eea;
            text-decoration: none;
            transition: color 0.2s;
        }

        .gdpr-breadcrumb a:hover {
            color: #764ba2;
        }

        .gdpr-breadcrumb-separator {
            color: #cbd5e0;
        }

        .gdpr-alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            border-left: 4px solid;
            backdrop-filter: blur(10px);
        }

        .gdpr-alert.info {
            background: rgba(59, 130, 246, 0.1);
            border-color: #3b82f6;
            color: #1e40af;
        }

        .gdpr-alert.warning {
            background: rgba(245, 158, 11, 0.1);
            border-color: #f59e0b;
            color: #92400e;
        }

        .gdpr-alert.success {
            background: rgba(16, 185, 129, 0.1);
            border-color: #10b981;
            color: #065f46;
        }

        .gdpr-alert.error {
            background: rgba(239, 68, 68, 0.1);
            border-color: #ef4444;
            color: #991b1b;
        }

        .gdpr-compliance-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(16, 185, 129, 0.1);
            color: #065f46;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .gdpr-compliance-icon {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }

        .gdpr-mobile-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        @media (max-width: 768px) {
            .gdpr-container {
                flex-direction: column;
            }

            .gdpr-sidebar {
                width: 100%;
                height: auto;
                position: fixed;
                top: 0;
                left: -100%;
                z-index: 999;
                transition: left 0.3s ease;
                height: 100vh;
                overflow-y: auto;
            }

            .gdpr-sidebar.open {
                left: 0;
            }

            .gdpr-main-content {
                padding: 1rem;
                margin-top: 4rem;
            }

            .gdpr-mobile-toggle {
                display: block;
            }

            .gdpr-page-title {
                font-size: 2rem;
            }

            .gdpr-header,
            .gdpr-content-card {
                padding: 1.5rem;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .gdpr-header,
            .gdpr-content-card {
                background: rgba(26, 32, 44, 0.95);
                color: #e2e8f0;
            }

            .gdpr-page-title {
                color: #e2e8f0;
            }

            .gdpr-page-subtitle {
                color: #a0aec0;
            }
        }
    </style>
@endpush

@section('content')
<div class="gdpr-layout">
    {{-- Mobile sidebar toggle --}}
    <button class="gdpr-mobile-toggle" onclick="toggleGdprSidebar()">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <div class="gdpr-container">
        {{-- GDPR Sidebar using existing sidebar system --}}
        <div class="gdpr-sidebar" id="gdprSidebar">
            <livewire:sidebar :context="'gdpr'" :currentRoute="Route::currentRouteName()" />
        </div>

        {{-- Main GDPR Content Area --}}
        <div class="gdpr-main-content">
            {{-- GDPR Header with breadcrumb and page info --}}
            <div class="gdpr-header">
                {{-- Breadcrumb Navigation --}}
                <nav class="gdpr-breadcrumb">
                    <a href="{{ route('dashboard') }}">{{ __('gdpr.breadcrumb.dashboard') }}</a>
                    <span class="gdpr-breadcrumb-separator">›</span>
                    <a href="{{ route('gdpr.consent') }}">{{ __('gdpr.breadcrumb.gdpr') }}</a>
                    @if(isset($breadcrumbItems) && count($breadcrumbItems) > 0)
                        @foreach($breadcrumbItems as $item)
                            <span class="gdpr-breadcrumb-separator">›</span>
                            @if($loop->last)
                                <span>{{ $item['label'] }}</span>
                            @else
                                <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                            @endif
                        @endforeach
                    @endif
                </nav>

                {{-- Page Title and Info --}}
                <h1 class="gdpr-page-title">
                    @yield('page-title', $pageTitle ?? __('gdpr.default_title'))
                </h1>

                @if($pageSubtitle ?? false)
                    <p class="gdpr-page-subtitle">{{ $pageSubtitle }}</p>
                @endif

                {{-- GDPR Compliance Badge --}}
                <div class="gdpr-compliance-badge">
                    <svg class="gdpr-compliance-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001z" clip-rule="evenodd"/>
                        <path fill-rule="evenodd" d="M13.707 7.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('gdpr.compliance_badge') }}
                </div>
            </div>

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
                @yield('gdpr-content')
            </div>
        </div>
    </div>
</div>
@endsection

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
