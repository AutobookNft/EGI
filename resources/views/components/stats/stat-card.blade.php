@props([
    'title' => '',
    'value' => 0,
    'formatted_value' => null,
    'icon' => 'chart-bar',
    'color' => 'blue',
    'trend' => null, // 'up', 'down', 'stable'
    'trend_value' => null,
    'size' => 'normal', // 'small', 'normal', 'large'
    'loading' => false
])

@php
$colorClasses = [
    'blue' => 'from-blue-500 to-blue-600 border-blue-500/20',
    'green' => 'from-green-500 to-green-600 border-green-500/20',
    'purple' => 'from-purple-500 to-purple-600 border-purple-500/20',
    'orange' => 'from-orange-500 to-orange-600 border-orange-500/20',
    'red' => 'from-red-500 to-red-600 border-red-500/20',
    'gray' => 'from-gray-500 to-gray-600 border-gray-500/20',
];

$sizeClasses = [
    'small' => 'p-4',
    'normal' => 'p-6',
    'large' => 'p-8'
];

$iconSizes = [
    'small' => 'w-6 h-6',
    'normal' => 'w-8 h-8',
    'large' => 'w-10 h-10'
];

$textSizes = [
    'small' => ['title' => 'text-sm', 'value' => 'text-xl'],
    'normal' => ['title' => 'text-base', 'value' => 'text-2xl'],
    'large' => ['title' => 'text-lg', 'value' => 'text-3xl']
];

$gradient = $colorClasses[$color] ?? $colorClasses['blue'];
$padding = $sizeClasses[$size] ?? $sizeClasses['normal'];
$iconSize = $iconSizes[$size] ?? $iconSizes['normal'];
$titleClass = $textSizes[$size]['title'] ?? $textSizes['normal']['title'];
$valueClass = $textSizes[$size]['value'] ?? $textSizes['normal']['value'];
@endphp

<div class="bg-gray-800 border border-gray-700 rounded-lg {{ $padding }} transition-all duration-300 hover:border-gray-600 hover:shadow-lg">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-3">
            <div class="flex items-center justify-center {{ $iconSize }} rounded-lg bg-gradient-to-r {{ $gradient }}">
                @switch($icon)
                    @case('chart-bar')
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        @break
                    @case('currency-euro')
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                        @break
                    @case('users')
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                        @break
                    @case('collection')
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        @break
                    @case('trending-up')
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        @break
                    @case('heart')
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        @break
                    @default
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                @endswitch
            </div>
            <h3 class="{{ $titleClass }} font-medium text-gray-300">{{ $title }}</h3>
        </div>

        @if($trend)
            <div class="flex items-center space-x-1">
                @if($trend === 'up')
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                @elseif($trend === 'down')
                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                    </svg>
                @else
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                @endif
                @if($trend_value)
                    <span class="text-xs {{ $trend === 'up' ? 'text-green-400' : ($trend === 'down' ? 'text-red-400' : 'text-gray-400') }}">
                        {{ $trend_value }}
                    </span>
                @endif
            </div>
        @endif
    </div>

    <div class="space-y-1">
        @if($loading)
            <div class="animate-pulse">
                <div class="h-8 bg-gray-700 rounded w-3/4"></div>
            </div>
        @else
            <p class="{{ $valueClass }} font-bold text-white">
                {{ $formatted_value ?? ($value !== null ? number_format($value, 2) : '—') }}
            </p>
        @endif

        @if($slot->isNotEmpty())
            <div class="text-sm text-gray-400">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
