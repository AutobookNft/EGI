{{-- resources/views/egis/partials/sidebar/collection-link-section.blade.php --}}
{{-- 
    Sezione link alla collezione dell'EGI
    ORIGINE: righe 155-166 di show.blade.php
    VARIABILI: $collection
--}}

{{-- Collection Link --}}
<div class="pt-6 border-t border-gray-700/50">
    <a href="{{ route('home.collections.show', $collection->id) }}"
        class="inline-flex items-center text-gray-300 transition-colors duration-200 hover:text-white group">
        <svg class="w-4 h-4 mr-2 transition-transform duration-200 group-hover:-translate-x-1"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        {{ __('egi.view_full_collection') }}
    </a>
</div>
