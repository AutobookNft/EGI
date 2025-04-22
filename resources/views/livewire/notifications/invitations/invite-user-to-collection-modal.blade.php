<div>

        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center">
            <div class="bg-gray-800 p-6 rounded-2xl w-full max-w-md shadow-lg">
                <h3 class="text-2xl font-bold text-white mb-4">{{ __('Invite Collection Member') }}</h3>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-300">{{ __('Email') }}</label>
                    <input type="email" id="email" wire:model="email"
                        class="input input-bordered input-primary w-full bg-gray-700 text-white"
                        placeholder="{{ __('Enter user email') }}">
                    @error('email') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Role -->
                <div class="mb-4">
                    <label for="role" class="block text-sm font-medium text-gray-300">{{ __('Role') }}</label>
                    <select id="role" wire:model="role"
                        class="select select-bordered select-primary w-full bg-gray-700 text-white">
                        <option value="" disabled>{{ __('Select a role') }}</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    {{-- @error('role') <span class="text-error text-sm">{{ $message }}</span> @enderror --}}
                </div>

                <!-- Azioni -->
                <div class="flex justify-end space-x-4 mt-6">
                    <button wire:click="closeModal" class="btn btn-secondary">{{ __('Cancel') }}</button>
                    <button wire:click="invite" class="btn btn-primary">{{ __('Send Invitation') }}</button>
                </div>
            </div>
        </div>
   
</div>
