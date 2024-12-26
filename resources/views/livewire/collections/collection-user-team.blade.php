<div id="team_members" class="p-6 border border-gray-700 rounded-2xl bg-gray-800 shadow-lg">
    <!-- Titolo della sezione -->
    <div class="mb-6 flex items-start justify-between">
        <div>
            <h2 class="text-2xl font-bold text-white">{{ __('collection.team_members_title') }}</h2>
            <p class="text-sm text-gray-400">{{ __('collection.team_members_description') }}</p>
        </div>
        <button class="btn btn-primary" wire:click="dispatch('openInviteModal')">
            {{ __('collection.invite_team_member') }}
        </button>
    </div>

    <!-- Griglia dei Membri del Team -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        @foreach($teamUsers as $member)
            <div class="{{ $member->status === 'pending' ? 'bg-yellow-800' : 'bg-gray-900' }} p-4 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 cursor-pointer"
                 wire:click="$dispatch('openEditModal', {{ $member->id }})">
                <div class="flex items-center mb-4">
                    <img class="w-12 h-12 rounded-full" src="{{ $member->user->profile_photo_url }}" alt="{{ $member->user->name }}">
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-white">{{ $member->user->name }} {{ $member->user->last_name }}</h3>
                        <p class="text-sm text-gray-400">{{ __('Role: ') . $member->role }}</p>
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('Wallet:') }}</strong> {{ substr($member->wallet, 0, 6) }}...{{ substr($member->wallet, -4) }}
                    </p>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('Mint Royalty:') }}</strong> {{ $member->royalty_mint }}%
                    </p>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('Rebind Royalty:') }}</strong> {{ $member->royalty_rebind }}%
                    </p>
                </div>
                @if($member->status === 'pending')
                    <span class="block mt-2 px-2 py-1 text-sm font-medium text-yellow-800 bg-yellow-300 rounded-full">
                        {{ __('Pending') }}
                    </span>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Placeholder se non ci sono membri -->
    @if($teamUsers->isEmpty())
        <div class="mt-6 text-center">
            <p class="text-gray-400">{{ __('collection.no_team_members') }}</p>
        </div>
    @endif

    <!-- Include le Modali -->
    <livewire:collections.edit-wallet-modal />
    <livewire:collections.invite-user-to-team-modal :teamId="$teamId" />
</div>

