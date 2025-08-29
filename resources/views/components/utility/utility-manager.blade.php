{{-- Component Utility Manager per EGI con localizzazione multilingua --}}
@if($canEdit)
<div class="utility-manager-component bg-white rounded-lg shadow-lg p-6 mt-6">
    <div class="utility-header flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-gray-800">
            <span class="mr-2">⚡</span>
            {{ __('utility.title') }}
        </h3>

        @if($utility)
            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                {{ __('utility.status_configured') }}
            </span>
        @else
            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm">
                {{ __('utility.status_none') }}
            </span>
        @endif
    </div>

    {{-- Alert informativo --}}
    <div class="alert alert-info mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <p class="text-sm text-blue-800">
            <strong>ℹ️ {{ __('utility.note') }}:</strong>
            {{ __('utility.info_edit_before_publish') }}
        </p>
    </div>

    {{-- Form Utility --}}
    <form id="utility-form"
          action="{{ $utility ? route('utilities.update', $utility) : route('utilities.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-6">

        @csrf
        @if($utility) @method('PUT') @endif

        <input type="hidden" name="egi_id" value="{{ $egi->id }}">

        {{-- Selezione Tipo --}}
        <div class="form-group">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('utility.types.label') }}
            </label>

            <div class="grid grid-cols-2 gap-4">
                @foreach($utilityTypes as $type => $info)
                <label class="utility-type-option cursor-pointer">
                    <input type="radio"
                           name="type"
                           value="{{ $type }}"
                           {{ ($utility && $utility->type === $type) ? 'checked' : '' }}
                           class="hidden peer"
                           onchange="toggleUtilitySections('{{ $type }}')">

                    <div class="border-2 border-gray-200 rounded-lg p-4
                                peer-checked:border-primary-500 peer-checked:bg-primary-50
                                hover:border-gray-300 transition">
                        <div class="flex items-center mb-2">
                            <span class="text-2xl mr-2">{{ $info['icon'] }}</span>
                            <span class="font-semibold">{{ $info['label'] }}</span>
                        </div>
                        <p class="text-xs text-gray-600">{{ $info['description'] }}</p>
                    </div>
                </label>
                @endforeach
            </div>

            {{-- Opzione "Nessuna Utility" --}}
            @if($utility)
            <label class="flex items-center mt-4 cursor-pointer">
                <input type="radio" name="type" value="" class="mr-2">
                <span class="text-sm text-gray-600">{{ __('utility.types.remove') }}</span>
            </label>
            @endif
        </div>

        {{-- Sezione Dettagli Base (sempre visibile se type selezionato) --}}
        <div id="utility-base-section" style="display: {{ $utility ? 'block' : 'none' }}">
            {{-- Titolo --}}
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('utility.fields.title') }} *
                </label>
                <input type="text"
                       name="title"
                       value="{{ $utility?->title }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                       placeholder="{{ __('utility.fields.title_placeholder') }}">
            </div>

            {{-- Descrizione --}}
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('utility.fields.description') }}
                </label>
                <textarea name="description"
                          rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                          placeholder="{{ __('utility.fields.description_placeholder') }}">{{ $utility?->description }}</textarea>
            </div>
        </div>

        {{-- Sezione PHYSICAL (mostrata solo se type = physical/hybrid) --}}
        <div id="utility-physical-section"
             style="display: none"
             class="bg-gray-50 p-4 rounded-lg">

            <h4 class="font-semibold text-gray-800 mb-4">
                <span class="mr-2">🚚</span>
                {{ __('utility.shipping.title') }}
            </h4>

            {{-- Info Escrow basato sul prezzo --}}
            <div class="escrow-info mb-4 p-3 bg-{{ $escrowTiers['tier'] === 'immediate' ? 'green' : ($escrowTiers['tier'] === 'standard' ? 'yellow' : 'orange') }}-50
                        border border-{{ $escrowTiers['tier'] === 'immediate' ? 'green' : ($escrowTiers['tier'] === 'standard' ? 'yellow' : 'orange') }}-200 rounded">
                <p class="text-sm font-semibold mb-1">
                    {{ $escrowTiers['label'] }}
                </p>
                <p class="text-xs text-gray-700">
                    {{ $escrowTiers['description'] }}
                </p>
                @if(count($escrowTiers['requirements']) > 0)
                <ul class="mt-2 text-xs text-gray-600">
                    @foreach($escrowTiers['requirements'] as $req)
                    <li>• {{ $req }}</li>
                    @endforeach
                </ul>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Peso --}}
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('utility.shipping.weight') }} *
                    </label>
                    <input type="number"
                           name="weight"
                           step="0.1"
                           value="{{ $utility?->weight }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>

                {{-- Giorni spedizione --}}
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('utility.shipping.days') }}
                    </label>
                    <input type="number"
                           name="estimated_shipping_days"
                           value="{{ $utility?->estimated_shipping_days ?? 5 }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>

            {{-- Dimensioni --}}
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('utility.shipping.dimensions') }}
                </label>
                <div class="grid grid-cols-3 gap-2">
                    <input type="number"
                           name="dimensions[length]"
                           placeholder="{{ __('utility.shipping.length') }}"
                           value="{{ $utility?->dimensions['length'] ?? '' }}"
                           class="px-3 py-2 border border-gray-300 rounded-lg">
                    <input type="number"
                           name="dimensions[width]"
                           placeholder="{{ __('utility.shipping.width') }}"
                           value="{{ $utility?->dimensions['width'] ?? '' }}"
                           class="px-3 py-2 border border-gray-300 rounded-lg">
                    <input type="number"
                           name="dimensions[height]"
                           placeholder="{{ __('utility.shipping.height') }}"
                           value="{{ $utility?->dimensions['height'] ?? '' }}"
                           class="px-3 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>

            {{-- Checkbox opzioni --}}
            <div class="flex items-center space-x-6 mt-4">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox"
                           name="fragile"
                           value="1"
                           {{ $utility?->fragile ? 'checked' : '' }}
                           class="mr-2">
                    <span class="text-sm">{{ __('utility.shipping.fragile') }}</span>
                </label>

                <label class="flex items-center cursor-pointer">
                    <input type="checkbox"
                           name="insurance_recommended"
                           value="1"
                           {{ $utility?->insurance_recommended ? 'checked' : '' }}
                           class="mr-2">
                    <span class="text-sm">{{ __('utility.shipping.insurance') }}</span>
                </label>
            </div>

            {{-- Note spedizione --}}
            <div class="form-group mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('utility.shipping.notes') }}
                </label>
                <textarea name="shipping_notes"
                          rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                          placeholder="{{ __('utility.shipping.notes_placeholder') }}">{{ $utility?->shipping_notes }}</textarea>
            </div>
        </div>

        {{-- Sezione SERVICE (mostrata solo se type = service/hybrid) --}}
        <div id="utility-service-section"
             style="display: none"
             class="bg-gray-50 p-4 rounded-lg">

            <h4 class="font-semibold text-gray-800 mb-4">
                <span class="mr-2">🎯</span>
                {{ __('utility.service.title') }}
            </h4>

            <div class="grid grid-cols-2 gap-4">
                {{-- Validità --}}
                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('utility.service.valid_from') }}
                    </label>
                    <input type="date"
                           name="valid_from"
                           value="{{ $utility?->valid_from }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>

                <div class="form-group">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('utility.service.valid_until') }}
                    </label>
                    <input type="date"
                           name="valid_until"
                           value="{{ $utility?->valid_until }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>

            {{-- Numero utilizzi --}}
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('utility.service.max_uses') }}
                </label>
                <input type="number"
                       name="max_uses"
                       value="{{ $utility?->max_uses }}"
                       placeholder="{{ __('utility.service.max_uses_placeholder') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            {{-- Istruzioni attivazione --}}
            <div class="form-group">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('utility.service.instructions') }}
                </label>
                <textarea name="activation_instructions"
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                          placeholder="{{ __('utility.service.instructions_placeholder') }}">{{ $utility?->activation_instructions }}</textarea>
            </div>
        </div>

        {{-- Upload Media Gallery --}}
        <div id="utility-media-section"
             style="display: {{ $utility ? 'block' : 'none' }}"
             class="bg-gray-800/30 p-4 rounded-lg">

            <h4 class="font-semibold text-white mb-4">
                <span class="mr-2">📸</span>
                {{ __('utility.media.title') }}
            </h4>

            <p class="text-sm text-gray-400 mb-4">
                {{ __('utility.media.description') }}
            </p>

            {{-- Drag & Drop Area --}}
            <div class="upload-area border-2 border-dashed border-gray-600 rounded-lg p-6 text-center hover:border-emerald-500 transition-colors"
                 ondrop="dropHandler(event);" 
                 ondragover="dragOverHandler(event);"
                 ondragenter="dragEnterHandler(event);"
                 ondragleave="dragLeaveHandler(event);">
                <input type="file"
                       name="gallery[]"
                       multiple
                       accept="image/*"
                       class="hidden"
                       id="gallery-upload"
                       onchange="fileSelectHandler(event);">

                <label for="gallery-upload" class="cursor-pointer block">
                    <div class="text-gray-400">
                        <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="mt-2 text-sm">
                            {{ __('utility.media.upload_prompt') }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            Trascina qui le immagini o clicca per selezionare
                        </p>
                    </div>
                </label>
            </div>

            {{-- Preview area per nuove immagini --}}
            <div id="image-preview" class="mt-4 grid grid-cols-4 gap-2" style="display: none;">
                <!-- Qui verranno mostrate le anteprime delle immagini selezionate -->
            </div>

            {{-- Preview immagini esistenti --}}
            @if($utility && $utility->getMedia('utility_gallery')->count() > 0)
            <div class="existing-images mt-4">
                <p class="text-sm font-medium text-gray-300 mb-2">{{ __('utility.media.current_images') }}</p>
                <div class="grid grid-cols-4 gap-2">
                    @foreach($utility->getMedia('utility_gallery') as $media)
                    <div class="relative group">
                        <img src="{{ $media->getUrl('thumb') }}"
                             class="w-full h-24 object-cover rounded border border-gray-600">
                        <button type="button"
                                onclick="removeMedia({{ $media->id }})"
                                class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1
                                       opacity-0 group-hover:opacity-100 transition w-6 h-6 flex items-center justify-center text-xs">
                            ×
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Pulsanti Azione --}}
        <div class="flex justify-between items-center pt-6 border-t">
            <button type="button"
                    onclick="resetUtilityForm()"
                    class="text-gray-600 hover:text-gray-800">
                {{ __('label.cancel') }}
            </button>

            <button type="submit"
                    class="px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700
                           focus:outline-none focus:ring-2 focus:ring-emerald-500">
                {{ $utility ? __('label.update') : __('label.save') }} {{ __('utility.title') }}
            </button>
        </div>
    </form>
