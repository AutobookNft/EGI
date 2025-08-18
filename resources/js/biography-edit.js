console.log('🚀 Biography Edit JS caricato');

document.addEventListener('DOMContentLoaded', function() {
    // DEBUG: log bottoni tab trovati
    const tabButtons = document.querySelectorAll('.tab-button');
    console.log('DEBUG: trovati', tabButtons.length, 'tab-button');
    tabButtons.forEach((btn, i) => console.log('DEBUG: tab', i, btn.dataset.tab));

    const tabIds = ['basic-tab', 'media-tab', 'settings-tab'];
    const tabContents = tabIds.map(id => document.getElementById(id));

    tabButtons.forEach((button, idx) => {
        button.addEventListener('click', function() {
            tabButtons.forEach((btn, i) => {
                btn.classList.toggle('active', i === idx);
                if (tabContents[i]) tabContents[i].classList.toggle('hidden', i !== idx);
            });
        });
    });

    // Sincronizzazione Trix editor
    const form = document.getElementById('biography-form');
    if (form) {
        form.addEventListener('submit', function() {
            const trixEditor = document.querySelector('trix-editor[input="content-trix"]');
            const hiddenInput = document.getElementById('content-trix');
            if (trixEditor && hiddenInput) {
                // USA innerHTML per mantenere la formattazione HTML
                hiddenInput.value = trixEditor.innerHTML;
                console.log('📝 Contenuto HTML sincronizzato:', trixEditor.innerHTML);
            }
        });
    }


    // Inizializza editor
    initializeTrixEditor();

    // Inizializza upload
    initializeMediaUpload();

    initializeExistingImages();

    // Event delegation per i bottoni di eliminazione immagini
    document.addEventListener('click', function(e) {
        console.log('🖱️ Click rilevato su:', e.target);
        console.log('🏷️ Classi target:', e.target.classList?.toString());

        // Controlla se è un bottone di eliminazione immagine biografia
        const deleteButton = e.target.closest('.btn-delete-image');
        if (deleteButton) {
            console.log('✅ Bottone eliminazione immagine biografia cliccato!');
            const mediaId = deleteButton.getAttribute('data-media-id');
            console.log('🆔 Media ID:', mediaId);

            if (mediaId) {
                console.log('🚀 Chiamando window.removeImage...');
                window.removeImage(mediaId);
            } else {
                console.error('❌ Media ID non trovato!');
            }
            return;
        }

        // Controlla se è un bottone di eliminazione immagine capitolo
        const deleteChapterImageButton = e.target.closest('.btn-delete-chapter-image');
        if (deleteChapterImageButton) {
            console.log('✅ Bottone eliminazione immagine capitolo cliccato!');
            const mediaId = deleteChapterImageButton.getAttribute('data-media-id');
            const chapterId = deleteChapterImageButton.getAttribute('data-chapter-id');
            console.log('🆔 Media ID:', mediaId, 'Chapter ID:', chapterId);

            if (mediaId && chapterId) {
                console.log('🚀 Chiamando window.removeChapterImage...');
                window.removeChapterImage(chapterId, mediaId);
            } else {
                console.error('❌ Media ID o Chapter ID non trovato!');
            }
            return;
        }

        console.log('ℹ️ Click non su bottoni eliminazione');
    });    // Inizializza contatore caratteri
    initializeCharacterCounter();

    // === Capitoli: apertura modale ===
    const addChapterBtn = document.getElementById('add-chapter-btn');
    const chapterModal = document.getElementById('chapter-modal');
    const chapterModalContent = document.getElementById('chapter-modal-content');
    const closeChapterModalBtn = document.getElementById('close-chapter-modal');

    if (addChapterBtn && chapterModal && chapterModalContent && closeChapterModalBtn) {
        addChapterBtn.addEventListener('click', function () {
            renderChapterForm();
            chapterModal.classList.remove('hidden');
            // Mobile: Prevent body scroll
            document.body.classList.add('modal-open');
            // Focus management for accessibility
            setTimeout(() => {
                const firstInput = chapterModal.querySelector('input[name="title"]');
                if (firstInput) firstInput.focus();
            }, 100);
        });

        closeChapterModalBtn.addEventListener('click', closeChapterModal);

        // Close on backdrop click (but not on content click)
        chapterModal.addEventListener('click', function (e) {
            if (e.target === chapterModal) closeChapterModal();
        });

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !chapterModal.classList.contains('hidden')) {
                closeChapterModal();
            }
        });
    }

    function closeChapterModal() {
        chapterModal.classList.add('hidden');
        // Mobile: Restore body scroll
        document.body.classList.remove('modal-open');
        chapterModalContent.innerHTML = '';

        // Reset focus to add button for accessibility
        const addBtn = document.getElementById('add-chapter-btn');
        if (addBtn) addBtn.focus();
    }

    // === Render form creazione capitolo - Mobile First ===
    function renderChapterForm(chapter = null) {
        // Aggiorna il titolo della modale
        const modalTitle = document.getElementById('chapter-modal-title');
        if (modalTitle) {
            modalTitle.textContent = chapter ? 'Modifica Capitolo' : 'Aggiungi Capitolo';
        }

        chapterModalContent.innerHTML = `
            <form id="chapter-create-form" class="space-y-6">
                <!-- Titolo -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Titolo *</label>
                    <input type="text" name="title" required
                           class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-white
                                  focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574] transition-colors"
                           value="${chapter ? chapter.title : ''}"
                           placeholder="Inserisci il titolo del capitolo">
                </div>

                <!-- Date Range - Mobile Stack, Desktop Side by Side -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Dal</label>
                        <input type="date" name="date_from"
                               class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-white
                                      focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574] transition-colors"
                               value="${chapter && chapter.date_from ? chapter.date_from : ''}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Al</label>
                        <input type="date" name="date_to"
                               class="w-full rounded-lg border border-gray-600 bg-gray-800 px-4 py-3 text-white
                                      focus:border-[#D4A574] focus:ring-1 focus:ring-[#D4A574] transition-colors"
                               value="${chapter && chapter.date_to ? chapter.date_to : ''}">
                    </div>
                </div>

                <!-- Contenuto -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Contenuto *</label>
                    <input id="chapter-content-trix" type="hidden" name="content" value="${chapter ? (chapter.content || '') : ''}">
                    <trix-editor input="chapter-content-trix"
                                class="trix-editor-biography min-h-[200px] md:min-h-[300px] rounded-lg border border-gray-600 bg-gray-800"></trix-editor>
                </div>

                <!-- Upload Immagini -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Immagini</label>
                    <div class="border-2 border-dashed border-gray-600 rounded-lg p-4 hover:border-[#D4A574] transition-colors">
                        <input type="file" id="chapter-media-input" name="images[]" multiple accept="image/*"
                               class="block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg
                                      file:border-0 file:text-sm file:font-medium file:bg-[#D4A574] file:text-gray-900
                                      hover:file:bg-[#E6B885] file:cursor-pointer cursor-pointer">
                        <p class="mt-2 text-xs text-gray-400">PNG, JPG, GIF fino a 10MB. Seleziona più file contemporaneamente.</p>
                    </div>
                    <div id="chapter-media-gallery" class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3"></div>
                </div>

                <!-- Action Buttons - Mobile Stack, Desktop Row -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pt-6 border-t border-gray-700">
                    <!-- Delete button (if editing) -->
                    <button type="button" id="delete-chapter-btn"
                            class="order-last md:order-first px-4 py-2 rounded-lg bg-red-600 text-white font-medium
                                   hover:bg-red-700 transition-colors ${chapter ? '' : 'hidden'}">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Elimina
                    </button>

                    <!-- Cancel and Save buttons -->
                    <div class="flex gap-3">
                        <button type="button" id="cancel-chapter-btn"
                                class="flex-1 md:flex-none px-6 py-3 rounded-lg bg-gray-700 text-white font-medium
                                       hover:bg-gray-600 transition-colors">
                            Annulla
                        </button>
                        <button type="submit"
                                class="flex-1 md:flex-none px-6 py-3 rounded-lg bg-gradient-to-r from-[#D4A574] to-[#E6B885]
                                       text-gray-900 font-semibold hover:from-[#E6B885] hover:to-[#D4A574]
                                       transition-all duration-200 shadow-lg">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salva Capitolo
                        </button>
                    </div>
                </div>

                <!-- Error Message -->
                <div id="chapter-form-error" class="mt-4 p-4 bg-red-900/50 border border-red-500 rounded-lg text-red-300 text-sm hidden">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span id="chapter-form-error-text"></span>
                </div>
            </form>
        `;
        document.getElementById('cancel-chapter-btn').onclick = closeChapterModal;
        if (chapter) document.getElementById('delete-chapter-btn').onclick = () => deleteChapter(chapter.id);
        document.getElementById('chapter-create-form').onsubmit = submitChapterForm;
        document.getElementById('chapter-media-input').onchange = handleChapterMediaUpload;
        if (chapter && chapter.media) renderChapterMediaGallery(chapter.media);
    }

    function handleChapterMediaUpload(e) {
        const files = Array.from(e.target.files);
        if (!files.length) return;
        const gallery = document.getElementById('chapter-media-gallery');
        gallery.innerHTML = '';
        for (const file of files) {
            const reader = new FileReader();
            reader.onload = function (ev) {
                const div = document.createElement('div');
                div.className = 'relative group';
                div.innerHTML = `<img src="${ev.target.result}" class="h-32 w-full rounded-lg object-cover shadow-md"><button type="button" class="absolute right-2 top-2 rounded-full bg-red-600 p-1 text-white opacity-80 transition hover:opacity-100" onclick="this.parentNode.remove()">&times;</button>`;
                gallery.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    }

    // === Submit AJAX creazione capitolo ===
    async function submitChapterForm(e) {
        e.preventDefault();
        const form = e.target;
        const errorDiv = document.getElementById('chapter-form-error');
        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';
        const title = form.title.value.trim();
        const content = form.querySelector('[name="content"]').value.trim();
        const date_from = form.date_from.value;
        const date_to = form.date_to.value;
        const isEdit = !!form.dataset.chapterId;
        if (!title || !content) {
            errorDiv.textContent = 'Compila tutti i campi obbligatori.';
            errorDiv.classList.remove('hidden');
            return;
        }
        try {
            const biographyId = window.biographyId || (window.location.pathname.match(/biography\/(\d+)/) || [])[1];
            if (!biographyId) throw new Error('ID biografia non trovato');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const url = isEdit
                ? `/biography/${biographyId}/chapters/${form.dataset.chapterId}`
                : `/biography/${biographyId}/chapters`;
            const method = isEdit ? 'PUT' : 'POST';
            const body = JSON.stringify({
                title,
                content,
                date_from,
                date_to
            });

            console.log('Content being sent:', content);
            console.log('Content length:', content.length);
            console.log('Has HTML tags:', /<[^>]*>/g.test(content));

            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body,
                credentials: 'same-origin'
            });
            const result = await response.json();
            if (!result.success) throw new Error(result.message || 'Errore salvataggio capitolo');

            console.log('✅ Capitolo salvato:', result.data);

            if (isEdit) updateChapterInList(result.data); else appendChapterToList(result.data);

            // Upload media se selezionati
            const mediaInput = document.getElementById('chapter-media-input');
            if (mediaInput && mediaInput.files.length > 0) {
                console.log('📁 File selezionati per upload:', mediaInput.files.length);
                if (result.data && result.data.id) {
                    // CHIUDI LA MODALE PRIMA dell'upload per vedere l'animazione
                    closeChapterModal();

                    // Mostra messaggio più chiaro e specifico
                    showProcessingIndicator(result.data.id, `📤 Caricamento di ${mediaInput.files.length} nuove immagini in corso...`);
                    showSuccess(`✅ Capitolo salvato! Caricamento nuove immagini...`);

                    try {
                        await uploadChapterImages(result.data.id, mediaInput.files);
                        // Il successo finale verrà mostrato dalla funzione updateChapterWithPolling
                    } catch (error) {
                        console.error('❌ Errore durante upload media:', error);
                        hideProcessingIndicator(result.data.id);
                        showError('⚠️ Capitolo salvato, ma errore nell\'upload delle immagini: ' + error.message);
                    }
                } else {
                    console.error('❌ ID capitolo non trovato per upload media');
                    closeChapterModal();
                }
            } else {
                // Nessun media da caricare, chiudi modale e aggiorna immediatamente
                closeChapterModal();
                showSuccess(isEdit ? 'Capitolo aggiornato!' : 'Capitolo aggiunto!');
            }
        } catch (err) {
            errorDiv.textContent = err.message || 'Errore imprevisto.';
            errorDiv.classList.remove('hidden');
        }
    }

    // === Funzione upload immagini capitolo ===
    async function uploadChapterImages(chapterId, files) {
        console.log(`🚀 Inizio upload sequenziale per ${files.length} file...`);

        // Mostra indicatore di elaborazione
        showProcessingIndicator(chapterId, `Caricamento di ${files.length} immagini...`);

        let finalGallery = [];
        let successCount = 0;
        let errorCount = 0;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            console.log(`📤 Caricando file ${i+1}/${files.length}: ${file.name}`);

            // Aggiorna indicatore per file specifico
            showProcessingIndicator(chapterId, `Caricamento ${i+1}/${files.length}: ${file.name}`);

            const formData = new FormData();
            formData.append('images[]', file);

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch(`/biography/chapters/${chapterId}/media`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData,
                    credentials: 'same-origin'
                });

                const result = await response.json();

                if (!result.success || !result.gallery || result.gallery.length === 0) {
                    throw new Error(result.message || `Errore durante l'upload di ${file.name}`);
                }

                console.log(`✅ ${file.name} caricato con successo.`);
                finalGallery.push(...result.gallery);
                successCount++;

            } catch (err) {
                console.error(`❌ Fallito l'upload per ${file.name}:`, err);
                errorCount++;
            }
        }

        console.log('✅ Upload sequenziale completato.', { successCount, errorCount });

        // Aggiorna automaticamente il capitolo nella lista se ci sono stati upload riusciti
        if (successCount > 0) {
            console.log('🔄 Aggiornamento automatico del capitolo...');
            showProcessingIndicator(chapterId, `✨ Elaborazione di ${successCount} nuove immagini...`);
            await updateChapterWithPolling(chapterId, successCount);
        } else {
            hideProcessingIndicator(chapterId);
            showError('❌ Nessuna immagine è stata caricata con successo');
        }

        return finalGallery;
    }

    // === Funzione per aggiornare il capitolo con polling ===
    async function updateChapterWithPolling(chapterId, expectedNewImages = 1, maxAttempts = 10, delay = 2000) {
        console.log(`🔄 Iniziando polling per capitolo ${chapterId}, aspettando ${expectedNewImages} nuove immagini...`);

        // Ottieni lo stato attuale prima del polling
        const initialChapter = await fetchChapter(chapterId);
        const initialMediaCount = initialChapter?.media ? initialChapter.media.length : 0;
        const targetMediaCount = initialMediaCount; // In realtà dovremmo conoscere il numero precedente + le nuove

        console.log(`📊 Stato iniziale: ${initialMediaCount} immagini`);

        let attempts = 0;

        while (attempts < maxAttempts) {
            attempts++;
            console.log(`🔍 Tentativo ${attempts}/${maxAttempts} - Verificando elaborazione nuove immagini...`);

            // Aggiorna indicatore con tentativo corrente
            showProcessingIndicator(chapterId, `🔍 Verifica elaborazione nuove immagini... (${attempts}/${maxAttempts})`);

            try {
                // Fetch aggiornato del capitolo
                const updatedChapter = await fetchChapter(chapterId);

                if (!updatedChapter) {
                    console.error('❌ Impossibile recuperare i dati del capitolo');
                    break;
                }

                const currentMediaCount = updatedChapter.media ? updatedChapter.media.length : 0;
                console.log(`📊 Media count attuale: ${currentMediaCount}`);

                // Controlla se ci sono media e se hanno tutti le thumbnail processate
                if (currentMediaCount > 0) {
                    // Controlla solo le immagini più recenti (presumibilmente quelle appena caricate)
                    const recentMedia = updatedChapter.media.slice(-expectedNewImages);
                    const allRecentProcessed = recentMedia.every(media => {
                        const hasThumb = media.thumb_url || media.url;
                        const isProcessed = media.thumb_url !== null;
                        console.log(`🖼️ Media ${media.id}: hasThumb=${!!hasThumb}, isProcessed=${isProcessed}`);
                        return hasThumb;
                    });

                    if (allRecentProcessed) {
                        console.log('✅ Tutte le nuove immagini sono state elaborate!');
                        hideProcessingIndicator(chapterId);

                        // REFRESH COMPLETO per assicurarsi che tutte le immagini siano visibili
                        showSuccess(`✅ ${expectedNewImages} nuove immagini elaborate! Ricaricamento pagina...`);

                        setTimeout(() => {
                            console.log('🔄 Ricaricamento pagina per mostrare le nuove immagini elaborate...');
                            window.location.reload();
                        }, 1000);

                        return updatedChapter;
                    }
                }

                // Aggiorna comunque la UI con i dati attuali (ma senza modificare le immagini esistenti)
                updateChapterInList(updatedChapter);

                // Attesa prima del prossimo tentativo
                if (attempts < maxAttempts) {
                    console.log(`⏳ Attesa di ${delay}ms prima del prossimo controllo...`);
                    await new Promise(resolve => setTimeout(resolve, delay));
                }

            } catch (error) {
                console.error(`❌ Errore durante il polling (tentativo ${attempts}):`, error);

                if (attempts >= maxAttempts) {
                    hideProcessingIndicator(chapterId);

                    // Anche se il polling fallisce, facciamo un refresh per vedere eventuali immagini caricate
                    showSuccess('⚠️ Elaborazione completata. Ricaricamento pagina...');
                    setTimeout(() => {
                        console.log('🔄 Ricaricamento pagina dopo timeout polling...');
                        window.location.reload();
                    }, 1500);

                    break;
                }
            }
        }

        if (attempts >= maxAttempts) {
            console.warn('⚠️ Raggiunto numero massimo di tentativi per il polling delle nuove immagini');
            hideProcessingIndicator(chapterId);

            // Refresh anche quando raggiungiamo il massimo dei tentativi
            showSuccess('⚠️ Elaborazione completata. Ricaricamento pagina...');
            setTimeout(() => {
                console.log('🔄 Ricaricamento pagina dopo massimo tentativi...');
                window.location.reload();
            }, 1500);
        }
    }

    // === Funzione elimina immagine capitolo ===
    async function deleteChapterImage(chapterId, mediaId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const response = await fetch(`/biography/chapters/${chapterId}/media`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ media_id: mediaId }),
            credentials: 'same-origin'
        });
        const result = await response.json();
        if (!result.success) throw new Error(result.message || 'Errore eliminazione immagine');
        return result.gallery;
    }

    function updateChapterInList(chapter) {
        const chaptersSection = document.getElementById('biography-chapters-list');
        if (!chaptersSection) return;

        // Trova la card del capitolo da aggiornare
        const chapterCard = chaptersSection.querySelector(`[data-chapter-id="${chapter.id}"]`);
        if (chapterCard) {
            chapterCard.innerHTML = renderChapterCardHtml(chapter);
        }
    }

    function appendChapterToList(chapter) {
        let chaptersSection = document.getElementById('biography-chapters-list');
        if (!chaptersSection) {
            const btn = document.getElementById('add-chapter-btn');
            chaptersSection = document.createElement('div');
            chaptersSection.id = 'biography-chapters-list';
            chaptersSection.className = 'mt-10';
            btn.parentNode.insertAdjacentElement('afterend', chaptersSection);
        }
        const div = document.createElement('div');
        div.className = 'p-6 mb-6 shadow-lg rounded-xl bg-gray-800/60';
        div.dataset.chapterId = chapter.id;
        div.innerHTML = renderChapterCardHtml(chapter);
        chaptersSection.appendChild(div);
    }

    function renderChapterCardHtml(chapter) {
        return `
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-semibold text-white">${chapter.title}</h3>
                <div class="flex space-x-2">
                    <button type="button" class="edit-chapter-btn px-2 py-1 rounded bg-[#D4A574] text-gray-900 font-semibold hover:bg-[#E6B885]" data-id="${chapter.id}">Modifica</button>
                    <button type="button" class="delete-chapter-btn px-2 py-1 rounded bg-red-700 text-white hover:bg-red-600" data-id="${chapter.id}">Elimina</button>
                </div>
            </div>
            <div class="text-sm text-gray-400 mb-2">${chapter.date_from || ''} ${chapter.date_to ? '→ ' + chapter.date_to : ''}</div>
            <div class="prose prose-invert max-w-none text-white mb-2">${chapter.content}</div>
            ${(chapter.media && chapter.media.length > 0) ? `
                <div class="mb-2">
                    <div class="flex flex-wrap gap-2">
                        ${chapter.media.map(media => `
                            <div class="relative group bg-gray-900 rounded overflow-hidden" style="width: 80px; height: 80px;">
                                <img src="${media.thumb_url || media.url}"
                                     class="w-full h-full object-cover"
                                     alt="media"
                                     title="${media.file_name || 'Chapter media'}">
                                <div class="absolute top-1 right-1">
                                    <button class="btn-delete-chapter-image bg-red-500 hover:bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 shadow-lg"
                                            data-media-id="${media.id}"
                                            data-chapter-id="${chapter.id}"
                                            title="Elimina immagine"
                                            type="button">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            ` : ''}
        `;
    }

    document.addEventListener('click', async function(e) {
        if (e.target.classList.contains('edit-chapter-btn')) {
            const chapterId = e.target.dataset.id;
            const chapter = await fetchChapter(chapterId);
            renderChapterForm(chapter);
            document.getElementById('chapter-create-form').dataset.chapterId = chapterId;
            chapterModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
        if (e.target.classList.contains('delete-chapter-btn')) {
            const chapterId = e.target.dataset.id;
            await deleteChapter(chapterId);
        }
    });

    async function fetchChapter(chapterId) {
        const biographyId = window.biographyId || (window.location.pathname.match(/biography\/(\d+)/) || [])[1];
        const response = await fetch(`/biography/${biographyId}/chapters/${chapterId}`, {
            credentials: 'same-origin'
        });
        const result = await response.json();
        if (!result.success) throw new Error(result.message || 'Errore caricamento capitolo');
        return result.data;
    }


});

function initializeTabs() {
    console.log('🔧 Configurando tabs...');

    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = [
        document.getElementById('basic-tab'),
        document.getElementById('media-tab'),
        document.getElementById('settings-tab')
    ];

    // Attiva il primo tab di default
    tabButtons.forEach((btn, idx) => {
        if (idx === 0) {
            btn.classList.add('active');
            if (tabContents[idx]) tabContents[idx].classList.remove('hidden');
        } else {
            btn.classList.remove('active');
            if (tabContents[idx]) tabContents[idx].classList.add('hidden');
        }
    });

    tabButtons.forEach((button, idx) => {
        button.addEventListener('click', function() {
            tabButtons.forEach((btn, i) => {
                btn.classList.remove('active');
                if (tabContents[i]) tabContents[i].classList.add('hidden');
            });
            this.classList.add('active');
            if (tabContents[idx]) tabContents[idx].classList.remove('hidden');
        });
    });
}

function initializeTrixEditor() {
    console.log('🔧 Configurando Trix Editor...');

    const editor = document.querySelector('trix-editor[input="content-trix"]');
    if (!editor) {
        console.error('❌ Editor Trix non trovato');
        return;
    }

    // Imposta il contenuto esistente
    const hiddenInput = document.getElementById('content-trix');
    if (hiddenInput && hiddenInput.value) {
        // Corretto: aggiorna l'input hidden e dispatcha l'evento input
        hiddenInput.value = hiddenInput.value;
        hiddenInput.dispatchEvent(new Event('input'));
    }

    // Sync continuo per mantenere HTML - CAMBIATO
    editor.addEventListener('trix-change', function(e) {
        const content = e.target.innerHTML; // ← HTML formattato
        console.log('📝 Contenuto editor aggiornato, con HTML:', /<[^>]*>/g.test(content));

        // Aggiorna il hidden input con HTML
        if (hiddenInput) {
            hiddenInput.value = content;
        }
    });

    console.log('✅ Editor Trix configurato con sync HTML');
}

function initializeCharacterCounter() {
    console.log('🔧 Configurando contatore caratteri...');

    const excerptTextarea = document.getElementById('excerpt');
    const excerptCount = document.getElementById('excerpt-count');

    if (excerptTextarea && excerptCount) {
        excerptTextarea.addEventListener('input', function() {
            const count = this.value.length;
            excerptCount.textContent = count;

            // Cambia colore se si avvicina al limite
            if (count > 450) {
                excerptCount.classList.add('text-red-400');
            } else {
                excerptCount.classList.remove('text-red-400');
            }
        });
    }
}

function initializeMediaUpload() {
    console.log('📁 Configurando upload media...');

    const fileInput = document.getElementById('multiple-images-input');
    const uploadLoading = document.getElementById('upload-loading');
    const uploadError = document.getElementById('upload-error');
    const uploadSuccess = document.getElementById('upload-success');
    const imagesGrid = document.getElementById('images-grid');

    if (!fileInput) {
        console.error('❌ Input file non trovato');
        return;
    }

    fileInput.addEventListener('change', async function(e) {
        const files = Array.from(e.target.files);
        if (files.length === 0) {
            console.log('ℹ️ Nessun file selezionato');
            return;
        }

        console.log('📂 File selezionati:', files.length);

        // Mostra loading
        showLoading();
        hideError();
        hideSuccess();

        let successCount = 0;
        let errorCount = 0;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            console.log(`📤 Caricando file ${i+1}/${files.length}: ${file.name}`);

            try {
                const result = await uploadFile(file);
                console.log(`✅ File ${file.name} caricato con successo`);

                // Aggiungi immagine alla gallery
                addImageToGallery(result.media);
                successCount++;
            } catch (error) {
                console.error(`❌ Errore caricamento ${file.name}:`, error);
                errorCount++;
            }
        }

        // Nascondi loading
        hideLoading();

        // Mostra risultati
        if (successCount > 0) {
            showSuccess(`✅ ${successCount} immagini caricate con successo!`);
        }

        if (errorCount > 0) {
            showError(`❌ ${errorCount} immagini non sono state caricate`);
        }

        // Reset input
        e.target.value = '';
    });

    console.log('✅ Upload media configurato');
}

function initializeExistingImages() {
    console.log('🖼️ Inizializzando immagini esistenti...');

    if (window.existingImages && window.existingImages.length > 0) {
        console.log('📁 Immagini esistenti trovate:', window.existingImages.length);

        window.existingImages.forEach(image => {
            addImageToGallery({
                id: image.id,
                url: image.url,
                file_name: image.file_name
            });
        });
    } else {
        console.log('📁 Nessuna immagine esistente');
    }
}

async function uploadFile(file) {
    console.log('📤 Inizio upload:', file.name);

    // Validazione
    if (!file.type.startsWith('image/')) {
        throw new Error('Il file non è un\'immagine');
    }

    if (file.size > 10 * 1024 * 1024) {
        throw new Error('File troppo grande (massimo 2MB)');
    }

    // FormData
    const formData = new FormData();
    formData.append('file', file);
    formData.append('collection', 'main_gallery');

    console.log('📂 FormData preparata per l\'upload:', file.name);
    // controlliamo se l'ID della biografia è disponibile
    console.log('🔍 ID biografia:', window.biographyId || 'non disponibile');

    // Aggiungi l'ID della biografia se disponibile
    if (window.biographyId) {
        formData.append('biography_id', window.biographyId);
    }

    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        throw new Error('Token CSRF non trovato');
    }

    // Upload
    const response = await fetch('/biography/upload-media', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    });

    if (!response.ok) {
        const errorText = await response.text();
        throw new Error(`HTTP ${response.status}: ${errorText}`);
    }

    const result = await response.json();

    if (!result.success) {
        throw new Error(result.message || 'Upload fallito');
    }

    return result;
}

function addImageToGallery(media) {
    console.log('🖼️ Aggiungendo immagine alla gallery:', media.file_name);

    const imagesGrid = document.getElementById('images-grid');
    if (!imagesGrid) {
        console.error('❌ Grid immagini non trovato');
        return;
    }

    const imageElement = document.createElement('div');
    imageElement.className = 'relative overflow-hidden bg-gray-900 rounded-lg group';
    imageElement.style.width = '240px';
    imageElement.style.height = '240px';

    imageElement.innerHTML = `
        <img src="${media.url}" alt="${media.file_name}" class="w-full h-full object-cover">
        <div class="absolute top-2 right-2">
            <button class="btn-delete-image bg-red-500 hover:bg-red-600 text-white rounded-full p-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 shadow-lg" data-media-id="${media.id}" title="Elimina immagine" type="button">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
        <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-70 text-white text-sm p-2 truncate">
            ${media.file_name}
        </div>
    `;

    imagesGrid.appendChild(imageElement);
}

// Rende la funzione accessibile globalmente
window.removeImage = async function(mediaId) {
    console.log('🗑️ Rimozione immagine:', mediaId);

    if (!confirm('Sei sicuro di voler rimuovere questa immagine?')) {
        return;
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');

        const response = await fetch('/biography/remove-media', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ media_id: mediaId }),
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error('Errore nella rimozione');
        }

        const result = await response.json();

        if (result.success) {
            // Rimuovi l'elemento dalla gallery
            const imageElement = document.querySelector(`button[data-media-id="${mediaId}"]`);
            if (imageElement) {
                const parentElement = imageElement.closest('.relative.group');
                if (parentElement) {
                    parentElement.remove();
                }
            }

            showSuccess('Immagine rimossa con successo');
        } else {
            throw new Error(result.message || 'Errore nella rimozione');
        }
    } catch (error) {
        console.error('❌ Errore rimozione immagine:', error);
        showError('Errore durante la rimozione: ' + error.message);
    }
}

// Rende la funzione accessibile globalmente per le immagini dei capitoli
window.removeChapterImage = async function(chapterId, mediaId) {
    console.log('🗑️ Rimozione immagine capitolo:', { chapterId, mediaId });

    if (!confirm('Sei sicuro di voler rimuovere questa immagine dal capitolo?')) {
        return;
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');

        const response = await fetch(`/biography/chapters/${chapterId}/remove-media`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                media_id: mediaId
            }),
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error('Errore nella rimozione');
        }

        const result = await response.json();

        if (result.success) {
            // Rimuovi l'elemento dalla UI
            const imageElement = document.querySelector(`button[data-media-id="${mediaId}"][data-chapter-id="${chapterId}"]`);
            if (imageElement) {
                const parentElement = imageElement.closest('.relative.group');
                if (parentElement) {
                    parentElement.remove();
                }
            }

            showSuccess('Immagine del capitolo rimossa con successo');
        } else {
            throw new Error(result.message || 'Errore nella rimozione');
        }
    } catch (error) {
        console.error('❌ Errore rimozione immagine capitolo:', error);
        showError('Errore durante la rimozione: ' + error.message);
    }
}

async function deleteChapter(chapterId) {
    if (!confirm('Sei sicuro di voler eliminare questo capitolo?')) {
        return;
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const biographyId = window.biographyId || (window.location.pathname.match(/biography\/(\d+)/) || [])[1];

        const response = await fetch(`/biography/${biographyId}/chapters/${chapterId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });

        const result = await response.json();

        if (result.success) {
            // Rimuovi l'elemento dalla lista dei capitoli usando data-chapter-id
            const chapterElement = document.querySelector(`[data-chapter-id="${chapterId}"]`);
            if (chapterElement) {
                chapterElement.remove();
            }
            showSuccess('Capitolo eliminato con successo!');
            // Chiudi la modale se è aperta
            const chapterModal = document.getElementById('chapter-modal');
            if (chapterModal) {
                chapterModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        } else {
            throw new Error(result.message || 'Errore nell\'eliminazione del capitolo');
        }
    } catch (error) {
        console.error('❌ Errore eliminazione capitolo:', error);
        showError('Errore durante l\'eliminazione: ' + error.message);
    }
}

