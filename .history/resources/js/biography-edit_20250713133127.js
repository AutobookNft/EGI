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
                hiddenInput.value = trixEditor.editor.getDocument().toString();
            }
        });
    }

    // Inizializza editor
    initializeTrixEditor();

    // Inizializza upload
    initializeMediaUpload();

    // Inizializza contatore caratteri
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
            document.body.classList.add('overflow-hidden');
        });
        closeChapterModalBtn.addEventListener('click', closeChapterModal);
        chapterModal.addEventListener('click', function (e) {
            if (e.target === chapterModal) closeChapterModal();
        });
    }

    function closeChapterModal() {
        chapterModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        chapterModalContent.innerHTML = '';
    }

    // === Render form creazione capitolo ===
    function renderChapterForm(chapter = null) {
        chapterModalContent.innerHTML = `
            <h2 class="text-xl font-bold text-white mb-4">${chapter ? 'Modifica Capitolo' : 'Aggiungi Capitolo'}</h2>
            <form id="chapter-create-form" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-300 mb-1">Titolo *</label>
                    <input type="text" name="title" required class="w-full rounded border border-gray-600 bg-gray-800 px-3 py-2 text-white" value="${chapter ? chapter.title : ''}">
                </div>
                <div class="flex space-x-4">
                    <div class="flex-1">
                        <label class="block text-sm text-gray-300 mb-1">Dal</label>
                        <input type="date" name="date_from" class="w-full rounded border border-gray-600 bg-gray-800 px-3 py-2 text-white" value="${chapter && chapter.date_from ? chapter.date_from : ''}">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm text-gray-300 mb-1">Al</label>
                        <input type="date" name="date_to" class="w-full rounded border border-gray-600 bg-gray-800 px-3 py-2 text-white" value="${chapter && chapter.date_to ? chapter.date_to : ''}">
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-1">Contenuto *</label>
                    <input id="chapter-content-trix" type="hidden" name="content" value="${chapter ? (chapter.content || '') : ''}">
                    <trix-editor input="chapter-content-trix" class="trix-editor-biography"></trix-editor>
                </div>
                <div>
                    <label class="block text-sm text-gray-300 mb-1">Immagini</label>
                    <input type="file" id="chapter-media-input" name="images[]" multiple accept="image/*" class="block w-full text-white">
                    <div id="chapter-media-gallery" class="mt-4 grid grid-cols-2 gap-4"></div>
                </div>
                <div class="flex items-center justify-between mt-6">
                    <button type="button" id="delete-chapter-btn" class="px-4 py-2 rounded bg-red-700 text-white hover:bg-red-600 ${chapter ? '' : 'hidden'}">Elimina</button>
                    <div class="flex items-center space-x-2">
                        <button type="button" id="cancel-chapter-btn" class="px-4 py-2 rounded bg-gray-700 text-white hover:bg-gray-600">Annulla</button>
                        <button type="submit" class="px-6 py-2 rounded bg-[#D4A574] text-gray-900 font-semibold hover:bg-[#E6B885]">Salva Capitolo</button>
                    </div>
                </div>
                <div id="chapter-form-error" class="mt-2 text-red-400 text-sm hidden"></div>
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
            await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });
            const biographyId = window.biographyId || (window.location.pathname.match(/biography\/(\d+)/) || [])[1];
            if (!biographyId) throw new Error('ID biografia non trovato');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const url = isEdit ? `/api/biographies/${biographyId}/chapters/${form.dataset.chapterId}` : `/api/biographies/${biographyId}/chapters`;
            const method = isEdit ? 'PUT' : 'POST';
            const body = JSON.stringify({
                biography_id: biographyId,
                title,
                content,
                date_from,
                date_to
            });
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
            if (isEdit) updateChapterInList(result.data); else appendChapterToList(result.data);
            // Upload media se selezionati
            const mediaInput = document.getElementById('chapter-media-input');
            if (mediaInput && mediaInput.files.length > 0) {
                await uploadChapterMedia(result.data.id, mediaInput.files);
            }
            closeChapterModal();
            showSuccess(isEdit ? 'Capitolo aggiornato!' : 'Capitolo aggiunto!');
        } catch (err) {
            errorDiv.textContent = err.message || 'Errore imprevisto.';
            errorDiv.classList.remove('hidden');
        }
    }

    async function uploadChapterMedia(chapterId, files) {
        await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const formData = new FormData();
        for (const file of files) formData.append('images[]', file);
        await fetch(`/biography/chapters/${chapterId}/media`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: formData,
            credentials: 'same-origin'
        });
    }

    function updateChapterInList(chapter) {
        let chaptersSection = document.getElementById('biography-chapters-list');
        if (!chaptersSection) return;
        const divs = chaptersSection.querySelectorAll('.mb-6.rounded-xl.bg-gray-800\/60.p-6.shadow-lg');
        divs.forEach(div => {
            if (div.dataset.chapterId == chapter.id) {
                div.innerHTML = renderChapterCardHtml(chapter);
            }
        });
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
        div.className = 'mb-6 rounded-xl bg-gray-800/60 p-6 shadow-lg';
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
            <div class="mb-2">
                <div class="flex flex-wrap gap-2">
                    ${(chapter.media || []).map(m => `<img src="${m.thumb_url || m.url}" class="h-20 w-20 object-cover rounded shadow" alt="media">`).join('')}
                </div>
            </div>
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
        await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });
        const biographyId = window.biographyId || (window.location.pathname.match(/biography\/(\d+)/) || [])[1];
        const response = await fetch(`/api/biographies/${biographyId}/chapters/${chapterId}`, {
            credentials: 'same-origin'
        });
        const result = await response.json();
        if (!result.success) throw new Error(result.message || 'Errore caricamento capitolo');
        return result.data;
    }

    // === Aggiorna lista capitoli (append nuovo) ===
    function appendChapterToList(chapter) {
        // Trova la sezione capitoli (deve avere un id o classe nota)
        let chaptersSection = document.getElementById('biography-chapters-list');
        if (!chaptersSection) {
            // Sezione non esiste, la creo subito dopo il bottone
            const btn = document.getElementById('add-chapter-btn');
            chaptersSection = document.createElement('div');
            chaptersSection.id = 'biography-chapters-list';
            chaptersSection.className = 'mt-10';
            btn.parentNode.insertAdjacentElement('afterend', chaptersSection);
        }
        // Aggiungi il nuovo capitolo
        const div = document.createElement('div');
        div.className = 'mb-6 rounded-xl bg-gray-800/60 p-6 shadow-lg';
        div.innerHTML = `
            <h3 class="mb-2 text-lg font-semibold text-white">${chapter.title}</h3>
            <div class="prose prose-invert max-w-none text-white">${chapter.content}</div>
            <!-- Qui andrà la gestione media e i bottoni CRUD -->
        `;
        chaptersSection.appendChild(div);
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

    editor.addEventListener('trix-change', function(e) {
        const content = e.target.editor.getDocument().toString();
        console.log('📝 Contenuto editor aggiornato, lunghezza:', content.length);

        // Aggiorna il hidden input
        if (hiddenInput) {
            hiddenInput.value = content;
        }
    });

    console.log('✅ Editor Trix configurato con contenuto esistente');
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

async function uploadFile(file) {
    console.log('📤 Inizio upload:', file.name);

    // Validazione
    if (!file.type.startsWith('image/')) {
        throw new Error('Il file non è un\'immagine');
    }

    if (file.size > 2 * 1024 * 1024) {
        throw new Error('File troppo grande (massimo 2MB)');
    }

    // FormData
    const formData = new FormData();
    formData.append('file', file);
    formData.append('collection', 'main_gallery');

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
    imageElement.className = 'relative group';
    imageElement.innerHTML = `
        <div class="relative overflow-hidden rounded-lg bg-gray-900 aspect-square">
            <img src="${media.url}" alt="${media.file_name}"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity flex items-center justify-center">
                <button onclick="removeImage(${media.id})"
                        class="opacity-0 group-hover:opacity-100 bg-red-500 hover:bg-red-600 text-white rounded-full p-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div class="mt-2 text-sm text-gray-300 truncate">
            ${media.file_name}
        </div>
    `;

    imagesGrid.appendChild(imageElement);
}

async function removeImage(mediaId) {
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
            // Rimuovi l'elemento dalla gallery (cerca sia nelle immagini esistenti che in quelle nuove)
            const imageElement = document.querySelector(`button[onclick="removeImage(${mediaId})"]`);
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

async function deleteChapter(chapterId) {
    if (!confirm('Sei sicuro di voler eliminare questo capitolo?')) {
        return;
    }

    try {
        await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const biographyId = window.biographyId || (window.location.pathname.match(/biography\/(\d+)/) || [])[1];

        const response = await fetch(`/api/biographies/${biographyId}/chapters/${chapterId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });

        const result = await response.json();

        if (result.success) {
            // Rimuovi l'elemento dalla lista dei capitoli
            const chapterElement = document.querySelector(`.mb-6.rounded-xl.bg-gray-800/60.p-6.shadow-lg h3:contains("${result.data.title}")`);
            if (chapterElement) {
                chapterElement.closest('.mb-6').remove();
            }
            showSuccess('Capitolo eliminato con successo!');
            closeChapterModal(); // Chiudi la modale dopo l'eliminazione
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
    }
}

function hideSuccess() {
    const success = document.getElementById('upload-success');
    if (success) {
        success.classList.add('hidden');
    }
}

console.log('🎯 Biography Edit JS inizializzato completamente');