</div>

{{-- JavaScript per gestione form con testi localizzati --}}
<script>
let selectedFiles = [];

function toggleUtilitySections(type) {
    // Mostra sezione base
    document.getElementById('utility-base-section').style.display = type ? 'block' : 'none';
    document.getElementById('utility-media-section').style.display = type ? 'block' : 'none';

    // Mostra/nascondi sezioni specifiche
    const showPhysical = ['physical', 'hybrid'].includes(type);
    const showService = ['service', 'hybrid'].includes(type);

    document.getElementById('utility-physical-section').style.display = showPhysical ? 'block' : 'none';
    document.getElementById('utility-service-section').style.display = showService ? 'block' : 'none';
}

function resetUtilityForm() {
    if (confirm('{{ __('utility.confirm_reset') }}')) {
        document.getElementById('utility-form').reset();
        selectedFiles = [];
        document.getElementById('image-preview').style.display = 'none';
        document.getElementById('image-preview').innerHTML = '';
        toggleUtilitySections('');
    }
}

function removeMedia(mediaId) {
    if (confirm('{{ __('utility.confirm_remove_image') }}')) {
        // Aggiungi input nascosto per rimozione
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'remove_media[]';
        input.value = mediaId;
        document.getElementById('utility-form').appendChild(input);

        // Nascondi visivamente l'immagine
        event.target.closest('.relative').style.display = 'none';
    }
}

