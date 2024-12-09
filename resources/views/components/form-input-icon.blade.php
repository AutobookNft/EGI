@props([
    'type' => 'text',
    'label' => '',
    'model' => '',
    'id' => '',
    'required' => false,
    'class' => '',
    'placeholder' => '',
    'icon' => '',
    'iconPosition' => 'right',
    'value' => ''
])

<div>
    @if($label)
        <label for="{{ $id }}" class="block mb-1 text-sm font-medium">{{ $label }}</label>
    @endif

    <label class="input input-bordered flex items-center gap-2 {{ $class }}">
        @if($iconPosition === 'left')
            @include("components.icons.$icon")
        @endif

        <input
            type="{{ $type }}"
            @if($model) wire:model="{{ $model }}" @endif
            id="{{ $id }}"
            class="grow"
            placeholder="{{ $placeholder }}"
            @if($value) value="{{ $value }}" @endif
            {{ $required ? 'required' : '' }}
        >

        @if($iconPosition === 'right')
            @include("components.icons.$icon")
        @endif
    </label>

    @error($model)
        <span class="text-xs text-red-500">{{ $message }}</span>
    @enderror
</div> 
