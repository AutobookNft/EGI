{{-- resources/views/components/collector-card-list.blade.php --}}
{{--
* @package App\View\Components
* @author AI Assistant for Fabio Cherici
* @version 1.0.0 (FlorenceEGI - Collector Card List)
* @date 2025-01-25
* @purpose List version of collector card for mobile-optimized display
--}}

@props([
'collector',
'context' => 'default',
'showOwnershipBadge' => false,
'showBadge' => null,
'rank' => null
])

@php
// Context configurations for different usage scenarios
$contextConfig = [
'default' => [
'show_stats' => true,
'show_activity' => true,
'badge_color' => 'bg-emerald-500',
'badge_icon' => 'heart',
'badge_title' => __('collector.collector_badge')
],
'leaderboard' => [
'show_stats' => true,
'show_activity' => false,
'badge_color' => 'bg-yellow-500',
'badge_icon' => 'star',
'badge_title' => __('collector.top_collector')
],
'patron' => [
'show_stats' => false,
'show_activity' => true,
'badge_color' => 'bg-gold-500',
'badge_icon' => 'crown',
'badge_title' => __('collector.patron')
]
];

$config = $contextConfig[$context] ?? $contextConfig['default'];

// Badge logic - può essere sovrascritto dal parametro showBadge
$showBadge = $showBadge ?? $showOwnershipBadge;

// Determina l'immagine da utilizzare
$logo = config('app.logo');
$imageUrl = '';

if ($collector) {
if ($collector->profile_photo_url) {
$imageUrl = $collector->profile_photo_url;
} else {
$imageUrl = asset("images/logo/$logo");
}
} else {
$imageUrl = asset("images/logo/$logo");
}

// Get collector stats if available
$stats = [];
if ($collector && method_exists($collector, 'getCollectorStats')) {
$stats = $collector->getCollectorStats();
}
@endphp

