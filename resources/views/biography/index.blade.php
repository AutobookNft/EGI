{{--
/**
 * @Oracode Biography Index View - Listing Page
 * 🎯 Purpose: Display biography listing with hybrid authentication awareness
 * 🧱 Core Logic: Grid layout, filtering, pagination with FlorenceEGI branding
 * 🛡️ Security: Auth-aware CTAs, public/private indication, GDPR compliant
 *
 * @package Resources\Views\Biography
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.1 (FlorenceEGI Biography Web Display)
 * @date 2025-08-15
 * @purpose Biography listing page with hybrid authentication support and full i18n
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
                            "url": "{{ route('biography.public.show', $biography->slug) }}"
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
                        "name": "{{ __('biography.index_page.breadcrumb_home') }}",
                        "item": "{{ url('/') }}"
                    },
                    {
                        "@type": "ListItem",
                        "position": 2,
                        "name": "{{ __('biography.index_page.breadcrumb_biographies') }}",
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
        <section class="relative py-16 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
            <div class="absolute inset-0 bg-gradient-to-r from-yellow-900/20 to-green-900/20"></div>
            <div class="relative px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

                {{-- Breadcrumb --}}
                <nav class="mb-8" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4 text-sm">
                        <li>
                            <a href="{{ url('/') }}" class="text-gray-400 transition-colors hover:text-yellow-400">
                                <span class="sr-only">{{ __('biography.index_page.breadcrumb_home') }}</span>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path
                                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-3a1 1 0 011-1h2a1 1 0 011 1v3a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                    </path>
                                </svg>
                            </a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-4 font-medium text-yellow-400">{{ __('biography.index_page.breadcrumb_biographies') }}</span>
                        </li>
                    </ol>
                </nav>

                {{-- Page Header --}}
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-white md:text-5xl lg:text-6xl">
                        {!! __('biography.index_page.title') !!}
                    </h1>
                    <p class="max-w-3xl mx-auto mt-6 text-xl leading-relaxed text-gray-300">
                        {{ __('biography.index_page.subtitle') }}
                    </p>

                    {{-- Auth-aware CTA --}}
                    @if ($isAuthenticated)
                        @if ($canCreateBiography)
                            <div class="mt-8">
                                <a href="#" onclick="createBiographyModal()"
                                    class="inline-flex items-center px-8 py-3 text-lg font-semibold text-gray-900 transition-all duration-300 bg-yellow-400 rounded-full shadow-lg hover:bg-yellow-300 hover:shadow-xl">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    {{ __('biography.index_page.create_your_story') }}
                                </a>
                            </div>
                        @endif
                    @else
                        <div
                            class="flex flex-col items-center mt-8 space-y-4 sm:flex-row sm:justify-center sm:space-x-4 sm:space-y-0">
                            <button onclick="openWalletConnect()"
                                class="inline-flex items-center px-6 py-3 text-base font-semibold text-white transition-all duration-300 bg-indigo-600 rounded-full shadow-lg hover:bg-indigo-700 hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                    </path>
                                </svg>
                                {{ __('biography.index_page.connect_wallet') }}
                            </button>
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center px-6 py-3 text-base font-semibold text-yellow-400 transition-all duration-300 border-2 border-yellow-400 rounded-full hover:bg-yellow-400 hover:text-gray-900">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                {{ __('biography.index_page.register') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- Filters & Sort Section --}}
        <section class="py-8 bg-gray-800 border-b border-gray-700">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex flex-col items-center justify-between space-y-4 lg:flex-row lg:space-y-0">

                    {{-- Results Count --}}
                    <div class="text-gray-300">
                        <span class="text-lg font-semibold text-yellow-400">{{ $biographies->total() }}</span>
                        {{ Str::plural(__('biography.biography'), $biographies->total()) }}
                        @if ($accessLevel === 'authenticated')
                            <span class="text-sm text-gray-400">{{ __('biography.index_page.results_count_auth') }}</span>
                        @else
                            <span class="text-sm text-gray-400">{{ __('biography.index_page.results_count_public') }}</span>
                        @endif
                    </div>

                    {{-- Sort Controls --}}
                    <div class="flex items-center space-x-4">
                        <label for="sort-select" class="text-sm font-medium text-gray-300">{{ __('biography.index_page.sort_by') }}</label>
                        <select id="sort-select" onchange="updateSort()"
                            class="px-4 py-2 text-sm text-white bg-gray-700 border border-gray-600 rounded-lg focus:border-transparent focus:ring-2 focus:ring-yellow-400">
                            <option value="updated_at-desc"
                                {{ $currentSort === 'updated_at' && $currentDirection === 'desc' ? 'selected' : '' }}>
                                {{ __('biography.index_page.sort_newest') }}
                            </option>
                            <option value="updated_at-asc"
                                {{ $currentSort === 'updated_at' && $currentDirection === 'asc' ? 'selected' : '' }}>
                                {{ __('biography.index_page.sort_oldest') }}
                            </option>
                            <option value="title-asc"
                                {{ $currentSort === 'title' && $currentDirection === 'asc' ? 'selected' : '' }}>
                                {{ __('biography.index_page.sort_title_az') }}
                            </option>
                            <option value="title-desc"
                                {{ $currentSort === 'title' && $currentDirection === 'desc' ? 'selected' : '' }}>
                                {{ __('biography.index_page.sort_title_za') }}
                            </option>
                            <option value="created_at-desc"
                                {{ $currentSort === 'created_at' && $currentDirection === 'desc' ? 'selected' : '' }}>
                                {{ __('biography.index_page.sort_first_created') }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        {{-- Biographies Grid --}}
        <section class="py-12">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">

                @if ($biographies->count() > 0)
                    <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach ($biographies as $biography)
                            <article
                                class="relative overflow-hidden transition-all duration-300 transform bg-gray-800 border border-gray-700 shadow-lg group rounded-xl hover:-translate-y-1 hover:border-yellow-400/50 hover:shadow-2xl">

                                {{-- Biography Image --}}
                                <div
                                    class="relative h-48 overflow-hidden bg-gradient-to-br from-yellow-900/30 to-green-900/30">
                                    @if ($biography->getFirstMediaUrl('featured_image'))
                                        <img src="{{ $biography->getFirstMediaUrl('featured_image', 'web') }}"
                                            alt="{{ __('biography.index_page.alt_biography_of', ['name' => $biography->user->name]) }}"
                                            class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105"
                                            loading="lazy">
                                    @else
                                        <div class="flex items-center justify-center w-full h-full">
                                            <div class="text-center">
                                                <svg class="w-16 h-16 mx-auto text-yellow-400/50" fill="none"
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
                                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-yellow-300 rounded-full bg-gray-900/80 backdrop-blur-sm">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"
                                                    aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                        clip-rule="evenodd"></path>
                                                </svg>
                                                {{ __('biography.index_page.private_badge') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Biography Content --}}
                                <div class="p-6">

                                    {{-- Author Info --}}
                                    <div class="flex items-center mb-3">
                                        <div
                                            class="flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600">
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
                                        class="mb-2 text-lg font-bold text-white transition-colors line-clamp-2 group-hover:text-yellow-400">
                                        {{ $biography->title }}
                                    </h3>

                                    {{-- Biography Preview --}}
                                    <p class="mb-4 text-sm text-gray-300 line-clamp-3">
                                        {{ $biography->contentPreview }}
                                    </p>

                                    {{-- Biography Meta --}}
                                    <div class="flex items-center justify-between mb-4 text-xs text-gray-400">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                                </path>
                                            </svg>
                                            @if ($biography->type === 'chapters')
                                                {{ $biography->published_chapters_count }}
                                                {{ Str::plural(__('biography.chapter'), $biography->published_chapters_count) }}
                                            @else
                                                {{ __('biography.index_page.meta_single_story') }}
                                            @endif
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $biography->updated_at->diffForHumans() }}
                                        </div>
                                    </div>

                                    {{-- Read More Link --}}
                                    <a href="{{ route('biography.public.show', $biography->slug) }}"
                                        class="inline-flex items-center text-sm font-semibold text-yellow-400 transition-colors hover:text-yellow-300">
                                        {{ __('biography.index_page.read_biography') }}
                                        <svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1"
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
                        <div class="flex items-center justify-center w-24 h-24 mx-auto mb-6 bg-gray-800 rounded-full">
                            <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-white">{{ __('biography.index_page.empty_title') }}</h3>
                        <p class="max-w-md mx-auto mb-8 text-gray-400">
                            @if ($accessLevel === 'public')
                                {{ __('biography.index_page.empty_public_text') }}
                            @else
                                {{ __('biography.index_page.empty_auth_text') }}
                            @endif
                        </p>
                        @if ($canCreateBiography)
                            <button onclick="createBiographyModal()"
                                class="inline-flex items-center px-6 py-3 text-base font-semibold text-gray-900 transition-all duration-300 bg-yellow-400 rounded-full hover:bg-yellow-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                {{ __('biography.index_page.create_first_biography') }}
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
                        title: "{{ __('biography.index_page.modal_creation_title') }}",
                        text: "{{ __('biography.index_page.modal_creation_text') }}",
                        confirmButtonText: "{{ __('biography.index_page.modal_creation_button') }}",
                        confirmButtonColor: '#D4A574'
                    });
                } else {
                    alert("{{ __('biography.index_page.modal_creation_text') }}");
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
