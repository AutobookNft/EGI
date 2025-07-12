{{-- Navigation Script con Link Nascosti per Pagina Corrente --}}
@php
    $navLinkClasses = $isMobile
        ? 'text-gray-300 hover:bg-gray-800 hover:text-emerald-400 block px-3 py-2.5 rounded-md text-base font-medium transition'
        : 'text-gray-300 hover:text-emerald-400 transition px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800/40';
@endphp

{{-- Home Link - Solo se NON siamo in home --}}
@unless (request()->routeIs('home') || request()->is('/'))
    <a href="{{ url('/') }}" class="{{ $navLinkClasses }}"
        aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'home_link_aria_label') }}">
        {{ __('guest_layout.home') }}
    </a>
@endunless

{{-- Home Link - Solo se NON siamo in creators --}}
@unless (request()->routeIs('creator.index') || request()->is('/'))
    <a href="{{ url('/creator') }}" class="{{ $navLinkClasses }}"
        aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'home_link_aria_label') }}">
        {{ __('guest_layout.creators') }}
    </a>
@endunless

{{-- Collections Link - Solo se NON siamo nelle pagine collections --}}
@unless (request()->routeIs('home.collections.*'))
    <a href="{{ route('home.collections.index') }}" id="generic-collections-link-{{ $isMobile ? 'mobile' : 'desktop' }}"
        class="{{ $navLinkClasses }}"
        aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'collections_link_aria_label') }}">
        {{ __('guest_layout.collections') }}
    </a>
@endunless

{{-- EPPs Link - Solo se NON siamo nelle pagine EPPs --}}
@unless (request()->routeIs('epps.*'))
    <a href="{{ route('epps.index') }}" class="{{ $navLinkClasses }}"
        aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'epps_link_aria_label') }}">
        {{ __('guest_layout.epps') }}
    </a>
@endunless

@php
    $authType = App\Helpers\FegiAuth::getAuthType(); // 'strong', 'weak', 'guest'
@endphp
{{-- Create EGI Button - Sempre visibile, la logica di azione è gestita da JS in base allo stato utente --}}
<button type="button"
    class="js-create-egi-contextual-button {{ $navLinkClasses }} {{ $isMobile ? 'w-full text-left' : 'inline-flex items-center gap-1' }}"
    data-action="open-create-egi-contextual" data-auth-type="{{ $authType }}"
    aria-label="{{ __('guest_layout.create_egi') }}">
    @unless ($isMobile)
        <svg class="js-create-egi-button-icon h-4 w-4" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
            <path
                d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
        </svg>
    @endunless
    <span class="js-create-egi-button-text">
        {{ __('guest_layout.create_egi') }}
    </span>
</button>

{{-- Create Collection CTA - Solo se l'utente ha il permesso --}}
@can('create_collection')
    <div class="ml-2">
        <button type="button" data-action="open-create-collection-modal"
            class="font-source-sans inline-flex items-center rounded-md border border-transparent bg-gradient-to-r from-yellow-400 to-yellow-500 px-4 py-2 text-sm font-medium font-semibold text-gray-900 shadow-sm transition-all duration-200 hover:from-yellow-500 hover:to-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-gray-900"
            aria-label="{{ __('collection.create_new_collection') }}">
            <span class="material-symbols-outlined mr-2 text-sm" aria-hidden="true">add</span>
            <span class="hidden sm:inline">{{ __('collection.create_new_collection') }}</span>
            <span class="sm:hidden">{{ __('collection.new') }}</span>
        </button>
    </div>
@endcan

{{-- Butler Assistant Menu --}}
<li>
    <button type="button" id="open-butler-assistant"
        class="{{ $navLinkClasses }} flex items-center gap-1 align-middle" style="padding: 0 0.75rem; height: 100%;"
        aria-label="{{ __('assistant.open_butler_aria') }}">
        <span class="material-symbols-outlined align-middle" aria-hidden="true"
            style="font-size: 1.1em; vertical-align: middle;">support_agent</span>
        <span class="align-middle" style="vertical-align: middle;">{{ __('assistant.open_butler') }}</span>
    </button>
</li>
