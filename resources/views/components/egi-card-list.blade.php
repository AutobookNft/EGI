{{-- resources/views/components/egi-card-list.blade.php --}}
{{-- 📜 EGI List Card Component --}}
{{-- Displays a single EGI in horizontal list format --}}
{{-- Reusable for Collector, Creator, and Patron portfolios --}}

@props([
    'egi',
    'context' => 'collector', // 'collector', 'creator', 'patron'
    'portfolioOwner' => null,
    'showPurchasePrice' => true,
    'showOwnershipBadge' => true
])

@php
// Context-specific configurations
$contextConfig = [
    'collector' => [
        'badge_color' => 'bg-green-500',
        'badge_icon' => 'check',
        'badge_title' => __('collector.portfolio.owned'),
        'show_purchase' => true,
        'show_creator' => true
    ],
    'creator' => [
        'badge_color' => 'bg-blue-500',
        'badge_icon' => 'palette',
        'badge_title' => __('creator.portfolio.created'),
        'show_purchase' => false,
        'show_creator' => false
    ],
    'patron' => [
        'badge_color' => 'bg-yellow-500',
        'badge_icon' => 'heart',
        'badge_title' => __('patron.portfolio.supported'),
        'show_purchase' => true,
        'show_creator' => true
    ]
];

$config = $contextConfig[$context] ?? $contextConfig['collector'];
@endphp

<article class="relative p-4 overflow-hidden transition-all duration-300 border border-gray-700 shadow-lg group rounded-xl bg-gradient-to-r from-gray-800 to-gray-900 hover:border-purple-500 hover:shadow-2xl hover:shadow-purple-500/20">

    <div class="flex items-center gap-4">
        <!-- Image Section -->
        <div class="relative flex-shrink-0 w-20 h-20 overflow-hidden rounded-lg bg-gradient-to-br from-gray-700 to-gray-800">
            @if ($egi->main_image_url)
            <img src="{{ $egi->main_image_url }}" alt="{{ $egi->title }}"
                class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110">
            @else
            <div class="flex items-center justify-center w-full h-full bg-gradient-to-br from-purple-600/20 to-blue-600/20">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            @endif

            <!-- Context Badge -->
            @if ($showOwnershipBadge)
            <div class="absolute -right-1 -top-1">
                <div class="flex h-6 w-6 items-center justify-center rounded-full {{ $config['badge_color'] }} ring-2 ring-gray-800"
                     title="{{ $config['badge_title'] }}">
                    @if ($config['badge_icon'] === 'check')
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    @elseif ($config['badge_icon'] === 'palette')
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4 2a2 2 0 00-2 2v11a2 2 0 002 2h4a2 2 0 002-2V4a2 2 0 00-2-2H4zm0 2h4v11H4V4zm8-2a2 2 0 00-2 2v11a2 2 0 002 2h4a2 2 0 002-2V4a2 2 0 00-2-2h-4zm0 2h4v11h-4V4z"
                            clip-rule="evenodd" />
                    </svg>
                    @elseif ($config['badge_icon'] === 'heart')
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                            clip-rule="evenodd" />
                    </svg>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Content Section -->
        <div class="flex-1 min-w-0 mr-4">
            <!-- Title -->
            <h3 class="mb-1 text-lg font-bold text-white truncate transition-colors group-hover:text-purple-300">
                <a href="{{ route('egis.show', $egi->id) }}" class="hover:underline">
                    {{ $egi->title ?? '#' . $egi->id }}
                </a>
            </h3>

            <!-- Collection and Creator Info -->
            <div class="flex flex-wrap items-center gap-4 mb-2 text-sm text-gray-400">
                @if ($egi->collection)
                <div class="flex items-center gap-1">
                    <div class="w-3 h-3 rounded-full bg-gradient-to-r from-purple-500 to-blue-500"></div>
                    <a href="{{ route('home.collections.show', $egi->collection->id) }}"
                        class="hover:text-purple-400 transition-colors truncate max-w-[120px]">
                        {{ $egi->collection->collection_name }}
                    </a>
                </div>
                @endif

                @if ($config['show_creator'] && $egi->collection && $egi->collection->creator)
                <div class="flex items-center gap-1">
                    <div class="w-3 h-3 bg-gray-600 rounded-full"></div>
                    <span class="truncate max-w-[100px]">{{ $egi->collection->creator->name }}</span>
                </div>
                @endif
            </div>

            <!-- Purchase/Context Info -->
            @if ($config['show_purchase'] && $showPurchasePrice)
                @if ($context === 'collector' && $egi->pivot && $egi->pivot->offer_amount_eur)
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-400">{{ __('collector.portfolio.purchased_for') }}</span>
                    <span class="font-bold text-green-400">€{{ number_format($egi->pivot->offer_amount_eur, 2) }}</span>
                </div>
                @elseif ($context === 'patron' && isset($egi->support_amount))
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-400">{{ __('patron.portfolio.supported_for') }}</span>
                    <span class="font-bold text-yellow-400">€{{ number_format($egi->support_amount, 2) }}</span>
                </div>
                @endif
            @endif

            @if ($context === 'creator')
                <!-- Creator-specific info (creation date, sales, etc.) -->
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-400">{{ __('creator.portfolio.created_on') }}</span>
                    <span class="font-medium text-blue-400">{{ $egi->created_at->format('M Y') }}</span>
                </div>
            @endif
        </div>

        <!-- Action Button -->
        <div class="self-center flex-shrink-0">
            <a href="{{ route('egis.show', $egi->id) }}"
                class="inline-flex items-center justify-center rounded-lg bg-purple-600 p-2.5 text-white transition-all duration-200 hover:bg-purple-700 group-hover:bg-purple-500 hover:scale-105"
                title="{{ __('common.view_egi') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 6H5.25A2.25 2.25 0 003 8.25v7.5A2.25 2.25 0 005.25 18h7.5A2.25 2.25 0 0015 15.75v-7.5A2.25 2.25 0 0013.5 6z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m15.75 9 3.75 3.75-3.75 3.75" />
                </svg>
            </a>
        </div>
    </div>
</article>
