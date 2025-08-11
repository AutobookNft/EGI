<!-- Pulsante Reserve -->
<div class="order-1 md:order-2">
    <button id="reserveButton_{{ $instanceId }}"
            data-egi-id="{{ $hasCollections && $firstCollection ? $firstCollection->id : '' }}"
            data-collection-name="{{ $hasCollections && $firstCollection ? $firstCollection->collection_name : '' }}"
            @if(!($hasCollections && $firstCollection)) disabled @endif
            class="inline-flex items-center justify-center w-full px-6 py-3 text-sm font-bold border border-transparent rounded-md shadow-sm sm:text-base sm:w-auto focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-300
                    {{ $hasCollections && $firstCollection ? 'text-gray-900 bg-florence-gold hover:bg-florence-gold-dark focus:ring-florence-gold-light' : 'text-gray-500 bg-gray-300 cursor-not-allowed' }}
                    font-body reserve-button">  {{-- w-full sm:w-auto per mobile full-width --}}
        <span class="mr-2 material-symbols-outlined">bookmark_add</span>
        {{ $hasCollections && $firstCollection ? __('guest_home.reserve_this_egi_now') : __('guest_home.no_egi_available_for_reservation') }}
    </button>
</div>
