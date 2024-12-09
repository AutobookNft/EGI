@props([
    'label',
    'model',
    'id',
    'options' => [],
    'required' => false,
    'class' => '',
    'style' => 'primary', // primary, secondary, accent, info, success, warning, error
    'maxWidth' => 'xs' // xs, sm, md, lg, xl
])

<div>
    <label for="{{ $id }}" class="label text-sm">{{ $label }}</label>
    <select
        wire:model="{{ $model }}"
        id="{{ $id }}"
        class="select select-{{ $style }} max-w-{{ $maxWidth }} {{ $class }}"
        {{ $required ? 'required' : '' }}
    >
        {{ $slot }}
    </select>
    @error($model)
        <span class="text-xs text-red-500">{{ $message }}</span>
    @enderror
</div>
