{{--
    Lazy Image Component - Optimized for performance
    Usage: <x-lazy-image src="path/to/image.jpg" alt="Description" class="custom-classes" />
--}}

@props([
    'src' => null,
    'alt' => '',
    'class' => '',
    'width' => null,
    'height' => null,
    'priority' => false, // true for critical images (navbar, hero)
    'placeholder' => null, // Custom placeholder image
    'objectFit' => 'cover', // cover, contain, fill, etc.
    'sizes' => null, // Responsive sizes attribute
    'srcset' => null, // Responsive srcset
    'loading' => 'lazy', // lazy, eager, auto
    'decoding' => 'async', // async, sync, auto
    'fetchpriority' => null // high, low, auto
])

@php
    // Determine if this is a critical image that should load immediately
    $isCritical = $priority || $loading === 'eager';

    // Generate placeholder if not provided
    if (!$placeholder) {
        $placeholder = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 " .
                      ($width ?: '400') . " " . ($height ?: '300') . "'%3E" .
                      "%3Crect width='100%25' height='100%25' fill='%23374151'/%3E" .
                      "%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='%23666' font-size='14'%3ELoading...%3C/text%3E" .
                      "%3C/svg%3E";
    }

    // Build CSS classes
    $classes = collect([
        'transition-opacity duration-300 ease-in-out',
        $isCritical ? 'navbar-critical' : 'lazy-image opacity-0',
        'object-' . $objectFit,
        $class
    ])->filter()->implode(' ');

    // Determine loading strategy
    $loadingStrategy = $isCritical ? 'eager' : $loading;
    $dataAttributes = $isCritical ? [] : ['data-lazy' => $src];

    // Responsive attributes
    $responsiveAttrs = [];
    if ($sizes) $responsiveAttrs['sizes'] = $sizes;
    if ($srcset) $responsiveAttrs['srcset'] = $srcset;

    // Performance attributes
    $perfAttrs = [
        'loading' => $loadingStrategy,
        'decoding' => $decoding
    ];
    if ($fetchpriority) $perfAttrs['fetchpriority'] = $fetchpriority;
@endphp

@if($isCritical)
    {{-- Critical image - load immediately --}}
    <img
        src="{{ $src }}"
        alt="{{ $alt }}"
        class="{{ $classes }}"
        @if($width) width="{{ $width }}" @endif
        @if($height) height="{{ $height }}" @endif
        @foreach($responsiveAttrs as $attr => $value)
            {{ $attr }}="{{ $value }}"
        @endforeach
        @foreach($perfAttrs as $attr => $value)
            {{ $attr }}="{{ $value }}"
        @endforeach
        {{ $attributes->except(['src', 'alt', 'class', 'width', 'height', 'priority', 'placeholder', 'objectFit', 'sizes', 'srcset', 'loading', 'decoding', 'fetchpriority']) }}
    />
@else
    {{-- Lazy loaded image --}}
    <img
        src="{{ $placeholder }}"
        data-lazy="{{ $src }}"
        alt="{{ $alt }}"
        class="{{ $classes }}"
        @if($width) width="{{ $width }}" @endif
        @if($height) height="{{ $height }}" @endif
        @foreach($responsiveAttrs as $attr => $value)
            data-lazy-{{ $attr }}="{{ $value }}"
        @endforeach
        @foreach($perfAttrs as $attr => $value)
            {{ $attr }}="{{ $value }}"
        @endforeach
        {{ $attributes->except(['src', 'alt', 'class', 'width', 'height', 'priority', 'placeholder', 'objectFit', 'sizes', 'srcset', 'loading', 'decoding', 'fetchpriority']) }}
    />
@endif

{{-- Optional: Preload hint for critical images --}}
@if($isCritical && $fetchpriority === 'high')
    @push('head')
        <link rel="preload" as="image" href="{{ $src }}"
              @if($sizes) imagesizes="{{ $sizes }}" @endif
              @if($srcset) imagesrcset="{{ $srcset }}" @endif>
    @endpush
@endif

<style>
    .lazy-image.loaded {
        opacity: 1;
    }

    .lazy-image.error {
        opacity: 0.5;
        filter: grayscale(100%);
    }

    .lazy-image.error::after {
        content: "⚠️ Failed to load";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 12px;
        color: #666;
        background: rgba(0,0,0,0.8);
        padding: 4px 8px;
        border-radius: 4px;
    }
</style>
