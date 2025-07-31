{{-- resources/views/components/creator-card.blade.php --}}
{{--
 * @package App\View\Components
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI - Creator Card Blur Mobile Only)
 * @date 2025-07-31
 * @purpose Creator card with Blur-style mobile layout, desktop unchanged
 --}}

@props(['creator', 'imageType' => 'card', 'displayType' => 'default'])

@php
    $logo = config('app.logo');
    $imageUrl = '';

    if ($creator) {
        if ($creator->profile_photo_url) {
            $imageUrl = $creator->profile_photo_url;
        } else {
            $imageUrl = asset("images/logo/$logo");
        }
    } else {
        $imageUrl = asset("images/logo/$logo");
    }
@endphp

@if ($creator)
    {{-- NFT-STYLE CARD (Blur-inspired) --}}
    <a href="{{ route('creator.home', ['id' => $creator->id]) }}"
        class="group block w-full overflow-hidden rounded-xl bg-gray-900 shadow-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl"
        aria-label="{{ sprintf(__('View creator profile %s'), $creator->name) }}">

        {{-- Creator Image Section --}}
        <div class="relative aspect-square w-full bg-gradient-to-br from-gray-800 to-gray-900">
            <img src="{{ $imageUrl }}" alt="{{ $creator->name }}"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy"
                decoding="async">

            {{-- Corner Badge "C" --}}
            <div class="absolute left-3 top-3">
                <span
                    class="flex h-7 w-7 items-center justify-center rounded-full bg-orange-500 text-sm font-bold text-white shadow-lg">
                    C
                </span>
            </div>

            {{-- Creator ID --}}
            <div class="absolute right-3 top-3">
                <span class="rounded-lg bg-black bg-opacity-75 px-2 py-1 text-sm font-bold text-white backdrop-blur-sm">
                    #{{ $creator->id }}
                </span>
            </div>
        </div>

        {{-- Info Section --}}
        <div class="bg-gray-800 p-4">
            {{-- Creator Name --}}
            <h3
                class="mb-3 truncate text-lg font-bold text-white transition-colors duration-200 group-hover:text-orange-400">
                {{ $creator->name }}
            </h3>

            {{-- Price Section (NFT-style) --}}
            <div class="flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-xs uppercase tracking-wide text-gray-400">CREATOR</span>
                    <span class="text-sm font-semibold text-white">
                        {{ $creator->collections_count ?? 0 }} ⚡
                    </span>
                </div>

                <div class="flex flex-col text-right">
                    <span class="text-xs uppercase tracking-wide text-gray-400">WORKS</span>
                    <span class="text-sm font-semibold text-white">
                        {{ $creator->artworks_count ?? 0 }} 🎨
                    </span>
                </div>
            </div>
        </div>
    </a>
@else
    <div class="flex h-full w-full items-center justify-center rounded-xl bg-gray-800 p-4 text-center text-gray-500">
        {{ __('Creator data not available.') }}
    </div>
@endif
