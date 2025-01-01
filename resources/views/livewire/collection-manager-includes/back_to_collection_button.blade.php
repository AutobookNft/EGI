<div class="mt-8 ml-2">
    <a href="{{ route('collections.edit', ['id' => $collection->id]) }}" class="p-4">
        <!-- disegna un bottone primario -->
        <div class="flex gap-2 border border-green-500 p-4 w-fit rounded-lg items-center bg-green-500 hover:bg-green-600 text-white font-bold shadow-md transition-all duration-300 cursor-pointer">
            <x-repo-icon name="back" class="text-gray-500 opacity-50" />
            {{ __('collection.came_back_to_collection') }}
        </div>

    </a>
</div>
