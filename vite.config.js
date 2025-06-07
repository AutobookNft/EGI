import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    // 🔥 FIX URL: pulizia da caratteri malformati (\x3a, \) letti da Vite
    let appUrl = env.APP_URL || 'http://localhost';
    appUrl = appUrl.replace(/\\x3a/g, ':').replace(/\\/g, '/');

    return {
        plugins: [
            laravel({
                input: [
                    'resources/js/logo3d.js',
                    'resources/css/app.css',
                    'resources/css/guest.css',
                    'resources/css/gdpr.css',
                    'home-nft.css',
                    'resources/css/create-collection-modal-context.css',
                    'resources/js/app.js',
                    'resources/js/collection.js',
                    'resources/ts/main.js',
                    'resources/js/guest.js',
                    'resources/js/polyfills.js',
                    'home-nft.js',
                    'resources/js/components/create-collection-modal.js',
                    // 🎯 USER DOMAINS - Personal Data
                    'resources/css/domains/personal-data.css',
                    'resources/js/domains/personal-data.ts',
                    // 🎯 USER DOMAINS - Organization Data (future)
                    'resources/css/domains/organization-data.css',
                    'resources/js/domains/organization-data.ts',
                    // 🎯 USER DOMAINS - Documents (future)
                    'resources/css/domains/documents.css',
                    'resources/js/domains/documents.ts',
                    // 🎯 USER DOMAINS - Invoice Preferences (future)
                    'resources/css/domains/invoice-preferences.css',
                    'resources/js/domains/invoice-preferences.ts',
                    // Ultra Upload Manager
                    'vendor/ultra/ultra-upload-manager/resources/ts/core/file_upload_manager.ts',
                    'vendor/ultra/ultra-upload-manager/resources/css/app.css',
                ],
                refresh: [
                    'resources/**',
                    'routes/**',
                    'app/**',
                    'packages/ultra/egi-module/resources/**',
                    'packages/ultra/egi-module/routes/**',
                    'packages/ultra/egi-module/src/**',
                ],
            }),
        ],
        define: {
            // 🔑 Qui passiamo l'URL pulito a tutto il codice JS
            'process.env.APP_URL': JSON.stringify(appUrl),
        },
        resolve: {
            alias: {
                '@': path.resolve(__dirname, './resources/js'),
                '@ts': path.resolve(__dirname, './resources/ts'),
                '@domains': path.resolve(__dirname, './resources/js/domains'),
                '@ultra-images': path.resolve(__dirname, './vendor/ultra/ultra-upload-manager/resources/ts/assets/images'),
            },
            preserveSymlinks: true,
        },
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
                    '**/storage/**',
                ]
            },
            fs: {
                allow: [
                    '.',
                    path.resolve(__dirname, './packages/ultra'),
                    path.resolve(__dirname, './vendor/ultra'),
                ],
            },
        },
        build: {
            outDir: 'public/build',
            manifest: true,
            sourcemap: true,
            cssCodeSplit: true,
            chunkSizeWarningLimit: 1000,
            rollupOptions: {
                output: {
                    entryFileNames: `assets/[name]-[hash].js`,
                    chunkFileNames: `assets/[name]-[hash].js`,
                    assetFileNames: `assets/[name]-[hash].[ext]`,
                }
            }
        },
        optimizeDeps: {
            include: ['tailwindcss', 'daisyui', 'three', 'three/examples/jsm/controls/OrbitControls.js'],
        }
    };
});
