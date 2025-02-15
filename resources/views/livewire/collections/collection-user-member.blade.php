@php
    // Gestione del permesso per creare un wallet
    $canCreateWallet = (new \App\Livewire\Collections\CollectionUserMember)->userHasPermissionInCollection($collectionId, 'create_wallet');
    $collectionId = $collection->id;
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
                    <button id ="inviteNewMember" class="btn btn-primary w-full sm:w-auto" wire:click="dispatch('openInviteModal')">
                        {{ __('collection.invite_collection_member') }}
                    </button>
                    <!-- Bottone per creare un nuovo wallet -->
                    <button id="createNewWallet" name = "createNewWallet" class="btn btn-primary w-full sm:w-auto">
                        {{ __('collection.wallet.create_the_wallet') }}
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Sezione Membri della Collection -->
    <h3 class="text-xl font-bold text-white mb-4">{{ __('collection.members') }}</h3>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        @foreach($collectionUsers as $member)
            <div
                data-user-id="{{ $member->user_id }}"
                data-collection-id="{{ $member->collection_id }}"
                class="{{ $member->status === 'pending' ? 'bg-yellow-800' : 'bg-gray-900' }} p-4 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-center mb-4">
                    <img class="w-12 h-12 rounded-full" src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}">
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-white">{{ $member->user->name }} {{ $member->user->last_name }}</h3>
                        <p class="text-sm text-gray-400">{{ __('collection.wallet.user_role') .': '. $member->role }}</p>
                        <p class="text-sm text-gray-400">{{ __('User id: ') . $member->user_id }}</p>
                    </div>
                </div>

                @if(
                    !$member->wallet &&
                    $canCreateWallet &&
                    !$member->notificationPayloadWallets->contains('status', 'pending_create') &&
                    !in_array($member->role, ['natan', 'EPP'])
                )
                    <!-- Bottone per creare un nuovo wallet gestito da JavaScript -->
                    <button
                        data-collection-id="{{ $member->collection_id }}"
                        data-user-id="{{ $member->user_id }}"
                        data-user="{{$member->user_id}}"
                        class="create-wallet-btn btn btn-primary w-full mt-4">
                        {{ __('collection.wallet.create_the_wallet') }}
                    </button>
                @endif
            </div>
        @endforeach

        <!-- Sezione Invitation proposal -->
        @foreach($invitationProposal as $member)

            <div
                data-user-id="{{ $member->receiver_id }}"
                data-id="{{ $member->id }}"
                class="{{ $member->status === 'pending' ? 'bg-yellow-800' : 'bg-gray-900' }} p-4 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">

                <div class="flex items-center mb-4">
                    <img class="w-12 h-12 rounded-full" src="{{ $member->receiver->profile_photo_url }}" alt="{{ $member->receiver->name }}">
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-white">{{ $member->receiver->name }} {{ $member->receiver->last_name }}</h3>
                        <p class="text-sm text-gray-400">{{ __('collection.wallet.user_role') .': '. $member->role }}</p>
                        <p class="text-sm text-gray-400">{{ __('User id: ') . $member->receiver->user_id }}</p>
                    </div>
                </div>

                <!-- Aggiungiamo qui il bottone Elimina -->
                <div class="mt-4 flex justify-end">
                    <button
                        data-id="{{ $member->id }}"
                        data-collection="{{ $member->collection_id }}"
                        data-user="{{ $member->receiver_id }}"  {{-- Questo è l'utente che ha creato il wallet --}}
                        class="delete-proposal-invitation px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md transition-colors duration-150 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        {{ __('label.delete') }}
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Sezione Wallet -->
    <h3 class="text-xl font-bold text-white mt-8 mb-4">{{ __('collection.wallet.wallets') }}</h3>
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

                    @if( $wallet && !$wallet->notificationPayloadWallets->contains('status', 'pending_update'))
                    <!-- Bottone gestito da JavaScript per modificare il wallet -->
                        {{ $wallet->wallet }}
                        <button
                            data-wallet-address="{{ $wallet->wallet }}"
                            data-collection-id="{{ $wallet->collection_id }}"
                            data-user-id="{{ $wallet->user_id }}"
                            data-royalty-mint="{{ $wallet->royalty_mint }}"
                            data-royalty-rebind="{{ $wallet->royalty_rebind }}"
                            data-user="{{$wallet->user_id}}"
                            class="update-wallet-btn btn btn-primary w-full mt-4">
                            {{ __('collection.wallet.manage_wallet') }}
                        </button>
                    @endif

                    {{-- <button wire:click="$dispatch('openHandleWallets', { walletId: {{ $wallet->id }} })" class="btn btn-primary mt-4 w-full">
                        {{ __('collection.wallet.manage_wallet') }}
                    </button> --}}
                @endif
            </div>
        @endforeach
    </div>

    <!--  Sezione Wallet proposal -->
    <h3 class="text-xl font-bold text-white mt-8 mb-4">{{ __('collection.wallet.wallets') }}</h3>
    <div id="wallet-list" class="grid grid-cols-1 gap-6 md:grid-cols-3">
        @foreach($walletProposals as $wallet)

            @php
                /** @param string $status */
                $status = App\Enums\NotificationStatus::fromDatabase($wallet->status);
                $isPending = Illuminate\Support\Str::contains($wallet->status, $status);
            @endphp

             <!--
                1️⃣ Prima regola:
                Se l'utente non ha i permessi ($canCreateWallet == false), oppure il wallet appartiene a "EPP" o "Natan", la schedina sarà sempre grigia (bg-gray-700 opacity-75 cursor-not-allowed) e l'utente non potrà interagire.
                2️⃣ Seconda regola:
                Se il wallet è pending, allora sarà giallo (bg-yellow-800), ma solo se l'utente ha i permessi.
                3️⃣ Terza regola:

                Se il wallet è "normale" e approvato, la schedina sarà grigia scura (bg-gray-900).
                📝 Esempi di comportamento
                $canCreateWallet	    $wallet->platform_role	  $isPending	Colore finale
                ❌ false	                qualsiasi	            ✅ true	    ⚫ bg-gray-700 opacity-75 (non interattivo)
                ❌ false	                qualsiasi	            ❌ false     ⚫ bg-gray-700 opacity-75 (non interattivo)
                ✅ true	                "natan" / "EPP"	        ✅ true	    ⚫ bg-gray-700 opacity-75 (non interattivo)
                ✅ true	                "natan" / "EPP"	        ❌ false	    ⚫ bg-gray-700 opacity-75 (non interattivo)
                ✅ true	                altro	                ✅ true	    🟡 bg-yellow-800
                ✅ true	                altro	                ❌ false	    ⚫ bg-gray-900
            -->
             <div
             id="wallet-{{ $wallet->id }}"
             class="wallet-item
             {{ !$canCreateWallet || in_array($wallet->platform_role, ['natan', 'EPP'])
                ? 'bg-gray-700 opacity-75 cursor-not-allowed'
                : ($isPending ? 'bg-yellow-800' : 'bg-gray-900') }}
                p-4 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300">

                    <div>
                        <p class="text-sm text-gray-400">
                            <strong>{{ __('collection.wallet.user_role')}}:</strong> {{ $wallet->platform_role }}
                        </p>
                        <p class="text-sm text-gray-400">
                            <strong>{{ __('collection.wallet.address') }}:</strong> {{ substr($wallet->wallet, 0, 15) }}...{{ substr($wallet->wallet, -4) }}
                        </p>
                        <p class="text-sm text-gray-400">
                            <strong>{{ __('collection.wallet.royalty_mint') }}:</strong> {{ $wallet->royalty_mint }}%
                        </p>
                        <p class="text-sm text-gray-400">
                            <strong>{{ __('collection.wallet.royalty_rebind') }}:</strong> {{ $wallet->royalty_rebind }}%
                        </p>

                        <!-- Nome e Cognome dell'Utente Correlato -->
                        @if($wallet->receiver)
                            <p class="text-sm text-gray-400">
                                <strong>{{ __('collection.wallet.approver') }}:</strong> {{ $wallet->receiver->name }} {{ $wallet->receiver->last_name }}
                            </p>
                        @else
                            <p class="text-sm text-gray-400">
                                <strong>{{ __('collection.wallet.approver') }}:</strong> {{ __('Unassigned') }}
                            </p>
                        @endif

                        <!-- Aggiungiamo qui il bottone Elimina -->
                        <div class="mt-4 flex justify-end">
                            <button
                                data-id="{{ $wallet->id }}"
                                data-collection="{{ $collectionId }}"
                                data-user="{{ $wallet->receiver_id }}"  {{-- Questo è l'utente che riceve  il wallet --}}
                                class="delete-proposal-wallet px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md transition-colors duration-150 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                {{ __('label.delete') }}
                            </button>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <!-- Bottone che permette di aprire la collection -->
        @include('livewire.collection-manager-includes.back_to_collection_button')

        <!-- Include le Modali -->

        <livewire:notifications.invitations.invite-user-to-collection-modal :collectionId="$collectionId" />



    </div>



