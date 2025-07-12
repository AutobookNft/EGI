<div class="space-y-6">
    <!-- Loading Overlay -->
    <div wire:loading.delay.longer class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-lg p-6 flex items-center space-x-3">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#D4A574]"></div>
            <span class="text-white font-medium">Salvataggio in corso...</span>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-900/50 border border-red-500 rounded-lg p-4">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="font-medium text-red-300">Errori di validazione</h3>
            </div>
            <ul class="mt-2 text-sm text-red-200 space-y-1">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Success Message -->
    @if(session()->has('success'))
        <div class="bg-green-900/50 border border-green-500 rounded-lg p-4">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="text-green-300 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-700">
        <nav class="flex space-x-8">
            <button wire:click="setActiveTab('basic')"
                    class="py-3 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'basic' ? 'border-[#D4A574] text-[#D4A574]' : 'border-transparent text-gray-400 hover:text-white' }}">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Informazioni Base</span>
                </div>
            </button>

            @if($type === 'chapters')
                <button wire:click="setActiveTab('chapters')"
                        class="py-3 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'chapters' ? 'border-[#D4A574] text-[#D4A574]' : 'border-transparent text-gray-400 hover:text-white' }}">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span>Capitoli</span>
                    </div>
                </button>
            @endif

            <button wire:click="setActiveTab('media')"
                    class="py-3 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'media' ? 'border-[#D4A574] text-[#D4A574]' : 'border-transparent text-gray-400 hover:text-white' }}">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Media</span>
                </div>
            </button>

            <button wire:click="setActiveTab('settings')"
                    class="py-3 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'settings' ? 'border-[#D4A574] text-[#D4A574]' : 'border-transparent text-gray-400 hover:text-white' }}">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Impostazioni</span>
                </div>
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="space-y-6">
        <!-- Basic Information Tab -->
        @if($activeTab === 'basic')
            <div class="space-y-6">
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-300 mb-2">
                        {{ __('biography.form.title') }} *
                    </label>
                    <input type="text"
                           id="title"
                           wire:model.live.debounce.500ms="title"
                           class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574] transition-colors"
                           placeholder="{{ __('biography.form.title_placeholder') }}">
                    @error('title')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-300 mb-2">
                        {{ __('biography.form.type') }} *
                    </label>
                    <select id="type"
                            wire:model.live="type"
                            class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574] transition-colors">
                        <option value="single">{{ __('biography.type.single') }}</option>
                        <option value="chapters">{{ __('biography.type.chapters') }}</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                                <!-- Content (only for single type) -->
                @if($type === 'single')
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-300 mb-2">
                            {{ __('biography.form.content') }} *
                        </label>
                        <div class="trix-container">
                            <input id="content-trix" name="content" type="hidden" wire:model="content">
                            <trix-editor
                                input="content-trix"
                                class="trix-editor-biography border border-gray-600 rounded-lg bg-gray-800 text-white min-h-[300px] focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574] transition-colors"
                                placeholder="{{ __('biography.form.content_placeholder') }}"
                                wire:ignore
                                x-data="trixEditor()"
                                x-init="initTrix()"
                                @trix-change="updateContent($event)">
                            </trix-editor>
                        </div>
                        @error('content')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Excerpt -->
                <div>
                    <label for="excerpt" class="block text-sm font-medium text-gray-300 mb-2">
                        {{ __('biography.form.excerpt') }}
                    </label>
                    <textarea id="excerpt"
                              wire:model.live.debounce.500ms="excerpt"
                              rows="3"
                              maxlength="500"
                              class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574] transition-colors"
                              placeholder="{{ __('biography.form.excerpt_placeholder') }}"></textarea>
                    <p class="mt-1 text-xs text-gray-400">
                        {{ __('biography.form.excerpt_help') }} ({{ strlen($excerpt) }}/500)
                    </p>
                    @error('excerpt')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        @endif

        <!-- Chapters Tab -->
        @if($activeTab === 'chapters' && $type === 'chapters')
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-white">Gestione Capitoli</h3>
                    <button wire:click="addChapter"
                            class="inline-flex items-center px-4 py-2 bg-[#D4A574] text-gray-900 font-medium rounded-lg hover:bg-[#E6B885] transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Aggiungi Capitolo
                    </button>
                </div>

                <!-- Chapters List -->
                <div class="space-y-4">
                    @forelse($chapters as $index => $chapter)
                        <div class="bg-gray-800/50 border border-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="font-medium text-white">{{ $chapter['title'] }}</h4>
                                    <p class="text-sm text-gray-400 mt-1">
                                        @if($chapter['date_from'])
                                            {{ $chapter['date_from'] }}
                                            @if($chapter['date_to'] && !$chapter['is_ongoing'])
                                                - {{ $chapter['date_to'] }}
                                            @elseif($chapter['is_ongoing'])
                                                - In corso
                                            @endif
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button wire:click="editChapter({{ $index }})"
                                            class="p-2 text-gray-400 hover:text-[#D4A574] transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="deleteChapter({{ $index }})"
                                            class="p-2 text-gray-400 hover:text-red-400 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p>Nessun capitolo ancora. Inizia aggiungendo il primo capitolo.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- Media Tab -->
        @if($activeTab === 'media')
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-white">Gestione Media</h3>

                <!-- Featured Image -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        {{ __('biography.form.featured_image') }}
                    </label>
                    <div class="space-y-4">
                        <input type="file"
                               wire:model="featuredImage"
                               accept="image/*"
                               class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#D4A574] file:text-gray-900 hover:file:bg-[#E6B885] transition-colors">
                        <p class="text-xs text-gray-400">
                            {{ __('biography.form.featured_image_hint') }}
                        </p>
                        @error('featuredImage')
                            <p class="text-sm text-red-400">{{ $message }}</p>
                        @enderror

                        @if($featuredImage)
                            <div class="mt-4">
                                <img src="{{ $featuredImage->temporaryUrl() }}"
                                     alt="Preview"
                                     class="w-48 h-32 object-cover rounded-lg">
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Gallery Images -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Immagini Galleria
                    </label>
                    <input type="file"
                           wire:model="galleryImages"
                           multiple
                           accept="image/*"
                           class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#D4A574] file:text-gray-900 hover:file:bg-[#E6B885] transition-colors">
                    @error('galleryImages.*')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror

                    @if($galleryImages)
                        <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($galleryImages as $image)
                                <img src="{{ $image->temporaryUrl() }}"
                                     alt="Gallery preview"
                                     class="w-full h-32 object-cover rounded-lg">
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Settings Tab -->
        @if($activeTab === 'settings')
            <div class="space-y-6">
                <h3 class="text-lg font-medium text-white">Impostazioni</h3>

                <!-- Privacy Settings -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-300">
                                {{ __('biography.form.is_public') }}
                            </label>
                            <p class="text-xs text-gray-400">
                                {{ __('biography.form.is_public_help') }}
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                   wire:model="isPublic"
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#D4A574]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4A574]"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-300">
                                {{ __('biography.form.is_completed') }}
                            </label>
                            <p class="text-xs text-gray-400">
                                {{ __('biography.form.is_completed_help') }}
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                   wire:model="isCompleted"
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#D4A574]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4A574]"></div>
                        </label>
                    </div>
                </div>

                <!-- Display Settings -->
                <div class="space-y-4">
                    <h4 class="font-medium text-white">Opzioni Visualizzazione</h4>

                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-300">
                                Mostra Timeline
                            </label>
                            <p class="text-xs text-gray-400">
                                Visualizza la timeline nei capitoli
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                   wire:model="settings.show_timeline"
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#D4A574]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4A574]"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-300">
                                Permetti Commenti
                            </label>
                            <p class="text-xs text-gray-400">
                                Abilita i commenti sulla biografia
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                   wire:model="settings.allow_comments"
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#D4A574]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4A574]"></div>
                        </label>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-between pt-6 border-t border-gray-700">
        <div class="flex items-center space-x-4">
            @if($isEditing)
                <span class="text-sm text-gray-400">
                    Ultima modifica: {{ now()->format('d/m/Y H:i') }}
                </span>
            @endif
        </div>

        <div class="flex items-center space-x-4">
            <button wire:click="save"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#D4A574] to-[#E6B885] text-gray-900 font-semibold rounded-lg hover:from-[#E6B885] hover:to-[#D4A574] transition-all duration-200 disabled:opacity-50">
                <svg wire:loading.remove.delay class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <div wire:loading.delay class="w-5 h-5 mr-2">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-gray-900"></div>
                </div>
                {{ $isEditing ? __('biography.form.update_biography') : __('biography.form.create_biography') }}
            </button>
        </div>
    </div>

    <!-- Chapter Form Modal -->
    @if($showChapterForm)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-gray-800 rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto m-4">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-white">
                            {{ $currentChapter['id'] ? 'Modifica Capitolo' : 'Nuovo Capitolo' }}
                        </h3>
                        <button wire:click="cancelChapterEdit"
                                class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <!-- Chapter Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Titolo Capitolo *
                            </label>
                            <input type="text"
                                   wire:model="currentChapter.title"
                                   class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574] transition-colors"
                                   placeholder="Inserisci il titolo del capitolo">
                            @error('currentChapter.title')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Chapter Content -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">
                                Contenuto *
                            </label>
                            <div class="trix-container">
                                <input id="chapter-content-trix" name="chapter_content" type="hidden" wire:model="currentChapter.content">
                                <trix-editor
                                    input="chapter-content-trix"
                                    class="trix-editor-chapter border border-gray-600 rounded-lg bg-gray-700 text-white min-h-[200px] focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574] transition-colors"
                                    placeholder="Racconta questa parte della tua storia..."
                                    wire:ignore
                                    x-data="trixChapterEditor()"
                                    x-init="initChapterTrix()"
                                    @trix-change="updateChapterContent($event)">
                                </trix-editor>
                            </div>
                            @error('currentChapter.content')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date Range -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">
                                    Data Inizio
                                </label>
                                <input type="date"
                                       wire:model="currentChapter.date_from"
                                       class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574] transition-colors">
                                @error('currentChapter.date_from')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">
                                    Data Fine
                                </label>
                                <input type="date"
                                       wire:model="currentChapter.date_to"
                                       :disabled="$wire.currentChapter.is_ongoing"
                                       class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574] transition-colors disabled:opacity-50">
                                @error('currentChapter.date_to')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Options -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-300">
                                    In Corso
                                </label>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           wire:model="currentChapter.is_ongoing"
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#D4A574]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4A574]"></div>
                                </label>
                            </div>

                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium text-gray-300">
                                    Pubblicato
                                </label>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox"
                                           wire:model="currentChapter.is_published"
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#D4A574]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#D4A574]"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Chapter Actions -->
                    <div class="flex items-center justify-end space-x-4 mt-6 pt-6 border-t border-gray-700">
                        <button wire:click="cancelChapterEdit"
                                class="px-4 py-2 text-gray-400 hover:text-white transition-colors">
                            Annulla
                        </button>
                        <button wire:click="saveChapter"
                                class="px-6 py-2 bg-[#D4A574] text-gray-900 font-medium rounded-lg hover:bg-[#E6B885] transition-colors">
                            Salva Capitolo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2/dist/trix.css">
