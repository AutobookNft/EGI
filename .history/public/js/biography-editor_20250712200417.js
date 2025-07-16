console.log('Biography Editor JS caricato');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM pronto - inizializzo editor biografia');

    // Fix Editor Trix
    setupTrixEditorFixed();

    // Fix Upload Media
    setupMediaUploadFixed();
});

function setupTrixEditorFixed() {
    console.log('Configurando Trix Editor...');

    // Editor principale
    const mainEditor = document.querySelector('trix-editor[input="content-trix"]');
    if (mainEditor) {
        console.log('Editor principale trovato');

        mainEditor.addEventListener('trix-change', function(e) {
            console.log('Trix changed - content length:', e.target.editor.getDocument().toString().length);

            const hiddenInput = document.getElementById('content-trix');
            if (hiddenInput) {
                hiddenInput.value = e.target.editor.getDocument().toString();

                // Livewire sync
                if (window.Livewire) {
                    window.Livewire.emit('updateTrixContent', hiddenInput.value);
                }
            }
        });

        console.log('✅ Editor principale configurato');
    } else {
        console.log('❌ Editor principale NON trovato');
    }

    // Editor capitoli
    const chapterEditor = document.querySelector('trix-editor[input="chapter-content-trix"]');
    if (chapterEditor) {
        console.log('Editor capitoli trovato');

        chapterEditor.addEventListener('trix-change', function(e) {
            console.log('Chapter Trix changed');

            const hiddenInput = document.getElementById('chapter-content-trix');
            if (hiddenInput) {
                hiddenInput.value = e.target.editor.getDocument().toString();

                // Livewire sync
                if (window.Livewire) {
                    window.Livewire.emit('updateChapterTrixContent', hiddenInput.value);
                }
            }
        });

        console.log('✅ Editor capitoli configurato');
    }
}

function setupMediaUploadFixed() {
    console.log('Configurando upload media...');

    const fileInput = document.getElementById('multiple-images-input');
    if (!fileInput) {
        console.log('❌ Input file NON trovato');
        return;
    }

    console.log('✅ Input file trovato');

    fileInput.addEventListener('change', async function(e) {
        console.log('File selezionati:', e.target.files.length);

        const files = Array.from(e.target.files);
        if (files.length === 0) return;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            console.log(`Caricando file ${i+1}/${files.length}: ${file.name}`);

            try {
                await uploadFileFixed(file);
                console.log(`✅ File ${file.name} caricato`);
            } catch (error) {
                console.error(`❌ Errore file ${file.name}:`, error);
                alert(`Errore caricamento ${file.name}: ${error.message}`);
            }
        }

        console.log('Tutti i file processati - ricarico pagina...');
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    });
}

async function uploadFileFixed(file) {
    console.log('Upload file:', file.name, 'Dimensione:', file.size);

    // Validazione
    if (!file.type.startsWith('image/')) {
        throw new Error('Non è un\'immagine');
    }

    if (file.size > 2 * 1024 * 1024) {
        throw new Error('File troppo grande (max 2MB)');
    }

    // FormData
    const formData = new FormData();
    formData.append('file', file);
    formData.append('collection', 'main_gallery');

    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        throw new Error('CSRF token non trovato');
    }

    console.log('Inviando richiesta upload...');

    // Upload
    const response = await fetch('/biography/upload-media', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'Accept': 'application/json'
        }
    });

    console.log('Response status:', response.status);

    if (!response.ok) {
        const errorText = await response.text();
        console.error('Response error:', errorText);
        throw new Error(`HTTP ${response.status}: ${errorText}`);
    }

    const result = await response.json();
    console.log('Upload result:', result);

    if (!result.success) {
        throw new Error(result.message || 'Upload fallito');
    }

    return result;
}
