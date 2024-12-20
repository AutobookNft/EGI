<div class="drawer-side">
    <label for="main-drawer" class="drawer-overlay"></label>
    <aside class="min-h-screen w-80 bg-base-100 flex flex-col">
        <!-- Titolo del Contesto -->
        <div class="p-4 bg-gray-900 text-white text-2xl font-bold">
            {{ __($contextTitle) }}
        </div>

        <!-- Menu -->
        <div class="space-y-4 p-4 flex-1 overflow-y-auto">
            @if (!empty($menus))
                @foreach ($menus as $menu)
                    @if (empty($menu['permission']) || Gate::allows($menu['permission']))
                        @if (!empty($menu['items']))
                            <!-- Summary con sottomenù -->
                            <details class="collapse collapse-arrow bg-base-200">
                                <summary class="collapse-title text-lg font-medium flex items-center gap-2">
                                    @if (!empty($menu['icon']))
                                        {!! $menu['icon'] !!}
                                    @endif
                                    <span>{{ $menu['name'] }}</span>
                                </summary>

                                <div class="collapse-content space-y-2 ml-4">
                                    @foreach ($menu['items'] as $item)
                                        @if (empty($item['permission']) || Gate::allows($item['permission']))
                                            <a href="{{ route($item['route']) }}"
                                               class="btn btn-ghost w-full justify-start flex items-center gap-2 hover:bg-gray-200">
                                                {!! $item['icon'] !!}
                                                <span>{{ $item['name'] }}</span>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </details>
                        @else
                            <!-- Summary senza voci -->
                            <a href="{{ route($menu['summary_route']) }}"
                               class="text-lg font-medium text-gray-700 flex items-center gap-2 hover:text-blue-600">
                                @if (!empty($menu['icon']))
                                    {!! $menu['icon'] !!}
                                @endif
                                <span>{{ $menu['name'] }}</span>
                            </a>
                            <div class="border-t border-gray-300 my-4"></div>
                        @endif
                    @endif
                @endforeach
            @else
                <p class="text-center text-gray-500">Nessun menu disponibile</p>
            @endif
        </div>
    </aside>
</div>

