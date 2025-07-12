@extends('layouts.app')

@section('title', $biography ? $biography->title : __('biography.view.title'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    @if($biography)
        <!-- Hero Section -->
        <div class="relative overflow-hidden bg-gradient-to-r from-[#1B365D] to-[#2D5016]">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 font-serif">
                        {{ $biography->title }}
                    </h1>
                    @if($biography->excerpt)
                        <p class="text-xl text-[#D4A574] max-w-3xl mx-auto mb-8">
                            {{ $biography->excerpt }}
                        </p>
                    @endif

                    <!-- Author Info -->
                    <div class="flex items-center justify-center space-x-4 text-white">
                        <div class="flex items-center space-x-2">
                            @if($user->profile_photo_url)
                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                                     class="w-10 h-10 rounded-full border-2 border-[#D4A574]">
                            @else
                                <div class="w-10 h-10 bg-[#D4A574] rounded-full flex items-center justify-center">
                                    <span class="text-gray-900 font-semibold">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <span class="font-medium">{{ $user->name }}</span>
                        </div>

                        <span class="text-gray-300">•</span>

                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ $biography->getEstimatedReadingTime() }} {{ __('biography.min_read') }}</span>
                        </div>

                        @if($biography->type === 'chapters')
                            <span class="text-gray-300">•</span>
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>{{ $biography->chapters->count() }} {{ __('biography.chapters') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            @if($biography->type === 'single')
                <!-- Single Biography Content -->
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-8 mb-8">
                    <div class="prose prose-invert prose-lg max-w-none">
                        {!! $biography->content !!}
                    </div>
                </div>
            @else
                <!-- Chapters Biography Content -->
                <div class="space-y-8">
                    @foreach($biography->chapters as $chapter)
                        <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl overflow-hidden">
                            <!-- Chapter Header -->
                            <div class="p-6 border-b border-gray-700">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h2 class="text-2xl font-semibold text-white mb-2">
                                            {{ $chapter->title }}
                                        </h2>
                                        @if($chapter->subtitle)
                                            <p class="text-[#D4A574] text-lg mb-3">
                                                {{ $chapter->subtitle }}
                                            </p>
                                        @endif

                                        <!-- Chapter Meta -->
                                        <div class="flex items-center space-x-4 text-sm text-gray-400">
                                            @if($chapter->date_from)
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    {{ $chapter->date_from->format('M Y') }}
                                                    @if($chapter->date_to && !$chapter->is_ongoing)
                                                        - {{ $chapter->date_to->format('M Y') }}
                                                    @elseif($chapter->is_ongoing)
                                                        - {{ __('biography.ongoing') }}
                                                    @endif
                                                </span>
                                            @endif

                                            @if($chapter->chapter_type)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#1B365D] text-white">
                                                    {{ __('biography.chapter_type.' . $chapter->chapter_type) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if($isOwn)
                                        <div class="flex items-center space-x-2">
                                            <button onclick="editChapter({{ $chapter->id }})"
                                                    class="p-2 text-gray-400 hover:text-[#D4A574] transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Chapter Content -->
                            <div class="p-6">
                                <div class="prose prose-invert prose-lg max-w-none">
                                    {!! $chapter->content !!}
                                </div>

                                <!-- Chapter Media -->
                                @if($chapter->media && $chapter->media->count() > 0)
                                    <div class="mt-8">
                                        <h4 class="text-lg font-semibold text-white mb-4">
                                            {{ __('biography.media') }}
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @foreach($chapter->media as $media)
                                                <div class="bg-gray-700 rounded-lg overflow-hidden">
                                                    @if($media->type === 'image')
                                                        <img src="{{ $media->url }}" alt="{{ $media->alt_text }}"
                                                             class="w-full h-48 object-cover">
                                                    @elseif($media->type === 'video')
                                                        <video controls class="w-full h-48 object-cover">
                                                            <source src="{{ $media->url }}" type="video/mp4">
                                                            {{ __('biography.video_not_supported') }}
                                                        </video>
                                                    @endif
                                                    @if($media->caption)
                                                        <div class="p-3">
                                                            <p class="text-sm text-gray-300">{{ $media->caption }}</p>
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
            @if($biography->media && $biography->media->count() > 0)
                <div class="mt-12">
                    <h3 class="text-2xl font-semibold text-white mb-6">
                        {{ __('biography.gallery') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($biography->media as $media)
                            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl overflow-hidden">
                                @if($media->type === 'image')
                                    <img src="{{ $media->url }}" alt="{{ $media->alt_text }}"
                                         class="w-full h-64 object-cover">
                                @elseif($media->type === 'video')
                                    <video controls class="w-full h-64 object-cover">
                                        <source src="{{ $media->url }}" type="video/mp4">
                                        {{ __('biography.video_not_supported') }}
                                    </video>
                                @endif
                                @if($media->caption)
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
            @if($isOwn)
                <div class="mt-12 flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('biography.manage') }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-[#1B365D] text-white font-semibold rounded-lg hover:bg-[#2D5016] transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        {{ __('biography.edit_biography') }}
                    </a>

                    <button onclick="shareBiography()"
                            class="inline-flex items-center justify-center px-6 py-3 bg-[#2D5016] text-white font-semibold rounded-lg hover:bg-[#1B365D] transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        {{ __('biography.share') }}
                    </button>
                </div>
            @endif
        </div>
    @else
        <!-- No Biography State -->
        <div class="min-h-screen flex items-center justify-center">
            <div class="text-center">
                <div class="mx-auto w-24 h-24 bg-gray-800 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-[#D4A574]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-semibold text-white mb-4">
                    {{ __('biography.view.no_biography_title') }}
                </h2>
                <p class="text-gray-400 mb-8 max-w-md mx-auto">
                    {{ __('biography.view.no_biography_description') }}
                </p>
                @if($isOwn)
                    <a href="{{ route('biography.manage') }}"
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#D4A574] to-[#E6B885] text-gray-900 font-semibold rounded-lg shadow-lg hover:from-[#E6B885] hover:to-[#D4A574] transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ __('biography.create_first') }}
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function editChapter(chapterId) {
    // Redirect to chapter editor
    window.location.href = `/biography/manage?chapter=${chapterId}`;
}

function shareBiography() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $biography->title ?? "" }}',
            text: '{{ $biography->excerpt ?? "" }}',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('{{ __("biography.link_copied") }}');
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

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
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
