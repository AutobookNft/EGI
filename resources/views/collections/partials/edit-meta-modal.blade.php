{{-- resources/views/collections/partials/edit-meta-modal.blade.php --}}
@php
    $canEdit = auth()->check() && auth()->id() === ($collection->creator_id ?? null);
@endphp
@if($canEdit)
<div id="editMetaModal"
    class="fixed inset-0 z-50 items-center justify-center hidden"
    data-update-url="{{ route('collections.update', ['collection' => $collection->id]) }}"
    data-collection-id="{{ $collection->id }}"
    data-toast-updated="{{ __('collection.show.toast_updated') }}"
    aria-hidden="true">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/60"></div>

    {{-- Panel --}}
    <div class="relative w-full max-w-2xl p-6 mx-auto bg-gray-900 border border-gray-700 shadow-xl rounded-xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-white">{{ __('collection.show.edit_modal_title') }}</h3>
            <button type="button" class="text-gray-300 hover:text-white material-symbols-outlined" data-edit-close>close</button>
        </div>

        <div id="editMetaErrors" class="hidden p-3 mb-3 text-sm text-red-300 rounded bg-red-900/30"></div>

        <form id="editMetaForm" class="space-y-4" autocomplete="off">
            @csrf
            <div>
                <label class="block mb-1 text-sm text-gray-300">{{ __('collection.show.field_name') }}</label>
                <input name="collection_name" type="text" maxlength="150" required
                       class="w-full px-3 py-2 text-white bg-gray-800 border border-gray-700 rounded"
                       value="{{ e($collection->collection_name) }}" />
            </div>

            <div>
                <label class="block mb-1 text-sm text-gray-300">{{ __('collection.show.field_description') }}</label>
                <textarea name="description" maxlength="2000" rows="3"
                          class="w-full px-3 py-2 text-white bg-gray-800 border border-gray-700 rounded">{{ e($collection->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block mb-1 text-sm text-gray-300">{{ __('collection.show.field_website_url') }}</label>
                    <input name="url_collection_site" type="url" maxlength="255"
                           class="w-full px-3 py-2 text-white bg-gray-800 border border-gray-700 rounded"
                           value="{{ e($collection->url_collection_site) }}" />
                </div>
                <div>
                    <label class="block mb-1 text-sm text-gray-300">{{ __('collection.show.field_type') }}</label>
                    <input name="type" type="text" maxlength="50"
                           class="w-full px-3 py-2 text-white bg-gray-800 border border-gray-700 rounded"
                           value="{{ e($collection->type) }}" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="block mb-1 text-sm text-gray-300">{{ __('collection.show.field_floor_price') }}</label>
                    <input name="floor_price" type="number" min="0" step="0.01"
                           class="w-full px-3 py-2 text-white bg-gray-800 border border-gray-700 rounded"
                           value="{{ e($collection->floor_price) }}" />
                </div>
                <div class="flex items-center gap-2 mt-6">
                    <input id="is_published_input" name="is_published" type="checkbox" class="w-4 h-4"
                           {{ $collection->is_published ? 'checked' : '' }}
                           {{ (method_exists($collection,'canBePublished') && !$collection->canBePublished()) ? 'disabled' : '' }} />
                    <label for="is_published_input" class="text-sm text-gray-300">{{ __('collection.show.toggle_published') }}</label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" class="px-4 py-2 font-medium text-gray-200 bg-gray-700 rounded hover:bg-gray-600" data-edit-cancel>{{ __('collection.show.btn_cancel') }}</button>
                <button type="button" class="px-4 py-2 font-medium text-white rounded bg-emerald-600 hover:bg-emerald-700" data-edit-save>{{ __('collection.show.btn_save') }}</button>
            </div>
        </form>
    </div>
</div>
@endif
