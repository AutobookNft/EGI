{{--
@Oracode Component: Loading Spinner (OS1-Compliant)
🎯 Purpose: Consistent loading indicator across Personal Data Domain
🛡️ Privacy: Accessible loading state for all users
🧱 Core Logic: CSS-only spinner with accessibility support

@props [
    'size' => string ('sm'|'md'|'lg'),
    'color' => string ('indigo'|'blue'|'green'|'gray'),
    'text' => string|null
]
--}}

@props([
    'size' => 'md',
    'color' => 'indigo',
    'text' => null
])

@php
    $sizeClasses = [
        'sm' => 'w-4 h-4',
        'md' => 'w-6 h-6',
        'lg' => 'w-8 h-8',
        'xl' => 'w-12 h-12'
    ];

    $colorClasses = [
        'indigo' => 'text-indigo-600',
        'blue' => 'text-blue-600',
        'green' => 'text-green-600',
        'gray' => 'text-gray-600',
        'white' => 'text-white'
    ];

    $spinnerSize = $sizeClasses[$size] ?? $sizeClasses['md'];
    $spinnerColor = $colorClasses[$color] ?? $colorClasses['indigo'];
@endphp

<div class="inline-flex items-center" role="status" aria-label="{{ $text ?: __('common.loading') }}">
    {{-- Spinner SVG --}}
    <svg class="animate-spin {{ $spinnerSize }} {{ $spinnerColor }}"
         fill="none"
         viewBox="0 0 24 24"
         aria-hidden="true">
        <circle class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"></circle>
        <path class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>

    {{-- Loading Text --}}
    @if($text)
        <span class="ml-2 text-sm text-gray-700">{{ $text }}</span>
    @endif

    {{-- Screen Reader Text --}}
    <span class="sr-only">{{ $text ?: __('common.loading') }}</span>
</div>

{{-- Spinner Animation Styles --}}
<style>
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Respect user's motion preferences */
    @media (prefers-reduced-motion: reduce) {
        .animate-spin {
            animation: none;
        }

        .animate-spin::after {
            content: "⏳";
            font-size: inherit;
            color: inherit;
        }
    }
</style>
