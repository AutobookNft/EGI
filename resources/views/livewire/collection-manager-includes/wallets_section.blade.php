<div id="wallet_section" class="p-4 border border-gray-300 rounded-lg bg-white shadow-md">
    <!-- Titolo della sezione -->
    <div class="mb-4">
        <h2 class="text-lg font-semibold text-gray-800">{{ __('collection.wallet_section_title') }}</h2>
        <p class="text-sm text-gray-500">{{ __('collection.wallet_section_description') }}</p>
    </div>

    <!-- Vista degli wallets Desktop -->
    @if($wallets)
        <div class="hidden md:block overflow-x-auto">
            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">{{ __('collection.wallet_address') }}</th>
                        <th class="border border-gray-300 px-4 py-2">{{ __('collection.user_role') }}</th>
                        <th class="border border-gray-300 px-4 py-2">{{ __('collection.royalty_mint') }}</th>
                        <th class="border border-gray-300 px-4 py-2">{{ __('collection.royalty_rebind') }}</th>
                        <th class="border border-gray-300 px-4 py-2">{{ __('collection.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($wallets as $wallet)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">
                                @if ($wallet->wallet)
                                    <div class="tooltip tooltip-right text-sm" data-tip="{{ $wallet->wallet }}">
                                        <span class="ml-2 text-blue-500 hover:underline" onclick="copyToClipboard('{{ $wallet->wallet }}')">
                                            {{ $wallet->short_wallet }}
                                        </span>
                                    </div>
                                    <button
                                        class="ml-2 text-blue-500 hover:underline"
                                        onclick="copyToClipboard('{{ $wallet->wallet }}')"
                                    >
                                        {{ __('Copia') }}
                                    </button>
                                @endif
                            </td>
                            <td class="border border-gray-300 px-4 py-2">{{ $wallet->user_role ?? __('collection.role_unknown') }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $wallet->royalty_mint }}%</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $wallet->royalty_rebind }}%</td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ $wallet->status ? __('collection.active') : __('collection.inactive') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border border-gray-300 px-4 py-2 text-center text-gray-500">
                                {{ __('collection.no_wallets') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <!-- Vista degli wallets mobile -->
    @if($wallets)
        <div class="block md:hidden">
            @forelse ($wallets as $wallet)
                <div class="p-4 mb-4 border border-gray-300 rounded-lg bg-gray-50">
                    <p class="text-gray-500">
                        <label>{{ __('collection.wallet.address') }}:</label>
                        <div class="tooltip tooltip-right" data-tip="{{ $wallet->wallet }}">
                            <span class= "ml-2 text-blue-500 hover:underline text-xs" onclick="copyToClipboard('{{ $wallet->wallet }}')">{{ $wallet->short_wallet }}</span>
                        </div>
                        <button
                            class="ml-2 text-blue-500 hover:underline"
                            onclick="copyToClipboard('{{ $wallet->wallet }}')"
                        >
                            {{ __('Copia') }}
                        </button>
                    </p>
                    <p class="text-sm text-gray-500">
                        <strong>{{ __('collection.wallet.user_role') }}:</strong> {{ $wallet->user_role ?? __('collection.wallet.role_unknown') }}
                    </p>
                    <p class="text-sm text-gray-500">
                        <strong>{{ __('collection.wallet.royalty_mint') }}:</strong> {{ $wallet->royalty_mint }}%
                    </p>
                    <p class="text-sm text-gray-500">
                        <strong>{{ __('collection.wallet.royalty_rebind') }}:</strong> {{ $wallet->royalty_rebind }}%
                    </p>
                    <p class="text-sm text-gray-500">
                        <strong>{{ __('collection.wallet.status') }}:</strong>
                        {{ $wallet->status ? __('label.active') : __('label.inactive') }}
                    </p>
                </div>
            @empty
                <p class="text-center text-gray-500">{{ __('collection.no_wallets') }}</p>
            @endforelse
        </div>
    @endif
</div>
