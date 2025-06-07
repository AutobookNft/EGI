@props([
    'type' => 'button',
    'class' => '',
])

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' =>
        'inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-2xl
        font-semibold text-xs text-white uppercase tracking-widest shadow-sm
        hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500
        focus:ring-offset-2 transition ease-in-out duration-150 ' . $class
    ]) }}
>
    {{ $slot }}
</button>
