{{-- resources/views/components/homepage-egi-list.blade.php --}}
{{--
* @package App\View\Components
* @author AI Assistant for Fabio Cherici
* @version 1.0.0 (FlorenceEGI - List Mode with Content Selector)
* @date 2025-01-18
* @purpose List view with content type selector for mobile
--}}

@props([
'egis' => collect(),
'creators' => collect(),
'collections' => collect(),
'collectors' => collect()
])

@php
// 🧮 Contatori dinamici per il database
$creatorsCount = \App\Models\User::where('usertype', 'creator')->count();
$collectionsCount = \App\Models\Collection::count();
$egisCount = \App\Models\Egi::count();

// 🎯 Attivatori: User che hanno attualmente almeno 1 EGI con miglior offerta attiva
$activatorsCount = \DB::table('users')
->join('reservations', 'users.id', '=', 'reservations.user_id')
->where('reservations.is_current', true)
->where('reservations.status', 'active')
->whereNull('reservations.superseded_by_id')
->distinct('users.id')
->count('users.id');
@endphp

<section class="py-8 bg-gradient-to-br from-gray-900 via-gray-800 to-black">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

        {{-- Header Section --}}
        <div class="mb-8 text-center">
            <h2 class="mb-3 text-2xl font-bold text-white md:text-3xl">
                📋 <span class="text-transparent bg-gradient-to-r from-purple-400 to-blue-500 bg-clip-text">
                    {{ __('egi.list.title') }}
                </span>
            </h2>
            <p class="max-w-2xl mx-auto text-gray-300">
                {{ __('egi.list.subtitle') }}
            </p>
        </div>

        {{-- Content Type Selector --}}
        <div class="flex justify-center mb-6">
            <div class="flex flex-wrap gap-1 p-1 bg-gray-800 border border-gray-700 rounded-lg">
                {{-- EGI List Button --}}
                <button
                    class="px-3 py-2 text-xs font-medium transition-all duration-200 rounded content-type-btn active"
                    data-content="egi-list" aria-label="{{ __('egi.list.content_types.egi_list') }}">
                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 4h18v2H3V4zm0 7h18v2H3v-2zm0 7h18v2H3v-2z" />
                    </svg>
                    <span class="hidden sm:inline">EGI</span>
                </button>

                {{-- Creator Button --}}
                <button class="px-3 py-2 text-xs font-medium transition-all duration-200 rounded content-type-btn"
                    data-content="creator" aria-label="{{ __('egi.list.content_types.creators') }}">
                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('egi.list.creators') }}</span>
                </button>

                {{-- Collection Button --}}
                <button class="px-3 py-2 text-xs font-medium transition-all duration-200 rounded content-type-btn"
                    data-content="collection" aria-label="{{ __('egi.list.content_types.collections') }}">
                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-4h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zm-3-3h2c.55 0 1-.45 1-1s-.45-1-1-1H8c-.55 0-1 .45-1 1s.45 1 1 1zm6 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zm-3-3h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1z" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('egi.list.collections') }}</span>
                </button>

                {{-- Collector Button --}}
                <button class="px-3 py-2 text-xs font-medium transition-all duration-200 rounded content-type-btn"
                    data-content="collector" aria-label="{{ __('egi.list.content_types.collectors') }}">
                    <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span class="hidden sm:inline">{{ __('egi.list.collectors') }}</span>
                </button>
            </div>
        </div>

        {{-- Dynamic Content Header --}}
        <div class="mb-6 text-center">
            @php
            // 🔗 Mapping route per ogni tipo di contenuto
            $routeMapping = [
                'egi-list' => null,
                'creator' => route('creator.index'),
                'collection' => route('collections.index'),
                'collector' => route('collector.index')
            ];

            // 📊 Mapping contatori per ogni tipo
            $countMapping = [
                'egi-list' => $egisCount,
                'creator' => $creatorsCount,
                'collection' => $collectionsCount,
                'collector' => $activatorsCount
            ];
            @endphp

            <div id="content-type-header-container" class="inline-flex items-center gap-2">
                <h3 id="content-type-header"
                    class="text-xl font-bold text-white transition-all duration-300 cursor-pointer hover:text-purple-300"
                    data-route="" onclick="navigateToContent(this)">
                    {{ __('egi.list.headers.egi_list') }}
                </h3>
                <span id="content-type-count"
                    class="inline-flex items-center px-2 py-1 text-sm font-medium text-purple-300 border rounded-full bg-purple-900/30 border-purple-500/20">
                    {{ $egisCount }}
                </span>
            </div>
        </div>

        {{-- Content Container --}}
        <div class="relative">
            {{-- EGI List --}}
            <div class="grid grid-cols-1 gap-4 list-content content-egi-list" data-content="egi-list">
                @if($egis->count() > 0)
                    <div class="space-y-3">
                        @foreach($egis as $egi)
                            <x-egi-card-list :egi="$egi" context="creator" :showPurchasePrice="false" :showOwnershipBadge="false" />
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center text-gray-400">
                        {{ __('egi.list.empty_state.no_egis') }}
                    </div>
                @endif
            </div>

            {{-- Creator List --}}
            <div class="hidden grid-cols-1 gap-4 list-content content-creator" data-content="creator">
                @if($creators->count() > 0)
                    <div class="space-y-3">
                        @foreach($creators as $creator)
                            <x-creator-card-list :creator="$creator" :context="'list'" :showBadge="true" />
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center text-gray-400">
                        {{ __('egi.list.empty_state.no_creators') }}
                    </div>
                @endif
            </div>

            {{-- Collection List --}}
            <div class="hidden grid-cols-1 gap-4 list-content content-collection" data-content="collection">
                @if($collections->count() > 0)
                    <div class="space-y-3">
                        @foreach($collections as $collection)
                            <x-collection-card-list :collection="$collection" :context="'list'" :showBadge="true" />
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center text-gray-400">
                        {{ __('egi.list.empty_state.no_collections') }}
                    </div>
                @endif
            </div>

            {{-- Collector List --}}
            <div class="hidden grid-cols-1 gap-4 list-content content-collector" data-content="collector">
                @if($collectors->count() > 0)
                    <div class="space-y-3">
                        @foreach($collectors as $collector)
                            <x-collector-card-list :collector="$collector" :context="'list'" :showBadge="true" />
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center text-gray-400">
                        {{ __('egi.list.empty_state.no_collectors') }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Empty State --}}
        @if($egis->count() === 0 && $creators->count() === 0 && $collections->count() === 0 && $collectors->count() === 0)
        <div class="py-12 text-center">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 bg-gray-700 rounded-full">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="mb-2 text-lg font-semibold text-white">{{ __('egi.list.empty_state.title') }}</h3>
            <p class="max-w-md mx-auto text-gray-400">
                {{ __('egi.list.empty_state.subtitle') }}
            </p>
        </div>
        @endif
    </div>
