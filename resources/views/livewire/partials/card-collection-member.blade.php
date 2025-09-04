<div data-user-id="{{ $member->user_id }}" data-collection-id="{{ $member->collection_id }}"
    class="{{ $member->status === 'pending' ? 'from-yellow-900/50 to-amber-900/30 ring-2 ring-yellow-500/30 animate-pulse' : 'from-gray-800 to-gray-900' }} group relative rounded-2xl bg-gradient-to-br p-6 shadow-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">

    <!-- Status indicator dot -->
    @if ($member->status === 'pending')
        <div class="absolute right-4 top-4">
            <span class="relative flex w-3 h-3">
                <span
                    class="absolute inline-flex w-full h-full bg-yellow-400 rounded-full opacity-75 animate-ping"></span>
                <span class="relative inline-flex w-3 h-3 bg-yellow-500 rounded-full"></span>
            </span>
        </div>
    @endif

    <div class="flex items-start gap-4">
        <!-- Avatar with gradient border -->
        <div class="relative">
            <div class="rounded-full bg-gradient-to-tr from-purple-600 to-blue-500 p-0.5">
                <img class="border-2 border-gray-900 rounded-full h-14 w-14"
                    src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}">
            </div>
        </div>

        <!-- User info -->
        <div class="flex-1 min-w-0">
            <h3 class="text-xl font-bold text-transparent bg-gradient-to-r from-purple-400 to-blue-500 bg-clip-text">
                {{ $member->user->name }} {{ $member->user->last_name }}
            </h3>

            <div class="mt-2 space-y-1">
                <!-- Role badge -->
                <div
                    class="inline-flex items-center rounded-full bg-gray-700 px-2.5 py-0.5 text-xs font-medium text-blue-400">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $member->role }}
                </div>

                <!-- User ID with copy indicator -->
                <div class="flex items-center font-mono text-sm text-gray-400">
                    <span class="opacity-70">ID:</span>
                    <span class="ml-1">{{ $member->user_id }}</span>
                    <button class="ml-2 text-gray-500 transition-colors hover:text-blue-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if (
        !$member->wallet &&
            $canCreateWallet &&
            !$member->notificationPayloadWallets->contains('status', 'pending_create') &&
            !in_array($member->role, ['Natan', 'EPP']))
        <!-- Create Wallet Button -->
        <button data-collection-id="{{ $member->collection_id }}" data-wallet-address="{{ $memberWallet ?? '' }}" data-user-id="{{ $member->user_id }}"
            class="create-wallet-btn mt-4 flex w-full transform items-center justify-center rounded-xl bg-gradient-to-r from-purple-600 to-blue-500 px-6 py-2.5 font-medium text-white transition-all duration-300 hover:scale-[1.02] hover:from-purple-700 hover:to-blue-600">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            {{ __('collection.wallet.create_the_wallet') }}
        </button>
    @endif
</div>
