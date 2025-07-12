{{--
/**
 * @Oracode Biography Index View - Listing Page
 * 🎯 Purpose: Display biography listing with hybrid authentication awareness
 * 🧱 Core Logic: Grid layout, filtering, pagination with FlorenceEGI branding
 * 🛡️ Security: Auth-aware CTAs, public/private indication, GDPR compliant
 *
 * @package Resources\Views\Biography
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI Biography Web Display)
 * @date 2025-07-03
 * @purpose Biography listing page with hybrid authentication support
 */
--}}

<x-guest-layout>
    {{-- Page Title & Meta --}}
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="metaDescription">{{ $metaDescription }}</x-slot>

    {{-- Schema.org Structured Data --}}
    <x-slot name="schemaMarkup">
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "CollectionPage",
            "name": "{{ $title }}",
            "description": "{{ $metaDescription }}",
            "url": "{{ route('biography.index') }}",
            "mainEntity": {
                "@type": "ItemList",
                "numberOfItems": {{ $biographies->total() }},
                "itemListElement": [
                    @foreach($biographies as $index => $biography)
                    {
                        "@type": "ListItem",
                        "position": {{ $index + 1 }},
                        "item": {
                            "@type": "Person",
                            "@id": "{{ route('biography.public.show', $biography->slug) }}",
                            "name": "{{ $biography->user->name }}",
                            "description": "{{ Str::limit($biography->contentPreview, 160) }}",
                            "url": "{{ route('biography.show', $biography->slug) }}"
                        }
                    }{{ !$loop->last ? ',' : '' }}
                    @endforeach
                ]
            },
            "breadcrumb": {
                "@type": "BreadcrumbList",
                "itemListElement": [
                    {
                        "@type": "ListItem",
                        "position": 1,
                        "name": "Home",
                        "item": "{{ url('/') }}"
                    },
                    {
                        "@type": "ListItem",
                        "position": 2,
                        "name": "Biografie",
                        "item": "{{ route('biography.index') }}"
                    }
                ]
            }
        }
        </script>
    </x-slot>

    {{-- Disable Hero Section --}}
    <x-slot name="noHero">true</x-slot>

    {{-- Main Content --}}
    <div class="min-h-screen bg-gray-900">

        {{-- Header Section --}}
        <section class="relative bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 py-16">
            <div class="absolute inset-0 bg-gradient-to-r from-yellow-900/20 to-green-900/20"></div>
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                {{-- Breadcrumb --}}
                <nav class="mb-8" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4 text-sm">
                        <li>
                            <a href="{{ url('/') }}" class="text-gray-400 transition-colors hover:text-yellow-400">
                                <span class="sr-only">Home</span>
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path
                                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-3a1 1 0 011-1h2a1 1 0 011 1v3a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                    </path>
                                </svg>
                            </a>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-5 w-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-4 font-medium text-yellow-400">Biografie</span>
                        </li>
                    </ol>
                </nav>

                {{-- Page Header --}}
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-white md:text-5xl lg:text-6xl">
                        Storie del
                        <span class="bg-gradient-to-r from-yellow-400 to-yellow-600 bg-clip-text text-transparent">
                            Nuovo Rinascimento
                        </span>
                    </h1>
                    <p class="mx-auto mt-6 max-w-3xl text-xl leading-relaxed text-gray-300">
                        Scopri le biografie di creator, mecenati e visionari che stanno costruendo il futuro ecologico
                        digitale
                    </p>

                    {{-- Auth-aware CTA --}}
                    @if ($isAuthenticated)
                        @if ($canCreateBiography)
                            <div class="mt-8">
                                <a href="#" onclick="createBiographyModal()"
                                    class="inline-flex items-center rounded-full bg-yellow-400 px-8 py-3 text-lg font-semibold text-gray-900 shadow-lg transition-all duration-300 hover:bg-yellow-300 hover:shadow-xl">
                                    <svg class="mr-2 h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Crea la tua Storia
                                </a>
                            </div>
                        @endif
                    @else
                        <div
                            class="mt-8 flex flex-col items-center space-y-4 sm:flex-row sm:justify-center sm:space-x-4 sm:space-y-0">
                            <button onclick="openWalletConnect()"
                                class="inline-flex items-center rounded-full bg-indigo-600 px-6 py-3 text-base font-semibold text-white shadow-lg transition-all duration-300 hover:bg-indigo-700 hover:shadow-xl">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                    </path>
                                </svg>
                                Connetti Wallet
                            </button>
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center rounded-full border-2 border-yellow-400 px-6 py-3 text-base font-semibold text-yellow-400 transition-all duration-300 hover:bg-yellow-400 hover:text-gray-900">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Registrati
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Filters & Sort Section --}}
        <section class="border-b border-gray-700 bg-gray-800 py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center justify-between space-y-4 lg:flex-row lg:space-y-0">

                    {{-- Results Count --}}
                    <div class="text-gray-300">
                        <span class="text-lg font-semibold text-yellow-400">{{ $biographies->total() }}</span>
                        {{ Str::plural('biografia', $biographies->total()) }}
                        @if ($accessLevel === 'authenticated')
                            <span class="text-sm text-gray-400">(tue + pubbliche)</span>
                        @else
                            <span class="text-sm text-gray-400">(pubbliche)</span>
                        @endif
                    </div>

                    {{-- Sort Controls --}}
                    <div class="flex items-center space-x-4">
                        <label for="sort-select" class="text-sm font-medium text-gray-300">Ordina per:</label>
                        <select id="sort-select" onchange="updateSort()"
                            class="rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 text-sm text-white focus:border-transparent focus:ring-2 focus:ring-yellow-400">
                            <option value="updated_at-desc"
                                {{ $currentSort === 'updated_at' && $currentDirection === 'desc' ? 'selected' : '' }}>
                                Più recenti
                            </option>
                            <option value="updated_at-asc"
                                {{ $currentSort === 'updated_at' && $currentDirection === 'asc' ? 'selected' : '' }}>
                                Meno recenti
                            </option>
                            <option value="title-asc"
                                {{ $currentSort === 'title' && $currentDirection === 'asc' ? 'selected' : '' }}>
                                Titolo A-Z
                            </option>
                            <option value="title-desc"
                                {{ $currentSort === 'title' && $currentDirection === 'desc' ? 'selected' : '' }}>
                                Titolo Z-A
                            </option>
                            <option value="created_at-desc"
                                {{ $currentSort === 'created_at' && $currentDirection === 'desc' ? 'selected' : '' }}>
                                Prima creazione
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        {{-- Biographies Grid --}}
        <section class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                @if ($biographies->count() > 0)
                    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach ($biographies as $biography)
                            <article
                                class="group relative transform overflow-hidden rounded-xl border border-gray-700 bg-gray-800 shadow-lg transition-all duration-300 hover:-translate-y-1 hover:border-yellow-400/50 hover:shadow-2xl">

                                {{-- Biography Image --}}
                                <div
                                    class="relative h-48 overflow-hidden bg-gradient-to-br from-yellow-900/30 to-green-900/30">
                                    @if ($biography->getFirstMediaUrl('featured_image'))
                                        <img src="{{ $biography->getFirstMediaUrl('featured_image', 'web') }}"
                                            alt="Biografia di {{ $biography->user->name }}"
                                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                            loading="lazy">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center">
                                            <div class="text-center">
                                                <svg class="mx-auto h-16 w-16 text-yellow-400/50" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                    </path>
                                                </svg>
                                                <span
                                                    class="mt-2 text-sm text-gray-400">{{ $biography->user->name }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Privacy Badge --}}
                                    @if (!$biography->is_public)
                                        <div class="absolute right-3 top-3">
                                            <span
                                                class="inline-flex items-center rounded-full bg-gray-900/80 px-2 py-1 text-xs font-medium text-yellow-300 backdrop-blur-sm">
                                                <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20"
                                                    aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                Privata
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Biography Content --}}
                                <div class="p-6">

                                    {{-- Author Info --}}
                                    <div class="mb-3 flex items-center">
                                        <div
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600">
                                            <span class="text-sm font-bold text-gray-900">
                                                {{ strtoupper(substr($biography->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-white">{{ $biography->user->name }}</p>
                                            <p class="text-xs text-gray-400">
                                                {{ $biography->created_at->format('M Y') }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Biography Title --}}
                                    <h3
                                        class="mb-2 line-clamp-2 text-lg font-bold text-white transition-colors group-hover:text-yellow-400">
                                        {{ $biography->title }}
                                    </h3>

                                    {{-- Biography Preview --}}
                                    <p class="mb-4 line-clamp-3 text-sm text-gray-300">
                                        {{ $biography->contentPreview }}
                                    </p>

                                    {{-- Biography Meta --}}
                                    <div class="mb-4 flex items-center justify-between text-xs text-gray-400">
                                        <div class="flex items-center">
                                            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                                </path>
                                            </svg>
                                            @if ($biography->type === 'chapters')
                                                {{ $biography->published_chapters_count }}
                                                {{ Str::plural('capitolo', $biography->published_chapters_count) }}
                                            @else
                                                Storia unica
                                            @endif
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $biography->updated_at->diffForHumans() }}
                                        </div>
                                    </div>

                                    {{-- Read More Link --}}
                                    <a href="{{ route('biography.show', $biography->slug) }}"
                                        class="inline-flex items-center text-sm font-semibold text-yellow-400 transition-colors hover:text-yellow-300">
                                        Leggi biografia
                                        <svg class="ml-1 h-4 w-4 transition-transform group-hover:translate-x-1"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if ($biographies->hasPages())
                        <div class="mt-12">
                            <nav class="flex items-center justify-center" aria-label="Pagination">
                                {{ $biographies->appends(request()->query())->links('pagination.custom-biography') }}
                            </nav>
                        </div>
                    @endif
                @else
                    {{-- Empty State --}}
                    <div class="py-20 text-center">
                        <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-gray-800">
                            <svg class="h-12 w-12 text-gray-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-white">Nessuna biografia trovata</h3>
                        <p class="mx-auto mb-8 max-w-md text-gray-400">
                            @if ($accessLevel === 'public')
                                Non ci sono ancora biografie pubbliche da mostrare. Sii il primo a condividere la tua
                                storia!
                            @else
                                Non hai ancora creato biografie. Inizia a raccontare la tua storia nel Nuovo
                                Rinascimento.
                            @endif
                        </p>
                        @if ($canCreateBiography)
                            <button onclick="createBiographyModal()"
                                class="inline-flex items-center rounded-full bg-yellow-400 px-6 py-3 text-base font-semibold text-gray-900 transition-all duration-300 hover:bg-yellow-300">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Crea la tua prima Biografia
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    </div>

    {{-- JavaScript for Interactive Features --}}
    @push('scripts')
        <script>
            /**
             * @Oracode Biography Index JavaScript
             * 🎯 Purpose: Handle sorting, filtering, and modal interactions
             */

            // Sort functionality
            function updateSort() {
                const select = document.getElementById('sort-select');
                const [sort, direction] = select.value.split('-');
                const url = new URL(window.location);
                url.searchParams.set('sort', sort);
                url.searchParams.set('direction', direction);
                url.searchParams.delete('page'); // Reset to first page
                window.location.href = url.toString();
            }

            // Create biography modal (placeholder for future implementation)
            function createBiographyModal() {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Creazione Biografia',
                        text: 'Funzionalità in sviluppo. Presto potrai creare la tua biografia!',
                        confirmButtonText: 'Ho capito',
                        confirmButtonColor: '#D4A574'
                    });
                } else {
                    alert('Funzionalità in sviluppo. Presto potrai creare la tua biografia!');
                }
            }

            // Wallet connect (integrates with existing FEGI system)
            function openWalletConnect() {
                if (window.openSecureWalletModal) {
                    window.openSecureWalletModal();
                } else {
                    window.location.href = '{{ route('register') }}';
                }
            }

            // Smooth scroll for pagination
            document.addEventListener('DOMContentLoaded', function() {
                const paginationLinks = document.querySelectorAll('.pagination a');
                paginationLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        // Let the navigation happen, then smooth scroll to top
                        setTimeout(() => {
                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                        }, 100);
                    });
                });
            });
        </script>
    @endpush
</x-guest-layout>
