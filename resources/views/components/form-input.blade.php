@props([
    'type' => 'text',
    'label',
    'model',
    'datatip',
    'id',
    'required' => false,
    'width_label' => '',
    'width_input' => '',
    'placeholder' => '',
    'style' => 'primary',
    'icon' => null,

])

<div>
    <label class="text-sm label">{{ $label }}</label>
    <label
        class="for={{ $id }} input-{{ $style }} {{ $width_label }} input input-bordered flex items-center gap-2 text-sm w-full"
    >
        @if ($iconHtml)
            <div>{!! $iconHtml !!}</div>
        @endif

        <!-- Div tooltip che si comporta da "contenitore" flessibile -->
        <div class="tooltip tooltip-info flex-1 min-w-0" data-tip="{{ $datatip }}">
            <input
                type="{{ $type }}"
                wire:model="{{ $model }}"
                id="{{ $id }}"
                class="border-0 border-e-orange-800 focus:border-transparent focus:ring-0 {{ $width_input }} text-sm w-full"
                placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}
            >
        </div>
    </label>

    @error($model)
        <span class="text-xs text-red-500">{{ $message }}</span>
    @enderror
</div>

