@props([
    'userType' => 'creator',
    'limit' => null
])

@php
use App\Models\User;
use App\Models\PaymentDistribution;
use App\Models\Reservation;
use App\Enums\PaymentDistribution\UserTypeEnum;

// Logica diversa per "activator" - prende utenti con qualsiasi prenotazione
if ($userType === 'activator') {
    // Usa la relazione reservations del modello User (qualsiasi prenotazione)
    $allUsers = User::whereHas('reservations')
        ->orderBy('created_at', 'desc');
} else {
    // Comportamento normale per creator e epp
    $allUsers = User::where('usertype', $userType)
        ->orderBy('created_at', 'desc');
}

if ($limit) {
    $allUsers->limit($limit);
}

$users = $allUsers->get();

// Calcola le statistiche separatamente per ogni utente
$usersWithStats = $users->map(function($user) use ($userType) {
    if ($userType === 'activator') {
        // Per gli activator, usa il metodo getCollectorStats() del modello User
        $collectorStats = $user->getCollectorStats();

        // Calcola il totale delle prenotazioni (attive + completate)
        $totalReservations = $user->reservations()->count();

        return [
            'user' => $user,
            'stats' => [
                'total_amount' => $collectorStats['total_spent_eur'] ?? 0,
                'count' => $totalReservations,
                'avg_amount' => $totalReservations > 0 ?
                    ($collectorStats['total_spent_eur'] / $totalReservations) : 0
            ]
        ];
    } else {
        // Comportamento normale per altri tipi
        $statsUserType = $userType;

        $userStats = PaymentDistribution::where('user_id', $user->id)
            ->where('user_type', $statsUserType)
            ->selectRaw('
                COUNT(*) as distributions_count,
                SUM(amount_eur) as total_earnings,
                AVG(amount_eur) as avg_earnings
            ')
            ->first();

        return [
            'user' => $user,
            'stats' => [
                'total_amount' => $userStats->total_earnings ?? 0,
                'count' => $userStats->distributions_count ?? 0,
                'avg_amount' => $userStats->avg_earnings ?? 0
            ]
        ];
    }
});

// Riordina per totale guadagni (se hai statistiche) o per data creazione
$topUsers = $usersWithStats->sortByDesc(function($item) {
    return $item['stats']['total_amount'] > 0 ? $item['stats']['total_amount'] : 0;
});

// Configurazione per ogni user_type
$typeConfig = match($userType) {
    'creator' => [
        'title' => __('statistics.top_creators'),
        'title_all' => __('statistics.all_creators'),
        'icon' => 'palette',
        'color' => 'text-purple-400'
    ],
    'activator' => [
        'title' => __('statistics.top_activators'),
        'title_all' => __('statistics.all_activators'),
        'icon' => 'flash_on',
        'color' => 'text-blue-400'
    ],
    'epp' => [
        'title' => __('statistics.top_epp'),
        'title_all' => __('statistics.all_epp'),
        'icon' => 'eco',
        'color' => 'text-green-400'
    ],
    default => [
        'title' => 'Top Users',
        'title_all' => 'All Users',
        'icon' => 'person',
        'color' => 'text-gray-400'
    ]
};

// ID univoco per evitare conflitti
$instanceId = uniqid();
@endphp

{{-- User List Container --}}
<div class="w-full mt-4 mb-4" id="userList_{{ $instanceId }}">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6 ml-2">
        <h3 class="text-lg font-semibold text-white">
            <span class="{{ $typeConfig['color'] }} material-symbols-outlined text-base mr-1">{{ $typeConfig['icon'] }}</span>
            {{ $limit ? $typeConfig['title'] : $typeConfig['title_all'] }}
        </h3>
        <div class="mr-2 text-sm text-gray-400">
            {{ count($topUsers) }}
            @if($userType === 'activator')
                {{ __('statistics.activators_count') }}
            @else
                {{ __('statistics.' . $userType . '_count') }}
            @endif
            @if($usersWithStats->where('stats.total_amount', '>', 0)->count() > 0)
                ({{ $usersWithStats->where('stats.total_amount', '>', 0)->count() }} {{ __('statistics.with_earnings') }})
            @endif
        </div>
    </div>

    {{-- Lista degli Users con scroll --}}
    @if(count($topUsers) > 0)
        <div class="pr-2 space-y-1 overflow-y-auto max-h-96">
            @foreach($topUsers as $index => $item)
                <x-user-card-list-small
                    :user="$item['user']"
                    :userType="$userType"
                    :stats="$item['stats']"
                    :rank="$limit ? ($index + 1) : null"
                />
            @endforeach
        </div>
    @else
        <div class="py-12 text-center text-gray-400">
            <span class="w-16 h-16 mx-auto mb-4 opacity-50 {{ $typeConfig['color'] }} material-symbols-outlined text-6xl">
                {{ $typeConfig['icon'] }}
            </span>
            <p class="text-lg">{{ __('statistics.no_users_data') }}</p>
            <p class="text-sm">{{ __('statistics.users_coming_soon') }}</p>
        </div>
    @endif
</div>

{{-- Stili CSS per scroll personalizzato --}}
<style>
/* Custom scrollbar per la lista */
#userList_{{ $instanceId }} .max-h-96 {
    scrollbar-width: thin;
    scrollbar-color: #4B5563 transparent;
}

#userList_{{ $instanceId }} .max-h-96::-webkit-scrollbar {
    width: 6px;
}

#userList_{{ $instanceId }} .max-h-96::-webkit-scrollbar-track {
    background: transparent;
}

#userList_{{ $instanceId }} .max-h-96::-webkit-scrollbar-thumb {
    background-color: #4B5563;
    border-radius: 3px;
}

#userList_{{ $instanceId }} .max-h-96::-webkit-scrollbar-thumb:hover {
    background-color: #6B7280;
}

/* Smooth scroll */
#userList_{{ $instanceId }} .max-h-96 {
    scroll-behavior: smooth;
}

/* Hover effects per le card */
#userList_{{ $instanceId }} .space-y-1 > * {
    transition: transform 0.2s ease;
}

#userList_{{ $instanceId }} .space-y-1 > *:hover {
    transform: translateX(4px);
}
</style>
