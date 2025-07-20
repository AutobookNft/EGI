<x-guest-layout>
    <x-slot
        name="title">{{ $biography ? $biography->title . ' - ' . $user->name : __('biography.show.title') }}</x-slot>
    <x-slot
        name="metaDescription">{{ $biography ? $biography->excerpt : __('biography.show.no_biography_description') }}</x-slot>

    {{-- Disable Hero Section --}}
    <x-slot name="noHero">true</x-slot>
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
        @if ($biography)
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
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
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
                <!-- Contenuto principale della biografia -->
                <div class="p-8 mb-8 border border-gray-700 rounded-xl bg-gray-800/50 backdrop-blur-sm">
                    <div class="prose-sm prose prose-invert max-w-none">
                        {!! $biography->content !!}
                    </div>
                </div>
                <!-- Media della biografia principale -->
                @if ($biography->media && $biography->media->count() > 0)
                    <div class="mb-12">
                        <h3 class="mb-6 text-2xl font-semibold text-white">
                            {{ __('biography.gallery') }}
                        </h3>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($biography->media as $media)
                                <div
                                    class="overflow-hidden border border-gray-700 rounded-xl bg-gray-800/50 backdrop-blur-sm">
                                    @if (Str::startsWith($media->mime_type, 'image/'))
                                        @dump($media-)
                                        <img src="{{ $media->url }}"
                                            alt="{{ $media->alt_text ?? ($media->name ?? $media->file_name) }}"
                                            title="{{ $media->url }}" class="object-cover w-full h-64">
                                    @elseif(Str::startsWith($media->mime_type, 'video/'))
                                        <video controls class="object-cover w-full h-64">
                                            <source src="{{ $media->url }}" type="{{ $media->mime_type }}">
                                            {{ __('biography.video_not_supported') }}
                                        </video>
                                    @endif
                                    @if ($media->caption)
                                        <div class="p-4">
                                            <p class="text-gray-300">{{ $media->caption }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Capitoli -->
                @if ($biography->chapters && $biography->chapters->count() > 0)
                    <div class="space-y-8">
                        @foreach ($biography->chapters as $chapter)
                            <div
                                class="overflow-hidden border border-gray-700 rounded-xl bg-gray-800/50 backdrop-blur-sm">
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
                                    @if ($chapter->media && $chapter->media->count() > 0)
                                        <div class="mt-8">
                                            <h4 class="mb-4 text-lg font-semibold text-white">
                                                {{ __('biography.media_label') }}
                                            </h4>
                                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                                @foreach ($chapter->media as $media)
                                                    <div class="overflow-hidden bg-gray-700 rounded-lg">
                                                        @if (Str::startsWith($media->mime_type, 'image/'))
                                                            <img src="{{ $media->url }}"
                                                                alt="{{ $media->alt_text ?? ($media->name ?? $media->file_name) }}"
                                                                class="object-cover w-full h-48">
                                                        @elseif(Str::startsWith($media->mime_type, 'video/'))
                                                            <video controls class="object-cover w-full h-48">
                                                                <source src="{{ $media->url }}"
                                                                    type="{{ $media->mime_type }}">
                                                                {{ __('biography.video_not_supported') }}
                                                            </video>
                                                        @endif
                                                        @if ($media->caption)
                                                            <div class="p-3">
                                                                <p class="text-sm text-gray-300">{{ $media->caption }}
                                                                </p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
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
