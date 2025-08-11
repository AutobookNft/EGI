{{-- resources/views/collections/show.blade.php --}}
{{-- 🎨 ORACODE REDESIGN: Galleria Imponente Mobile-First --}}
{{-- Trasformazione orchestrata per massimo impatto visivo e UX --}}
{{-- Focus: Hero Impact + Info Critica + Griglia Adattiva + Micro-animazioni --}}
{{-- Includiamo il layout principale per le collezioni --}}
{{-- Questo layout gestisce il titolo, la descrizione e gli script condivisi --}}

<x-collection-layout :title="$collection->collection_name . ' | FlorenceEGI'"
    :metaDescription="Str::limit($collection->description, 155) ?? __('collection.show.details_for_collection') . ' ' . $collection->collection_name">

    {{-- Schema.org ottimizzato --}}
    <x-slot name="schemaMarkup">
        <script type="application/ld+json">
            {
            "@context": "https://schema.org",
            "@type": "CollectionPage",
            "name": "{{ $collection->collection_name }}",
            "description": "{{ $collection->description }}",
            "image": "{{ $collection->image_banner ? Storage::url($collection->image_banner) : asset('images/default_banner.jpg') }}",
            "author": {
                "@type": "Person",
                "name": "{{ $collection->creator->name ?? __('collection.show.unknown_creator_schema') }}"
            },
            "numberOfItems": "{{ $collection->egis_count ?? 0 }}",
            "mainEntity": {
                "@type": "CreativeWork",
                "name": "{{ $collection->collection_name }}"
            }
        }
        </script>
    </x-slot>

    {{-- 🎯 SEZIONE INFORMAZIONI CRITICHE (Sopra al Banner) --}}
    <div class="bg-gray-900 border-b border-gray-800">
        <div class="container px-4 py-6 mx-auto sm:px-6 lg:px-8">
            {{-- Breadcrumb migliorato --}}
            <nav class="flex items-center mb-4 space-x-2 text-sm text-gray-400" aria-label="Breadcrumb">
                <a href="{{ route('home.collections.index') }}"
                    class="transition-colors duration-200 hover:text-emerald-400">
                    <span class="mr-1 text-base material-symbols-outlined">collections</span>
                    {{ __('collection.show.collections_breadcrumb') }}
                </a>
                <span class="text-xs material-symbols-outlined">chevron_right</span>
                <span class="font-medium text-gray-300">{{ Str::limit($collection->collection_name, 30) }}</span>
            </nav>

            {{-- Quick Stats Cards - Mobile First --}}
            <div class="grid grid-cols-2 gap-3 lg:grid-cols-4 sm:gap-4">
                <div class="p-3 text-center rounded-lg stat-card info-glass sm:p-4">
                    <div class="text-xl font-bold sm:text-2xl text-emerald-400">{{ $collection->EGI_number ??
                        $collection->egis_count ?? 0 }}</div>
                    <div class="text-xs tracking-wider text-gray-400 uppercase">{{ __('collection.show.egis') }}</div>
                </div>
                <div class="p-3 text-center rounded-lg stat-card info-glass sm:p-4">
                    <div class="text-xl font-bold text-pink-400 sm:text-2xl">{{ $collection->likes_count ?? 0 }}</div>
                    <div class="text-xs tracking-wider text-gray-400 uppercase">{{ __('collection.show.likes') }}</div>
                </div>
                <div class="p-3 text-center rounded-lg stat-card info-glass sm:p-4">
                    <div class="text-xl font-bold text-blue-400 sm:text-2xl">{{ $collection->reservations_count ?? 0 }}
                    </div>
                    <div class="text-xs tracking-wider text-gray-400 uppercase">{{ __('collection.show.reserved') }}
                    </div>
                </div>
                <div class="p-3 text-center rounded-lg stat-card info-glass sm:p-4">
                    @if($collection->floor_price && $collection->floor_price > 0)
                    <div class="text-xl font-bold text-yellow-400 sm:text-2xl">{{
                        number_format($collection->floor_price, 2) }}</div>
                    <div class="text-xs tracking-wider text-gray-400 uppercase">{{ __('collection.show.algo_floor') }}
                    </div>
                    @else
                    <div class="text-xl font-bold text-purple-400 sm:text-2xl">{{ __('collection.show.free_mint') }}
                    </div>
                    <div class="text-xs tracking-wider text-gray-400 uppercase">{{ __('collection.show.mint_label') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 🎨 HERO BANNER POTENZIATO --}}
    <section class="relative overflow-hidden">
        {{-- Background con Parallax Effect --}}
        <div class="absolute inset-0 z-0 parallax-banner">
            @if($collection->image_banner)
            <img src="{{ $collection->image_banner }}" alt="Banner for {{ $collection->collection_name }}"
                class="object-cover w-full h-full scale-105">
            @else
            <div class="w-full h-full bg-gradient-to-br from-indigo-900 via-purple-900 to-gray-900"></div>
            @endif
            {{-- Overlay gradiente potenziato --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/60 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/30 to-transparent"></div>
        </div>

        {{-- Hero Content --}}
        <div class="container relative z-10 px-4 py-16 mx-auto sm:px-6 lg:px-8 sm:py-20 lg:py-24">
            <div class="max-w-4xl">
                <div class="flex items-center gap-3 mb-6">
                    @if($collection->creator)
                    {{-- Se il creator esiste, rendi tutto cliccabile --}}
                    <a href="{{ route('creator.home', ['id' => $collection->creator->id]) }}"
                        class="flex items-center gap-3 transition-all duration-200 hover:opacity-80 group">

                        {{-- Avatar Creator --}}
                        <div class="flex-shrink-0">
                            @if ($collection->creator->profile_photo_url)
                            <img src="{{ $collection->creator->profile_photo_url }}"
                                alt="{{ $collection->creator->name }}"
                                class="object-cover w-12 h-12 transition-transform duration-200 border-2 rounded-full border-white/30 group-hover:scale-110">
                            @else
                            <div
                                class="flex items-center justify-center w-12 h-12 transition-transform duration-200 rounded-full bg-gradient-to-br from-emerald-500 to-blue-500 group-hover:scale-110">
                                <span class="text-lg font-bold text-white">{{ substr($collection->creator->name ?? 'U',
                                    0, 1) }}</span>
                            </div>
                            @endif
                        </div>

                        {{-- Creator Name + Badge --}}
                        <div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="font-semibold text-white transition-colors duration-200 group-hover:text-emerald-400">{{
                                    $collection->creator->name ?? __('collection.show.unknown_creator') }}</span>
                                @if ($collection->creator->usertype === 'verified')
                                <span class="text-lg text-blue-400 material-symbols-outlined"
                                    title="{{ __('collection.show.verified_creator') }}">verified</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-300 transition-colors duration-200 group-hover:text-gray-200">{{
                                __('collection.show.collection_creator') }}</p>
                        </div>
                    </a>
                    @else
                    {{-- Se il creator non esiste, mostra solo un div non cliccabile --}}
                    <div class="flex items-center gap-3">
                        {{-- Avatar placeholder --}}
                        <div class="flex-shrink-0">
                            <div
                                class="flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-emerald-500 to-blue-500">
                                <span class="text-lg font-bold text-white">U</span>
                            </div>
                        </div>

                        {{-- Creator Name placeholder --}}
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-white">{{ __('collection.show.unknown_creator')
                                    }}</span>
                            </div>
                            <p class="text-sm text-gray-300">{{ __('collection.show.collection_creator') }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Titolo della Collezione --}}

                {{-- Collection Name + Avatar --}}
                <div class="flex flex-col items-start gap-6 mb-8 sm:flex-row sm:items-center">
                    {{-- Collection Avatar --}}
                    <div
                        class="flex-shrink-0 w-24 h-24 overflow-hidden bg-gray-800 border-4 sm:w-32 sm:h-32 rounded-2xl border-white/20">
                        @if($collection->image_avatar)
                        <img src="{{ $collection->image_avatar }}" alt="{{ $collection->collection_name }}"
                            class="object-cover w-full h-full">
                        @else
                        <div
                            class="flex items-center justify-center w-full h-full bg-gradient-to-br from-purple-500 to-pink-500">
                            <span class="text-3xl text-white material-symbols-outlined">image</span>
                        </div>
                        @endif
                    </div>

                    {{-- Title + Description --}}
                    <div class="flex-1 min-w-0">
                        <h1 class="mb-4 text-3xl font-bold leading-tight text-white sm:text-4xl lg:text-5xl">
                            {{ $collection->collection_name }}
                        </h1>

                        @if($collection->description)
                        <p class="text-base leading-relaxed text-gray-200 sm:text-lg line-clamp-3 sm:line-clamp-none">
                            {{ $collection->description }}
                        </p>
                        @endif
                    </div>
                </div>

                {{-- CTA Section --}}
                <div class="flex flex-col gap-4 sm:flex-row">
                    <button
                        class="btn-primary-glow flex items-center justify-center px-6 py-3 rounded-lg text-white font-semibold text-sm sm:text-base like-button {{ $collection->is_liked ?? false ? 'is-liked' : '' }}"
                        data-collection-id="{{ $collection->id }}" data-resource-type="collection"
                        data-resource-id="{{ $collection->id }}"
                        data-like-url="{{ route('api.toggle.collection.like', $collection->id) }}">
                        <span class="mr-2 material-symbols-outlined icon-heart">{{ $collection->is_liked ?? false ?
                            'favorite' : 'favorite_border' }}</span>
                        <span class="like-text">{{ $collection->is_liked ?? false ? __('collection.show.liked') :
                            __('collection.show.like_collection') }}</span>
                        <span class="ml-2 like-count-display">({{ $collection->likes_count ?? 0 }})</span>
                    </button>

                    <button
                        class="flex items-center justify-center px-6 py-3 text-sm font-semibold text-white transition-all duration-300 border rounded-lg bg-white/10 backdrop-blur-sm border-white/20 hover:bg-white/20 sm:text-base"
                        onclick="navigator.share ? navigator.share({title: '{{ $collection->collection_name }}', url: window.location.href}) : copyToClipboard(window.location.href)">
                        <span class="mr-2 material-symbols-outlined">share</span>
                        {{ __('collection.show.share') }}
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- 🌳 EPP SECTION (Se presente) --}}
    @if($collection->epp)
    <div class="border-b border-gray-800 bg-gradient-to-r from-green-900/20 to-emerald-900/20">
        <div class="container px-4 py-6 mx-auto sm:px-6 lg:px-8">
            <div class="p-4 hero-glass rounded-xl sm:p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 p-3 rounded-lg bg-green-500/20">
                        <span class="text-2xl text-green-400 material-symbols-outlined">eco</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="mb-2 text-lg font-semibold text-white">{{
                            __('collection.show.supporting_environmental_project') }}</h3>
                        <h4 class="mb-2 font-medium text-emerald-400">{{ $collection->epp->name }}</h4>
                        <p class="mb-3 text-sm text-gray-300 line-clamp-2">{{ $collection->epp->description }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-green-400">{{ __('collection.show.epp_percentage') }}
                                {{
                                __('collection.show.of_sales_support_this_project') }}</span>
                            <a href="{{ route('epps.show', $collection->epp_id) }}"
                                class="flex items-center gap-1 text-sm font-medium text-emerald-400 hover:text-emerald-300">
                                {{ __('collection.show.learn_more') }}
                                <span class="text-sm material-symbols-outlined">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 🎨 GRIGLIA EGI PRINCIPALE --}}
    <main class="py-8 bg-gray-900 sm:py-12">
        <div class="container px-4 mx-auto sm:px-6 lg:px-8">
            {{-- Header con Filtri --}}
            <div class="flex flex-col items-start justify-between gap-4 mb-8 sm:flex-row sm:items-center">
                <div>
                    <h2 class="mb-2 text-2xl font-bold text-white sm:text-3xl">
                        {{ __('collection.show.collection_items') }}
                    </h2>
                    <p class="text-sm text-gray-400">
                        {{ $collection->egis_count ?? 0 }} {{ __('collection.show.unique_digital_assets') }}
                    </p>
                </div>

                {{-- Filtri e ordinamento --}}
                <div class="flex flex-col w-full gap-3 sm:flex-row sm:w-auto">
                    <select id="egis-sort"
                        class="px-4 py-2 text-sm text-white bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="position">{{ __('collection.show.position') }}</option>
                        <option value="newest">{{ __('collection.show.newest') }}</option>
                        <option value="oldest">{{ __('collection.show.oldest') }}</option>
                        <option value="price_low">{{ __('collection.show.price_low_to_high') }}</option>
                        <option value="price_high">{{ __('collection.show.price_high_to_low') }}</option>
                    </select>

                    {{-- View Toggle --}}
                    <div class="flex p-1 bg-gray-800 rounded-lg">
                        <button
                            class="px-3 py-1 text-sm font-medium text-white bg-indigo-600 rounded view-toggle active"
                            data-view="grid">
                            <span class="text-sm material-symbols-outlined">grid_view</span>
                        </button>
                        <button class="px-3 py-1 text-sm font-medium text-gray-400 rounded view-toggle hover:text-white"
                            data-view="list">
                            <span class="text-sm material-symbols-outlined">view_list</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Container EGI Responsivo --}}
            <div class="egi-grid" id="egis-container">
                @php
                // Determina se l'utente corrente è il creator di questa collezione
                $isCreatorViewing = false;
                if (auth()->check()) {
                $isCreatorViewing = auth()->id() === $collection->creator_id;
                } elseif (session('connected_user_id')) {
                $isCreatorViewing = session('connected_user_id') === $collection->creator_id;
                }

                // TEMPORARY: Forziamo per test se è la collezione del creator ID 4
                if ($collection->creator_id === 4) {
                $isCreatorViewing = true;
                }
                @endphp

                @forelse($collection->egis as $index => $egi)

                {{-- Grid Item (shown in grid mode) --}}
                <div class="egi-item card-hover grid-view">
                    <x-egi-card :egi="$egi" :collection="$collection" :portfolioContext="$isCreatorViewing"
                        :portfolioOwner="$isCreatorViewing ? $collection->creator : null"
                        :creatorPortfolioContext="$isCreatorViewing" />
                </div>

                {{-- List Item (shown in list mode) --}}
                <div class="egi-item list-view" style="display: none;">
                    <x-egi-card-list :egi="$egi" :context="'collection'" :showBadge="false" :showPurchasePrice="false"
                        :showOwnershipBadge="false" />
                </div>
                @empty
                {{-- Stato Vuoto Migliorato --}}
                <div class="col-span-full">
                    <div class="px-6 py-16 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 mb-6 bg-gray-800 rounded-full">
                            <span class="text-2xl text-gray-400 material-symbols-outlined">image</span>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-white">{{ __('collection.show.no_egis_yet') }}</h3>
                        <p class="max-w-md mx-auto mb-6 text-gray-400">
                            {{ __('collection.show.no_egis_message') }}
                        </p>
                        @if(auth()->id() === $collection->creator_id)
                        <button class="px-6 py-3 font-semibold text-white rounded-lg btn-primary-glow">
                            <span class="mr-2 material-symbols-outlined">add</span>
                            {{ __('collection.show.add_first_egi') }}
                        </button>
                        @endif
                    </div>
                </div>
                @endforelse
            </div>

            {{-- Load More Button (se necessario) --}}
            @if($collection->egis->count() >= 20)
            <div class="mt-12 text-center">
                <button
                    class="px-8 py-3 font-medium text-white transition-colors duration-200 bg-gray-800 rounded-lg hover:bg-gray-700">
                    {{ __('collection.show.load_more_items') }}
                </button>
            </div>
            @endif
        </div>
    </main>

    {{-- 📚 COLLEZIONI CORRELATE --}}
    @if(isset($relatedCollections) && $relatedCollections->count() > 0)
    <section class="py-12 bg-gray-800">
        <div class="container px-4 mx-auto sm:px-6 lg:px-8">
            <h2 class="mb-8 text-2xl font-bold text-center text-white">
                {{ __('collection.show.more_from_this_creator') }}
            </h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($relatedCollections->take(3) as $relatedCollection)
                <div class="card-hover">
                    <x-collection-card :collection="$relatedCollection" imageType="card" />
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- 🚀 FLOATING ACTIONS (Mobile) --}}
    <div class="floating-actions lg:hidden">
        <div
            class="flex items-center gap-3 px-4 py-3 border border-gray-700 rounded-full bg-gray-900/90 backdrop-blur-sm">
            <button
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition-colors bg-indigo-600 rounded-full hover:bg-indigo-700">
                <span class="text-sm material-symbols-outlined">favorite_border</span>
                {{ __('collection.show.like_collection') }}
            </button>
            <button
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition-colors bg-gray-700 rounded-full hover:bg-gray-600">
                <span class="text-sm material-symbols-outlined">share</span>
                {{ __('collection.show.share') }}
            </button>
        </div>
    </div>

    {{-- JavaScript Enhancements --}}
    @push('scripts')
    <script>
        // Like button functionality
