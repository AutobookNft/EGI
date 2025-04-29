<div class="matrix-container">
    <div id="logo3D-container"
         style="width: 100%; height: 400px; position: relative;"
         data-model-path="{{ asset('models/logo3D.glb') }}">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #666;">
            Caricamento in corso...
        </div>
    </div>
    <div class="absolute bottom-4 left-4 bg-black/50 text-white text-xs px-3 py-1.5 rounded-full backdrop-blur-sm border border-purple-500/30 z-10 font-medium">
        Robot Logo
    </div>

<!-- Utilizziamo un pacchetto "all-in-one" che include tutto ciò che serve -->
<script src="https://cdn.jsdelivr.net/npm/three@0.137.0/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.137.0/examples/js/controls/OrbitControls.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.137.0/examples/js/loaders/GLTFLoader.min.js"></script>

<script>
// Codice molto semplificato per minimizzare gli errori
document.addEventListener('DOMContentLoaded', function() {
    // Ottieni riferimenti
    const container = document.getElementById('logo3D-container');
    if (!container) return;

    const modelPath = container.getAttribute('data-model-path');
    const loadingEl = container.querySelector('div');

    try {
        // Setup base
        const scene = new THREE.Scene();

        // MODIFICA 1: Cambia il colore dello sfondo qui
        // Puoi usare qualsiasi codice colore esadecimale
        scene.background = new THREE.Color(0x121229); // Grigio scuro

        const camera = new THREE.PerspectiveCamera(45, container.clientWidth / container.clientHeight, 0.1, 1000);
        // Posiziona la camera più lontano per vedere il modello ingrandito
        camera.position.set(0, 0, 20);

        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(container.clientWidth, container.clientHeight);
        container.appendChild(renderer.domElement);

        // Luci
        const light = new THREE.AmbientLight(0xffffff);
        scene.add(light);

        // Aggiungiamo una luce direzionale per migliorare la visibilità
        const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
        directionalLight.position.set(1, 1, 1);
        scene.add(directionalLight);

        // Controlli
        const controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;
        controls.maxDistance = 50;

        // Variabile per memorizzare il riferimento al modello
        let modelObject = null;

        // Variabile per controllare la rotazione automatica
        let autoRotate = true;
        // Velocità di rotazione (in radianti per frame)
        const rotationSpeed = 0.005;

        // Carica modello
        const loader = new THREE.GLTFLoader();
        loader.load(
            modelPath,
            function(gltf) {
                loadingEl.style.display = 'none';
                scene.add(gltf.scene);

                // Salva il riferimento al modello
                modelObject = gltf.scene;

                // Centra il modello
                const box = new THREE.Box3().setFromObject(gltf.scene);
                const center = box.getCenter(new THREE.Vector3());
                gltf.scene.position.sub(center);

                // Calcola le dimensioni
                const size = box.getSize(new THREE.Vector3());
                const maxDim = Math.max(size.x, size.y, size.z);

                // Invece di ridurre il modello se è troppo grande,
                // lo ingrandiamo sempre di 4 volte rispetto alla scala ottimale
                // Prima calcoliamo la scala ottimale
                const optimalScale = 4 / maxDim;
                // Poi moltiplichiamo per 4 per renderlo 4 volte più grande
                const finalScale = optimalScale * 4;
                gltf.scene.scale.set(finalScale, finalScale, finalScale);

                // Aggiustiamo la posizione della camera in base alle nuove dimensioni
                const scaledSize = size.clone().multiplyScalar(finalScale);
                const scaledMaxDim = Math.max(scaledSize.x, scaledSize.y, scaledSize.z);

                // Calcoliamo la distanza ottimale della camera
                const fov = camera.fov * (Math.PI / 180);
                const cameraDistance = scaledMaxDim / (2 * Math.tan(fov / 2));

                // Aggiorniamo la posizione della camera
                camera.position.z = cameraDistance * 1.2; // Un po' più lontano per vedere tutto
                camera.updateProjectionMatrix();

                // MODIFICA 2: Aggiungiamo possibilità di attivare/disattivare rotazione cliccando sul modello
                renderer.domElement.addEventListener('click', function() {
                    autoRotate = !autoRotate;
                });

                // Animazione
                function animate() {
                    requestAnimationFrame(animate);

                    // MODIFICA 3: Rotazione automatica del modello
                    if (modelObject && autoRotate) {
                        modelObject.rotation.y += rotationSpeed;
                    }

                    controls.update();
                    renderer.render(scene, camera);
                }
                animate();
            },
            function(xhr) {
                // Progress
                if (xhr.lengthComputable) {
                    loadingEl.textContent = Math.round((xhr.loaded / xhr.total) * 100) + '% caricato';
                }
            },
            function(error) {
                // Error
                loadingEl.textContent = 'Errore nel caricamento del modello';
                console.error(error);
            }
        );

        // Ridimensionamento
        window.addEventListener('resize', function() {
            camera.aspect = container.clientWidth / container.clientHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(container.clientWidth, container.clientHeight);
        });
    } catch (error) {
        console.error('Errore:', error);
        loadingEl.textContent = 'Errore di inizializzazione: ' + error.message;
    }
});
</script></div>