@if ($collector)
{{-- Collector Card List Component --}}
<article
    class="relative p-4 transition-all duration-300 border collector-card-list group bg-gray-800/50 rounded-xl border-gray-700/50 hover:border-gray-600 hover:bg-gray-800/70"
    data-collector-id="{{ $collector->id }}">

    <div class="flex items-start gap-4">
        <!-- Avatar Section -->
        <a href="{{ route('collector.home', ['id' => $collector->id]) }}"
            class="relative flex-shrink-0 overflow-hidden transition-all duration-300 rounded-lg cursor-pointer w-28 h-28 bg-gradient-to-br from-gray-700 to-gray-800 group-hover:ring-2 group-hover:ring-emerald-400">

            <img src="{{ $imageUrl }}" alt="{{ $collector->first_name }} {{ $collector->last_name }}"
                class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110">

            <!-- Hover overlay for visual feedback -->
            <div
                class="absolute inset-0 flex items-center justify-center transition-opacity duration-300 opacity-0 bg-emerald-400/20 group-hover:opacity-100">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 6H5.25A2.25 2.25 0 003 8.25v7.5A2.25 2.25 0 005.25 18h7.5A2.25 2.25 0 0015 15.75v-7.5A2.25 2.25 0 0013.5 6z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 9 3.75 3.75-3.75 3.75" />
                </svg>
            </div>

            <!-- Rank Badge (if provided) -->
            @if ($rank)
            <div class="absolute -left-2 -top-2">
                <div
                    class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 ring-2 ring-gray-800">
                    #{{ $rank }}
                </div>
            </div>
            @endif

            <!-- Context Badge -->
            @if ($showBadge)
            <div class="absolute -right-1 -top-1">
                <div class="flex h-6 w-6 items-center justify-center rounded-full {{ $config['badge_color'] }} ring-2 ring-gray-800"
                    title="{{ $config['badge_title'] }}">
                    @if ($config['badge_icon'] === 'heart')
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                            clip-rule="evenodd" />
                    </svg>
                    @elseif ($config['badge_icon'] === 'star')
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 15.585l-6.16 3.251a.5.5 0 01-.725-.527l1.176-6.859L.146 7.41a.5.5 0 01.277-.854l6.877-.999L10.44.69a.5.5 0 01.894 0l3.14 6.367 6.877.999a.5.5 0 01.277.854l-4.972 4.04 1.176 6.859a.5.5 0 01-.725.527L10 15.585z"
                            clip-rule="evenodd" />
                    </svg>
                    @elseif ($config['badge_icon'] === 'crown')
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    @endif
                </div>
            </div>
            @endif
        </a>

        <!-- Content Section -->
        <div class="flex-1 min-w-0 mr-4">
            <!-- Name and Username -->
            <h3 class="mb-1 text-lg font-bold text-white truncate transition-colors group-hover:text-emerald-300">
                <a href="{{ route('collector.home', ['id' => $collector->id]) }}" class="hover:underline">
                    {{ $collector->first_name }} {{ $collector->last_name }}
                </a>
            </h3>

            @if ($collector->username)
            <p class="mb-2 text-sm text-gray-400">@{{ $collector->username }}</p>
            @endif

            <!-- Bio/Description -->
            @if ($collector->bio)
            <p class="mb-2 text-sm text-gray-400 line-clamp-2">
                {{ Str::limit($collector->bio, 120) }}
            </p>
            @endif

            <!-- Stats Section - Always show basic stats -->
            <div class="flex flex-wrap items-center gap-4 mb-2 text-sm text-gray-400">
                <!-- EGI Posseduti (sempre visualizzato) -->
                @php
                $ownedCount = $collector->owned_egis_count ?? $stats['owned_egis'] ?? 0;
                @endphp
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium text-emerald-300">{{ $ownedCount }}</span>
                    <span>EGI {{ __('collector.owned') }}</span>
                </div>

                <!-- Prenotazioni Attive (sempre visualizzato) -->
                @php
                $reservationsCount = $collector->active_reservations_count ?? $stats['active_reservations'] ?? 0;
                @endphp
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium text-blue-300">{{ $reservationsCount }}</span>
                    <span>{{ __('collector.active') }}</span>
                </div>

                <!-- Total Value (se disponibile) -->
                @if (isset($stats['total_value']) && $stats['total_value'] > 0)
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium text-yellow-300">€{{ number_format($stats['total_value'], 0) }}</span>
                </div>
                @endif
            </div> <!-- Recent Activity -->
            @if ($config['show_activity'] && isset($collector->latest_activity))
            <div class="flex items-center gap-2 mb-2 text-sm">
                <div class="w-3 h-3 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500"></div>
                <span class="text-gray-400">{{ __('collector.latest_activity') }}:</span>
                <span class="text-emerald-400 truncate max-w-[120px]">
                    {{ $collector->latest_activity->type ?? __('collector.no_activity') }}
                </span>
            </div>
            @endif

            <!-- Collector Level (if available) -->
            @if (isset($collector->collector_level) || isset($stats['level']))
            @php
            $level = $collector->collector_level ?? $stats['level'] ?? 1;
            $levelColors = [
            1 => 'from-gray-500 to-gray-600',
            2 => 'from-green-500 to-green-600',
            3 => 'from-blue-500 to-blue-600',
            4 => 'from-purple-500 to-purple-600',
            5 => 'from-yellow-400 to-orange-500'
            ];
            $levelColor = $levelColors[min($level, 5)] ?? $levelColors[1];
            @endphp
            <div class="flex items-center gap-2 mb-2 text-sm">
                <svg class="w-4 h-4 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                        clip-rule="evenodd" />
                </svg>
                <span class="text-gray-400">{{ __('collector.level') }}</span>
                <span
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gradient-to-r {{ $levelColor }} text-white">
                    {{ $level }}
                </span>
            </div>
            @endif

            <!-- Collector Badge and Join Date -->
            <div class="flex items-center justify-between mt-2">
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-white rounded-full bg-gradient-to-r from-emerald-500 to-teal-500">
                        {{ __('common.collector') }}
                    </span>
                </div>
                @if ($collector->created_at)
                <div class="flex items-center gap-1 text-xs text-gray-500">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>{{ $collector->created_at->format('M Y') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</article>
@endif
