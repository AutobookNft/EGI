<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Faviicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>{{ trans('uploadmanager::uploadmanager.first_template_title') }}</title>

@vite([
    'resources/css/app.css',
    'resources/js/app.js',
    'vendor/ultra/ultra-upload-manager/resources/css/app.css',
    'vendor/ultra/ultra-upload-manager/resources/ts/core/file_upload_manager.ts'
])

<script>
    window.allowedExtensions = @json(config('AllowedFileType.collection.allowed_extensions', []));
    window.allowedMimeTypes = @json(config('AllowedFileType.collection.allowed_mime_types', []));
    window.maxSize = {{ config('AllowedFileType.collection.max_size', 10 * 1024 * 1024) }};

    // Caricamento configurazione
    fetch('{{ route("global.config") }}', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        }
    })
    .then(response => response.json())
    .then(config => {
        Object.assign(window, config);
        document.dispatchEvent(new Event('configLoaded'));
    })
    .catch(error => console.error('Error loading configuration:', error));
</script>

<style>

    body {
        background-color: #0f1018; /* Un blu scuro/nero che si abbina al tuo design */
        min-height: 100vh; /* Assicura che il body copra almeno l'intera altezza della viewport */
    }


    /* Layout con tre colonne */
    .three-column-layout {
        display: flex;
        width: 100%;
        max-width: 1800px;
        margin: 0 auto;
        padding-bottom: 3rem;
        min-height: 100vh;
        gap: 20px;
    }

    /* Colonna sinistra (Matrix) */
    .left-column {
        width: 25%; /* Aumentato da 300px a 25% */
        flex-shrink: 0;
        height: 600px;
    }

    /* Colonna centrale (Form) */
    .center-column {
        flex: 0 1 auto;
        width: 50%;
        min-width: 400px;
        max-height: 95vh; /* Limita l'altezza al 90% dell'altezza della viewport */
        overflow-y: auto; /* Aggiungi scrolling verticale se il contenuto è troppo alto */
    }

    /* Riduci lo spazio interno del contenitore principale */
    .center-column > div {
        padding: 5px; /* Ridotto da 8px */
    }

    /* Riduci i margini verticali tra le sezioni */
    .center-column .mb-6 {
        margin-bottom: 1rem; /* Ridotto da 1.5rem (valore originale di mb-6) */
    }

    /* Riduci l'altezza dell'area di drop */
    .center-column #upload-drop-zone {
        height: 160px; /* Ridotto da 256px (h-64) */
    }

    /* Personalizza scrollbar per Chrome/Safari/Edge */
    .center-column::-webkit-scrollbar {
        width: 8px;
    }

    .center-column::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    .center-column::-webkit-scrollbar-thumb {
        background: rgba(139, 92, 246, 0.5); /* Viola chiaro semi-trasparente */
        border-radius: 10px;
    }

    .center-column::-webkit-scrollbar-thumb:hover {
        background: rgba(139, 92, 246, 0.8);
    }

    /* Per Firefox */
    .center-column {
        scrollbar-width: thin;
        scrollbar-color: rgba(139, 92, 246, 0.5) rgba(0, 0, 0, 0.1);
    }

    /* Colonna destra (3D) */
    .right-column {
        width: 25%; /* Aumentato da 300px a 25% */
        flex-shrink: 0;
        height: 600px;
    }

    /* Contenitore per l'effetto Matrix */
    .matrix-container {
        width: 100%;
        height: 100%;
        background: linear-gradient(145deg, #1a1a2e, #121229);
        border-radius: 1.5rem;
        box-shadow: 0 25px 50px -12px rgba(79, 70, 229, 0.2);
        overflow: hidden;
        position: relative;
    }

    /* Contenitore per l'animazione 3D */
    .animation-container {
        width: 100%;
        height: 100%;
        background: linear-gradient(145deg, #1a1a2e, #121229);
        border-radius: 1.5rem;
        box-shadow: 0 25px 50px -12px rgba(79, 70, 229, 0.2);
        overflow: hidden;
        position: relative;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .animation-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 60px -15px rgba(79, 70, 229, 0.25);
    }

    /* Responsive per tablet */
    @media (max-width: 1280px) {
        .three-column-layout {
            flex-wrap: wrap;
        }

        .center-column {
        width: 100%;
        min-width: 0;
        margin-bottom: 20px;
        order: -1;
    }

        .left-column, .right-column {
            width: calc(50% - 10px);
            height: 400px;
        }
    }

    /* Responsive per mobile */
    @media (max-width: 768px) {
        .three-column-layout {
            flex-direction: column;
        }

        .left-column, .right-column {
            width: 100%;
            height: 250px;
        }
    }
</style>

</head>

<body id="uploading_files" class="bg-gray-900 font-sans antialiased">
    <div style="height: 40px;"></div>

    <div class="three-column-layout mt-24">
        <!-- Left Column (Matrix) -->
        <div class="left-column">
            <div class="matrix-container">
                <canvas id="matrix-canvas" class="w-full h-full"></canvas>
                <!-- Etichetta informativa -->
                <div class="absolute bottom-4 left-4 bg-black/50 text-white text-xs px-3 py-1.5 rounded-full backdrop-blur-sm border border-purple-500/30 z-10 font-medium">
                    Matrix Code
                </div>
            </div>
        </div>

        <div class="left-column">
            <div class="matrix-container">
                <div id="logo3D-container"
                style="width: 100%; height: 400px; position: relative;"
                data-model-path="{{ asset('models/logo3D.glb') }}">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #666;">
                    Caricamento in corso...
                </div>
                </div>

                <script>
                // Funzione che carica un singolo script e restituisce una Promise
                function loadScript(url) {
                    return new Promise((resolve, reject) => {
                        console.log("Caricamento script:", url);
                        const script = document.createElement('script');
                        script.src = url;
                        script.async = true;
                        script.onload = () => {
                            console.log("Script caricato con successo:", url);
                            resolve();
                        };
                        script.onerror = (e) => {
                            console.error("Errore nel caricamento dello script:", url, e);
                            reject(new Error(`Impossibile caricare lo script: ${url}`));
                        };
                        document.head.appendChild(script);
                    });
                }

                // Attendi che il DOM sia pronto
                document.addEventListener('DOMContentLoaded', function() {
                    const container = document.getElementById('logo3D-container');
                    if (!container) {
                        console.error("Container logo3D-container non trovato");
                        return;
                    }

                    // Ottieni il percorso del modello dall'attributo data
                    const modelPath = container.dataset.modelPath;
                    if (!modelPath) {
                        console.error("Percorso del modello non specificato nell'attributo data-model-path");
                        return;
                    }

                    console.log("Percorso del modello:", modelPath);

                    const loadingEl = container.querySelector('div');

                    // Carica Three.js
                    loadScript('https://cdnjs.cloudflare.com/ajax/libs/three.js/0.150.0/three.min.js')
                        .then(() => {
                            // Verifica che THREE sia stato caricato correttamente
                            if (typeof THREE === 'undefined') {
                                throw new Error("THREE non è stato definito correttamente");
                            }
                            console.log("THREE caricato:", THREE);
                            loadingEl.textContent = 'Caricamento modulo OrbitControls...';

                            // Carica OrbitControls
                            return loadScript('https://cdnjs.cloudflare.com/ajax/libs/three.js/0.150.0/examples/js/controls/OrbitControls.js');
                        })
                        .then(() => {
                            // Verifica che OrbitControls sia stato caricato
                            if (!THREE.OrbitControls) {
                                throw new Error("THREE.OrbitControls non è stato definito");
                            }
                            console.log("OrbitControls caricato:", THREE.OrbitControls);
                            loadingEl.textContent = 'Caricamento modulo GLTFLoader...';

                            // Carica GLTFLoader
                            return loadScript('https://cdnjs.cloudflare.com/ajax/libs/three.js/0.150.0/examples/js/loaders/GLTFLoader.js');
                        })
                        .then(() => {
                            // Verifica che GLTFLoader sia stato caricato
                            if (!THREE.GLTFLoader) {
                                throw new Error("THREE.GLTFLoader non è stato definito");
                            }
                            console.log("GLTFLoader caricato:", THREE.GLTFLoader);
                            loadingEl.textContent = 'Inizializzazione visualizzatore...';

                            // Inizializza Three.js
                            setupViewer();
                        })
                        .catch(error => {
                            console.error("Errore durante l'inizializzazione:", error);
                            loadingEl.textContent = `Errore: ${error.message}`;
                        });

                    // Funzione per configurare il visualizzatore 3D
                    function setupViewer() {
                        try {
                            // Crea la scena
                            const scene = new THREE.Scene();
                            scene.background = new THREE.Color(0xf0f0f0); // Grigio chiaro

                            // Configura la camera
                            const camera = new THREE.PerspectiveCamera(45, container.clientWidth / container.clientHeight, 0.1, 1000);
                            camera.position.set(0, 0, 5);

                            // Crea il renderer
                            const renderer = new THREE.WebGLRenderer({ antialias: true });
                            renderer.setSize(container.clientWidth, container.clientHeight);
                            container.appendChild(renderer.domElement);

                            // Aggiungi luci
                            const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
                            scene.add(ambientLight);

                            const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
                            directionalLight.position.set(0, 10, 10);
                            scene.add(directionalLight);

                            // Aggiungi controlli
                            const controls = new THREE.OrbitControls(camera, renderer.domElement);
                            controls.enableDamping = true;
                            controls.dampingFactor = 0.05;

                            // Carica il modello
                            loadingEl.textContent = 'Caricamento modello 3D...';

                            console.log("Caricamento modello da:", modelPath);

                            // Verifica se il file è accessibile
                            fetch(modelPath)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`Errore HTTP: ${response.status}`);
                                    }
                                    console.log("Il file esiste e può essere recuperato");
                                    loadingEl.textContent = 'File trovato, caricamento in corso...';

                                    // Procedi con il caricamento del modello
                                    const loader = new THREE.GLTFLoader();
                                    loader.load(
                                        modelPath,
                                        // Success callback
                                        function(gltf) {
                                            console.log("Modello caricato con successo:", gltf);
                                            loadingEl.style.display = 'none';

                                            // Aggiungi il modello alla scena
                                            scene.add(gltf.scene);

                                            // Centra il modello
                                            const box = new THREE.Box3().setFromObject(gltf.scene);
                                            const center = box.getCenter(new THREE.Vector3());
                                            gltf.scene.position.x = -center.x;
                                            gltf.scene.position.y = -center.y;
                                            gltf.scene.position.z = -center.z;

                                            // Ridimensiona il modello per adattarlo alla vista
                                            const size = box.getSize(new THREE.Vector3());
                                            const maxDim = Math.max(size.x, size.y, size.z);
                                            if (maxDim > 4) {
                                                const scale = 4 / maxDim;
                                                gltf.scene.scale.set(scale, scale, scale);
                                            }

                                            // Anima
                                            function animate() {
                                                requestAnimationFrame(animate);
                                                controls.update();
                                                renderer.render(scene, camera);
                                            }
                                            animate();
                                        },
                                        // Progress callback
                                        function(xhr) {
                                            if (xhr.lengthComputable) {
                                                const percent = Math.round((xhr.loaded / xhr.total) * 100);
                                                loadingEl.textContent = `Caricamento modello: ${percent}%`;
                                            }
                                        },
                                        // Error callback
                                        function(error) {
                                            console.error('Errore nel caricamento del modello:', error);
                                            loadingEl.textContent = `Errore caricamento modello: ${error.message}`;
                                        }
                                    );
                                })
                                .catch(error => {
                                    console.error("Errore accesso al file:", error);
                                    loadingEl.textContent = `File non trovato: ${error.message}`;
                                });

                            // Gestisci il ridimensionamento della finestra
                            window.addEventListener('resize', function() {
                                camera.aspect = container.clientWidth / container.clientHeight;
                                camera.updateProjectionMatrix();
                                renderer.setSize(container.clientWidth, container.clientHeight);
                            });

                        } catch (error) {
                            console.error("Errore durante il setup del visualizzatore:", error);
                            loadingEl.textContent = `Errore setup: ${error.message}`;
                        }
                    }
                });
                </script>
                <!-- Etichetta informativa -->
                <div class="absolute bottom-4 left-4 bg-black/50 text-white text-xs px-3 py-1.5 rounded-full backdrop-blur-sm border border-purple-500/30 z-10 font-medium">
                    Logo Franegette
                </div>
            </div>
        </div>


        <!-- Center Column (Main Form) -->
        <div class="center-column">
            <div class="p-8 bg-gradient-to-br from-gray-800 via-purple-900 to-blue-900 rounded-2xl shadow-2xl border border-purple-500/30 relative nft-background" id="upload-container">
                <!-- Title with EGI style -->
                <h2 class="text-4xl font-extrabold text-white mb-6 text-center tracking-wide drop-shadow-lg nft-title">
                    💎 {{ __('uploadmanager::uploadmanager.mint_your_masterpiece') }}
                </h2>

                <!-- Enterprise Features Badges -->
                <div class="flex flex-wrap justify-center gap-3 mb-6">
                    <div class="bg-blue-900/60 text-blue-200 px-3 py-1.5 rounded-lg text-sm font-medium flex items-center shadow-md" title="{{ trans('uploadmanager::uploadmanager.secure_storage_tooltip') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                        {{ trans('uploadmanager::uploadmanager.secure_storage') }}
                    </div>
                    <div class="bg-purple-900/60 text-purple-200 px-3 py-1.5 rounded-lg text-sm font-medium flex items-center shadow-md" title="{{ trans('uploadmanager::uploadmanager.virus_scan_tooltip') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        {{ trans('uploadmanager::uploadmanager.virus_scan_feature') }}
                    </div>
                    <div class="bg-green-900/60 text-green-200 px-3 py-1.5 rounded-lg text-sm font-medium flex items-center shadow-md" title="{{ trans('uploadmanager::uploadmanager.advanced_validation_tooltip') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        {{ trans('uploadmanager::uploadmanager.advanced_validation') }}
                    </div>
                    <div class="bg-indigo-900/60 text-indigo-200 px-3 py-1.5 rounded-lg text-sm font-medium flex items-center shadow-md" title="{{ trans('uploadmanager::uploadmanager.storage_space_tooltip') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span>
                            <span id="storage-used">2.4</span>/<span id="storage-total">50</span> {{ trans('uploadmanager::uploadmanager.storage_space_unit') }}
                        </span>
                    </div>
                </div>

                <!-- Enhanced drag & drop upload area -->
                <div
                    class="w-full h-64 border-4 border-dashed border-blue-400/50 rounded-2xl mb-6 flex flex-col items-center justify-center p-8 transition-all duration-300 bg-purple-800/20 hover:bg-purple-800/30 group"
                    id="upload-drop-zone">
                    <!-- Drag & drop icon/illustration -->
                    <div class="text-5xl mb-4 text-blue-400 group-hover:scale-110 transition-transform duration-300">
                        📤
                    </div>
                    <!-- Instructions with improved contrast (Punto 5) -->
                    <p class="text-xl text-center text-white mb-6">
                        {{ trans('uploadmanager::uploadmanager.drag_files_here') }} <br>
                        <span class="text-blue-200 text-sm">{{ trans('uploadmanager::uploadmanager.or') }}</span>
                    </p>
                    <!-- Button styled with tooltip (Punto 5) -->
                    <label for="files" id="file-label" class="relative cursor-pointer rounded-full bg-gradient-to-r from-purple-600 to-blue-600 px-8 py-4 flex items-center justify-center text-lg font-semibold text-white transition-all duration-300 ease-in-out hover:from-purple-500 hover:to-blue-500 hover:shadow-xl nft-button group" aria-label="{{ trans('uploadmanager::uploadmanager.select_files_aria') }}">
                        {{ trans('uploadmanager::uploadmanager.select_files') }}
                        <input type="file" id="files" multiple class="absolute left-0 top-0 h-full w-full cursor-pointer opacity-0">
                        <!-- Tooltip (Punto 5) -->
                        <span class="absolute -top-12 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 w-48 text-center">
                            {{ trans('uploadmanager::uploadmanager.select_files_tooltip') }}
                        </span>
                    </label>
                    <div class="upload-dropzone text-center text-gray-200 text-sm mt-2">
                        <!-- About upload size -->
                    </div>
                </div>

                <div class="bg-gray-800/50 rounded-xl p-5 mb-6 border border-purple-500/30">
                    <h3 class="text-lg font-semibold text-white mb-4">{{ trans('uploadmanager::uploadmanager.quick_egi_metadata') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">

                        {{-- Riga 1: Titolo e Floor Price --}}
                        <div>
                            <label for="egi-title" class="block text-sm font-medium text-gray-300 mb-1">{{ trans('uploadmanager::uploadmanager.egi_title') }}</label>
                            <input type="text" id="egi-title" name="egi-title" placeholder="{{ trans('uploadmanager::uploadmanager.egi_title_placeholder') }}"
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-gray-500">
                                <p class="text-xs text-gray-400 mt-1">{{ trans('uploadmanager::uploadmanager.egi_title_info') }}</p>
                        </div>

                        <div>
                            <label for="egi-floor-price" class="block text-sm font-medium text-gray-300 mb-1">{{ trans('uploadmanager::uploadmanager.floor_price') }}</label>
                            <input type="number" step="0.01" min="0" id="egi-floor-price" name="egi-floor-price" placeholder="{{ trans('uploadmanager::uploadmanager.floor_price_placeholder') }}"
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-gray-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                            <p class="text-xs text-gray-400 mt-1">{{ trans('uploadmanager::uploadmanager.floor_price_info') }}</p>
                        </div>

                        {{-- Riga 2: Data e Posizione --}}
                        <div>
                            <label for="egi-date" class="block text-sm font-medium text-gray-300 mb-1">{{ trans('uploadmanager::uploadmanager.creation_date') }}</label>
                            <input type="date" id="egi-date" name="egi-date"
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-gray-500"
                                style="color-scheme: dark;">
                            <p class="text-xs text-gray-400 mt-1">{{ trans('uploadmanager::uploadmanager.creation_date_info') }}</p>
                        </div>

                        <div>
                            <label for="egi-position" class="block text-sm font-medium text-gray-300 mb-1">{{ trans('uploadmanager::uploadmanager.position') }}</label>
                            <input type="number" step="1" min="1" id="egi-position" name="egi-position" placeholder="{{ trans('uploadmanager::uploadmanager.position_placeholder') }}"
                                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-gray-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                            <p class="text-xs text-gray-400 mt-1">{{ trans('uploadmanager::uploadmanager.position_info') }}</p>
                        </div>

                        {{-- Riga 3: Descrizione (occupa 2 colonne) --}}
                        <div class="md:col-span-2">
                            <label for="egi-description" class="block text-sm font-medium text-gray-300 mb-1">{{ trans('uploadmanager::uploadmanager.egi_description') }}</label>
                            <textarea id="egi-description" name="egi-description" rows="3" placeholder="{{ trans('uploadmanager::uploadmanager.egi_description_placeholder') }}"
                                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-gray-500"></textarea>
                            <p class="text-xs text-gray-400 mt-1">{{ trans('uploadmanager::uploadmanager.metadata_notice') }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-3 my-6">
                    <input
                        class="me-2 h-4 w-8 appearance-none rounded-full bg-gray-600 before:pointer-events-none before:absolute before:h-4 before:w-4 before:rounded-full before:bg-transparent after:absolute after:z-[2] after:-mt-0.5 after:h-6 after:w-6 after:rounded-full after:bg-white after:shadow-md after:transition-all checked:bg-green-500 checked:after:ms-4 checked:after:bg-green-300 checked:after:shadow-md hover:cursor-pointer focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                        type="checkbox"
                        role="switch"
                        id="egi-publish"
                        name="egi-publish"
                        checked
                        title="{{ trans('uploadmanager::uploadmanager.toggle_publish_status') }}"
                    />
                    <label
                        class="font-medium hover:pointer-events-none text-green-300"
                        id="egi-publish_label"
                        for="egi-publish"
                    >{{ trans('uploadmanager::uploadmanager.publish_egi') }}</label>
                </div>

                <!-- Progress bar and virus switch -->
                <div class="mt-6 space-y-6">
                    <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-400 to-blue-500 h-3 rounded-full transition-all duration-500" id="progress-bar"></div>
                    </div>
                    <p class="text-gray-200 text-sm text-center"><span id="progress-text"></span></p>

                    <div class="flex items-center justify-center gap-3">
                        <input
                            class="me-2 h-4 w-8 appearance-none rounded-full bg-gray-600 before:pointer-events-none before:absolute before:h-4 before:w-4 before:rounded-full before:bg-transparent after:absolute after:z-[2] after:-mt-0.5 after:h-6 after:w-6 after:rounded-full after:bg-white after:shadow-md after:transition-all checked:bg-purple-600 checked:after:ms-4 checked:after:bg-purple-400 checked:after:shadow-md hover:cursor-pointer focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-gray-900"
                            type="checkbox"
                            role="switch"
                            id="scanvirus"
                            title="{{ trans('uploadmanager::uploadmanager.toggle_virus_scan') }}"
                        />
                        <label
                            class="text-red-400 font-medium hover:pointer-events-none"
                            id="scanvirus_label"
                            for="scanvirus"
                        >{{ trans('uploadmanager::uploadmanager.virus_scan_disabled') }}</label>
                    </div>
                    <p class="text-gray-200 text-sm text-center"><span id="virus-advise"></span></p>
                </div>

                <!-- Action buttons with EGI style and tooltips -->
                <div class="mt-10 flex justify-center space-x-6">
                    <button type="button" id="uploadBtn" class="relative bg-green-500 text-white px-8 py-4 rounded-full font-semibold text-lg nft-button opacity-50 cursor-not-allowed disabled:hover:bg-green-500 disabled:hover:shadow-none group" aria-label="{{ trans('uploadmanager::uploadmanager.save_aria') }}">
                        💾 {{ trans('uploadmanager::uploadmanager.save_the_files') }}
                        <span class="absolute -top-12 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 w-48 text-center pointer-events-none">
                            {{ trans('uploadmanager::uploadmanager.save_tooltip') }}
                        </span>
                    </button>
                    <button type="button" onclick="cancelUpload()" id="cancelUpload" class="relative bg-red-500 text-white px-8 py-4 rounded-full font-semibold text-lg nft-button opacity-50 cursor-not-allowed disabled:hover:bg-red-500 disabled:hover:shadow-none group" aria-label="{{ trans('uploadmanager::uploadmanager.cancel_aria') }}">
                        ❌ {{ trans('uploadmanager::uploadmanager.cancel') }}
                        <span class="absolute -top-12 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 w-48 text-center pointer-events-none">
                            {{ trans('uploadmanager::uploadmanager.cancel_tooltip') }}
                        </span>
                    </button>
                </div>

                <!-- Previews grid -->
                <div id="collection" class="mt-10 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                    <!-- Previews will be loaded dynamically via JS -->
                </div>

                <!-- Return to collection button with tooltip -->
                <div class="mt-6 flex justify-center">
                    <button type="button" onclick="redirectToCollection()" id="returnToCollection" class="relative bg-gray-700 text-white px-8 py-4 rounded-full font-semibold text-lg nft-button hover:bg-gray-600 group" aria-label="{{ trans('uploadmanager::uploadmanager.return_aria') }}">
                        🔙 {{ trans('uploadmanager::uploadmanager.return_to_collection') }}
                        <span class="absolute -top-12 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 w-48 text-center pointer-events-none">
                            {{ trans('uploadmanager::uploadmanager.return_tooltip') }}
                        </span>
                    </button>
                </div>

                <!-- Status and Upload status -->
                <div class="mt-10 text-center">
                    <p class="text-gray-200 text-sm"><span id="scan-progress-text"></span></p>
                </div>
                <div id="status" class="mt-6 text-center text-gray-200 text-lg font-medium"></div>
                <div id="upload-status" class="mt-8 text-center text-gray-200">
                    <p id="status-message">{{ trans('uploadmanager::uploadmanager.preparing_to_mint') }}</p>
                </div>
            </div>
        </div>

        <!-- Right Column (3D Animation) -->
        <div class="right-column">
            <div class="animation-container">
                <div id="dynamic-3d-container" class="w-full h-full bg-gradient-to-br from-gray-900/70 via-purple-950/70 to-black/70 rounded-2xl border border-purple-500/30 overflow-hidden relative">
                    <!-- Elemento di caricamento -->
                    <div id="loading" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-70 transition-opacity duration-1000 backdrop-blur-sm z-10">
                        <div class="text-white flex flex-col items-center">
                            <div class="w-12 h-12 border-4 border-t-purple-500 border-purple-300/20 rounded-full animate-spin mb-4"></div>
                            <div class="text-purple-200 font-medium tracking-wide">Caricamento EGI...</div>
                        </div>
                    </div>

                    <!-- Canvas per Three.js -->
                    <canvas id="webgl-canvas" class="w-full h-full relative z-0 rounded-2xl"></canvas>

                    <!-- Etichetta informativa -->
                    <div class="absolute bottom-4 left-4 bg-black/50 text-white text-xs px-3 py-1.5 rounded-full backdrop-blur-sm border border-purple-500/30 z-10 font-medium">
                        3D Preview
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Verifica che il canvas Matrix esista
            const matrixCanvas = document.getElementById('matrix-canvas');
            if (!matrixCanvas) return;

            const ctx = matrixCanvas.getContext('2d');

            // Impostazione dimensione canvas per adattarlo al contenitore
            function resizeCanvas() {
                const container = matrixCanvas.parentElement;
                matrixCanvas.width = container.clientWidth;
                matrixCanvas.height = container.clientHeight;
            }

            // Chiamiamo resize inizialmente
            resizeCanvas();

            // E ogni volta che la finestra cambia dimensione
            window.addEventListener('resize', resizeCanvas);

            // Caratteri per l'effetto Matrix (numeri, katakana giapponese, simboli)
            const katakana = 'アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワン';
            const latin = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            const nums = '0123456789';
            const symbols = '♔♕♖♗♘♙♚♛♜♝♞♟☀☁♟♤♧♥♦•◘○◙♂♀♪♫☼►◄↕‼▬↨↑↓→←∟↔▲▼!@#$%^&*()+';

            // Tutti i possibili caratteri
            const chars = katakana + latin + nums + symbols;

            // Font per i caratteri
            const fontSize = 14;
            const columns = Math.floor(matrixCanvas.width / fontSize);

            // Array per tenere traccia della posizione Y di ogni colonna
            const drops = [];
            const speeds = [];

            // Inizializza tutte le gocce ad una posizione Y casuale
            for (let i = 0; i < columns; i++) {
                drops[i] = Math.random() * -100;
                // Velocità casuale per ogni colonna tra 0.2 e 0.5
                speeds[i] = 0.2 + Math.random() * 0.3;
            }

            // Funzione per disegnare l'effetto Matrix
            function drawMatrix() {
                // Overlay semi-trasparente per creare l'effetto "fade"
                ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
                ctx.fillRect(0, 0, matrixCanvas.width, matrixCanvas.height);

                // Colore verde tipico dell'effetto Matrix, con variazioni per profondità
                ctx.fillStyle = '#0f0';
                ctx.font = fontSize + 'px monospace';

                // Loop attraverso ogni goccia
                for (let i = 0; i < drops.length; i++) {
                    // Scegli un carattere casuale
                    const text = chars.charAt(Math.floor(Math.random() * chars.length));

                    // Alcune gocce sono più luminose per un effetto di profondità
                    if (Math.random() > 0.975) {
                        ctx.fillStyle = '#fff'; // Bianco brillante occasionale
                    } else if (Math.random() > 0.95) {
                        ctx.fillStyle = '#9ff'; // Ciano chiaro
                    } else if (Math.random() > 0.9) {
                        ctx.fillStyle = '#6f6'; // Verde chiaro
                    } else {
                        ctx.fillStyle = '#0f0'; // Verde standard
                    }

                    // x = i * dimensione font, y = valore in drops[i] * dimensione font
                    ctx.fillText(text, i * fontSize, drops[i] * fontSize);

                    // Aggiungi randomness all'effetto
                    if (drops[i] * fontSize > matrixCanvas.height && Math.random() > 0.975) {
                        drops[i] = 0;
                    }

                    // Incrementa la posizione Y utilizzando la velocità personalizzata
                    drops[i] += speeds[i];
                }

                // Richiama la funzione per l'animazione
                requestAnimationFrame(drawMatrix);
            }

            // Avvia l'animazione
            drawMatrix();
        });
    </script>

</body>
</html>
