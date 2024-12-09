<button
    type="{{ $type }}"
    {{ $attributes->merge([
        'class' => 'btn btn-' . $style .
                  ($size ? ' btn-' . $size : '') .
                  ' transition-transform duration-150 transform shadow-lg' .
                  ' active:shadow-md active:translate-y-1 hover:scale-105' .
                  ' focus:outline-none focus:ring-2 focus:ring-' . $style . ' focus:ring-opacity-50' .
                  ' ' . $class
    ]) }}
>
    {{ $slot }}
</button> 
