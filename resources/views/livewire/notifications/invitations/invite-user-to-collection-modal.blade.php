<div>

    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-md rounded-2xl bg-gray-800 p-6 shadow-lg">
            <h3 class="mb-4 text-2xl font-bold text-white">{{ __('Invite Collection Member') }}</h3>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-300">{{ __('Email') }}</label>
                <input type="email" id="email" wire:model="email"
                    class="input input-bordered input-primary w-full bg-gray-700 text-white"
                    placeholder="{{ __('Enter user email') }}">
                @error('email')
                    <span class="text-sm text-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Role -->
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-300">{{ __('Role') }}</label>
                <select id="role" wire:model="role"
                    class="select select-bordered select-primary w-full bg-gray-700 text-white">
                    <option value="" disabled>{{ __('Select a role') }}</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
                @error('role')
                    <span class="text-sm text-error">{{ $message }}</span>
                @enderror
            </div>

            <!-- Errori generali -->
            @error('invitation')
                <div class="mb-4 rounded-md border border-red-600 bg-red-900 p-3">
                    <span class="text-sm text-red-300">{{ $message }}</span>
                </div>
            @enderror

            <!-- Azioni -->
            <div class="mt-6 flex justify-end space-x-4">
                <button wire:click="closeModal" class="btn btn-secondary">{{ __('Cancel') }}</button>
                <button wire:click="invite" class="btn btn-primary">{{ __('Send Invitation') }}</button>
            </div>
        </div>
    </div>

</div>
