<div class="drawer-side">
    <!-- drawer-overlay gestisce il click fuori dalla sidebar per chiuderla su mobile -->
    <label for="main-drawer" class="drawer-overlay"></label>
    {{-- MODIFICA 1: Sfondo sidebar più scuro e testo leggermente più chiaro per contrasto elegante --}}
    <aside class="flex flex-col min-h-screen w-80 bg-neutral text-neutral-content">
        <!-- Titolo del Contesto -->
        {{-- MODIFICA 2: Titolo contesto con un accento e meno stacco --}}
        <div class="p-6 text-2xl font-semibold border-b border-neutral-focus">
            {{-- text-white era ok, ma text-neutral-content si adatta allo sfondo --}}
            {{-- bg-gray-900 è molto scuro, usiamo il focus del tema DaisyUI o un colore specifico --}}
            {{ __($contextTitle) }}
        </div>

        {{-- MODIFICA 3: Spaziatura per il back-button --}}
        <div class="px-4 py-6">
            <x-back-button />
        </div>

        {{-- MODIFICA 4: Separatore più sottile o diverso se mantenuto --}}
        {{-- <x-separator class="border-neutral-focus/50" /> --}}
        {{-- Oppure lo rimuoviamo e usiamo margini/padding --}}

        <!-- Menu -->
        {{-- MODIFICA 5: Padding generale del contenitore menu e spaziatura tra gruppi --}}
        <div class="flex-1 px-4 py-2 space-y-3 overflow-y-auto">
            @if (!empty($menus))
                @foreach ($menus as $key => $menu) {{-- Aggiunto $key per logica item attivi --}}
                    @if (empty($menu['permission']) || Gate::allows($menu['permission']))
                        @php
                            // Logica per determinare se il gruppo o un suo item è attivo
                            // Questa è una semplificazione, dovrai adattarla alla tua logica di route attive
                            // Potresti passare una variabile $activeMenuKey o $activeRouteName dal controller
                            $isGroupActive = false; // Inizializza
                            $currentRouteName = Route::currentRouteName();

                            if (!empty($menu['items'])) {
                                foreach ($menu['items'] as $subItem) {
                                    if ($currentRouteName == $subItem['route']) {
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
                            {{-- MODIFICA 6: Stile per <details> e <summary> --}}
                            <details class="bg-transparent collapse collapse-arrow group" @if($isGroupActive) open @endif>
                                <summary class="list-none
                                            {{ $isGroupActive ? 'bg-primary text-primary-content shadow-sm rounded-md' : 'hover:bg-base-content hover:bg-opacity-10 rounded-md' }}
                                            transition-colors duration-150 ease-in-out cursor-pointer
                                            focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"> {{-- Miglioramento accessibilità focus --}}
                                    {{-- Contenitore Flex per icona e testo, indipendente dalla freccia del details --}}
                                    <div class="flex items-center gap-3 px-3 py-3 text-base font-medium collapse-title"> {{-- collapse-title gestisce il padding per la freccia --}}
                                        @if (!empty($menu['icon']))
                                            <span class="flex-shrink-0 {{ $isGroupActive ? '' : 'opacity-60 group-hover:opacity-100 transition-opacity' }}">
                                                {!! $menu['icon'] !!}
                                            </span>
                                        @endif
                                        <span class="flex-grow truncate">{{ $menu['name'] }}</span>
                                    </div>
                                </summary>


                                {{-- MODIFICA 7: Stile per il contenuto del sottomenu --}}
                                <div class="pt-2 pb-1 pl-6 pr-2 space-y-1 collapse-content">
                                    @foreach ($menu['items'] as $item)
                                        @if (empty($item['permission']) || Gate::allows($item['permission']))
                                            @php
                                                $isItemActive = ($currentRouteName == $item['route']);
                                            @endphp
                                            <a href="{{ route($item['route']) }}"
                                               class="flex items-center justify-start w-full gap-3 px-3 py-2.5 rounded-md
                                                      text-sm {{-- Aumentato leggermente il font size per leggibilità --}}
                                                      {{ $isItemActive ? 'bg-primary/80 text-primary-content font-semibold shadow-sm' : 'hover:bg-base-content hover:bg-opacity-10' }}
                                                      transition-colors duration-150 ease-in-out">
                                                @if (!empty($item['icon']))
                                                    <span class="flex-shrink-0 {{ $isGroupActive ? '' : 'opacity-60 group-hover:opacity-100 transition-opacity' }}">
                                                        {!! $item['icon'] !!}
                                                    </span>
                                                @else
                                                    {{-- Placeholder per allineamento se l'icona manca --}}
                                                    <span class="w-5 h-5"></span>
                                                @endif
                                                <span class="flex-grow truncate">{{ $item['name'] }}</span>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </details>
                        @else
                            <!-- Link diretto (senza sottomenu) -->
                            {{-- MODIFICA 8: Stile per link diretti --}}
                            <a href="{{ route($menu['summary_route']) }}"
                               class="flex items-center gap-3 px-3 py-3 text-base font-medium rounded-md list-none
                                      {{ $isGroupActive ? 'bg-primary text-primary-content shadow-sm' : 'hover:bg-base-content hover:bg-opacity-10' }}
                                      transition-colors duration-150 ease-in-out">
                                @if (!empty($menu['icon']))
                                     <span class="flex-shrink-0 {{ $isGroupActive ? '' : 'opacity-60 group-hover:opacity-100 transition-opacity' }}">
                                        {!! $menu['icon'] !!}
                                    </span>
                                @endif
                                <span class="flex-grow truncate">{{ $item['name'] }}</span>
                            </a>
                        @endif

                        {{-- MODIFICA 9: Separatore tra gruppi di menu più discreto o rimosso se la spaziatura è sufficiente --}}
                        @if (!$loop->last) {{-- Non mostrare il separatore dopo l'ultimo elemento --}}
                           {{-- <div class="my-2 border-t border-neutral-focus/30"></div> --}}
                           {{-- Oppure rimuovere <x-separator /> se `space-y-3` è sufficiente --}}
                           <x-separator class="!my-1 border-neutral-focus/20" /> {{-- Rende lo spazio e il separatore più piccolo --}}
                        @endif

                    @endif
                @endforeach
            @else
                <p class="text-center text-neutral-content opacity-60">{{-- Testo per "nessun menu" --}}
                    Nessun menu disponibile
                </p>
            @endif
        </div>
    </aside>
</div>
