<div class="drawer-side">
    <label for="main-drawer" class="drawer-overlay"></label>
    <aside class="min-h-screen w-80 bg-base-100 flex flex-col">
        <!-- Titolo del Contesto -->
        <div class="p-4 bg-gray-900 text-white text-2xl font-bold">
            {{ __($contextTitle) }}
        </div>

        <!-- Menu -->
        <div class="space-y-4 p-4 flex-1 overflow-y-auto">
            @foreach ($menus as $menu)
                <div class="mb-4">
                    <div class="text-lg font-semibold flex items-center gap-2">
                        @if (!empty($menu['icon']))
                            {!! $menu['icon'] !!}
                        @endif
                        {{ $menu['name'] }}
                    </div>

                    @if (!empty($menu['items']))
                        <ul class="mt-2 space-y-2">
                            @foreach ($menu['items'] as $item)
                                @if (!empty($item['children']))
                                    <!-- Menu con sotto-menu -->
                                    <details class="collapse collapse-arrow bg-base-200">
                                        <summary class="collapse-title text-lg font-medium">
                                            <div class="flex gap-2">
                                                @if (!empty($item['icon']))
                                                    {!! $item['icon'] !!}
                                                @endif
                                                {{ $item['name'] }}
                                            </div>
                                        </summary>
                                        <div class="collapse-content space-y-2">
                                            @foreach ($item['children'] as $child)
                                                <a href="{{ route($child['route']) }}"
                                                   class="btn btn-ghost w-full justify-start">
                                                    @if (!empty($child['icon']))
                                                        {!! $child['icon'] !!}
                                                    @endif
                                                    {{ $child['name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </details>
                                @else
                                    <!-- Menu senza sotto-menu -->
                                    <a href="{{ route($item['route']) }}"
                                       class="btn btn-ghost w-full justify-start">
                                        @if (!empty($item['icon']))
                                            {!! $item['icon'] !!}
                                        @endif
                                        {{ $item['name'] }}
                                    </a>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
    </aside>
</div>
