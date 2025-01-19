<div class="bg-gray-700 p-4 mb-4 rounded-lg shadow-lg">
    <!-- Header più compatto -->
    <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-600">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10A8 8 0 11.997 10 8 8 0 0118 10zm-8 4a1 1 0 100-2 1 1 0 000 2zm-1-8a1 1 0 012 0v4a1 1 0 01-2 0V6z" clip-rule="evenodd" />
            </svg>
            <h3 class="text-lg font-bold text-white">{{ __('collection.wallet.proposal_rejected') }}</h3>
        </div>
        <button
            wire:click="notificationArchive('{{ $notification->id }}', 'declined')"
            class="text-indigo-400 hover:text-indigo-300 transition-colors"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
        </button>
    </div>

    <!-- Contenuto principale più compatto -->
    <div class="space-y-3">
        <!-- Proposta -->
        <div class="bg-gray-800 p-3 rounded">
            <p class="text-sm text-gray-300">{{ $notification->data['message'] ?? __('No message provided.') }}</p>
        </div>

        <!-- Risposta -->
        <div class="bg-indigo-900 bg-opacity-50 p-3 rounded">
            <p class="text-sm text-white">{{ $notification->data['reason'] ?? __('No reason provided.') }}</p>
        </div>

        <!-- Meta info -->
        <div class="flex justify-between items-center text-xs text-gray-400">
            <p class="flex items-center">
                <svg class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" />
                </svg>
                {{ $notification->data['user'] ?? __('Unknown user.') }}
            </p>
            <p class="flex items-center">
                <svg class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
                {{ $notification->created_at->diffForHumans() }}
            </p>
        </div>
    </div>
</div>
