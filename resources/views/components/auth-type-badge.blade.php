{{--
@Oracode Component: Auth Type Badge (OS1-Compliant)
🎯 Purpose: Display current authentication type with visual indicator
🛡️ Privacy: Clear auth status for user awareness and GDPR compliance
🧱 Core Logic: FegiAuth integration with weak/strong authentication indicators

@props [
    'type' => string ('strong'|'weak'|'guest')
]
--}}

@props([
    'type' => 'guest'
])

@php
    $config = [
        'strong' => [
            'label' => __('auth.strong_auth'),
            'description' => __('auth.strong_auth_description'),
            'icon' => 'shield-check',
            'classes' => 'bg-green-100 text-green-800 border-green-200',
            'iconClasses' => 'text-green-600'
        ],
        'weak' => [
            'label' => __('auth.weak_auth'),
            'description' => __('auth.weak_auth_description'),
            'icon' => 'wallet',
            'classes' => 'bg-blue-100 text-blue-800 border-blue-200',
            'iconClasses' => 'text-blue-600'
        ],
        'guest' => [
            'label' => __('auth.guest'),
            'description' => __('auth.guest_description'),
            'icon' => 'user',
            'classes' => 'bg-gray-100 text-gray-800 border-gray-200',
            'iconClasses' => 'text-gray-600'
        ]
    ];

    $authConfig = $config[$type] ?? $config['guest'];
@endphp

<div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $authConfig['classes'] }}"
     title="{{ $authConfig['description'] }}">
    {{-- Auth Type Icon --}}
    <div class="flex-shrink-0 mr-2">
        @switch($authConfig['icon'])
            @case('shield-check')
                <svg class="w-4 h-4 {{ $authConfig['iconClasses'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                @break
            @case('wallet')
                <svg class="w-4 h-4 {{ $authConfig['iconClasses'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                @break
            @case('user')
            @default
                <svg class="w-4 h-4 {{ $authConfig['iconClasses'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
        @endswitch
    </div>

    {{-- Auth Type Label --}}
    <span>{{ $authConfig['label'] }}</span>

    {{-- Upgrade Indicator for Weak Auth --}}
    @if($type === 'weak')
        <a href="{{ route('account.upgrade') }}"
           class="inline-flex items-center ml-2 text-blue-600 transition-colors hover:text-blue-800"
           title="{{ __('auth.upgrade_to_strong') }}">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
        </a>
    @endif
</div>
