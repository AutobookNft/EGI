@php
    $navLinkClasses = $isMobile
        ? 'text-gray-300 hover:bg-gray-800 hover:text-emerald-400 block px-3 py-2.5 rounded-md text-base font-medium transition'
        : 'text-gray-300 hover:text-emerald-400 transition px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-800/40';
@endphp

@unless(request()->routeIs('home') || request()->is('/'))
    <a href="{{ url('/') }}" class="{{ $navLinkClasses }}" aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'home_link_aria_label') }}">{{ __('guest_layout.home') }}</a>
@endunless
<a href="{{ route('home.collections.index') }}" id="generic-collections-link-{{ $isMobile ? 'mobile' : 'desktop' }}" class="{{ $navLinkClasses }} @if(request()->routeIs('home.collections.*')) text-emerald-400 font-semibold aria-current="page" @endif" aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'collections_link_aria_label') }}">{{ __('guest_layout.collections') }}</a>
<a href="{{ route('epps.index') }}" class="{{ $navLinkClasses }} @if(request()->routeIs('epps.*')) text-emerald-400 font-semibold aria-current="page" @endif" aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'epps_link_aria_label') }}">{{ __('guest_layout.epps') }}</a>
<button type="button" data-action="open-connect-modal-or-create-egi" class="{{ $navLinkClasses }} {{ $isMobile ? 'w-full text-left' : 'flex items-center gap-1' }}" aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'create_egi_aria_label') }}">
    @unless($isMobile)
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
            <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
        </svg>
    @endunless
    {{ __('guest_layout.create_egi') }}
</button>
<button type="button" data-action="open-connect-modal-or-create-collection" class="{{ $navLinkClasses }} {{ $isMobile ? 'w-full text-left' : 'flex items-center gap-1' }}" aria-label="{{ __('guest_layout.' . ($isMobile ? 'mobile_' : '') . 'create_collection_aria_label') }}">
    @unless($isMobile)
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
            <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
        </svg>
    @endunless
    {{ __('guest_layout.create_collection') }}
</button>