// Drag & Drop handlers
function dragOverHandler(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.add('border-emerald-500', 'bg-gray-700/30');
}

function dragEnterHandler(ev) {
    ev.preventDefault();
}

function dragLeaveHandler(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.remove('border-emerald-500', 'bg-gray-700/30');
}

function dropHandler(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.remove('border-emerald-500', 'bg-gray-700/30');
    
    const files = ev.dataTransfer.files;
    handleFiles(files);
}

function fileSelectHandler(ev) {
    const files = ev.target.files;
    handleFiles(files);
}

function handleFiles(files) {
    const imageFiles = Array.from(files).filter(file => file.type.startsWith('image/'));
    
    if (imageFiles.length === 0) {
        alert('Per favore seleziona solo file immagine.');
        return;
    }
    
    // Verifica dimensione massima (10MB)
    const maxSize = 10 * 1024 * 1024; // 10MB
    const oversizedFiles = imageFiles.filter(file => file.size > maxSize);
    
    if (oversizedFiles.length > 0) {
        alert('Alcune immagini superano i 10MB e non possono essere caricate.');
        return;
    }
    
    // Aggiungi file all'array
    selectedFiles.push(...imageFiles);
    
    // Aggiorna l'input file con tutti i file selezionati
    updateFileInput();
    
    // Mostra preview
    showImagePreviews();
}

function updateFileInput() {
    const input = document.getElementById('gallery-upload');
    const dt = new DataTransfer();
    
    selectedFiles.forEach(file => {
        dt.items.add(file);
    });
    
    input.files = dt.files;
}

function showImagePreviews() {
    const previewContainer = document.getElementById('image-preview');
    
    if (selectedFiles.length === 0) {
        previewContainer.style.display = 'none';
        return;
    }
    
    previewContainer.style.display = 'grid';
    previewContainer.innerHTML = '';
    
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative group';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-24 object-cover rounded border border-gray-600">
                <button type="button" onclick="removeSelectedFile(${index})" 
                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1
                               opacity-0 group-hover:opacity-100 transition w-6 h-6 flex items-center justify-center text-xs">
                    ×
                </button>
                <div class="absolute bottom-1 left-1 bg-black/50 text-white text-xs px-1 rounded">
                    ${file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name}
                </div>
            `;
            previewContainer.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

function removeSelectedFile(index) {
    selectedFiles.splice(index, 1);
    updateFileInput();
    showImagePreviews();
}

// Inizializza stato form se utility esistente
@if($utility)
    toggleUtilitySections('{{ $utility->type }}');
@endif
</script>

@endif {{-- Fine if $canEdit --}}
