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

{{-- Dashboard Link - Solo per utenti autenticati --}}
@auth
    <a href="{{ route('dashboard') }}" class="{{ $navLinkClasses }}"
        aria-label="{{ __('Dashboard') }}">
        {{ __('Dashboard') }}
    </a>
@endauth

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
@endphp
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

{{-- Butler Assistant Menu --}}
<button type="button"
    id="open-butler-assistant"
    class="{{ $navLinkClasses }} {{ $isMobile ? 'w-full text-left' : '' }} flex items-center gap-1"
    aria-label="{{ __('assistant.open_butler_aria') }}">
    <span class="material-symbols-outlined" aria-hidden="true" style="font-size: 1.1em;">support_agent</span>
    <span>{{ __('assistant.open_butler') }}</span>
</button>
