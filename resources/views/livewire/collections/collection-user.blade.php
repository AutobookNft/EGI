<div id="collection_management" class="p-6 border border-gray-700 rounded-2xl bg-gray-800 shadow-lg">
    <!-- Titolo della sezione -->
    <div class="mb-6 flex items-start justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white">{{ __('collection.collection_members') }}</h2>
            <p class="text-sm text-gray-400">{{ __('collection.team_members_description') }}</p>
        </div>
        <div class="flex flex-wrap space-x-0 gap-4">
            <button class="btn btn-primary w-full sm:w-auto" wire:click="dispatch('openInviteModal')">
                {{ __('collection.invite_collection_member') }}
            </button>
            <button class="btn btn-primary w-full sm:w-auto" wire:click="$dispatch('openHandleWallets')">
                {{ __('collection.wallet.create_the_wallet') }}
            </button>
        </div>
    </div>
    <!-- Sezione Membri della Collection -->
    <h3 class="text-xl font-bold text-white mb-4">{{ __('collection.members') }}</h3>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        @foreach($collectionUsers as $member)
            <div class="{{ $member->status === 'pending' ? 'bg-yellow-800' : 'bg-gray-900' }} p-4 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 cursor-pointer">
                <div class="flex items-center mb-4">
                    <img class="w-12 h-12 rounded-full" src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}">
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-white">{{ $member->user->name }} {{ $member->user->last_name }}</h3>
                        <p class="text-sm text-gray-400">{{ __('Role: ') . $member->role }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Sezione Wallet -->
    <h3 class="text-xl font-bold text-white mt-8 mb-4">{{ __('collection.wallets') }}</h3>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        @foreach($wallets as $wallet)
            <div class="{{ in_array($wallet->platform_role, ['natan', 'EPP']) ? 'bg-gray-700 opacity-75 cursor-not-allowed' : 'bg-gray-900' }} p-4 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                <div>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('Role:') }}</strong> {{ $wallet->platform_role }}
                    </p>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('Wallet Address:') }}</strong> {{ substr($wallet->wallet, 0, 6) }}...{{ substr($wallet->wallet, -4) }}
                    </p>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('Mint Royalty:') }}</strong> {{ $wallet->royalty_mint }}%
                    </p>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('Rebind Royalty:') }}</strong> {{ $wallet->royalty_rebind }}%
                    </p>
                </div>
                @if(!in_array($wallet->platform_role, ['natan', 'EPP']) || Auth::user()->hasRole('superadmin'))
                    <button wire:click="$dispatch('openHandleWallets', { walletId: {{ $wallet->id }} })" class="btn btn-primary mt-4 w-full">
                        {{ __('collection.wallet.manage_wallet') }}
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Placeholder se non ci sono membri o wallet -->

    <!-- Include le Modali -->
    <livewire:collections.edit-wallet-modal />
    <livewire:collections.invite-user-to-collection-modal :collectionId="$collectionId" />
</div>
