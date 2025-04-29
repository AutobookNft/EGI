<div class="matrix-container">
    <div id="tunnel3D-container"
         style="width: 100%; height: 400px; position: relative;"
         data-model-path="{{ asset('models/logo3D.glb') }}">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #666;">
            Caricamento in corso...
        </div>
    </div>
    <div class="absolute bottom-4 left-4 bg-black/50 text-white text-xs px-3 py-1.5 rounded-full backdrop-blur-sm border border-purple-500/30 z-10 font-medium">
        Ragno tunnell
    </div>
</div>
<!-- Utilizziamo un pacchetto "all-in-one" che include tutto ciò che serve -->
<script src="https://cdn.jsdelivr.net/npm/three@0.137.0/build/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.137.0/examples/js/controls/OrbitControls.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.137.0/examples/js/loaders/GLTFLoader.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('tunnel3D-container');
    const width = container.clientWidth;
    const height = container.clientHeight;

    // Setup della scena, camera e renderer
    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0x0b0b3b); // Sfondo blu scuro simile all'immagine

    const camera = new THREE.PerspectiveCamera(60, width/height, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(width, height);
    container.appendChild(renderer.domElement);

    // Rimuovi il loader
    const loaderMsg = container.querySelector('div');
    if (loaderMsg) loaderMsg.style.display = 'none';

    // Crea il tunnel di linee
    const tunnelRadius = 50;
    const tunnelDepth = 200;
    const verticalLines = 20;
    const horizontalLines = 20;

    // Gruppo per contenere tutte le linee
    const tunnelGroup = new THREE.Group();
    scene.add(tunnelGroup);

    // Materiale linee - arancione brillante come nell'immagine
    const lineMaterial = new THREE.LineBasicMaterial({
        color: 0xffa500,
        transparent: true,
        opacity: 0.8
    });

    // Crea linee verticali
    for (let i = 0; i <= verticalLines; i++) {
        const theta = (i / verticalLines) * Math.PI * 2;
        const geometry = new THREE.BufferGeometry();

        const points = [];
        for (let j = 0; j <= tunnelDepth; j += 2) {
        const z = j - tunnelDepth / 2;
        const scale = 1 - Math.abs(z) / (tunnelDepth / 1.5);
        const x = Math.cos(theta) * tunnelRadius * scale;
        const y = Math.sin(theta) * tunnelRadius * scale;
        points.push(new THREE.Vector3(x, y, z));
        }

        geometry.setFromPoints(points);
        const line = new THREE.Line(geometry, lineMaterial);
        tunnelGroup.add(line);
    }

    // Crea linee orizzontali (anelli)
    for (let j = 0; j <= tunnelDepth; j += tunnelDepth / horizontalLines) {
        const z = j - tunnelDepth / 2;
        const scale = 1 - Math.abs(z) / (tunnelDepth / 1.5);

        const ringGeometry = new THREE.BufferGeometry();
        const ringPoints = [];

        for (let i = 0; i <= verticalLines; i++) {
        const theta = (i / verticalLines) * Math.PI * 2;
        const x = Math.cos(theta) * tunnelRadius * scale;
        const y = Math.sin(theta) * tunnelRadius * scale;
        ringPoints.push(new THREE.Vector3(x, y, z));
        }

        ringGeometry.setFromPoints(ringPoints);
        const ring = new THREE.Line(ringGeometry, lineMaterial);
        tunnelGroup.add(ring);
    }

    // Posiziona la camera
    camera.position.z = 100;

    // Animazione
    function animate() {
        requestAnimationFrame(animate);

        // Muovi il tunnel verso la camera
        tunnelGroup.position.z += 0.8;
        if (tunnelGroup.position.z > tunnelDepth / 2) {
        tunnelGroup.position.z = 0;
        }

        renderer.render(scene, camera);
    }

    animate();

    // Gestione del resize
    window.addEventListener('resize', () => {
        const w = container.clientWidth;
        const h = container.clientHeight;
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
        renderer.setSize(w, h);
    });
});
</script>
