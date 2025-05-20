@props([
    'gradient' => 'from-purple-400 to-pink-400',
    'borderColor' => 'purple'
])

<div class="p-6 text-center border nft-stat-card rounded-xl border-{{ $borderColor }}-500/20">
    <div class="text-3xl font-bold text-transparent bg-gradient-to-r {{ $gradient }} bg-clip-text md:text-4xl">
        @if ($animate)
            <span data-counter="{{ $value }}">0</span>
        @else
            <span>{{ $formattedValue() }}</span>
        @endif

        @if ($suffix)
            <span class="text-2xl md:text-3xl">{{ $suffix }}</span>
        @endif
    </div>
    <p class="mt-2 text-sm text-gray-400">{{ $label }}</p>
</div>
