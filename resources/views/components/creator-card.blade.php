{{-- resources/views/components/creator-card.blade.php --}}
@props(['creator'])

<div class="flex flex-col h-full overflow-hidden transition-shadow duration-300 bg-gray-800 shadow-lg rounded-2xl hover:shadow-2xl">
    <figure class="flex items-center justify-center w-full h-48 overflow-hidden bg-gray-900">
        @if($creator->profile_image) {{-- Assumiamo che esista un campo profile_image nel modello User --}}
            <img
                src="{{ $creator->profile_image }}"
                alt="{{ $creator->name }}"
                class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-105"
            >
        @else
            <div class="flex items-center justify-center w-full h-full bg-gradient-to-r from-green-500 to-teal-600">
                <span class="text-6xl text-white opacity-50 material-symbols-outlined">person</span> {{-- Icona generica --}}
            </div>
        @endif
    </figure>

    <div class="flex flex-col justify-between flex-grow p-4">
        <h2 class="mb-2 text-xl font-bold text-white">
            {{ $creator->name }}
        </h2>
        @if($creator->bio) {{-- Assumiamo esista un campo bio nel modello User --}}
            <p class="mb-4 text-sm text-gray-400 line-clamp-2">
                {{ Str::limit($creator->bio, 100) }}
            </p>
        @else
             <p class="mb-4 text-sm text-gray-400 line-clamp-2">
                {{ __('guest_home.creator_default_bio') }}
            </p>
        @endif

        <div class="flex items-center justify-between mt-auto">
            <span class="px-4 py-3 text-white rounded-full badge bg-gradient-to-r from-yellow-500 to-orange-500">
                Creator
            </span>
            <span class="text-xs text-gray-500">ID: {{ $creator->id }}</span>
        </div>

        <div class="flex justify-end mt-4 space-x-2">
            <a href="{{ route('creator.home', ['id' => $creator->id]) }}" class="btn btn-primary btn-sm">
                {{ __('guest_home.view_profile') }}
            </a>
        </div>
    </div>
</div>
