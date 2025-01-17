@php
    // Gestione del permesso per creare un wallet
    $canCreateWallet = (new \App\Livewire\Collections\CollectionUserMember)->userHasPermissionInCollection($collectionId, 'create_wallet');
@endphp

<div id="collection_management" class="p-6 border border-gray-700 rounded-2xl bg-gray-800 shadow-lg">
    <!-- Titolo della sezione -->
    <div id="collection_management" class="p-6 border border-gray-700 rounded-2xl bg-gray-800 shadow-lg">
        <!-- Titolo della sezione -->
        <div class="mb-6 flex items-start justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white">{{ $collectionName }}</h2>
                <p class="text-sm text-gray-400">
                    {{ __('collection.wallet.owner') }}: {{ $collectionOwner->name }} {{ $collectionOwner->last_name }}
                </p>
                <p class="text-sm text-gray-400">{{ __('collection.team_members_description') }}</p>
            </div>
            @if($canCreateWallet)

                <div class="flex flex-wrap space-x-0 gap-4">
                    <!-- Bottone per invitare un nuovo membro alla collection -->
                    <button class="btn btn-primary w-full sm:w-auto" wire:click="dispatch('openInviteModal')">
                        {{ __('collection.invite_collection_member') }}
                    </button>
                    <!-- Bottone per creare un nuovo wallet -->
                    <button class="btn btn-primary w-full sm:w-auto" wire:click="$dispatch('openForCreateNewWallets')">
                        {{ __('collection.wallet.create_the_wallet') }}
                    </button>
                </div>
            @endif
        </div>+
    </div>

    <!-- Sezione Membri della Collection -->
    <h3 class="text-xl font-bold text-white mb-4">{{ __('collection.members') }}</h3>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        @foreach($collectionUsers as $member)
            <div class="{{ $member->status === 'pending' ? 'bg-yellow-800' : 'bg-gray-900' }} p-4 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center mb-4">
                    <img class="w-12 h-12 rounded-full" src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}">
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-white">{{ $member->user->name }} {{ $member->user->last_name }}</h3>
                        <p class="text-sm text-gray-400">{{ __('collection.wallet.user_role') .': '. $member->role }}</p>
                        <p class="text-sm text-gray-400">{{ __('User id: ') . $member->user_id }}</p>
                    </div>
                </div>
                @if(!$member->wallet && $canCreateWallet)
                <!-- Bottone per creare un nuovo wallet. Il listener si trova in /home/fabio/EGI/app/Livewire/Collections/EditWalletModal.php -->
                    <button id="create_new_wallet" class="btn btn-primary w-full sm:w-auto" wire:click="$dispatch('openForCreateNewWallets', { collectionId: {{ $member->collection_id }}, userId: {{ $member->user_id }} })">
                        {{ __('collection.wallet.create_the_wallet') }}
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Sezione Wallet -->
    <h3 class="text-xl font-bold text-white mt-8 mb-4">{{ __('collection.wallets') }}</h3>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        @foreach($wallets as $wallet)
            <div class="{{ !$canCreateWallet || in_array($wallet->platform_role, ['natan', 'EPP']) ? 'bg-gray-700 opacity-75 cursor-not-allowed' : 'bg-gray-900' }}  p-4 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                <div>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('collection.wallet.user_role')}}:</strong> {{ $wallet->platform_role }}
                    </p>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('collection.wallet.address') }}:</strong> {{ substr($wallet->wallet, 0, 6) }}...{{ substr($wallet->wallet, -4) }}
                    </p>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('collection.wallet.royalty_mint') }}:</strong> {{ $wallet->royalty_mint }}%
                    </p>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('collection.wallet.royalty_rebind') }}:</strong> {{ $wallet->royalty_rebind }}%
                    </p>

                    <!-- Nome e Cognome dell'Utente Correlato -->
                    @if($wallet->user)
                        <p class="text-sm text-gray-400">
                            <strong>{{ __('collection.wallet.owner') }}:</strong> {{ $wallet->user->name }} {{ $wallet->user->last_name }}
                        </p>
                    @else
                        <p class="text-sm text-gray-400">
                            <strong>{{ __('collection.wallet.owner') }}:</strong> {{ __('Unassigned') }}
                        </p>
                    @endif
                </div>

                @if($canCreateWallet && (!in_array($wallet->platform_role, ['natan', 'EPP']) || Auth::user()->hasRole('superadmin')))
                    <!-- Bottone per gestire il wallet. Il listener si trova in /home/fabio/EGI/app/Livewire/Collections/EditWalletModal.php -->
                    <button wire:click="$dispatch('openHandleWallets', { walletId: {{ $wallet->id }} })" class="btn btn-primary mt-4 w-full">
                        {{ __('collection.wallet.manage_wallet') }}
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    <h3 class="text-xl font-bold text-white mt-8 mb-4">{{ __('collection.wallets') }}</h3>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        @foreach($walletProposals as $wallet)
            <div class="{{ !$canCreateWallet || in_array($wallet->platform_role, ['natan', 'EPP']) ? 'bg-gray-700 opacity-75 cursor-not-allowed' : 'bg-gray-900' }} {{ $wallet->status === 'pending' ? 'bg-yellow-800' : 'bg-gray-900' }}  p-4 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                <div>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('collection.wallet.user_role')}}:</strong> {{ $wallet->platform_role }}
                    </p>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('collection.wallet.address') }}:</strong> {{ substr($wallet->change_details['wallet_address'], 0, 15) }}...{{ substr($wallet->wallet, -4) }}
                    </p>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('collection.wallet.royalty_mint') }}:</strong> {{ $wallet->change_details['royalty_mint'] }}%
                    </p>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('collection.wallet.royalty_rebind') }}:</strong> {{ $wallet->change_details['royalty_rebind'] }}%
                    </p>

                    <!-- Nome e Cognome dell'Utente Correlato -->
                    @if($wallet->approver)
                        <p class="text-sm text-gray-400">
                            <strong>{{ __('collection.wallet.royalty_rebind') }}:</strong> {{ $wallet->approver->name }} {{ $wallet->approver->last_name }}
                        </p>
                    @else
                        <p class="text-sm text-gray-400">
                            <strong>{{ __('collection.wallet.approver') }}:</strong> {{ __('Unassigned') }}
                        </p>
                    @endif
                </div>

            </div>
        @endforeach

    </div>

    <!-- Bottone che permette di aprire la collection -->
    @include('livewire.collection-manager-includes.back_to_collection_button')

    <!-- Placeholder se non ci sono membri o wallet -->

    <!-- Include le Modali -->
    <livewire:collections.edit-wallet-modal />
    <livewire:collections.invite-user-to-collection-modal :collectionId="$collectionId" />
</div>
