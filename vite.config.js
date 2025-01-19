import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: [
                'resources/**',
                'routes/**',
                'app/**',
            ],
        }),
    ],
    server: {
        hmr: {
            overlay: true,
        },
        watch: {
            usePolling: true,
            interval: 1000,
            ignored: [
                '**/node_modules/**',
                '**/.git/**',
                '**/.venv/**',
                '**/vendor/**',
                // Ignora i link simbolici ricorsivi
                '**/EGI/EGI/**'
            ]
        }
    },
    // Rimosso il blocco css che causava il problema
    build: {
        cssCodeSplit: true,
        chunkSizeWarningLimit: 1000,
    },
    optimizeDeps: {
        include: ['tailwindcss', 'daisyui'],
    }
});