<style>
/* Trix Editor Dark Theme Customization for FlorenceEGI */
trix-editor {
    background-color: rgb(31 41 55) !important;
    color: white !important;
    border: 1px solid rgb(75 85 99) !important;
    border-radius: 0.5rem !important;
    font-family: ui-sans-serif, system-ui, sans-serif !important;
}

.trix-editor-biography {
    min-height: 300px !important;
}

.trix-editor-chapter {
    min-height: 200px !important;
}

trix-toolbar {
    background-color: rgb(55 65 81) !important;
    border-bottom: 1px solid rgb(75 85 99) !important;
    border-radius: 0.5rem 0.5rem 0 0 !important;
    padding: 0.75rem !important;
}

trix-toolbar .trix-button-group {
    background-color: transparent !important;
    border: none !important;
    margin-right: 0.5rem !important;
}

trix-toolbar .trix-button {
    background-color: rgb(75 85 99) !important;
    border: 1px solid rgb(107 114 128) !important;
    color: white !important;
    border-radius: 0.375rem !important;
    margin: 0 0.125rem !important;
    padding: 0.375rem 0.75rem !important;
    transition: all 0.2s ease !important;
}

trix-toolbar .trix-button:hover {
    background-color: #D4A574 !important;
    border-color: #D4A574 !important;
    color: rgb(17 24 39) !important;
}

