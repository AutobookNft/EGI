{{-- Navigation Script con Link Nascosti per Pagina Corrente --}}
@php
    $navLinkClasses = $isMobile
        ? 'text-gray-300 hover:bg-gray-800 hover:text-emerald-400 block px-3 py-2.5 rounded-md text-base font-medium transition'
        : 'text-gray-300 hover:text-emerald-400 transition px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800/40';
@endphp

{{-- Home Link - Su mobile sempre visibile, su desktop solo se non siamo in home --}}
@if ($isMobile || !(request()->routeIs('home') || request()->is('/')))
    <a href="{{ url('/') }}" class="{{ $navLinkClasses }}"
        aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'home_link_aria_label') }}">
        {{ __('guest_layout.home') }}
    </a>
@endif

{{-- Creators Link - Su mobile sempre visibile, su desktop solo se non siamo in creators --}}
@if ($isMobile || !(request()->routeIs('creator.index') || request()->is('/')))
    <a href="{{ url('/creator') }}" class="{{ $navLinkClasses }}"
        aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'home_link_aria_label') }}">
        {{ __('guest_layout.creators') }}
    </a>
@endif

{{-- Collections Link - Su mobile sempre visibile, su desktop solo se non siamo nelle collections --}}
@if ($isMobile || !request()->routeIs('home.collections.*'))
    <a href="{{ route('home.collections.index') }}" id="generic-collections-link-{{ $isMobile ? 'mobile' : 'desktop' }}"
        class="{{ $navLinkClasses }}"
        aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'collections_link_aria_label') }}">
        {{ __('guest_layout.collections') }}
    </a>
@endif

{{-- EPPs Link - Su mobile sempre visibile, su desktop solo se non siamo negli EPPs --}}
@if ($isMobile || !request()->routeIs('epps.*'))
    <a href="{{ route('epps.index') }}" class="{{ $navLinkClasses }}"
        aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'epps_link_aria_label') }}">
        {{ __('guest_layout.epps') }}
    </a>
@endif

@php
    $authType = App\Helpers\FegiAuth::getAuthType(); // 'strong', 'weak', 'guest'
    $user = App\Helpers\FegiAuth::user(); // User object or null
    $canCreateEgi = $user && $user->can('create_egi');
@endphp

{{-- Le mie Collezioni Dropdown - Solo per mobile e solo per utenti loggati --}}
@can('create_egi')
    @if ($isMobile)
        @auth
            <button type="button"
                id="mobile-collection-list-dropdown-button"
                class="{{ $navLinkClasses }} w-full text-left flex items-center justify-between"
                aria-expanded="false" aria-haspopup="true">
                <span class="flex items-center gap-2">
                    <span class="text-base material-symbols-outlined" aria-hidden="true">view_carousel</span>
                    <span>{{ __('collection.my_galleries') }}</span>
                </span>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>
            {{-- Dropdown menu mobile --}}
            <div id="mobile-collection-list-dropdown-menu" class="hidden mt-1 mx-3 mb-2 py-1 bg-gray-800 rounded-md shadow-lg max-h-[40vh] overflow-y-auto border border-gray-700">
                <div id="mobile-collection-list-loading" class="px-4 py-3 text-sm text-center text-gray-400">{{ __('collection.loading_galleries') }}</div>
                <div id="mobile-collection-list-empty" class="hidden px-4 py-3 text-sm text-center text-gray-400">{{ __('collection.no_galleries_found') }} <a href="{{ route('collections.create') }}" class="underline hover:text-emerald-400">{{ __('collection.create_one_question') }}</a></div>
                <div id="mobile-collection-list-error" class="hidden px-4 py-3 text-sm text-center text-red-400">{{ __('collection.error_loading_galleries') }}</div>
            </div>
        @endauth
    @endif

    {{-- Create EGI Button - Sempre visibile, la logica di azione è gestita da JS in base allo stato utente --}}
    <button type="button"
        class="js-create-egi-contextual-button {{ $navLinkClasses }} {{ $isMobile ? 'w-full text-left' : 'inline-flex items-center gap-1' }}"
        data-action="open-create-egi-contextual" data-auth-type="{{ $authType }}"
        aria-label="{{ __('guest_layout.create_egi') }}">
        @if ($isMobile)
            {{-- Versione Mobile - icona + testo allineati a sinistra --}}
            <span class="flex items-center gap-1">
                <svg class="w-4 h-4 js-create-egi-button-icon" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                    <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
                </svg>
                <span class="js-create-egi-button-text">{{ __('guest_layout.create_egi') }}</span>
            </span>
        @else
            {{-- Versione Desktop - layout inline --}}
            <svg class="w-4 h-4 js-create-egi-button-icon" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
            </svg>
            <span class="js-create-egi-button-text">{{ __('guest_layout.create_egi') }}</span>
        @endif
    </button>
@endcan

{{-- Create Collection CTA - Solo se l'utente ha il permesso --}}
@can('create_collection')
    @if ($isMobile)
        {{-- Versione Mobile - button full width --}}
        <button type="button" data-action="open-create-collection-modal"
            class="w-full px-3 py-2.5 text-base font-semibold text-gray-900 transition-all duration-200 border border-transparent rounded-md shadow-sm bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-gray-900 text-left flex items-center"
            aria-label="{{ __('collection.create_new_collection') }}">
            <span class="mr-2 text-sm material-symbols-outlined" aria-hidden="true">add</span>
            <span>{{ __('collection.create_new_collection') }}</span>
        </button>
    @else
        {{-- Versione Desktop - button compatto --}}
        <div class="ml-2">
            <button type="button" data-action="open-create-collection-modal"
                class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 transition-all duration-200 border border-transparent rounded-md shadow-sm font-source-sans bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                aria-label="{{ __('collection.create_new_collection') }}">
                <span class="mr-2 text-sm material-symbols-outlined" aria-hidden="true">add</span>
                <span class="hidden sm:inline">{{ __('collection.create_new_collection') }}</span>
                <span class="sm:hidden">{{ __('collection.new') }}</span>
            </button>
        </div>
    @endif
@endcan