// Utility functions
function showLoading() {
    const loading = document.getElementById('upload-loading');
    if (loading) {
        loading.classList.remove('hidden');
        loading.classList.add('flex');
    }
}

function hideLoading() {
    const loading = document.getElementById('upload-loading');
    if (loading) {
        loading.classList.add('hidden');
        loading.classList.remove('flex');
    }
}

function showError(message) {
    const error = document.getElementById('upload-error');
    if (error) {
        error.textContent = message;
        error.classList.remove('hidden');
    }
}

function hideError() {
    const error = document.getElementById('upload-error');
    if (error) {
        error.classList.add('hidden');
    }
}

function showSuccess(message) {
    const success = document.getElementById('upload-success');
    if (success) {
        success.textContent = message;
        success.classList.remove('hidden');

        // Nascondi automaticamente dopo 3 secondi
        setTimeout(() => {
            hideSuccess();
        }, 3000);
    }
}

function hideSuccess() {
    const success = document.getElementById('upload-success');
    if (success) {
        success.classList.add('hidden');
    }
}

// === Funzioni di utilità per il feedback durante l'elaborazione ===
function showProcessingIndicator(chapterId, message = 'Elaborazione immagini in corso...') {
    const chapterElement = document.querySelector(`[data-chapter-id="${chapterId}"]`);
    if (chapterElement) {
        // Trova o crea l'indicatore
        let indicator = chapterElement.querySelector('.processing-indicator');
        if (!indicator) {
            indicator = document.createElement('div');
            indicator.className = 'flex items-center p-2 mt-2 space-x-2 rounded-lg processing-indicator bg-blue-900/50';
            indicator.innerHTML = `
                <div class="h-4 w-4 animate-spin rounded-full border-b-2 border-blue-400"></div>
                <span class="text-sm text-blue-300">${message}</span>
            `;
            chapterElement.appendChild(indicator);
        } else {
            indicator.querySelector('span').textContent = message;
        }
    }
}

function hideProcessingIndicator(chapterId) {
    const chapterElement = document.querySelector(`[data-chapter-id="${chapterId}"]`);
    if (chapterElement) {
        const indicator = chapterElement.querySelector('.processing-indicator');
        if (indicator) {
            indicator.remove();
        }
    }
}

console.log('🎯 Biography Edit JS inizializzato completamente');

// Test debug - verifica che le funzioni siano disponibili
console.log('🔧 TEST: window.removeImage definita?', typeof window.removeImage);
console.log('🔧 TEST: window.removeChapterImage definita?', typeof window.removeChapterImage);

if (typeof window.removeImage === 'function') {
    console.log('✅ window.removeImage è disponibile globalmente');
} else {
    console.error('❌ window.removeImage NON è disponibile globalmente');
}

if (typeof window.removeChapterImage === 'function') {
    console.log('✅ window.removeChapterImage è disponibile globalmente');
} else {
    console.error('❌ window.removeChapterImage NON è disponibile globalmente');
}
