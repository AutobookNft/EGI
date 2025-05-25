{{-- resources/views/components/creator-cta.blade.php --}}

@props([
    'title' => __('guest_home.are_you_artist_title'),
    'description' => __('guest_home.are_you_artist_description'),
    'buttonText' => __('guest_home.create_your_gallery'),
    'buttonLink' => route('register'),
    'buttonIcon' => 'bolt' // Material icon name o "lightning" per l'icona originale
])

<section class="py-16 text-center bg-gray-50 md:py-20"
    aria-labelledby="creator-cta-heading">
    <div class="container px-4 mx-auto sm:px-6 lg:px-8">
        <h2 id="creator-cta-heading"
            class="mb-4 text-3xl font-bold text-center text-purple-600">
            {{ $title }}
        </h2>
        <p class="max-w-2xl mx-auto mb-10 text-lg text-gray-600">
            {{ $description }}
        </p>

        <a href="{{ $buttonLink }}"
            class="inline-flex items-center px-8 py-4 font-bold text-white transition-colors bg-purple-600 rounded-lg shadow-md hover:bg-purple-700">
            <span>{{ $buttonText }}</span>

            @if($buttonIcon === 'lightning')
                <svg class="w-6 h-6 ml-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            @else
                <span class="ml-3 material-symbols-outlined">{{ $buttonIcon }}</span>
            @endif
        </a>
    </div>
</section>
