<div class="bg-gray-700 hover:bg-gray-650 transition-colors duration-200 p-4 mb-4 rounded-lg shadow-lg">
    <!-- Header con icona di stato -->
    <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-600">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-red-500/20 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-white">Proposal rejected</h3>
        </div>

        <!-- Pulsante di archiviazione -->
        <button
            wire:click="notificationArchive('{{ $notification->id }}', 'declined')"
            class="text-gray-400 hover:text-indigo-400 transition-colors p-1"
            title="Archive notification"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
        </button>
    </div>

    <!-- Contenuto principale -->
    <div class="space-y-3">
        <!-- Messaggio principale -->
        <div class="bg-gray-800/50 p-3 rounded-lg border border-gray-600/30">
            <p class="text-gray-300">{{ $notification->data['message'] }}</p>
        </div>

        <!-- Motivo del rifiuto -->
        <div class="bg-red-500/10 p-3 rounded-lg border border-red-500/20">
            <div class="flex items-start space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-red-100">{{ $notification->data['reason'] }}</p>
            </div>
        </div>

        <!-- Footer info -->
        <div class="flex items-center justify-between text-sm text-gray-400 pt-2">
            <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span>{{ $notification->data['user'] }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $notification->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>
</div>
