@props([
    'for',
    'value',
    'class' => '',
])

<label for="{{ $for }}" {{ $attributes->merge(['class' => 'block text-sm font-medium text-gray-700 ' . $class]) }}>
    {{ $value ?? $slot }}
</label>
