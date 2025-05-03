{{-- resources/views/components/collection-card.blade.php --}}
{{-- 📜 Oracode Blade Component: Collection Card --}}
{{-- Displays a single EGI Collection card with image, basic info, stats, and actions. --}}
{{-- Designed for use within grids like collections-grid.blade.php. --}}
{{-- Expects a $collection object (App\Models\Collection). --}}
{{-- Parameters like $editable or $imageType can be passed via @props if needed, --}}
{{-- but here we assume $collection and potentially $editable / $imageType are passed directly. --}}
{{-- Uses Tailwind CSS for styling. --}}

{{-- 🧱 Card Container --}}
{{-- @style: Background, rounded corners, shadow, overflow hidden for image clipping, flex column layout, transition for hover effects. --}}

<div class="collection-card bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 ease-in-out overflow-hidden flex flex-col">

    {{-- 🖼️ Sezione Immagine (con overlay per badge) --}}
    {{-- @style: Relative positioning for badges, aspect ratio (aspect-video or aspect-square), background for placeholder. --}}
    <div class="relative w-full aspect-video bg-gray-200"> {{-- Adjust aspect ratio as needed: aspect-video, aspect-square, aspect-[4/3] etc. --}}

        {{-- 🎯 Immagine Principale o Placeholder --}}
        {{-- @logic: Display image if available, otherwise show a placeholder. --}}
        @php
            // Determina quale immagine usare (card di default o altra passata)
            $imagePath = $imageType ?? 'card' === 'card' ? $collection->image_card : ($imageType === 'banner' ? $collection->image_banner : $collection->image_avatar);

        @endphp

        @if ($imagePath)
            {{-- @style: Cover the container, center the image. --}}
            {{-- @accessibility-trait: Alt text describes the collection. `loading=lazy` for performance. --}}
            <img src="{{ $imagePath }}"
                 alt="{{ $collection->collection_name }}"
                 class="absolute inset-0 w-full h-full object-cover"
                 loading="lazy">
        @else
            {{-- @style: Placeholder styling, centered icon. --}}
            <div class="absolute inset-0 w-full h-full flex items-center justify-center bg-gray-300 text-gray-500">
                {{-- @accessibility-trait: Icon is decorative. --}}
                <svg class="w-12 h-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm16.5-1.5H3.75" />
                </svg>
            </div>
        @endif

        {{-- 🏷️ Badge Status (se non pubblicata) --}}
        {{-- @logic: Show only if status is not 'published'. --}}
        @if ($collection->status !== 'published')
            {{-- @style: Positioned top-left, specific background/text color based on status (example). --}}
            <span @class([
                'absolute top-2 left-2 inline-block px-2 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider',
                'bg-yellow-200 text-yellow-800' => $collection->status === 'draft',
                'bg-blue-200 text-blue-800' => $collection->status === 'pending_approval', // Example
                'bg-gray-200 text-gray-800' => !in_array($collection->status, ['draft', 'pending_approval']), // Fallback
            ])>
                {{ __(ucfirst(str_replace('_', ' ', $collection->status))) }}
            </span>
        @endif

        {{-- 💎 Badge Tipo (se definito) --}}
        {{-- @logic: Show if type is set. --}}
        @if ($collection->type)
            {{-- @style: Positioned top-right, standard badge style. --}}
            <span class="absolute top-2 right-2 inline-block bg-indigo-100 text-indigo-800 px-2 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider">
                {{ __(ucfirst($collection->type)) }}
            </span>
        @endif

        {{-- ❤️ Pulsante Like (overlay sull'immagine) --}}
        {{-- @style: Positioned bottom-right, circular, background with opacity, hover effect. --}}
        {{-- @interactivity: Requires JS for toggling state and count update. `data-collection-id` for JS targeting. --}}
        <button aria-label="{{ __('Like this collection') }}"
                class="absolute bottom-2 right-2 w-8 h-8 rounded-full bg-black bg-opacity-40 hover:bg-opacity-60 text-white flex items-center justify-center transition duration-150 ease-in-out like-button {{ $collection->is_liked ?? false ? 'is-liked' : '' }}"
                data-collection-id="{{ $collection->id }}"
                data-like-url="{{ route('api.toggle.collection.like', $collection->id) }}" {{-- Assicurati che questa rotta API esista --}}
                >
            {{-- @accessibility-trait: Screen reader text within button. --}}
            <span class="sr-only">{{ __('Like') }}</span>
            {{-- @style: Icon changes color based on 'is-liked' class (added by JS). --}}
            <svg class="w-5 h-5 icon-heart" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 0 1 5.656 0L10 6.343l1.172-1.171a4 4 0 1 1 5.656 5.656L10 17.657l-6.828-6.829a4 4 0 0 1 0-5.656Z" clip-rule="evenodd" />
            </svg>
        </button>
        {{-- Stile CSS per il cuore quando è liked (da mettere nel CSS principale o <style>) --}}
        {{--
            <style>
                .like-button.is-liked .icon-heart { color: #ef4444; } /* text-red-500 */
                .like-button:not(.is-liked) .icon-heart { color: white; }
            </style>
         --}}

    </div>

    {{-- ℹ️ Sezione Informazioni --}}
    {{-- @style: Padding, flex layout for vertical stacking, grow to fill space. --}}
    <div class="p-4 flex-grow flex flex-col justify-between">
        <div>
            {{-- 👑 Titolo Collection (Link) --}}
            {{-- @style: Font size, weight, color, truncation for long names, hover effect. --}}
            <h3 class="text-lg font-semibold text-gray-900 mb-2 truncate">
                <a href="{{ route('home.collections.show', $collection->id) }}" class="hover:text-indigo-600 transition-colors duration-200" title="{{ $collection->collection_name }}">
                    {{ $collection->collection_name }}
                </a>
            </h3>

            {{-- 👤 Creator Info --}}
            {{-- @style: Flex layout, vertical centering, text size/color. --}}
            <div class="flex items-center text-sm text-gray-600 mb-4">
                {{-- Avatar --}}
                @if ($collection->creator && $collection->creator->profile_photo_url)
                    {{-- @accessibility-trait: Alt text identifies the creator. --}}
                    <img src="{{ $collection->creator->profile_photo_url }}" alt="{{ $collection->creator->name }}" class="w-6 h-6 rounded-full mr-2 object-cover">
                @else
                    {{-- Placeholder Avatar --}}
                    <span class="inline-block h-6 w-6 rounded-full overflow-hidden bg-gray-200 mr-2">
                        <svg class="h-full w-full text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </span>
                @endif
                {{-- Nome Creator --}}
                <span class="creator-name truncate">{{ $collection->creator->name ?? __('Unknown Creator') }}</span>

                {{-- 💎 Badge Verificato (Esempio) --}}
                @if ($collection->creator && $collection->creator->usertype === 'verified') {{-- Ajusta logica verifica --}}
                    <svg class="ml-1 w-4 h-4 text-blue-500 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" title="{{ __('Verified Creator') }}">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.06 0l4-5.5Z" clip-rule="evenodd" />
                    </svg>
                @endif
            </div>

            {{-- 📊 Statistiche (EGI Count, Likes, Reservations) --}}
            {{-- @style: Flex layout, gap, text size/color for stats. --}}
            <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                {{-- Numero EGI --}}
                <div class="flex items-center" title="{{ __('Number of EGIs') }}">
                    {{-- Icona EGI (esempio cubo) --}}
                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                    <span>{{ $collection->EGI_number ?? $collection->egis_count ?? 0 }}</span> {{-- Usa EGI_number se esiste, altrimenti il count eager-loaded --}}
                </div>
                {{-- Conteggio Likes --}}
                <div class="flex items-center" title="{{ __('Likes') }}">
                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M9.653 16.915l-.005-.003-.019-.01a20.759 20.759 0 0 1-1.162-.682 22.045 22.045 0 0 1-2.582-1.9C4.045 12.733 2 10.352 2 7.5a4.5 4.5 0 0 1 8-2.828A4.5 4.5 0 0 1 18 7.5c0 2.852-2.044 5.233-3.885 6.82a22.049 22.049 0 0 1-3.744 2.582l-.019.01-.005.003h-.002a.739.739 0 0 1-.69.001l-.002-.001Z" />
                    </svg>
                    <span class="like-count-display">{{ $collection->likes_count ?? 0 }}</span> {{-- Usa il count eager-loaded --}}
                </div>
                {{-- Conteggio Reservations --}}
                <div class="flex items-center" title="{{ __('Reservations') }}">
                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75a3 3 0 0 1-3 3h-3.75a3 3 0 0 1-3-3V6m-1.5 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V7.125c0-.621-.504-1.125-1.125-1.125H18M12 9.75a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5a.75.75 0 0 1 .75-.75Zm-4.5 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm10.5 0a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" />
                    </svg>
                    <span>{{ $collection->reservations_count ?? 0 }}</span> {{-- Usa il count eager-loaded --}}
                </div>
            </div>
        </div>

        {{-- 🌿 Info EPP e Prezzo --}}
        <div class="px-4 pb-4">
             {{-- Progetto EPP Supportato --}}
             @if ($collection->epp)
                {{-- @style: Badge EPP style, flex centering. --}}
                <div class="mb-3 text-xs inline-flex items-center bg-green-100 text-green-800 px-2.5 py-1 rounded-full font-medium">
                    <svg class="w-3 h-3 mr-1.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm3.84-9.21-.98-.565a1 1 0 0 0-1.382.564l-.006.01L8.62 8.71l-1.561-.896a1 1 0 0 0-1.382.565l-.979.565a1 1 0 0 0 .564 1.382l2.31 1.33a1 1 0 0 0 1.382-.565l1.519-2.633a1 1 0 0 0-.564-1.382Z" clip-rule="evenodd" />
                    </svg>
                    {{-- @i18n: Use localization helper. --}}
                    <span>{{ __('Supports:') }} {{ $collection->epp->name }}</span>
                </div>
            @endif

            {{-- 💰 Floor Price --}}
            @if ($collection->floor_price && $collection->floor_price > 0)
                {{-- @style: Text size, color, font weight. --}}
                <div class="text-sm text-gray-700">
                    <span class="font-medium text-gray-500">{{ __('Floor Price:') }}</span>
                    <span class="font-semibold text-gray-900 ml-1">{{ number_format($collection->floor_price, 2) }} <span class="text-xs text-gray-500">ALGO</span></span>
                </div>
            @endif
        </div>

    </div>

    {{-- 🎬 Footer Card (Link Vista) --}}
    {{-- @style: Border top, padding, flex layout. --}}
    <div class="border-t border-gray-200 px-4 py-3 z-30">
        <a href="{{ route('home.collections.show', $collection->id) }}"
           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
           {{-- @accessibility-trait: Icon is decorative. --}}
           <svg class="w-5 h-5 mr-2 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.18l.879-.879a1.651 1.651 0 0 1 2.336 0l.879.879a1.651 1.651 0 0 0 2.336 0l.879-.879a1.651 1.651 0 0 1 2.336 0l.879.879a1.651 1.651 0 0 0 2.336 0l.879-.879a1.651 1.651 0 0 1 2.336 0l.879.879a1.651 1.651 0 0 1 0 1.18l-.879.879a1.651 1.651 0 0 1-2.336 0l-.879-.879a1.651 1.651 0 0 0-2.336 0l-.879.879a1.651 1.651 0 0 1-2.336 0l-.879-.879a1.651 1.651 0 0 0-2.336 0l-.879.879a1.651 1.651 0 0 1-2.336 0l-.879-.879Zm16.473-4.425a.823.823 0 0 1 0 1.166l-1.888 1.888a.823.823 0 0 1-1.167 0l-.878-.878a.823.823 0 0 0-1.167 0l-.878.878a.823.823 0 0 1-1.167 0l-.878-.878a.823.823 0 0 0-1.167 0l-.878.878a.823.823 0 0 1-1.167 0l-.878-.878a.823.823 0 0 0-1.167 0l-.878.878a.823.823 0 0 1-1.167 0L.664 7.33a.823.823 0 0 1 0-1.166l.878-.878a.823.823 0 0 1 1.167 0l.878.878a.823.823 0 0 0 1.167 0l.878-.878a.823.823 0 0 1 1.167 0l.878.878a.823.823 0 0 0 1.167 0l.878-.878a.823.823 0 0 1 1.167 0l.878.878a.823.823 0 0 0 1.167 0l.878-.878a.823.823 0 0 1 1.167 0l1.888 1.888a.823.823 0 0 1 0 1.166Z" clip-rule="evenodd" />
            </svg>
            {{ __('View Collection') }}
        </a>
    </div>

</div> {{-- Fine Card Container --}}