trix-toolbar .trix-button.trix-active {
    background-color: #D4A574 !important;
    border-color: #D4A574 !important;
    color: rgb(17 24 39) !important;
}

trix-toolbar .trix-button:not(:disabled) {
    background-color: rgb(75 85 99) !important;
}

trix-toolbar .trix-dialogs {
    background-color: rgb(31 41 55) !important;
    border: 1px solid rgb(75 85 99) !important;
    border-radius: 0.5rem !important;
}

trix-toolbar .trix-dialog {
    background-color: rgb(31 41 55) !important;
    color: white !important;
    padding: 1rem !important;
}

trix-toolbar .trix-dialog__link-fields input {
    background-color: rgb(55 65 81) !important;
    border: 1px solid rgb(75 85 99) !important;
    color: white !important;
    border-radius: 0.375rem !important;
    padding: 0.5rem !important;
}

/* Content Area Styling */
trix-editor h1 { color: #D4A574 !important; font-size: 1.875rem !important; font-weight: 700 !important; margin: 1rem 0 !important; }
trix-editor h2 { color: #E6B885 !important; font-size: 1.5rem !important; font-weight: 600 !important; margin: 0.875rem 0 !important; }
trix-editor h3 { color: white !important; font-size: 1.25rem !important; font-weight: 600 !important; margin: 0.75rem 0 !important; }
trix-editor p { margin: 0.5rem 0 !important; line-height: 1.6 !important; }
trix-editor strong { color: #D4A574 !important; }
trix-editor em { color: #E6B885 !important; }
trix-editor a { color: #D4A574 !important; text-decoration: underline !important; }
trix-editor blockquote { border-left: 4px solid #D4A574 !important; padding-left: 1rem !important; margin: 1rem 0 !important; color: rgb(156 163 175) !important; font-style: italic !important; }
trix-editor ul, trix-editor ol { margin: 0.5rem 0 !important; padding-left: 1.5rem !important; }
trix-editor li { margin: 0.25rem 0 !important; }
</style>
@endpush

@push('scripts')
<script type="text/javascript" src="https://unpkg.com/trix@2/dist/trix.umd.min.js"></script>
<script>
// Disable file uploads in Trix
addEventListener("trix-file-accept", function(event) {
    event.preventDefault()
})

// Alpine.js components for Trix integration
function trixEditor() {
    return {
        content: @entangle('content'),

        initTrix() {
            this.$nextTick(() => {
                const editor = this.$el;
                const trixEditor = editor.editor;

                // Set initial content
                if (this.content) {
                    trixEditor.loadHTML(this.content);
                }

                // Listen for changes and update Livewire
                editor.addEventListener('trix-change', (e) => {
                    const htmlContent = trixEditor.getDocument().toString();
                    @this.call('updateTrixContent', htmlContent);
                });
            });
        },

        updateContent(event) {
            const htmlContent = event.target.editor.getDocument().toString();
            @this.call('updateTrixContent', htmlContent);
        }
    }
}

function trixChapterEditor() {
    return {
        chapterContent: @entangle('currentChapter.content'),

        initChapterTrix() {
            this.$nextTick(() => {
                const editor = this.$el;
                const trixEditor = editor.editor;

                // Set initial content
                if (this.chapterContent) {
                    trixEditor.loadHTML(this.chapterContent);
                }

                // Listen for changes and update Livewire
                editor.addEventListener('trix-change', (e) => {
                    const htmlContent = trixEditor.getDocument().toString();
                    @this.call('updateChapterTrixContent', htmlContent);
                });
            });
        },

        updateChapterContent(event) {
            const htmlContent = event.target.editor.getDocument().toString();
            @this.call('updateChapterTrixContent', htmlContent);
        }
    }
}
</script>
@endpush
