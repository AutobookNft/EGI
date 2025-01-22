<div class="{{ $notification->outcome === 'rejected' ? 'bg-red-600' : 'bg-emerald-600' }} p-6 mb-4 rounded-lg shadow-lg">
    <!-- Header con icona -->
    <div class="flex items-center mb-3">
        <div class="{{ $notification->outcome === 'rejected' ? 'bg-red-500' : 'bg-emerald-500' }} p-2 rounded-full mr-3">
            @if($notification->outcome === 'rejected')
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            @endif
        </div>
        <h3 class="text-lg font-semibold text-white">
            {{ $notification->outcome === 'rejected'
                ? __('Proposta di collaborazione rifiutata')
                : __('Proposta di collaborazione approvata')
            }}
        </h3>
    </div>

    <!-- Contenuto della notifica -->
    <div class="space-y-2 mb-4">
        <p class="{{ $notification->outcome === 'rejected' ? 'text-red-100' : 'text-emerald-100' }}">
            {{ $notification->data['message'] }}
        </p>

        <div class="flex items-center {{ $notification->outcome === 'rejected' ? 'text-red-100' : 'text-emerald-100' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="font-medium">{{ $notification->data['user_name'] }}</span>
        </div>

        <div class="flex items-center {{ $notification->outcome === 'rejected' ? 'text-red-100' : 'text-emerald-100' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <span class="font-medium">{{ $notification->data['collection_name'] }}</span>
        </div>
    </div>

    <!-- Footer con timestamp -->
    <div class="flex items-center {{ $notification->outcome === 'rejected' ? 'text-red-200' : 'text-emerald-200' }} text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ $notification->created_at->diffForHumans() }}
    </div>
</div>
