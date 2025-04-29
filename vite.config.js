import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path'; // Importa il modulo 'path' di Node.js

export default defineConfig({
    plugins: [
        laravel({
            // Input Principali di FlorenceEGI
            input: [
                'resources/js/logo3d.js',
                'resources/css/app.css',
                'resources/js/app.js',
                // --- Input dai Pacchetti Ultra/EGI (Aggiunti) ---
                // Aggiungi qui gli entry point JS/TS/CSS dei pacchetti che DEVONO essere
                // compilati direttamente da Vite nell'applicazione principale.
                // Se UUM o EGI JS/TS sono importati da 'resources/js/app.js', potresti
                // non aver bisogno di elencarli qui. Ma se sono usati separatamente, includili.
                // Esempio per UUM (verifica se importato da app.js o se serve qui):
                // 'vendor/ultra/ultra-upload-manager/resources/js/app.js', // Se UUM ha un suo app.js
                 'vendor/ultra/ultra-upload-manager/resources/ts/core/file_upload_manager.ts', // Se l'app usa direttamente il manager TS
                // Aggiungi eventuali entry point CSS di UUM se necessario:
                 'vendor/ultra/ultra-upload-manager/resources/css/app.css', // Esempio
                // Aggiungi eventuali entry point JS/TS/CSS dell'EGI Module se necessario:
                // 'packages/ultra/egi-module/resources/js/egi-feature.js', // Esempio
            ],
            // Refresh: Mantieni le tue configurazioni, aggiungi i percorsi dei pacchetti se vuoi che modifiche lì triggerino refresh
            refresh: [
                'resources/**',
                'routes/**',
                'app/**',
                'packages/ultra/egi-module/resources/**', // Aggiunto refresh per EGI module
                'packages/ultra/egi-module/routes/**',   // Aggiunto refresh per rotte EGI module
                'packages/ultra/egi-module/src/**',      // Aggiunto refresh per src EGI module
                 // Potresti aggiungere anche vendor/ultra/ultra-upload-manager/** ma può rallentare
            ],
        }),
    ],
    resolve: {
        // --- Alias (Aggiunti dalla Sandbox) ---
        alias: {
            // Alias standard per l'applicazione principale
            '@': path.resolve(__dirname, './resources/js'),
            '@ts': path.resolve(__dirname, './resources/ts'),
            // Alias per le immagini di UUM (essenziale se showEmoji.ts è usato)
            // Usa path.resolve per percorsi assoluti robusti
            '@ultra-images': path.resolve(__dirname, './vendor/ultra/ultra-upload-manager/resources/ts/assets/images'),
            // Aggiungi altri alias se necessari per EGI module o altri pacchetti Ultra
            // '@egi-assets': path.resolve(__dirname, './packages/ultra/egi-module/resources/assets'), // Esempio
        },
        // --- Preserve Symlinks (Aggiunto dalla Sandbox - CRUCIALE) ---
        // Necessario per far funzionare correttamente Vite con i pacchetti linkati
        // tramite repository 'path' o 'vcs' in composer.json.
        preserveSymlinks: true,
    },
    server: {
        // Mantieni la tua configurazione HMR
        hmr: {
            overlay: true,
        },
        // Mantieni la tua configurazione watch
        watch: {
            usePolling: true,
            interval: 1000,
            ignored: [ // Mantieni le tue regole, assicurati non ignorino 'packages' o 'vendor/ultra'
                '**/node_modules/**',
                '**/.git/**',
                '**/.venv/**',
                // '**/vendor/**', // Rimuovi o modifica questa riga se vuoi watchare i pacchetti linkati
                '**/storage/**', // Aggiungi storage per sicurezza
            ]
        },
        // --- FS Allow (Aggiunto dalla Sandbox - CRUCIALE) ---
        // Permette al server di sviluppo Vite di servire file dai pacchetti linkati.
        fs: {
            allow: [
                '.', // Root di FlorenceEGI
                // Permetti accesso a TUTTI i pacchetti locali in packages/ultra
                path.resolve(__dirname, './packages/ultra'),
                // Permetti accesso a TUTTI i pacchetti Ultra linkati in vendor
                path.resolve(__dirname, './vendor/ultra'),
                // Potrebbe servire anche per dipendenze specifiche come Spatie, Livewire se caricate in modo particolare
                // path.resolve(__dirname, './vendor/spatie'),
                // path.resolve(__dirname, './vendor/livewire'),
            ],
        },
    },
    // Mantieni la tua configurazione build (ma assicurati non entri in conflitto con resolve/alias)
    build: {
        outDir: 'public/build', // Standard Laravel
        manifest: true,        // Standard Laravel
        sourcemap: true,       // Abilita sourcemap per il build
        cssCodeSplit: true,
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
             output: {
                 // Evita codice immediato nei chunk se necessario
                 // entryFileNames: `assets/[name].js`, // Potresti voler il nome con hash per il caching
                 entryFileNames: `assets/[name]-[hash].js`,
                 chunkFileNames: `assets/[name]-[hash].js`,
                 assetFileNames: `assets/[name]-[hash].[ext]`,
             }
        }
    },
    // Mantieni la tua configurazione optimizeDeps
    optimizeDeps: {
        include: ['tailwindcss', 'daisyui', 'three', 'three/examples/jsm/controls/OrbitControls.js'],
    }
});
