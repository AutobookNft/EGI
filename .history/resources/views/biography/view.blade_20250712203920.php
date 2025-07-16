<x-app-layout>
    <x-slot name="title">{{ $biography ? $biography->title : __('biography.view.title') }}</x-slot>

    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
        @if ($biography)
            <!-- Hero Section -->
            <div class="relative overflow-hidden bg-gradient-to-r from-[#1B365D] to-[#2D5016]">
                <div class="absolute inset-0 bg-black/20"></div>
                <div class="relative mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
                    <div class="text-center">
                        <h1 class="mb-6 font-serif text-4xl font-bold text-white md:text-5xl">
                            {{ $biography->title }}
                        </h1>
                        @if ($biography->excerpt)
                            <p class="mx-auto mb-8 max-w-3xl text-xl text-[#D4A574]">
                                {{ $biography->excerpt }}
                            </p>
                        @endif

                        <!-- Author Info -->
                        <div class="flex items-center justify-center space-x-4 text-white">
                            <div class="flex items-center space-x-2">
                                @if ($user->profile_photo_url)
                                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                                        class="h-10 w-10 rounded-full border-2 border-[#D4A574]">
                                @else
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#D4A574]">
                                        <span class="font-semibold text-gray-900">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <span class="font-medium">{{ $user->name }}</span>
                            </div>

                            <span class="text-gray-300">•</span>

                            <div class="flex items-center space-x-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ $biography->getEstimatedReadingTime() }} {{ __('biography.min_read') }}</span>
                            </div>

                            @if ($biography->type === 'chapters')
                                <span class="text-gray-300">•</span>
                                <div class="flex items-center space-x-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    <span>{{ $biography->chapters->count() }} {{ __('biography.chapters') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Section -->
            <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
                @if ($biography->type === 'single')
                    <!-- Single Biography Content -->
                    <div class="mb-8 rounded-xl border border-gray-700 bg-gray-800/50 p-8 backdrop-blur-sm">
                        <div class="prose prose-lg prose-invert max-w-none">
                            {!! $biography->content !!}
                        </div>
                    </div>
                @else
                    <!-- Chapters Biography Content -->
                    <div class="space-y-8">
                        @foreach ($biography->chapters as $chapter)
                            <div
                                class="overflow-hidden rounded-xl border border-gray-700 bg-gray-800/50 backdrop-blur-sm">
                                <!-- Chapter Header -->
                                <div class="border-b border-gray-700 p-6">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h2 class="mb-2 text-2xl font-semibold text-white">
                                                {{ $chapter->title }}
                                            </h2>
                                            @if ($chapter->subtitle)
                                                <p class="mb-3 text-lg text-[#D4A574]">
                                                    {{ $chapter->subtitle }}
                                                </p>
                                            @endif

                                            <!-- Chapter Meta -->
                                            <div class="flex items-center space-x-4 text-sm text-gray-400">
                                                @if ($chapter->date_from)
                                                    <span class="flex items-center">
                                                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor"
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

                                        @if ($isOwn)
                                            <div class="flex items-center space-x-2">
                                                <button onclick="editChapter({{ $chapter->id }})"
                                                    class="p-2 text-gray-400 transition-colors hover:text-[#D4A574]">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
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
                                                {{ __('biography.media') }}
                                            </h4>
                                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                                @foreach ($chapter->media as $media)
                                                    <div class="overflow-hidden rounded-lg bg-gray-700">
                                                        @if ($media->type === 'image')
                                                            <img src="{{ $media->url }}"
                                                                alt="{{ $media->alt_text }}"
                                                                class="h-48 w-full object-cover">
                                                        @elseif($media->type === 'video')
                                                            <video controls class="h-48 w-full object-cover">
                                                                <source src="{{ $media->url }}" type="video/mp4">
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

                <!-- Biography Media -->
                @if ($biography->media && $biography->media->count() > 0)
                    <div class="mt-12">
                        <h3 class="mb-6 text-2xl font-semibold text-white">
                            {{ __('biography.gallery') }}
                        </h3>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                            @foreach ($biography->media as $media)
                                <div
                                    class="overflow-hidden rounded-xl border border-gray-700 bg-gray-800/50 backdrop-blur-sm">
                                    @if ($media->type === 'image')
                                        <img src="{{ $media->url }}" alt="{{ $media->alt_text }}"
                                            class="h-64 w-full object-cover">
                                    @elseif($media->type === 'video')
                                        <video controls class="h-64 w-full object-cover">
                                            <source src="{{ $media->url }}" type="video/mp4">
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

                <!-- Action Buttons -->
                @if ($isOwn)
                    <div class="mt-12 flex flex-col justify-center space-y-4 sm:flex-row sm:space-x-4 sm:space-y-0">
                        <a href="{{ route('biography.manage') }}"
                            class="inline-flex items-center justify-center rounded-lg bg-[#1B365D] px-6 py-3 font-semibold text-white transition-colors hover:bg-[#2D5016]">
                            <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            {{ __('biography.edit_biography') }}
                        </a>
                    </div>
                @endif
            </div>
        @else
            <!-- No Biography State -->
            <div class="flex min-h-screen items-center justify-center">
                <div class="text-center">
                    <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-gray-800">
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
                    <p class="mx-auto mb-8 max-w-md text-gray-400">
                        {{ __('biography.show.no_biography_description') }}
                    </p>
                    <a href="{{ route('creator.show', $user->slug) }}"
                        class="inline-flex items-center rounded-lg bg-gradient-to-r from-[#D4A574] to-[#E6B885] px-6 py-3 font-semibold text-gray-900 shadow-lg transition-all duration-200 hover:from-[#E6B885] hover:to-[#D4A574]">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ __('biography.view_profile') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>

@push('scripts')
    <script>
        function editChapter(chapterId) {
            // Redirect to chapter editor
            window.location.href = `/biography/manage?chapter=${chapterId}`;
        }

        function shareBiography() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $biography->title ?? '' }}',
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
@endpush

@push('styles')
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
@endpush
