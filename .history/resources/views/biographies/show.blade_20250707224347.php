{{--
/**
 * @Oracode Biography Show View - Detail Page
 * 🎯 Purpose: Display single biography with chapters and hybrid authentication awareness
 * 🧱 Core Logic: Biography content, chapter timeline, media gallery, social sharing
 * 🛡️ Security: Owner controls, auth-aware CTAs, privacy indicators, GDPR compliant
 *
 * @package Resources\Views\Biography
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI Biography Web Display)
 * @date 2025-07-03
 * @purpose Biography detail page with chapter navigation and media support
 */
--}}

<x-guest-layout>
    {{-- Page Title & Meta --}}
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="metaDescription">{{ $metaDescription }}</x-slot>
    <x-slot name="canonicalUrl">{{ $canonicalUrl }}</x-slot>

    {{-- Schema.org Structured Data --}}
    <x-slot name="schemaMarkup">
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Person",
            "@id": "{{ $canonicalUrl }}",
            "name": "{{ $biography->user->name }}",
            "description": "{{ $biography->contentPreview }}",
            "url": "{{ $canonicalUrl }}",
            @if($biography->getFirstMediaUrl('featured_image'))
            "image": "{{ $biography->getFirstMediaUrl('featured_image', 'web') }}",
            @endif
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "{{ $canonicalUrl }}"
            },
            "author": {
                "@type": "Person",
                "name": "{{ $biography->user->name }}",
                "identifier": "{{ $biography->user->id }}"
            },
            "dateCreated": "{{ $biography->created_at->toISOString() }}",
            "dateModified": "{{ $biography->updated_at->toISOString() }}",
            "publisher": {
                "@type": "Organization",
                "name": "FlorenceEGI",
                "url": "{{ url('/') }}",
                "logo": {
                    "@type": "ImageObject",
                    "url": "{{ asset('images/logo/logo_1.webp') }}"
                }
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
                    },
                    {
                        "@type": "ListItem",
                        "position": 3,
                        "name": "{{ $biography->title }}",
                        "item": "{{ $canonicalUrl }}"
                    }
                ]
            }
            @if($biography->type === 'chapters' && $chapters->count() > 0)
            ,
            "hasPart": [
                @foreach($chapters as $chapter)
                {
                    "@type": "Article",
                    "@id": "{{ $canonicalUrl }}#chapter-{{ $chapter->id }}",
                    "headline": "{{ $chapter->title }}",
                    "description": "{{ $chapter->contentPreview }}",
                    "dateCreated": "{{ $chapter->created_at->toISOString() }}",
                    "dateModified": "{{ $chapter->updated_at->toISOString() }}",
                    "author": {
                        "@type": "Person",
                        "name": "{{ $biography->user->name }}"
                    }
                }{{ !$loop->last ? ',' : '' }}
                @endforeach
            ]
            @endif
        }
        </script>
    </x-slot>

    {{-- Disable Hero Section --}}
    <x-slot name="noHero">true</x-slot>

    {{-- Main Content --}}
    <div class="min-h-screen bg-gray-900">

        {{-- Hero Section with Featured Image --}}
        <section class="relative">
            <div class="relative h-64 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 md:h-80 lg:h-96">
                @if($biography->getFirstMediaUrl('featured_image'))
                    <img src="{{ $biography->getFirstMediaUrl('featured_image', 'web') }}"
                         alt="Biografia di {{ $biography->user->name }}"
                         class="absolute inset-0 object-cover w-full h-full opacity-30">
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/50 to-transparent"></div>

                {{-- Breadcrumb --}}
                <nav class="relative z-10 px-4 pt-6 mx-auto max-w-7xl sm:px-6 lg:px-8" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4 text-sm">
                        <li>
                            <a href="{{ url('/') }}" class="text-gray-300 transition-colors hover:text-yellow-400">
                                <span class="sr-only">Home</span>
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-3a1 1 0 011-1h2a1 1 0 011 1v3a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                            </a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('biography.index') }}" class="ml-4 text-gray-300 transition-colors hover:text-yellow-400">
                                Biografie
                            </a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-4 font-medium text-yellow-400">{{ Str::limit($biography->title, 30) }}</span>
                        </li>
                    </ol>
                </nav>

                {{-- Hero Content --}}
                <div class="absolute bottom-0 left-0 right-0 z-10 px-4 pb-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex flex-col items-start justify-between lg:flex-row lg:items-end">

                        {{-- Biography Info --}}
                        <div class="flex-1 mb-6 lg:mb-0">
                            {{-- Privacy & Type Badges --}}
                            <div class="flex items-center mb-4 space-x-3">
                                @if(!$biography->is_public)
                                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-yellow-300 rounded-full bg-gray-900/80 backdrop-blur-sm">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Biografia Privata
                                    </span>
                                @endif
                                <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-green-300 rounded-full bg-gray-900/80 backdrop-blur-sm">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    {{ $biography->type === 'chapters' ? 'Biografia a Capitoli' : 'Storia Unica' }}
                                </span>
                            </div>

                            {{-- Author & Title --}}
                            <div class="flex items-center mb-4">
                                <div class="flex items-center justify-center w-12 h-12 mr-4 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600">
                                    <span class="text-lg font-bold text-gray-900">
                                        {{ strtoupper(substr($biography->user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <h1 class="text-3xl font-bold text-white md:text-4xl lg:text-5xl">
                                        {{ $biography->title }}
                                    </h1>
                                    <p class="mt-1 text-lg text-gray-300">
                                        di <span class="font-semibold text-yellow-400">{{ $biography->user->name }}</span>
                                    </p>
                                </div>
                            </div>

                            {{-- Biography Meta --}}
                            <div class="flex flex-wrap items-center gap-6 text-sm text-gray-300">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $estimatedReadingTime }} min di lettura
                                </div>
                                @if($biography->type === 'chapters')
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        {{ $chapters->count() }} {{ Str::plural('capitolo', $chapters->count()) }}
                                    </div>
                                @endif
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Ultimo aggiornamento: {{ $biography->updated_at->format('d M Y') }}
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-wrap items-center gap-3">
                            @if($isOwner)
                                {{-- Owner Actions --}}
                                @if($canEditBiography)
                                    <button onclick="editBiography()"
                                            class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 transition-all duration-300 bg-yellow-400 rounded-lg hover:bg-yellow-300">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Modifica
                                    </button>
                                @endif
                                @if($canCreateChapter && $biography->type === 'chapters')
                                    <button onclick="createChapter()"
                                            class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white transition-all duration-300 bg-green-600 border border-green-600 rounded-lg hover:bg-green-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Nuovo Capitolo
                                    </button>
                                @endif
                            @endif

                            {{-- Share Button --}}
                            <button onclick="shareBiography()"
                                    class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-300 transition-all duration-300 border border-gray-600 rounded-lg hover:bg-gray-800 hover:border-gray-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                </svg>
                                Condividi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Main Content Area --}}
        <div class="px-4 py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-12">

                {{-- Main Biography Content --}}
                <main class="lg:col-span-8">

                    @if($biography->type === 'single')
                        {{-- Single Biography Content --}}
                        <article class="prose prose-lg prose-invert max-w-none">
                            <div class="p-8 bg-gray-800 border border-gray-700 rounded-xl">
                                {!! nl2br(e($biography->content)) !!}
                            </div>
                        </article>
                    @else
                        {{-- Chapters Biography Content --}}
                        @if($chapters->count() > 0)
                            <div class="space-y-8">
                                @foreach($chapters as $chapter)
                                    <article id="chapter-{{ $chapter->id }}"
                                             class="overflow-hidden bg-gray-800 border border-gray-700 rounded-xl scroll-mt-24">

                                        {{-- Chapter Header --}}
                                        <div class="px-8 py-6 border-b border-gray-600 bg-gradient-to-r from-gray-800 to-gray-700">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center mb-2">
                                                        @switch($chapter->chapter_type)
                                                            @case('milestone')
                                                                <svg class="w-6 h-6 mr-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                                </svg>
                                                                @break
                                                            @case('achievement')
                                                                <svg class="w-6 h-6 mr-3 text-green-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                @break
                                                            @default
                                                                <svg class="w-6 h-6 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                </svg>
                                                        @endswitch
                                                        <h3 class="text-xl font-bold text-white">{{ $chapter->title }}</h3>
                                                    </div>
                                                    @if($chapter->date_from)
                                                        <p class="text-sm text-gray-400">
                                                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            {{ $chapter->dateRangeDisplay }}
                                                        </p>
                                                    @endif
                                                </div>
                                                @if($isOwner && $canManageChapters)
                                                    <div class="flex items-center space-x-2">
                                                        <button onclick="editChapter({{ $chapter->id }})"
                                                                class="p-2 text-gray-400 transition-colors rounded-lg hover:text-yellow-400 hover:bg-gray-700"
                                                                title="Modifica capitolo">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Chapter Content --}}
                                        <div class="p-8">
                                            <div class="prose prose-lg prose-invert max-w-none">
                                                {!! nl2br(e($chapter->content)) !!}
                                            </div>

                                            {{-- Chapter Media Gallery --}}
                                            @if($chapter->getMedia('chapter_images')->count() > 0)
                                                <div class="mt-8">
                                                    <h4 class="mb-4 text-lg font-semibold text-white">Gallery</h4>
                                                    <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
                                                        @foreach($chapter->getMedia('chapter_images') as $media)
                                                            <div class="relative cursor-pointer group" onclick="openImageModal('{{ $media->getUrl('full') }}', '{{ $media->name }}')">
                                                                <img src="{{ $media->getUrl('card') }}"
                                                                     alt="{{ $media->name }}"
                                                                     class="object-cover w-full h-32 transition-transform duration-300 rounded-lg group-hover:scale-105"
                                                                     loading="lazy">
                                                                <div class="absolute inset-0 flex items-center justify-center transition-all duration-300 bg-black bg-opacity-0 rounded-lg group-hover:bg-opacity-30">
                                                                    <svg class="w-8 h-8 text-white transition-opacity duration-300 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                                    </svg>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            {{-- No Chapters State --}}
                            <div class="py-20 text-center">
                                <div class="flex items-center justify-center w-24 h-24 mx-auto mb-6 bg-gray-800 rounded-full">
                                    <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="mb-2 text-xl font-semibold text-white">Nessun capitolo disponibile</h3>
                                <p class="max-w-md mx-auto mb-8 text-gray-400">
                                    @if($isOwner)
                                        Questa biografia è strutturata a capitoli ma non ne sono ancora stati creati.
                                    @else
                                        L'autore non ha ancora pubblicato capitoli per questa biografia.
                                    @endif
                                </p>
                                @if($isOwner && $canCreateChapter)
                                    <button onclick="createChapter()"
                                            class="inline-flex items-center px-6 py-3 text-base font-semibold text-gray-900 transition-all duration-300 bg-yellow-400 rounded-full hover:bg-yellow-300">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Crea il primo Capitolo
                                    </button>
                                @endif
                            </div>
                        @endif
                    @endif
                </main>

                {{-- Sidebar --}}
                <aside class="lg:col-span-4">
                    <div class="sticky space-y-8 top-8">

                        {{-- Table of Contents (for chapters biography) --}}
                        @if($biography->type === 'chapters' && $chapterNavigation->count() > 0)
                            <div class="p-6 bg-gray-800 border border-gray-700 rounded-xl">
                                <h4 class="flex items-center mb-4 text-lg font-semibold text-white">
                                    <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                    </svg>
                                    Indice dei Capitoli
                                </h4>
                                <nav class="space-y-2">
                                    @foreach($chapterNavigation as $nav)
                                        <a href="#chapter-{{ $nav['id'] }}"
                                           class="flex items-center p-3 text-sm text-gray-300 transition-colors rounded-lg hover:text-white hover:bg-gray-700 group">
                                            <span class="flex items-center justify-center flex-shrink-0 w-6 h-6 mr-3 text-xs font-semibold transition-colors bg-gray-700 rounded-full group-hover:bg-yellow-400 group-hover:text-gray-900">
                                                {{ $nav['position'] }}
                                            </span>
                                            <div class="flex-1">
                                                <div class="font-medium">{{ $nav['title'] }}</div>
                                                @if($nav['date_range'])
                                                    <div class="text-xs text-gray-500">{{ $nav['date_range'] }}</div>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </nav>
                            </div>
                        @endif

                        {{-- Author Info --}}
                        <div class="p-6 bg-gray-800 border border-gray-700 rounded-xl">
                            <h4 class="mb-4 text-lg font-semibold text-white">L'Autore</h4>
                            <div class="flex items-center mb-4">
                                <div class="flex items-center justify-center w-16 h-16 mr-4 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600">
                                    <span class="text-xl font-bold text-gray-900">
                                        {{ strtoupper(substr($biography->user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <h5 class="text-lg font-semibold text-white">{{ $biography->user->name }}</h5>
                                    <p class="text-sm text-gray-400">
                                        Membro dal {{ $biography->user->created_at->format('M Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-sm text-gray-300">
                                <p class="mb-3">Partecipante del Nuovo Rinascimento Ecologico Digitale</p>
                                @if($walletAddress)
                                    <div class="p-3 bg-gray-700 rounded-lg">
                                        <p class="mb-1 text-xs text-gray-400">Wallet Address</p>
                                        <code class="font-mono text-xs text-green-400 break-all">{{ Str::limit($walletAddress, 20, '...') }}</code>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Biography Stats --}}
                        <div class="p-6 bg-gray-800 border border-gray-700 rounded-xl">
                            <h4 class="mb-4 text-lg font-semibold text-white">Statistiche</h4>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-400">Tempo di lettura</span>
                                    <span class="text-sm font-semibold text-white">{{ $estimatedReadingTime }} min</span>
                                </div>
                                @if($biography->type === 'chapters')
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-400">Capitoli totali</span>
                                        <span class="text-sm font-semibold text-white">{{ $chapters->count() }}</span>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-400">Creata il</span>
                                    <span class="text-sm font-semibold text-white">{{ $biography->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-400">Ultimo aggiornamento</span>
                                    <span class="text-sm font-semibold text-white">{{ $biography->updated_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Share Widget --}}
                        <div class="p-6 bg-gray-800 border border-gray-700 rounded-xl">
                            <h4 class="mb-4 text-lg font-semibold text-white">Condividi questa Storia</h4>
                            <div class="flex flex-wrap gap-2">
                                <button onclick="shareToTwitter()"
                                        class="inline-flex items-center justify-center flex-1 px-3 py-2 text-sm font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"></path>
                                    </svg>
                                    Twitter
                                </button>
                                <button onclick="shareToLinkedIn()"
                                        class="inline-flex items-center justify-center flex-1 px-3 py-2 text-sm font-medium text-white transition-colors bg-blue-800 rounded-lg hover:bg-blue-900">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"></path>
                                    </svg>
                                    LinkedIn
                                </button>
                            </div>
                            <button onclick="copyBiographyLink()"
                                    class="inline-flex items-center justify-center w-full px-4 py-2 mt-2 text-sm font-medium text-gray-300 transition-all duration-300 border border-gray-600 rounded-lg hover:bg-gray-700 hover:border-gray-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Copia Link
                            </button>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    {{-- Image Modal --}}
    <div id="imageModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black bg-opacity-75" onclick="closeImageModal()">
        <div class="relative max-w-4xl max-h-full">
            <button onclick="closeImageModal()"
                    class="absolute z-10 p-2 text-white transition-all bg-black bg-opacity-50 rounded-full top-4 right-4 hover:bg-opacity-75">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <img id="modalImage" src="" alt="" class="object-contain max-w-full max-h-full rounded-lg">
        </div>
    </div>

    {{-- JavaScript for Interactive Features --}}
    @push('scripts')
    <script>
        /**
         * @Oracode Biography Show JavaScript
         * 🎯 Purpose: Handle chapter navigation, sharing, modals, and owner actions
         */

        // Smooth scroll for chapter navigation
        document.addEventListener('DOMContentLoaded', function() {
            const tocLinks = document.querySelectorAll('a[href^="#chapter-"]');
            tocLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);
                    if (targetElement) {
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start',
                            inline: 'nearest'
                        });
                        // Update URL without triggering page reload
                        history.pushState(null, null, this.getAttribute('href'));
                    }
                });
            });

            // Handle direct chapter links from URL
            if (window.location.hash) {
                setTimeout(() => {
                    const targetElement = document.querySelector(window.location.hash);
                    if (targetElement) {
                        targetElement.scrollIntoView({ behavior: 'smooth' });
                    }
                }, 100);
            }
        });

        // Image modal functions
        function openImageModal(imageUrl, imageName) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = imageUrl;
            modalImage.alt = imageName;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Share functions
        function shareBiography() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $biography->title }}',
                    text: '{{ Str::limit($biography->contentPreview, 100) }}',
                    url: '{{ $canonicalUrl }}'
                });
            } else {
                copyBiographyLink();
            }
        }

        function shareToTwitter() {
            const text = encodeURIComponent('{{ $biography->title }} - {{ Str::limit($biography->contentPreview, 100) }}');
            const url = encodeURIComponent('{{ $canonicalUrl }}');
            window.open(`https://twitter.com/intent/tweet?text=${text}&url=${url}`, '_blank');
        }

        function shareToLinkedIn() {
            const url = encodeURIComponent('{{ $canonicalUrl }}');
            window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank');
        }

        function copyBiographyLink() {
            navigator.clipboard.writeText('{{ $canonicalUrl }}').then(() => {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Link copiato!',
                        text: 'Il link della biografia è stato copiato negli appunti.',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    alert('Link copiato negli appunti!');
                }
            });
        }

        // Owner functions (placeholders for future implementation)
        @if($isOwner)
        function editBiography() {
            if (window.Swal) {
                Swal.fire({
                    icon: 'info',
                    title: 'Modifica Biografia',
                    text: 'Funzionalità in sviluppo. Presto potrai modificare la tua biografia!',
                    confirmButtonText: 'Ho capito',
                    confirmButtonColor: '#D4A574'
                });
            } else {
                alert('Funzionalità in sviluppo. Presto potrai modificare la tua biografia!');
            }
        }

        function createChapter() {
            if (window.Swal) {
                Swal.fire({
                    icon: 'info',
                    title: 'Nuovo Capitolo',
                    text: 'Funzionalità in sviluppo. Presto potrai aggiungere nuovi capitoli!',
                    confirmButtonText: 'Ho capito',
                    confirmButtonColor: '#D4A574'
                });
            } else {
                alert('Funzionalità in sviluppo. Presto potrai aggiungere nuovi capitoli!');
            }
        }

        function editChapter(chapterId) {
            if (window.Swal) {
                Swal.fire({
                    icon: 'info',
                    title: 'Modifica Capitolo',
                    text: 'Funzionalità in sviluppo. Presto potrai modificare i capitoli!',
                    confirmButtonText: 'Ho capito',
                    confirmButtonColor: '#D4A574'
                });
            } else {
                alert('Funzionalità in sviluppo. Presto potrai modificare i capitoli!');
            }
        }
        @endif

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // ESC to close modal
            if (e.key === 'Escape') {
                closeImageModal();
            }

            // Ctrl/Cmd + S to share (prevent default save)
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                shareBiography();
            }
        });
    </script>
    @endpush
</x-guest-layout>
