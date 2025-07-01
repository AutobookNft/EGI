{{-- Navigation Script con Link Nascosti per Pagina Corrente --}}
@php
    $navLinkClasses = $isMobile
        ? 'text-gray-300 hover:bg-gray-800 hover:text-emerald-400 block px-3 py-2.5 rounded-md text-base font-medium transition'
        : 'text-gray-300 hover:text-emerald-400 transition px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800/40';
@endphp

{{-- Home Link - Solo se NON siamo in home --}}
@unless(request()->routeIs('home') || request()->is('/'))
    <a href="{{ url('/') }}" class="{{ $navLinkClasses }}" aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'home_link_aria_label') }}">
        {{ __('guest_layout.home') }}
    </a>
@endunless

{{-- Home Link - Solo se NON siamo in creators --}}
@unless(request()->routeIs('creator.index') || request()->is('/'))
    <a href="{{ url('/creator') }}" class="{{ $navLinkClasses }}" aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'home_link_aria_label') }}">
        {{ __('guest_layout.creators') }}
    </a>
@endunless

{{-- Collections Link - Solo se NON siamo nelle pagine collections --}}
@unless(request()->routeIs('home.collections.*'))
    <a href="{{ route('home.collections.index') }}"
       id="generic-collections-link-{{ $isMobile ? 'mobile' : 'desktop' }}"
       class="{{ $navLinkClasses }}"
       aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'collections_link_aria_label') }}">
        {{ __('guest_layout.collections') }}
    </a>
@endunless

{{-- EPPs Link - Solo se NON siamo nelle pagine EPPs --}}
@unless(request()->routeIs('epps.*'))
    <a href="{{ route('epps.index') }}"
       class="{{ $navLinkClasses }}"
       aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'epps_link_aria_label') }}">
        {{ __('guest_layout.epps') }}
    </a>
@endunless

{{-- Create EGI Button - Sempre visibile (oppure aggiungi condizione se vuoi) --}}
<button type="button"
        data-action="open-connect-modal-or-create-egi"
        class="{{ $navLinkClasses }} {{ $isMobile ? 'w-full text-left' : 'flex items-center gap-1' }}"
        aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'create_egi_aria_label') }}">
    @unless($isMobile)
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
            <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
        </svg>
    @endunless
    {{ __('guest_layout.create_egi') }}
</button>

{{-- Create Collection CTA - Solo se l'utente ha il permesso --}}
@can('create_collection')
    <div class="ml-2">
        <button type="button"
                data-action="open-create-collection-modal"
                class="inline-flex items-center px-4 py-2 text-sm font-medium font-semibold text-gray-900 transition-all duration-200 border border-transparent rounded-md shadow-sm bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-gray-900 font-source-sans"
                aria-label="{{ __('collection.create_new_collection') }}">
            <span class="mr-2 text-sm material-symbols-outlined" aria-hidden="true">add</span>
            <span class="hidden sm:inline">{{ __('collection.create_new_collection') }}</span>
            <span class="sm:hidden">{{ __('collection.new') }}</span>
        </button>
    </div>
@endcan
