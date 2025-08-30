{{-- resources/views/egis/partials/modals/delete-confirmation-modal.blade.php --}}
{{-- 
    Modal di conferma eliminazione EGI
    ORIGINE: righe 174-199 di show.blade.php
    VARIABILI: $canDeleteEgi, $egi
--}}

{{-- Delete Confirmation Modal --}}
@if($canDeleteEgi)
<div id="delete-modal"
    class="fixed inset-0 z-50 items-center justify-center hidden bg-black/50 backdrop-blur-sm">
    <div class="max-w-md p-6 mx-4 bg-gray-800 border rounded-xl border-red-700/30">
        <div class="text-center">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                    </path>
                </svg>
            </div>
            <h3 class="mb-2 text-lg font-medium text-white">{{ __('egi.crud.delete_confirmation_title') }}</h3>
            <p class="mb-6 text-sm text-gray-300">{{ __('egi.crud.delete_confirmation_message') }}</p>

            <div class="flex gap-3">
                <button id="delete-cancel"
                    class="flex-1 px-4 py-2 text-white transition-colors bg-gray-600 rounded-lg hover:bg-gray-700">
                    {{ __('egi.crud.cancel') }}
                </button>
                <form id="delete-form" action="{{ route('egis.destroy', $egi->id) }}" method="POST"
                    class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full px-4 py-2 text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700">
                        {{ __('egi.crud.delete_confirm') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