</section>

{{-- List Content Switcher JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentTypeBtns = document.querySelectorAll('.content-type-btn');
    const listContents = document.querySelectorAll('.list-content');
    const contentHeader = document.getElementById('content-type-header');
    const contentCount = document.getElementById('content-type-count');

    let currentContentType = 'egi-list';

    // Header text mapping
    const headerTexts = {
        'egi-list': '{{ __('egi.list.headers.egi_list') }}',
        'creator': '{{ __('egi.list.headers.creators') }}',
        'collection': '{{ __('egi.list.headers.collections') }}',
        'collector': '{{ __('egi.list.headers.collectors') }}'
    };

    // Route mapping
    const routeMapping = {
        'egi-list': null,
        'creator': '{{ route('creator.index') }}',
        'collection': '{{ route('collections.index') }}',
        'collector': '{{ route('collector.index') }}'
    };

    // Count mapping
    const countMapping = {
        'egi-list': {{ $egisCount }},
        'creator': {{ $creatorsCount }},
        'collection': {{ $collectionsCount }},
        'collector': {{ $activatorsCount }}
    };

    // Show content function
    function showContent(contentType) {
        // Hide all content containers
        listContents.forEach(content => {
            content.classList.add('hidden');
            content.classList.remove('grid');
        });

        // Show selected content
        const targetContent = document.querySelector(`.content-${contentType}`);
        if (targetContent) {
            targetContent.classList.remove('hidden');
            targetContent.classList.add('grid');
        }
    }

    // Content Type Button Logic
    contentTypeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            currentContentType = this.dataset.content;

            // Update active button
            contentTypeBtns.forEach(b => {
                b.classList.remove('active', 'bg-purple-600', 'text-white');
                b.classList.add('text-gray-400');
            });
            this.classList.add('active', 'bg-purple-600', 'text-white');
            this.classList.remove('text-gray-400');

            // Update header
            updateHeader(currentContentType);

            // Show content
            showContent(currentContentType);
        });
    });

    // Update header function
    function updateHeader(contentType) {
        if (contentHeader && contentCount) {
            contentHeader.style.opacity = '0.5';
            contentCount.style.opacity = '0.5';

            setTimeout(() => {
                contentHeader.textContent = headerTexts[contentType] || headerTexts['egi-list'];
                contentCount.textContent = countMapping[contentType] || 0;

                const route = routeMapping[contentType];
                contentHeader.setAttribute('data-route', route || '');

                if (route) {
                    contentHeader.classList.add('cursor-pointer', 'hover:text-purple-300');
                    contentHeader.classList.remove('cursor-default');
                } else {
                    contentHeader.classList.add('cursor-default');
                    contentHeader.classList.remove('cursor-pointer', 'hover:text-purple-300');
                }

                contentHeader.style.opacity = '1';
                contentCount.style.opacity = '1';
            }, 150);
        }
    }

    // Initialize
    const activeBtn = document.querySelector('.content-type-btn.active');
    if (activeBtn) {
        activeBtn.classList.add('bg-purple-600', 'text-white');
        activeBtn.classList.remove('text-gray-400');
    }

    showContent(currentContentType);
});

// Navigation function
function navigateToContent(element) {
    const route = element.getAttribute('data-route');
    if (route && route !== '') {
        window.location.href = route;
    }
}
</script>

{{-- Custom Styles --}}
<style>
.content-type-btn.active {
    background: linear-gradient(135deg, #8b5cf6 0%, #3b82f6 100%);
}

.content-type-btn {
    min-width: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.list-content {
    max-height: 80vh;
    overflow-y: auto;
    scrollbar-width: thin;
}
</style>
