<div id="wallet_section" class="p-6 border border-gray-700 rounded-2xl bg-gray-800 shadow-lg">

    <!-- Titolo della sezione -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">{{ __('collection.wallet_section_title') }}</h2>
        <p class="text-sm text-gray-400">{{ __('collection.wallet_section_description') }}</p>
    </div>

    <!-- Vista Desktop degli Wallets -->
    @if($wallets)
        <div class="hidden md:block overflow-x-auto">
            <table class="table-auto w-full border-collapse rounded-2xl shadow-md overflow-hidden">
                <thead class="bg-gradient-to-r from-blue-500 to-purple-600 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">{{ __('collection.wallet_address') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('collection.user_role') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('collection.royalty_mint') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('collection.royalty_rebind') }}</th>
                        <th class="px-4 py-3 text-left">{{ __('collection.status') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-900 text-gray-300">
                    @forelse ($wallets as $wallet)
                        <tr class="hover:bg-gray-700 transition-colors duration-200">
                            <td class="px-4 py-3">
                                @if ($wallet->wallet)
                                    <div class="tooltip tooltip-right" data-tip="{{ $wallet->wallet }}">
                                        <span class="text-blue-400 hover:underline cursor-pointer" onclick="copyToClipboard('{{ $wallet->wallet }}')">
                                            {{ $wallet->short_wallet }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $wallet->user_role ?? __('collection.role_unknown') }}</td>
                            <td class="px-4 py-3">{{ $wallet->royalty_mint }}%</td>
                            <td class="px-4 py-3">{{ $wallet->royalty_rebind }}%</td>
                            <td class="px-4 py-3">
                                <span class="badge {{ $wallet->status ? 'bg-green-500' : 'bg-red-500' }} text-white px-2 py-1 rounded-full">
                                    {{ $wallet->status ? __('collection.active') : __('collection.inactive') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-center text-gray-400">
                                {{ __('collection.no_wallets') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <!-- Vista Mobile degli Wallets -->
    @if($wallets)
        <div class="block md:hidden">
            @forelse ($wallets as $wallet)
                <div class="p-4 mb-4 bg-gray-900 rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300">
                    <p class="text-gray-300">
                        <strong>{{ __('collection.wallet.address') }}:</strong>
                        <span class="text-blue-400 hover:underline cursor-pointer" onclick="copyToClipboard('{{ $wallet->wallet }}')">
                            {{ $wallet->short_wallet }}
                        </span>
                    </p>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('collection.wallet.user_role') }}:</strong> {{ $wallet->user_role ?? __('collection.wallet.role_unknown') }}
                    </p>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('collection.wallet.royalty_mint') }}:</strong> {{ $wallet->royalty_mint }}%
                    </p>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('collection.wallet.royalty_rebind') }}:</strong> {{ $wallet->royalty_rebind }}%
                    </p>
                    <p class="text-sm text-gray-400">
                        <strong>{{ __('collection.wallet.status') }}:</strong>
                        <span class="badge {{ $wallet->status ? 'bg-green-500' : 'bg-red-500' }} text-white px-2 py-1 rounded-full">
                            {{ $wallet->status ? __('label.active') : __('label.inactive') }}
                        </span>
                    </p>
                </div>
            @empty
                <p class="text-center text-gray-400">{{ __('collection.no_wallets') }}</p>
            @endforelse
        </div>
    @endif

</div>
