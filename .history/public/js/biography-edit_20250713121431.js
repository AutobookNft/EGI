console.log('🚀 Biography Edit JS caricato');

document.addEventListener('DOMContentLoaded', function() {
    console.log('🔥 DOM pronto - inizializzo edit biografia');

    // Gestione tab
    const tabButtons = document.querySelectorAll('.tab-button');
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
        editor.editor.loadHTML(hiddenInput.value);
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
        }
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
            body: JSON.stringify({ media_id: mediaId })
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
