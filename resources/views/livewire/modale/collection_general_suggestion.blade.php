@php
    use Illuminate\Support\Str;

    $markdownFile = resource_path('markdown/collection_general_suggestion.md');
    $content = Str::markdown(file_get_contents($markdownFile));
@endphp

<!-- Finestra modale -->
<div x-data="{ open: false }">

    <!-- Pulsante per aprire la modale -->
    <x-suggestion-icon :tooltip="__('collection.tips_to_optimize_your_collection')" icon-color="#5f6368" />

    <!-- Modale -->
    <div
        x-show="open"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
        x-cloak
    >
        <div class="bg-white rounded-lg shadow-lg max-w-3xl w-full p-6 relative overflow-hidden">
            <!-- Titolo -->
            <div class="border-b pb-4 mb-4">
                <h2 class="text-xl font-bold text-gray-800">{{ __('collection.tips_to_optimize_your_collection') }}</h2>
                <button
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-800"
                    @click="open = false"
                >
                    &times;
                </button>
            </div>

            <!-- Contenuto Scrollabile -->
            <div class="overflow-y-auto max-h-96 px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 prose">
                {!! $content !!}
            </div>

            <!-- Pulsante di chiusura -->
            <div class="text-right mt-4">
                <button
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                    @click="open = false"
                >
                    Chiudi
                </button>
            </div>
        </div>
    </div>
</div>
