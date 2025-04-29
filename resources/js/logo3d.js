// public/js/logo3d.js o resources/js/logo3d.js
let THREE, OrbitControls, GLTFLoader;

// Funzione di inizializzazione esportata
export async function initLogo3D(containerSelector, modelPath) {

    if (!THREE) {
        // Carica le librerie da CDN
        await Promise.all([
            // loadScript('https://cdn.jsdelivr.net/npm/three@0.150.0/build/three.min.js'),
            // loadScript('https://cdn.jsdelivr.net/npm/three@0.150.0/examples/js/controls/OrbitControls.js'),
            loadScript('https://cdn.jsdelivr.net/npm/three@0.150.0/examples/js/loaders/GLTFLoader.js')
        ]);

        // Assegna le variabili globali dopo il caricamento
        // THREE = window.THREE;
        // OrbitControls = window.THREE.OrbitControls;
        GLTFLoader = window.THREE.GLTFLoader;
    }

    // Funzione helper per caricare script
    function loadScript(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    // Assicurati che il selettore del container sia fornito
    if (!containerSelector) {
        console.error('Selettore del container 3D non specificato');
        return;
    }

    // Cerca il container
    const container = document.querySelector(containerSelector);
    if (!container) {
        console.error(`Container non trovato: ${containerSelector}`);
        return;
    }

    // Crea l'indicatore di caricamento se non esiste
    let loadingIndicator = container.querySelector('.logo3d-loading');
    if (!loadingIndicator) {
        loadingIndicator = document.createElement('div');
        loadingIndicator.className = 'logo3d-loading';
        loadingIndicator.style.cssText = 'position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-family: Arial, sans-serif; color: #666;';
        loadingIndicator.textContent = 'Caricamento modello 3D in corso...';
        container.appendChild(loadingIndicator);
    }

    // Variabili per la scena Three.js
    let camera, scene, renderer, controls, animationFrameId;

    // Inizializza la scena
    init();

    function init() {
        // Crea la scena
        scene = new THREE.Scene();
        scene.background = new THREE.Color(0xffffff); // Sfondo bianco

        // Configura la camera
        camera = new THREE.PerspectiveCamera(45, container.clientWidth / container.clientHeight, 0.1, 1000);
        camera.position.set(0, 0, 5);

        // Aggiungi luci
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
        scene.add(ambientLight);

        const mainLight = new THREE.DirectionalLight(0xffffff, 0.8);
        mainLight.position.set(10, 10, 10);
        scene.add(mainLight);

        const fillLight = new THREE.DirectionalLight(0xffffff, 0.4);
        fillLight.position.set(-10, 5, -10);
        scene.add(fillLight);

        // Crea il renderer
        renderer = new THREE.WebGLRenderer({
            antialias: true,
            alpha: true
        });
        renderer.setSize(container.clientWidth, container.clientHeight);
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.outputEncoding = THREE.sRGBEncoding;
        renderer.toneMapping = THREE.ACESFilmicToneMapping;

        // Aggiungi il canvas al container
        container.appendChild(renderer.domElement);

        // Crea i controlli
        controls = new OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;
        controls.minDistance = 2;
        controls.maxDistance = 20;

        // Carica il modello GLB
        const loader = new GLTFLoader();

        loader.load(
            modelPath,
            function(gltf) {
                // Modello caricato con successo
                loadingIndicator.style.display = 'none';

                const model = gltf.scene;
                scene.add(model);

                // Centra e ridimensiona il modello
                const box = new THREE.Box3().setFromObject(model);
                const center = box.getCenter(new THREE.Vector3());
                model.position.sub(center);

                // Ridimensiona il modello se necessario
                const size = box.getSize(new THREE.Vector3());
                const maxDim = Math.max(size.x, size.y, size.z);
                if (maxDim > 4) {
                    const scale = 4 / maxDim;
                    model.scale.set(scale, scale, scale);
                }

                // Posiziona la camera in base alle dimensioni del modello
                const fov = camera.fov * (Math.PI / 180);
                const cameraDistance = Math.max(size.x, size.y) / (2 * Math.tan(fov / 2));
                camera.position.z = cameraDistance * 1.5;
                camera.updateProjectionMatrix();

                // Gestisci le animazioni
                if (gltf.animations && gltf.animations.length) {
                    const mixer = new THREE.AnimationMixer(model);
                    const action = mixer.clipAction(gltf.animations[0]);
                    action.play();

                    const clock = new THREE.Clock();

                    function animate() {
                        const delta = clock.getDelta();
                        mixer.update(delta);
                        controls.update();
                        renderer.render(scene, camera);
                        animationFrameId = requestAnimationFrame(animate);
                    }

                    animate();
                } else {
                    // Rendering senza animazioni
                    function render() {
                        controls.update();
                        renderer.render(scene, camera);
                        animationFrameId = requestAnimationFrame(render);
                    }

                    render();
                }
            },
            // Callback progresso
            function(xhr) {
                const percentComplete = xhr.loaded / xhr.total * 100;
                loadingIndicator.textContent = Math.round(percentComplete) + '% caricato';
            },
            // Callback errore
            function(error) {
                console.error('Errore nel caricamento del modello 3D:', error);
                loadingIndicator.textContent = 'Errore nel caricamento del modello';
            }
        );

        // Gestisci ridimensionamento finestra
        window.addEventListener('resize', onWindowResize);
    }

    function onWindowResize() {
        camera.aspect = container.clientWidth / container.clientHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(container.clientWidth, container.clientHeight);
    }

    // Restituisci una funzione di pulizia
    return function cleanup() {
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
        }

        window.removeEventListener('resize', onWindowResize);

        if (renderer) {
            renderer.dispose();
            if (renderer.domElement && renderer.domElement.parentNode) {
                renderer.domElement.parentNode.removeChild(renderer.domElement);
            }
        }

        // Rimuovi tutti i figli dalla scena
        while (scene.children.length > 0) {
            scene.remove(scene.children[0]);
        }
    };
}
