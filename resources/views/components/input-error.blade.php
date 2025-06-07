@props([
    'messages' => [],
    'class' => '',
])
@if ($messages)
    <span {{ $attributes->merge(['class' => 'text-xs text-red-500 ' . $class]) }}>
        {{ is_array($messages) ? implode(', ', $messages) : $messages }}
    </span>
@endif
