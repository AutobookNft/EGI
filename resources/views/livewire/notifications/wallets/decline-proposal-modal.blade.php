<div class="{{ $isVisible ? 'block' : 'hidden' }}">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
        <!-- Modale -->
        <div class="bg-gray-800 rounded-lg shadow-2xl w-full max-w-lg p-6 text-white">
            <!-- Header -->
            <div class="flex justify-between items-center border-b border-gray-700 pb-4">
                <h5 class="text-xl font-bold">{{ __('Decline Proposal') }}</h5>
                <button type="button" class="text-gray-400 hover:text-gray-300" wire:click="closeModal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="mt-4">
                <form wire:submit.prevent="decline">
                    <div class="mb-4">
                        <label for="reason" class="block text-sm font-medium text-gray-300">{{ __('Reason for Decline') }}</label>
                        <textarea id="reason"
                                  class="w-full bg-gray-700 text-white border border-gray-600 rounded-lg p-3 focus:ring-indigo-500 focus:border-indigo-500"
                                  wire:model="reason"
                                  rows="4"
                                  required></textarea>
                        @error('reason') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Actions -->
                    <div class="flex justify-end space-x-4">
                        <button type="button" wire:click="closeModal" class="bg-gray-600 hover:bg-gray-500 text-white px-4 py-2 rounded-lg">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg">
                            {{ __('Decline') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
