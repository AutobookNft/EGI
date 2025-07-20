<x-guest-layout>
    <x-slot
        name="title">{{ $biography ? $biography->title . ' - ' . $user->name : __('biography.show.title') }}</x-slot>
    <x-slot
        name="metaDescription">{{ $biography ? $biography->excerpt : __('biography.show.no_biography_description') }}</x-slot>

    {{-- Disable Hero Section --}}
    <x-slot name="noHero">true</x-slot>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
        @if ($biography)

            {{-- DEBUG SEMPLICE --}}
            <div style="background: red; color: white; padding: 20px; margin: 20px; font-size: 16px;">
                <strong>DEBUG INFO:</strong><br>
                Biography ID: {{ $biography->id }}<br>
                Total Media: {{ $biography->media ? $biography->media->count() : 'NULL' }}<br>
                Main Gallery: {{ $biography->getMedia('main_gallery')->count() }}<br>
                @if($biography->getMedia('main_gallery')->count() > 0)
                    First Image URL: {{ $biography->getMedia('main_gallery')->first()->getUrl() }}
                @endif
            </div>
            <!-- Hero Section -->
            <div class="relative overflow-hidden bg-gradient-to-r from-[#1B365D] to-[#2D5016]">
                <div class="absolute inset-0 bg-black/20"></div>
                <div class="relative max-w-4xl px-4 py-16 mx-auto sm:px-6 lg:px-8">
                    <div class="text-center">
                        <!-- Author Avatar and Info -->
                        <div class="mb-8">
                            @if ($user->profile_photo_url)
                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                                    class="mx-auto mb-4 h-24 w-24 rounded-full border-4 border-[#D4A574]">
                            @else
                                <div
                                    class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-[#D4A574]">
                                    <span
                                        class="text-3xl font-bold text-gray-900">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <h2 class="mb-2 text-2xl font-semibold text-white">{{ $user->name }}</h2>
                            @if ($user->bio)
                                <p class="mx-auto max-w-2xl text-[#D4A574]">{{ $user->bio }}</p>
                            @endif
                        </div>

                        <!-- Biography Title -->
                        <h1 class="mb-6 font-serif text-4xl font-bold text-white md:text-5xl">
                            {{ $biography->title }}
                        </h1>
                        @if ($biography->excerpt)
                            <p class="mx-auto mb-8 max-w-3xl text-xl text-[#D4A574]">
                                {{ $biography->excerpt }}
                            </p>
                        @endif

                        <!-- Reading Info -->
                        <div class="flex items-center justify-center space-x-6 text-white">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ $biography->getEstimatedReadingTime() }} {{ __('biography.min_read') }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span>{{ $biography->updated_at->format('M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Section -->
            <div class="max-w-4xl px-4 py-12 mx-auto sm:px-6 lg:px-8">

                <!-- ========== BIOGRAFIA PRINCIPALE ========== -->
                <div class="mb-16">
                    <!-- Header Biografia Principale -->
                    <div class="mb-8 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 mb-4 rounded-full bg-gradient-to-r from-[#D4A574] to-[#E6B885]">
                            <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.168 18.477 18.582 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h2 class="mb-2 text-3xl font-bold text-white">La Biografia</h2>
                        <div class="w-24 h-1 bg-gradient-to-r from-[#D4A574] to-[#E6B885] mx-auto rounded-full"></div>
                    </div>

                    <!-- Featured Image della Biografia (se presente) -->
                    @if ($biography->getFirstMedia('featured_image'))
                        @php $featuredImage = $biography->getFirstMedia('featured_image'); @endphp
                        <div class="mb-8">
                            <div class="overflow-hidden border-2 border-[#D4A574] rounded-2xl bg-gray-800/50 shadow-2xl">
                                <img src="{{ $featuredImage->hasGeneratedConversion('web') ? $featuredImage->getUrl('web') : $featuredImage->getUrl() }}"
                                     alt="{{ $featuredImage->getCustomProperty('alt_text') ?? $biography->title }}"
                                     class="object-cover w-full h-96">
                                @if ($featuredImage->getCustomProperty('caption'))
                                    <div class="p-6 bg-gradient-to-r from-[#1B365D]/80 to-[#2D5016]/80">
                                        <p class="font-medium text-center text-white">{{ $featuredImage->getCustomProperty('caption') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Contenuto principale della biografia -->
                    <div class="p-8 mb-8 border-2 border-[#D4A574] rounded-2xl bg-gradient-to-br from-gray-800/60 to-gray-900/60 backdrop-blur-sm shadow-2xl">
                        <div class="leading-relaxed prose prose-base prose-invert max-w-none">
                            {!! $biography->content !!}
                        </div>
                    </div>

                    <!-- Galleria Media della Biografia Principale -->
                    @if ($biography->getMedia('main_gallery')->count() > 0)
                        <div class="mb-8">
                            <div class="flex items-center mb-6">
                                <div class="flex items-center justify-center w-10 h-10 mr-4 rounded-full bg-[#D4A574]">
                                    <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-semibold text-white">
                                    Galleria Principale
                                </h3>
                            </div>
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                                @foreach ($biography->getMedia('main_gallery') as $media)
                                    <div class="group relative overflow-hidden border-2 border-gray-600 hover:border-[#D4A574] rounded-xl bg-gray-800/50 backdrop-blur-sm transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                                        @if (Str::startsWith($media->mime_type, 'image/'))
                                            <!-- Use optimized 'web' conversion for display, fallback to original -->
                                            <img src="{{ $media->hasGeneratedConversion('web') ? $media->getUrl('web') : $media->getUrl() }}"
                                                alt="{{ $media->getCustomProperty('alt_text') ?? $media->name }}"
                                                class="object-cover w-full h-64 transition-opacity group-hover:opacity-90"
                                                loading="lazy">

                                            <!-- Overlay with full size link -->
                                            <div class="absolute inset-0 flex items-center justify-center transition-opacity bg-black bg-opacity-0 group-hover:bg-opacity-30">
                                                <a href="{{ $media->getUrl() }}"
                                                   target="_blank"
                                                   class="opacity-0 group-hover:opacity-100 bg-gradient-to-r from-[#D4A574] to-[#E6B885] text-gray-900 rounded-full p-3 transition-all duration-300 transform hover:scale-110">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        @elseif(Str::startsWith($media->mime_type, 'video/'))
                                            <video controls class="object-cover w-full h-64">
                                                <source src="{{ $media->getUrl() }}" type="{{ $media->mime_type }}">
                                                {{ __('biography.video_not_supported') }}
                                            </video>
                                        @endif

                                        @if ($media->getCustomProperty('caption'))
                                            <div class="p-4 bg-gradient-to-r from-gray-800 to-gray-700">
                                                <p class="text-sm text-center text-gray-300">{{ $media->getCustomProperty('caption') }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- ========== SEPARATORE ========== -->
                @if ($biography->chapters && $biography->chapters->count() > 0)
                    <div class="relative mb-16">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t-2 border-gradient-to-r from-transparent via-[#D4A574] to-transparent"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <div class="px-6 py-3 bg-gradient-to-r from-[#1B365D] to-[#2D5016] rounded-full border-2 border-[#D4A574]">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-6 h-6 text-[#D4A574]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    <span class="text-xl font-bold text-white">I Capitoli</span>
                                    <svg class="w-6 h-6 text-[#D4A574]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Capitoli -->
                @if ($biography->chapters && $biography->chapters->count() > 0)
                    <div class="space-y-8">
                        @foreach ($biography->chapters as $chapter)
                            <div class="overflow-hidden border border-gray-700 rounded-xl bg-gray-800/50 backdrop-blur-sm">
                                <!-- Chapter Header -->
                                <div class="p-6 border-b border-gray-700">
                                    <h2 class="mb-3 text-2xl font-semibold text-white">
                                        {{ $chapter->title }}
                                    </h2>
                                    @if ($chapter->subtitle)
                                        <p class="mb-4 text-lg text-[#D4A574]">
                                            {{ $chapter->subtitle }}
                                        </p>
                                    @endif
                                    <!-- Chapter Meta -->
                                    <div class="flex items-center space-x-4 text-sm text-gray-400">
                                        @if ($chapter->date_from)
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                {{ $chapter->date_from->format('M Y') }}
                                                @if ($chapter->date_to && !$chapter->is_ongoing)
                                                    - {{ $chapter->date_to->format('M Y') }}
                                                @elseif($chapter->is_ongoing)
                                                    - {{ __('biography.ongoing') }}
                                                @endif
                                            </span>
                                        @endif
                                        @if ($chapter->chapter_type)
                                            <span
                                                class="inline-flex items-center rounded-full bg-[#1B365D] px-2.5 py-0.5 text-xs font-medium text-white">
                                                {{ __('biography.chapter_type.' . $chapter->chapter_type) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Chapter Content -->
                                <div class="p-6">
                                    <div class="prose prose-lg prose-invert max-w-none">
                                        {!! $chapter->content !!}
                                    </div>

                                    <!-- Chapter Media -->
                                    @if ($chapter->getMedia('chapter_images')->count() > 0)
                                        <div class="mt-8">
                                            <h4 class="mb-4 text-lg font-semibold text-white">
                                                {{ __('biography.media_label') }}
                                            </h4>
                                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                                @foreach ($chapter->getMedia('chapter_images') as $media)
                                                    <div class="relative overflow-hidden transition-transform bg-gray-700 rounded-lg group hover:scale-105">
                                                        @if (Str::startsWith($media->mime_type, 'image/'))
                                                            <!-- Use optimized 'card' conversion for chapter images -->
                                                            <img src="{{ $media->hasGeneratedConversion('card') ? $media->getUrl('card') : $media->getUrl() }}"
                                                                alt="{{ $media->getCustomProperty('alt_text') ?? $media->name }}"
                                                                class="object-cover w-full h-48 transition-opacity group-hover:opacity-90"
                                                                loading="lazy">

                                                            <!-- Overlay with full size link -->
                                                            <div class="absolute inset-0 flex items-center justify-center transition-opacity bg-black bg-opacity-0 group-hover:bg-opacity-30">
                                                                <a href="{{ $media->getUrl() }}"
                                                                   target="_blank"
                                                                   class="p-2 text-gray-900 transition-opacity bg-white rounded-full opacity-0 group-hover:opacity-100 bg-opacity-90">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        @elseif(Str::startsWith($media->mime_type, 'video/'))
                                                            <video controls class="object-cover w-full h-48">
                                                                <source src="{{ $media->getUrl() }}" type="{{ $media->mime_type }}">
                                                                {{ __('biography.video_not_supported') }}
                                                            </video>
                                                        @endif

                                                        @if ($media->getCustomProperty('caption'))
                                                            <div class="p-3">
                                                                <p class="text-sm text-gray-300">{{ $media->getCustomProperty('caption') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Chapter Featured Image -->
                                    @if ($chapter->getFirstMedia('chapter_featured'))
                                        @php $chapterFeatured = $chapter->getFirstMedia('chapter_featured'); @endphp
                                        <div class="mt-6">
                                            <img src="{{ $chapterFeatured->hasGeneratedConversion('full') ? $chapterFeatured->getUrl('full') : $chapterFeatured->getUrl() }}"
                                                 alt="{{ $chapterFeatured->getCustomProperty('alt_text') ?? $chapter->title }}"
                                                 class="object-cover w-full h-64 rounded-lg">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Author CTA -->
                <div
                    class="mt-12 rounded-xl border border-gray-700 bg-gradient-to-r from-[#1B365D]/50 to-[#2D5016]/50 p-8 text-center">
                    <h3 class="mb-4 text-2xl font-semibold text-white">
                        {{ __('biography.discover_more') }}
                    </h3>
                    <p class="max-w-2xl mx-auto mb-6 text-gray-300">
                        {{ __('biography.discover_more_description') }}
                    </p>
                    <div class="flex flex-col justify-center space-y-4 sm:flex-row sm:space-x-4 sm:space-y-0">
                        <a href="{{ route('creator.home', $user->id) }}"
                            class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-[#D4A574] to-[#E6B885] px-6 py-3 font-semibold text-gray-900 transition-all duration-200 hover:from-[#E6B885] hover:to-[#D4A574]">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ __('biography.view_profile') }}
                        </a>

                        <button onclick="shareBiography()"
                            class="inline-flex items-center justify-center rounded-lg bg-[#2D5016] px-6 py-3 font-semibold text-white transition-colors hover:bg-[#1B365D]">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z">
                                </path>
                            </svg>
                            {{ __('biography.share') }}
                        </button>
                    </div>
                </div>
            </div>
        @else
            <!-- No Biography State -->
            <div class="flex items-center justify-center min-h-screen">
                <div class="text-center">
                    <div class="flex items-center justify-center w-24 h-24 mx-auto mb-6 bg-gray-800 rounded-full">
                        <svg class="h-12 w-12 text-[#D4A574]" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <h2 class="mb-4 text-2xl font-semibold text-white">
                        {{ __('biography.show.no_biography_title') }}
                    </h2>
                    <p class="max-w-md mx-auto mb-8 text-gray-400">
                        {{ __('biography.show.no_biography_description') }}
                    </p>
                    <a href="{{ route('creator.home', $user->id) }}"
                        class="inline-flex items-center rounded-lg bg-gradient-to-r from-[#D4A574] to-[#E6B885] px-6 py-3 font-semibold text-gray-900 shadow-lg transition-all duration-200 hover:from-[#E6B885] hover:to-[#D4A574]">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ __('biography.view_profile') }}
                    </a>
                </div>
            </div>
        @endif
    </div>

    <script>
        function shareBiography() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $biography->title ?? '' }} - {{ $user->name }}',
                    text: '{{ $biography->excerpt ?? '' }}',
                    url: window.location.href
                });
            } else {
                // Fallback: copy to clipboard
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('{{ __('biography.link_copied') }}');
                });
            }
        }
    </script>

    <style>
        .prose {
            color: #D1D5DB;
        }

        .prose h1,
        .prose h2,
        .prose h3,
        .prose h4,
        .prose h5,
        .prose h6 {
            color: #F9FAFB;
        }

        .prose a {
            color: #D4A574;
        }

        .prose a:hover {
            color: #E6B885;
        }

        .prose blockquote {
            border-left-color: #D4A574;
            color: #9CA3AF;
        }

        .prose code {
            background-color: #374151;
            color: #E5E7EB;
        }

        .prose pre {
            background-color: #1F2937;
            color: #E5E7EB;
        }
    </style>
</x-guest-layout>
