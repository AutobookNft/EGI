@props([
    'id',
    'name',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'disabled' => false,
    'required' => false,
    'class' => '',
])

<input
    id="{{ $id }}"
    name="{{ $name }}"
    type="{{ $type }}"
    value="{{ old($name, $value) }}"
    placeholder="{{ $placeholder }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $required ? 'required' : '' }}
    {{ $attributes->merge(['class' => 'block w-full mt-1 rounded-md shadow-sm ' . $class]) }}
>
