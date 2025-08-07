{{-- resources/views/collector/index.blade.php --}}
<x-guest-layout :title="__('collector.index.page_title')" :metaDescription="__('collector.index.meta_description')">

    <x-slot name="heroFullWidth">
        <div class="relative bg-gray-900 py-16 sm:py-24 lg:py-32">
            <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="font-display text-4xl font-extrabold text-white sm:text-5xl lg:text-6xl">
                        {{ __('collector.index.main_title') }}
                    </h1>
                    <p class="mx-auto mt-4 max-w-3xl text-xl text-gray-300">
                        {{ __('collector.index.subtitle') }}
                    </p>
                </div>

                <div class="mt-12 rounded-xl bg-gray-800 p-6 shadow-lg">
                    <form action="{{ route('collector.index') }}" method="GET"
                        class="grid grid-cols-1 gap-6 md:grid-cols-3 lg:grid-cols-4">
                        <div class="md:col-span-2 lg:col-span-2">
                            <label for="query" class="sr-only block text-sm font-medium text-gray-300">
                                {{ __('collector.index.search_placeholder') }}
                            </label>
                            <div class="relative">
                                <input type="search" name="query" id="query" value="{{ $query ?? '' }}"
                                    placeholder="{{ __('collector.index.search_placeholder') }}"
                                    class="block w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-3 text-white placeholder-gray-400 focus:border-verde-rinascita focus:ring-verde-rinascita">
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="material-symbols-outlined text-gray-400">search</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="sort" class="sr-only block text-sm font-medium text-gray-300">
                                {{ __('collector.index.sort_by') }}
                            </label>
                            <select id="sort" name="sort"
                                class="block w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-3 text-white focus:border-verde-rinascita focus:ring-verde-rinascita">
                                <option value="latest" @selected($sort == 'latest')>
                                    {{ __('collector.index.sort_latest') }}</option>
                                <option value="most_egis" @selected($sort == 'most_egis')>
                                    {{ __('collector.index.sort_most_egis') }}</option>
                                <option value="most_spent" @selected($sort == 'most_spent')>
                                    {{ __('collector.index.sort_most_spent') }}</option>
                            </select>
                        </div>

                        <div class="flex justify-end md:col-span-3 lg:col-span-4">
                            <a href="{{ route('collector.index') }}"
                                class="btn btn-secondary rounded-lg px-6 py-3 text-white transition-colors duration-200 hover:bg-gray-700">
                                {{ __('collector.index.reset_filters') }}
                            </a>
                            <button type="submit"
                                class="btn btn-primary ml-4 rounded-lg bg-verde-rinascita px-6 py-3 text-white transition-colors duration-200 hover:bg-verde-rinascita-dark">
                                {{ __('collector.index.apply_filters') }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @forelse($collectors as $collector)
                        <x-collector-card :collector="$collector" />
                    @empty
                        <div class="col-span-full py-12 text-center text-gray-400">
                            <p>{{ __('collector.index.no_collectors_found') }}</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-12">
                    {{ $collectors->links() }}
                </div>
            </div>
        </div>
    </x-slot>

</x-guest-layout>
