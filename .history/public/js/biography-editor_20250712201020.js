console.log('🚀 Biography Editor JS caricato - versione corretta');

document.addEventListener('DOMContentLoaded', function() {
    console.log('🔥 DOM pronto - inizializzo editor biografia');

    // Fix Editor Trix
    setupTrixEditorFixed();

    // Fix Upload Media
    setupMediaUploadFixed();
});

function setupTrixEditorFixed() {
    console.log('🔧 Configurando Trix Editor...');

    // Editor principale
    const mainEditor = document.querySelector('trix-editor[input="content-trix"]');
    if (mainEditor) {
        console.log('✅ Editor principale trovato');

        mainEditor.addEventListener('trix-change', function(e) {
            const content = e.target.editor.getDocument().toString();
            console.log('📝 Trix changed - content length:', content.length);

            const hiddenInput = document.getElementById('content-trix');
            if (hiddenInput) {
                hiddenInput.value = content;
                console.log('💾 Hidden input aggiornato');

                // Trigger input event per Livewire
                hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                console.log('📡 Event input triggerato per Livewire');
            } else {
                console.error('❌ Hidden input non trovato');
            }
        });

        console.log('✅ Editor principale configurato correttamente');
    } else {
        console.error('❌ Editor principale NON trovato');
        console.log('🔍 Elementi trovati:', document.querySelectorAll('trix-editor'));
    }

    // Editor capitoli
    const chapterEditor = document.querySelector('trix-editor[input="chapter-content-trix"]');
    if (chapterEditor) {
        console.log('✅ Editor capitoli trovato');

        chapterEditor.addEventListener('trix-change', function(e) {
            const content = e.target.editor.getDocument().toString();
            console.log('📝 Chapter Trix changed - length:', content.length);

            const hiddenInput = document.getElementById('chapter-content-trix');
            if (hiddenInput) {
                hiddenInput.value = content;
                hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                console.log('📡 Chapter input triggerato');
            }
        });

        console.log('✅ Editor capitoli configurato');
    } else {
        console.log('ℹ️ Editor capitoli non presente (normale se non sei in modalità capitoli)');
    }
}

function setupMediaUploadFixed() {
    console.log('📁 Configurando upload media...');

    const fileInput = document.getElementById('multiple-images-input');
    if (!fileInput) {
        console.error('❌ Input file NON trovato');
        console.log('🔍 Input disponibili:', document.querySelectorAll('input[type="file"]'));
        return;
    }

    console.log('✅ Input file trovato:', fileInput);

    fileInput.addEventListener('change', async function(e) {
        console.log('📂 File selezionati:', e.target.files.length);

        const files = Array.from(e.target.files);
        if (files.length === 0) {
            console.log('ℹ️ Nessun file selezionato');
            return;
        }

        // Mostra loading
        showLoadingFixed();

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            console.log(`📤 Caricando file ${i+1}/${files.length}: ${file.name} (${file.size} bytes)`);

            try {
                const result = await uploadFileFixed(file);
                console.log(`✅ File ${file.name} caricato con successo:`, result);
                showSuccessMessage(`✅ ${file.name} caricato!`);
            } catch (error) {
                console.error(`❌ Errore caricamento ${file.name}:`, error);
                showErrorMessage(`❌ Errore ${file.name}: ${error.message}`);
            }
        }

        hideLoadingFixed();
        e.target.value = ''; // Reset input

        console.log('🔄 Tutti i file processati - ricarico pagina in 2 secondi...');
        setTimeout(() => {
            console.log('🔄 Ricaricamento pagina...');
            window.location.reload();
        }, 2000);
    });

    console.log('✅ Upload media configurato correttamente');
}

async function uploadFileFixed(file) {
    console.log('📤 Inizio upload:', file.name, 'Tipo:', file.type, 'Dimensione:', file.size);

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

    console.log('📦 FormData creato');

    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        throw new Error('Token CSRF non trovato nella pagina');
    }

    const token = csrfToken.getAttribute('content');
    console.log('🔐 Token CSRF trovato:', token.substring(0, 10) + '...');

    console.log('🌐 Invio richiesta a: /biography/upload-media');

    // Upload
    const response = await fetch('/biography/upload-media', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        }
    });

    console.log('📡 Response ricevuta - status:', response.status, response.statusText);

    if (!response.ok) {
        const errorText = await response.text();
        console.error('❌ Response error:', errorText);
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
    }

    const result = await response.json();
    console.log('📄 Response JSON:', result);

    if (!result.success) {
        throw new Error(result.message || 'Upload fallito - nessun messaggio dal server');
    }

    return result;
}

// Utility functions
function showLoadingFixed() {
    console.log('⏳ Mostro loading...');
    const loading = document.getElementById('upload-loading');
    if (loading) {
        loading.classList.remove('hidden');
        loading.classList.add('flex');
        console.log('✅ Loading mostrato');
    } else {
        console.log('⚠️ Elemento loading non trovato');
    }
}

function hideLoadingFixed() {
    console.log('⏳ Nascondo loading...');
    const loading = document.getElementById('upload-loading');
    if (loading) {
        loading.classList.add('hidden');
        loading.classList.remove('flex');
        console.log('✅ Loading nascosto');
    }
}

function showErrorMessage(message) {
    console.error('❌ Errore:', message);

    const errorEl = document.getElementById('upload-error');
    if (errorEl) {
        errorEl.textContent = message;
        errorEl.classList.remove('hidden');
    }

    // Alert di backup
    alert(message);
}

function showSuccessMessage(message) {
    console.log('✅ Successo:', message);

    // Notifica visiva
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        if (document.body.contains(notification)) {
            document.body.removeChild(notification);
        }
    }, 3000);
}

console.log('🎯 File biography-editor.js caricato completamente');
