@props(['format' => 'full', 'textColor' => 'cyan'])

@php
    // Determina le classi di stile in base al formato
    $containerClasses = match($format) {
        'compact' => 'flex items-center gap-1.5 text-sm',
        'footer' => 'text-sm text-gray-400',
        'card-stats' => 'flex justify-end w-full gap-4 py-2 mt-3 text-sm border-t border-b',
        'natan-badge' => 'flex items-center justify-center flex-wrap gap-x-4 gap-y-2 mt-2 text-sm',
        'equilibrium' => 'flex items-center justify-center gap-2 mt-4 opacity-90',
        'banner' => 'w-full py-1 bg-gradient-to-r from-emerald-900/90 to-emerald-800/90 border-b border-emerald-700/50 hidden md:block',
        default => 'flex items-center justify-center gap-2 mt-4 opacity-90',
    };

    $valueClasses = match($format) {
        'compact' => "text-sm font-medium text-{$textColor}-300",
        'footer' => 'text-gray-300 font-semibold',
        'card-stats' => "font-semibold text-{$textColor}-300",
        'natan-badge' => 'text-white/70',
        'equilibrium' => "text-xs text-{$textColor}-300 font-mono",
        'banner' => "text-{$textColor}-300 tabular-nums",
        default => "text-xs text-{$textColor}-300 font-mono",
    };

    // Classe per il bordo che dipende dal colore del testo
    $borderClass = "border-{$textColor}-500/20";

    // Determina la formattazione del numero in base al formato
    $decimals = $format === 'footer' ? 0 : 2;
@endphp

@if ($format === 'full')
    <div class="{{ $containerClasses }}">
        <div class="h-1.5 w-1.5 rounded-full bg-blue-400 animate-pulse"></div>
        <span class="{{ $valueClasses }}">
            <span class="tabular-nums" id="impact-realtime-counter">{{ $formattedTotal($decimals) }}</span> kg di plastica recuperati dagli oceani
        </span>
    </div>
@elseif ($format === 'compact')
    <div class="{{ $containerClasses }}">
        <div class="w-1 h-1 bg-blue-400 rounded-full animate-pulse"></div>
        <span class="{{ $valueClasses }}">{{ $formattedTotal($decimals) }} kg</span> di plastica recuperati
    </div>
@elseif ($format === 'footer')
    <span class="{{ $containerClasses }}">
        {{ __('guest_layout.total_plastic_recovered') }}:
        <strong class="{{ $valueClasses }}">{{ $formattedTotal($decimals) }} Kg</strong>
    </span>
@elseif ($format === 'card-stats')
    <div class="{{ $containerClasses }} {{ $borderClass }}">
        <div class="flex flex-col items-end">
            <span class="text-white/60 text-[11px]">Total Impact</span>
            <span class="{{ $valueClasses }}">{{ $formattedTotal($decimals) }} Kg</span>
        </div>
        <div class="flex flex-col items-end">
            <span class="text-white/60 text-[11px]">Active Projects</span>
            <span class="{{ $valueClasses }}">{{ $activeProjects }}</span>
        </div>
    </div>
@elseif ($format === 'natan-badge')
    <div class="{{ $containerClasses }}">
        <div class="flex items-center">
            <span class="mr-1 text-cyan-400 nft-pulse">◉</span>
            <span class="{{ $valueClasses }}">Items: {{ $totalItems }}</span>
        </div>
        <div class="flex items-center">
            <span class="mr-1 text-emerald-400 nft-pulse" style="animation-delay: 0.5s">◉</span>
            <span class="{{ $valueClasses }}">Owners: {{ $totalOwners }}</span>
        </div>
        <div class="flex items-center">
            <span class="mr-1 text-purple-400 nft-pulse" style="animation-delay: 0.25s">◉</span>
            <span class="{{ $valueClasses }}">Collections: {{ $totalCollections }}</span>
        </div>
    </div>
@elseif ($format === 'equilibrium')
    <div class="{{ $containerClasses }}">
        <div class="h-1.5 w-1.5 rounded-full bg-green-400 animate-pulse"></div>
        <span class="{{ $valueClasses }}">
            <span class="font-bold tabular-nums">{{ $formattedEquilibrium($decimals) }}</span> € di Equilibrium generato
        </span>
    </div>
@elseif ($format === 'reservations')
    <div class="{{ $containerClasses }}">
        <div class="flex flex-col items-start">
            <span class="text-white/60 text-[11px]">Prenotazioni Totali</span>
            <span class="{{ $valueClasses }}">{{ $formattedReservations('total', $decimals) }} €</span>
        </div>
        <div class="flex flex-col items-start">
            <span class="text-white/60 text-[11px]">Weak</span>
            <span class="{{ $valueClasses }}">{{ $formattedReservations('weak', $decimals) }} €</span>
        </div>
        <div class="flex flex-col items-start">
            <span class="text-white/60 text-[11px]">Strong</span>
            <span class="{{ $valueClasses }}">{{ $formattedReservations('strong', $decimals) }} €</span>
        </div>
    </div>
@elseif ($format === 'banner')
    <div class="{{ $containerClasses }} environmental-stats-banner">
        <div class="container flex flex-wrap items-center justify-center gap-6 px-4 mx-auto text-xs font-medium sm:gap-8 md:gap-10 lg:gap-12 sm:text-sm">
            <div class="flex items-center gap-1.5 text-emerald-300">
                <span class="text-base material-symbols-outlined">waves</span>
                <span class="{{ $valueClasses }}">{{ $formattedTotal($decimals) }} kg</span>
                <span class="text-emerald-200/80">plastica rimossa</span>
            </div>

            <div class="flex items-center gap-1.5 text-emerald-300">
                <span class="text-base material-symbols-outlined">forest</span>
                <span class="{{ $valueClasses }}">{{ $activeProjects }}</span>
                <span class="text-emerald-200/80">progetti attivi</span>
            </div>

            <div class="flex items-center gap-1.5 text-emerald-300">
                <span class="text-base material-symbols-outlined">eco</span>
                <span class="{{ $valueClasses }}">25%</span>
                <span class="text-emerald-200/80">destinato all'ambiente</span>
            </div>

            <div class="flex items-center gap-1.5 text-violet-300">
                <span class="text-base material-symbols-outlined">grid_view</span>
                <span class="{{ $valueClasses }}">{{ $totalItems }}</span>
                <span class="text-violet-200/80">EGI creati</span>
            </div>

            <div class="flex items-center gap-1.5 text-blue-300">
                <span class="text-base material-symbols-outlined">people</span>
                <span class="{{ $valueClasses }}">{{ $totalOwners }}</span>
                <span class="text-blue-200/80">collezionisti</span>
            </div>

            <div class="flex items-center gap-1.5 text-amber-300">
                <span class="text-base material-symbols-outlined">account_balance_wallet</span>
                <span class="{{ $valueClasses }}">{{ $formattedEquilibrium($decimals) }} €</span>
                <span class="text-amber-200/80">equilibrium generato</span>
            </div>
        </div>
    </div>
@endif