document.querySelectorAll('.like-button').forEach(button => {
    button.addEventListener('click', async function() {
        const collectionId = this.dataset.collectionId;
        const likeUrl = this.dataset.likeUrl;
        const icon = this.querySelector('.icon-heart');
        const text = this.querySelector('.like-text');
        const countDisplay = this.querySelector('.like-count-display');

        // Visual feedback immediato
        this.style.transform = 'scale(0.95)';
        setTimeout(() => {
            this.style.transform = 'scale(1)';
        }, 150);

        try {
            const response = await fetch(likeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (data.success) {
                this.classList.toggle('is-liked', data.is_liked);

                if (data.is_liked) {
                    icon.textContent = 'favorite';
                    text.textContent = '{{ __('collection.show.liked') }}';
                    this.style.background = 'linear-gradient(135deg, #ec4899 0%, #be185d 100%)';
                } else {
                    icon.textContent = 'favorite_border';
                    text.textContent = '{{ __('collection.show.like_collection') }}';
                    this.style.background = 'linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%)';
                }

                countDisplay.textContent = `(${data.likes_count ?? 0})`;

                // Celebrazione visiva
                if (data.is_liked) {
                    icon.style.animation = 'heartBeat 0.6s ease-in-out';
                    setTimeout(() => {
                        icon.style.animation = '';
                    }, 600);
                }
            }
        } catch (error) {
            console.error('Error toggling like:', error);
        }
    });
});

// View Toggle - SIMPLIFIED with direct style control
document.querySelectorAll('.view-toggle').forEach(button => {
    button.addEventListener('click', function() {
        const view = this.dataset.view;
        const container = document.getElementById('egis-container');

        // Update buttons
        document.querySelectorAll('.view-toggle').forEach(btn => {
            btn.classList.remove('active', 'bg-indigo-600', 'text-white');
            btn.classList.add('text-gray-400');
        });

        this.classList.add('active', 'bg-indigo-600', 'text-white');
        this.classList.remove('text-gray-400');

        // Toggle between grid and list items with direct style control
        if (view === 'list') {
            container.className = 'space-y-4';
            // Hide grid items, show list items
            container.querySelectorAll('.grid-view').forEach(item => {
                item.style.display = 'none';
            });
            container.querySelectorAll('.list-view').forEach(item => {
                item.style.display = 'block';
            });
        } else {
            container.className = 'egi-grid';
            // Show grid items, hide list items
            container.querySelectorAll('.grid-view').forEach(item => {
                item.style.display = 'block';
            });
            container.querySelectorAll('.list-view').forEach(item => {
                item.style.display = 'none';
            });
        }
    });
});

// EGI sorting functionality
document.getElementById('egis-sort').addEventListener('change', function() {
    const sortValue = this.value;
    const container = document.getElementById('egis-container');
    const items = Array.from(container.querySelectorAll('.egi-item'));

    items.sort((a, b) => {
        switch(sortValue) {
            case 'newest':
                // Assume data-created attribute exists or implement accordingly
                return new Date(b.dataset.created || 0) - new Date(a.dataset.created || 0);
            case 'oldest':
                return new Date(a.dataset.created || 0) - new Date(b.dataset.created || 0);
            case 'price_low':
                return (parseFloat(a.dataset.price || 0) - parseFloat(b.dataset.price || 0));
            case 'price_high':
                return (parseFloat(b.dataset.price || 0) - parseFloat(a.dataset.price || 0));
            default: // position
                return (parseInt(a.dataset.position || 999) - parseInt(b.dataset.position || 999));
        }
    });

    // Re-append sorted items
    items.forEach(item => container.appendChild(item));
});

// Parallax effect (performance-conscious)
let ticking = false;

function updateParallax() {
    const scrolled = window.pageYOffset;
    const parallax = document.querySelector('.parallax-banner');

    if (parallax) {
        const speed = scrolled * 0.5;
        parallax.style.transform = `translateY(${speed}px)`;
    }

    ticking = false;
}

function requestTick() {
    if (!ticking) {
        requestAnimationFrame(updateParallax);
        ticking = true;
    }
}

window.addEventListener('scroll', requestTick);

// Copy to clipboard utility
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed z-50 px-4 py-2 text-sm font-medium text-white transform -translate-x-1/2 bg-green-600 rounded-lg bottom-4 left-1/2';
        toast.textContent = '{{ __('collection.show.link_copied_to_clipboard') }}';
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    });
}

// Enhanced scroll animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.animationPlayState = 'running';
        }
    });
}, observerOptions);

// Observe all animated elements
document.querySelectorAll('.egi-item, .stat-card').forEach(el => {
    observer.observe(el);
});
    </script>
    @endpush

</x-collection-layout>

{{-- @vite(['resources/js/collections-show.js']) --}}
