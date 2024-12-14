<script>
    console.log('resources/views/livewire/sidebar.blade.php');
</script>
<div class="drawer-side">
    <label for="main-drawer" class="drawer-overlay"></label>
    <aside class="min-h-screen w-80 bg-base-100">
        <div class="space-y-4 p-4">
            @if (!empty($menus))
                @foreach ($menus as $menu)
                    @can($menu['permission'])
                        @if (count($menu['submenu']) > 0)

                            <!-- Summary con sottomenù -->
                            <details class="collapse collapse-arrow bg-base-200">
                                @if($menu['summary_head'] == true)
                                    <div class="flex gap-2">
                                        @if ($menu['summary_icon'])
                                            {!! $menu['summary_icon'] !!}
                                        @endif
                                        {{ $menu['name'] }}
                                    </div>
                                @else
                                    <summary class="collapse-title text-lg font-medium">
                                        <div class="flex gap-2">
                                            @if ($menu['summary_icon'])
                                                {!! $menu['summary_icon'] !!}
                                            @endif
                                            {{ $menu['name'] }}
                                        </div>
                                    </summary>
                                @endif

                                <div class="collapse-content space-y-2">
                                    @foreach ($menu['submenu'] as $submenu)
                                        @can($submenu['permission'])
                                        <a href="{{ Route::has($submenu['route']) ? route($submenu['route']) : '#' }}"
                                            class="{{ request()->routeIs($submenu['route']) ? 'btn-active' : '' }} btn btn-ghost w-full justify-start">
                                            {{-- @if($submenu['icon'] === 'fallback')
                                                <img src="{{ config('app.logo_04') }}" alt="{{ $submenu['name'] }}" class="w-12 h-12">
                                            @else --}}
                                                {!! $submenu['icon'] !!}
                                            {{-- @endif --}}
                                                {{ $submenu['name'] }}
                                            </a>
                                        @endcan
                                    @endforeach
                                </div>
                            </details>
                        @else
                            <!-- Summary senza voci -->
                            <a href="{{ Route::has($menu['summary_route']) ? route($menu['summary_route']) : '#' }}"
                                class="text-lg font-medium text-gray-500">
                                <div class="flex gap-2 mb-8 mt-32">
                                    <div class = "mt-0"> {!! $menu['summary_icon'] !!} </div>
                                    <div class = "mt-1">{{ $menu['name'] }}</div>
                                </div>
                            </a>
                            
                            <div class="border-t border-gray-200 dark:border-gray-600"></div>

                        @endif
                    @endcan
                @endforeach
            @else
                <p class="text-center">Nessun menu disponibile</p>
            @endif

        </div>
    </aside>
</div>
