<div class="bg-gray-800/50 rounded-lg p-3 mb-4 border border-purple-500/30">
    <h3 class="text-base font-semibold text-white mb-3">{{ trans('uploadmanager::uploadmanager.quick_egi_metadata') }}</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3">

        {{-- Riga 1: Titolo e Floor Price --}}
        <div>
            <label for="egi-title" class="block text-xs font-medium text-gray-300 mb-0.5">{{ trans('uploadmanager::uploadmanager.egi_title') }}</label>
            <input type="text" id="egi-title" name="egi-title" placeholder="{{ trans('uploadmanager::uploadmanager.egi_title_placeholder') }}"
                class="w-full px-2 py-1.5 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-gray-500 text-sm">
            <p class="text-[10px] text-gray-400 mt-0.5">{{ trans('uploadmanager::uploadmanager.egi_title_info') }}</p>
        </div>

        <div>
            <label for="egi-floor-price" class="block text-xs font-medium text-gray-300 mb-0.5">{{ trans('uploadmanager::uploadmanager.floor_price') }}</label>
            <input type="number" step="0.01" min="0" id="egi-floor-price" name="egi-floor-price" placeholder="{{ trans('uploadmanager::uploadmanager.floor_price_placeholder') }}"
                class="w-full px-2 py-1.5 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-gray-500 text-sm [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
            <p class="text-[10px] text-gray-400 mt-0.5">{{ trans('uploadmanager::uploadmanager.floor_price_info') }}</p>
        </div>

        {{-- Riga 2: Data e Posizione --}}
        <div>
            <label for="egi-date" class="block text-xs font-medium text-gray-300 mb-0.5">{{ trans('uploadmanager::uploadmanager.creation_date') }}</label>
            <input type="date" id="egi-date" name="egi-date"
                class="w-full px-2 py-1.5 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-gray-500 text-sm"
                style="color-scheme: dark;">
            <p class="text-[10px] text-gray-400 mt-0.5">{{ trans('uploadmanager::uploadmanager.creation_date_info') }}</p>
        </div>

        <div>
            <label for="egi-position" class="block text-xs font-medium text-gray-300 mb-0.5">{{ trans('uploadmanager::uploadmanager.position') }}</label>
            <input type="number" step="1" min="1" id="egi-position" name="egi-position" placeholder="{{ trans('uploadmanager::uploadmanager.position_placeholder') }}"
                class="w-full px-2 py-1.5 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-gray-500 text-sm [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
            <p class="text-[10px] text-gray-400 mt-0.5">{{ trans('uploadmanager::uploadmanager.position_info') }}</p>
        </div>

        {{-- Riga 3: Descrizione (occupa 2 colonne) --}}
        <div class="md:col-span-2">
            <label for="egi-description" class="block text-xs font-medium text-gray-300 mb-0.5">{{ trans('uploadmanager::uploadmanager.egi_description') }}</label>
            <textarea id="egi-description" name="egi-description" rows="2" placeholder="{{ trans('uploadmanager::uploadmanager.egi_description_placeholder') }}"
                    class="w-full px-2 py-1.5 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-gray-500 text-sm"></textarea>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ trans('uploadmanager::uploadmanager.metadata_notice') }}</p>
        </div>
    </div>
    <div class="flex items-center justify-end gap-2 my-4">
        <input
            class="me-1 h-3 w-6 appearance-none rounded-full bg-gray-600 before:pointer-events-none before:absolute before:h-3 before:w-3 before:rounded-full before:bg-transparent after:absolute after:z-[2] after:-mt-0.25 after:h-4 after:w-4 after:rounded-full after:bg-white after:shadow-sm after:transition-all checked:bg-green-500 checked:after:ms-3 checked:after:bg-green-300 checked:after:shadow-sm hover:cursor-pointer focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-gray-900"
            type="checkbox"
            role="switch"
            id="egi-publish"
            name="egi-publish"
            checked
            title="{{ trans('uploadmanager::uploadmanager.toggle_publish_status') }}"
        />
        <label
            class="font-medium hover:pointer-events-none text-green-300 text-xs"
            id="egi-publish_label"
            for="egi-publish"
        >{{ trans('uploadmanager::uploadmanager.publish_egi') }}</label>
    </div>
</div>
