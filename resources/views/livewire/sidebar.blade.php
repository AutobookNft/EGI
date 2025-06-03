<div class="drawer-side">
    <!-- drawer-overlay gestisce il click fuori dalla sidebar per chiuderla su mobile -->
    <label for="main-drawer" class="drawer-overlay"></label>
    <aside class="flex flex-col min-h-screen w-80 bg-neutral text-neutral-content">
        <!-- Titolo del Contesto -->
        <div class="p-6 text-2xl font-semibold border-b border-neutral-focus">
            {{ __($contextTitle) }}
        </div>

        <div class="px-4 py-6">
            <x-back-button />
        </div>

        <!-- Menu OS1 Enhanced with Modal Support -->
        <div class="flex-1 px-4 py-2 space-y-3 overflow-y-auto">
            @if (!empty($menus))
                @foreach ($menus as $key => $menu)
                    @if (empty($menu['permission']) || Gate::allows($menu['permission']))
                        @php
                            // Logica per determinare se il gruppo o un suo item è attivo
                            $isGroupActive = false;
                            $currentRouteName = Route::currentRouteName();

                            if (!empty($menu['items'])) {
                                foreach ($menu['items'] as $subItem) {
                                    // OS1 Enhancement: Skip modal actions in active route detection
                                    if (!$subItem['is_modal_action'] && $currentRouteName == $subItem['route']) {
                                        $isGroupActive = true;
                                        break;
                                    }
                                }
                            } elseif (isset($menu['summary_route']) && $currentRouteName == $menu['summary_route']) {
                                $isGroupActive = true;
                            }
                        @endphp

                        @if (!empty($menu['items']))
                            <!-- Summary con sottomenù -->
                            <details class="bg-transparent collapse collapse-arrow group" @if($isGroupActive) open @endif>
                                <summary class="list-none
                                            {{ $isGroupActive ? 'bg-primary text-primary-content shadow-sm rounded-md' : 'hover:bg-base-content hover:bg-opacity-10 rounded-md' }}
                                            transition-colors duration-150 ease-in-out cursor-pointer
                                            focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                                    <div class="flex items-center gap-3 px-3 py-3 text-base font-medium collapse-title">
                                        @if (!empty($menu['icon']))
                                            <span class="flex-shrink-0 {{ $isGroupActive ? '' : 'opacity-60 group-hover:opacity-100 transition-opacity' }}">
                                                {!! $menu['icon'] !!}
                                            </span>
                                        @endif
                                        <span class="flex-grow truncate">{{ $menu['name'] }}</span>
                                    </div>
                                </summary>

                                <!-- OS1 Enhanced Submenu Content -->
                                <div class="pt-2 pb-1 pl-6 pr-2 space-y-1 collapse-content">
                                    @foreach ($menu['items'] as $item)
                                        @if (!empty($item['permission']))
                                            @php
                                                // Only check active state for non-modal items
                                                $isItemActive = (!$item['is_modal_action'] && $currentRouteName == $item['route']);
                                            @endphp

                                            @if ($item['is_modal_action'])
                                                <!-- OS1 Modal Action Button -->
                                                <button type="button"
                                                       @foreach($item['html_attributes'] as $attr => $value)
                                                           {{ $attr }}="{{ $value }}"
                                                       @endforeach
                                                       class="flex items-center justify-start w-full gap-3 px-3 py-2.5 rounded-md text-left
                                                              text-sm hover:bg-base-content hover:bg-opacity-10
                                                              transition-colors duration-150 ease-in-out
                                                              focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                                                    @if (!empty($item['icon']))
                                                        <span class="flex-shrink-0 transition-opacity opacity-60 group-hover:opacity-100">
                                                            {!! $item['icon'] !!}
                                                        </span>
                                                    @else
                                                        <span class="w-5 h-5"></span>
                                                    @endif
                                                    <span class="flex-grow truncate">{{ $item['name'] }}</span>
                                                    <!-- OS1 Visual Indicator for Modal Actions -->
                                                    <span class="text-xs material-symbols-outlined opacity-40">
                                                        open_in_new
                                                    </span>
                                                </button>
                                            @else
                                                <!-- Traditional Route Link -->
                                                <a href="{{ $item['href'] }}"
                                                   class="flex items-center justify-start w-full gap-3 px-3 py-2.5 rounded-md
                                                          text-sm {{ $isItemActive ? 'bg-primary/80 text-primary-content font-semibold shadow-sm' : 'hover:bg-base-content hover:bg-opacity-10' }}
                                                          transition-colors duration-150 ease-in-out">
                                                    @if (!empty($item['icon']))
                                                        <span class="flex-shrink-0 {{ $isItemActive ? '' : 'opacity-60 group-hover:opacity-100 transition-opacity' }}">
                                                            {!! $item['icon'] !!}
                                                        </span>
                                                    @else
                                                        <span class="w-5 h-5"></span>
                                                    @endif
                                                    <span class="flex-grow truncate">{{ $item['name'] }}</span>
                                                </a>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                            </details>
                        @else
                            <!-- Link diretto (senza sottomenu) -->
                            @if (isset($menu['is_modal_action']) && $menu['is_modal_action'])
                                <!-- OS1 Modal Action (Direct) -->
                                <button type="button"
                                       @foreach($menu['html_attributes'] as $attr => $value)
                                           {{ $attr }}="{{ $value }}"
                                       @endforeach
                                       class="flex items-center w-full gap-3 px-3 py-3 text-base font-medium text-left list-none transition-colors duration-150 ease-in-out rounded-md hover:bg-base-content hover:bg-opacity-10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                                    @if (!empty($menu['icon']))
                                         <span class="flex-shrink-0 transition-opacity opacity-60 group-hover:opacity-100">
                                            {!! $menu['icon'] !!}
                                        </span>
                                    @endif
                                    <span class="flex-grow truncate">{{ $menu['name'] }}</span>
                                    <span class="text-xs material-symbols-outlined opacity-40">
                                        open_in_new
                                    </span>
                                </button>
                            @else
                                <!-- Traditional Route Link (Direct) -->
                                <a href="{{ route($menu['summary_route']) }}"
                                   class="flex items-center gap-3 px-3 py-3 text-base font-medium rounded-md list-none
                                          {{ $isGroupActive ? 'bg-primary text-primary-content shadow-sm' : 'hover:bg-base-content hover:bg-opacity-10' }}
                                          transition-colors duration-150 ease-in-out">
                                    @if (!empty($menu['icon']))
                                         <span class="flex-shrink-0 {{ $isGroupActive ? '' : 'opacity-60 group-hover:opacity-100 transition-opacity' }}">
                                            {!! $menu['icon'] !!}
                                        </span>
                                    @endif
                                    <span class="flex-grow truncate">{{ $menu['name'] }}</span>
                                </a>
                            @endif
                        @endif

                        @if (!$loop->last)
                           <x-separator class="!my-1 border-neutral-focus/20" />
                        @endif

                    @endif
                @endforeach
            @else
                <p class="text-center text-neutral-content opacity-60">
                    Nessun menu disponibile
                </p>
            @endif
        </div>
    </aside>
</div>
