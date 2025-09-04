@if(count($pendingNotifications) > 0)
    <!-- Responsive Grid Layout -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="header-buttons">
        @foreach($pendingNotifications as $notif)
            @include('livewire.partials.head-thumbnail', ['notif' => $notif])
        @endforeach
    </div>
@else
    <!-- Empty State - senza bordi eccessivi -->
    <div class="bg-white/5 rounded-2xl p-8 text-center">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="text-lg font-semibold text-gray-300 mb-2">{{ __('All Clear!') }}</h3>
        <p class="text-gray-400">{{ __('No pending notifications at the moment') }}</p>
    </div>
@endif
