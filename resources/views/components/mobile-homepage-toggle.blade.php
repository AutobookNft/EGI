{{-- resources/views/components/mobile-homepage-toggle.blade.php --}}
{{--
* @package App\View\Components
* @author AI Assistant for Fabio Cherici
* @version 2.0.0 (FlorenceEGI - Mobile Carousel Component - Simplified)
* @date 2025-01-19
* @purpose Mobile carousel component (toggle removed for cleaner UX)
--}}

@props([
'egis' => collect(),
'creators' => collect(),
'collections' => collect(),
'collectors' => collect()
])

<div class="lg:hidden">
    {{-- Simplified Header --}}
    {{-- <div class="py-6 bg-gradient-to-br from-gray-900 via-gray-800 to-black">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-6 text-center">
                <h2 class="mb-3 text-2xl font-bold text-white">
                    🎯 <span class="text-transparent bg-gradient-to-r from-purple-400 to-blue-500 bg-clip-text">
                        {{ __('egi.mobile_toggle.title') }}
                    </span>
                </h2>
                <p class="text-gray-300">
                    {{ __('egi.mobile_toggle.subtitle') }}
                </p>
            </div>
        </div>
    </div> --}}

    {{-- Carousel Content Only --}}
    <div id="mobile-content-container">
        {{-- <x-homepage-egi-carousel :egis="$egis" :creators="$creators" :collections="$collections":collectors="$collectors" /> --}}
        <x-collection-list :collections="$collections" />
    </div>
</div>
