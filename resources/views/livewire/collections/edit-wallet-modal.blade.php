<div>
    @if($show)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center">
            <div class="bg-gray-800 p-6 rounded-2xl w-full max-w-md shadow-lg">
                <h3 class="text-2xl font-bold text-white mb-4">{{ __('Edit Wallet Details') }}</h3>

                <!-- Wallet Address -->
                <div class="mb-4">
                    <label for="walletAddress" class="block text-sm font-medium text-gray-300">{{ __('Wallet Address') }}</label>
                    <input type="text" id="walletAddress" wire:model="walletAddress"
                        class="input input-bordered input-primary w-full bg-gray-700 text-white"
                        placeholder="{{ __('Enter wallet address') }}">
                    @error('walletAddress') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Royalty Mint -->
                <div class="mb-4">
                    <label for="royaltyMint" class="block text-sm font-medium text-gray-300">{{ __('Royalty Mint (%)') }}</label>
                    <input type="number" id="royaltyMint" wire:model="royaltyMint"
                        class="input input-bordered input-primary w-full bg-gray-700 text-white"
                        placeholder="{{ __('Enter royalty mint percentage') }}">
                    @error('royaltyMint') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Royalty Rebind -->
                <div class="mb-4">
                    <label for="royaltyRebind" class="block text-sm font-medium text-gray-300">{{ __('Royalty Rebind (%)') }}</label>
                    <input type="number" id="royaltyRebind" wire:model="royaltyRebind"
                        class="input input-bordered input-primary w-full bg-gray-700 text-white"
                        placeholder="{{ __('Enter royalty rebind percentage') }}">
                    @error('royaltyRebind') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Azioni -->
                <div class="flex justify-end space-x-4 mt-6">
                    <button wire:click="closeHandleWallets" class="btn btn-secondary">{{ __('label.cancel') }}</button>
                    @if($mode === 'create')
                        <button wire:click="createNewWallet" class="btn btn-secondary">{{ __('label.save') }}</button>
                    @else
                        <button wire:click="saveWallet" class="btn btn-primary">{{ __('label.save') }}</button>
                    @endif

                </div>
            </div>
        </div>
    @endif
</div>
