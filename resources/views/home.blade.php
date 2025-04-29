<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Faviicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    @vite([
        'resources/css/app.css',
        'vendor/ultra/ultra-upload-manager/resources/css/app.css',
    ])

    {{-- Icone di MaterialIcons --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css"/>

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
        /*
        ---------------------------------
        * stili per home
        ---------------------------------
        */

        /* Effetto hover per i bottoni */
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Stili per l'animazione */
        #hero-section {
            position: relative;
            overflow: hidden;
            /* Assicuriamo che l'altezza h-screen funzioni correttamente */
            height: 100vh; /* Tailwind h-screen usually translates to this */
            /* Aggiungiamo un background di fallback solido o un gradiente base qui se necessario */
            background-color: #064e3b; /* Colore solido di fallback */
        }
        #background-gradient {
            position: absolute;
            inset: 0;
            /* Possiamo mantenere questo gradiente, o spostarlo nel background-color della sezione */
            /* Se lo teniamo qui, assicuriamoci che sia lo strato più basso o abbia un z-index basso */
            background: linear-gradient(135deg, #052e16 0%, #064e3b 100%);
            z-index: -2; /* Mettiamo il gradiente sotto l'immagine e il canvas */
        }
        /* Nuovo div per l'immagine di sfondo opaca */
        #background-image-layer {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            /* <<< SOSTITUISCI QUESTA LINEA */
            background-image: url('/images/default/random_background/15.jpg'); /* <<<<< CON QUESTA */
            /* >>> FINE SOSTITUZIONE */
            background-size: cover;
            background-position: center;
            opacity: 0.35; /* <<< RICORDA DI CALIBRARE ANCORA QUESTA OPACITÀ SE NECESSARIO */
            z-index: 1;
            }
        #backgroundCanvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%; /* Assicuriamoci che copra l'intera area */
            height: 100%; /* Assicuriamoci che copra l'intera area */
            z-index: 0; /* Assicuriamoci che stia sopra l'immagine di sfondo */
            /* Il JS si occuperà di impostare width/height corrette sul canvas element */
        }
        .hero-content {
            position: relative; /* o absolute, purché abbia un z-index superiore */
            z-index: 10; /* Assicuriamoci che stia sopra il canvas */
            width: 100%; /* Fai in modo che il contenuto occupi lo spazio per centratura max-w-3xl */
            height: 100%; /* Fai in modo che il contenuto occupi lo spazio per centratura flex items-center */
            display: flex; /* Ripetuto da section, ma assicuriamoci che il contenuto interno sia centrato */
            align-items: center;
        }

    </style>

</head>

<body class="bg-gray-50 text-gray-800">

    <!-- Navbar -->
    <header class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <!-- Placeholder per il logo -->
            <div class="h-8 w-8 bg-green-600 rounded-full flex items-center justify-center text-white font-bold">F</div>
            <span class="font-semibold text-lg">Frangette</span>
        </div>
        <nav class="flex items-center gap-6">
            {{-- Bottone nella navbar - Aggiunta classe per selezione JS --}}
            {{-- <button id="open-upload-modal" type="button" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition btn-hover js-open-upload-modal">Crea EGI</button> --}}
            <button id="open-upload-modal" data-upload-type="egi" class="btn btn-primary">
                {{ __('uploadmanager::uploadmanager.create_egi') }}
            </button>
            <a href="upload/egi" class="hover:text-green-600 transition">Egi</a>
            <a href="collections" class="hover:text-green-600 transition">Collezioni</a>
            <a href="dashboard" class="hover:text-green-600 transition">Entra in Dashboard</a>
        </nav>
        </div>
    </header>

    <!-- Hero Section con animazione e strati di sfondo -->
    <section id="hero-section">
        <!-- 1. Gradiente di sfondo (strato più basso) -->
        <div id="background-gradient"></div>

        <!-- 2. Immagine di sfondo opaca (sopra gradiente, sotto canvas) -->
        <div id="background-image-layer"></div>

        <!-- 3. Canvas per l'animazione (sopra immagine, sotto contenuto) -->
        <canvas id="backgroundCanvas"></canvas>

        <!-- 4. Contenuto sovrapposto (strato più alto) -->
        <div class="hero-content">
        <div class="max-w-3xl mx-auto px-6 text-center text-white space-y-6">
            <h1 class="text-5xl font-extrabold">Scopri Arte Unica,<br/>Sostieni la Foresta Pluviale</h1>
            <div class="space-x-4">
            <a href="collections" class="inline-block px-6 py-3 bg-green-500 rounded-lg hover:bg-green-600 transition btn-hover">Esplora Collezioni</a>
            <a href="upload/egi" class="inline-block px-6 py-3 bg-white text-green-600 rounded-lg hover:bg-gray-100 transition btn-hover">Crea la tua Opera</a>
            </div>
        </div>
        </div>
    </section>

    <!-- Collezioni in Evidenza -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-2xl font-bold mb-6">Collezioni in Evidenza</h2>
        <div class="flex space-x-4 overflow-x-auto pb-4">
            <!-- Esempio di card -->
            <div class="min-w-[250px] bg-white rounded-2xl shadow-lg p-4 transition hover:shadow-xl">
            <!-- Placeholder per l'immagine -->
            <div class="w-full h-40 bg-gradient-to-r from-green-500 to-green-600 rounded-lg mb-4 flex items-center justify-center text-white font-bold">Natura</div>
            <h3 class="font-semibold">Collezione Verde</h3>
            <p class="text-sm text-gray-500">10 opere • 20% EPP</p>
            </div>

            <!-- Altra card -->
            <div class="min-w-[250px] bg-white rounded-2xl shadow-lg p-4 transition hover:shadow-xl">
            <div class="w-full h-40 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg mb-4 flex items-center justify-center text-white font-bold">Oceani</div>
            <h3 class="font-semibold">Collezione Blu</h3>
            <p class="text-sm text-gray-500">8 opere • 25% EPP</p>
            </div>

            <!-- Altra card -->
            <div class="min-w-[250px] bg-white rounded-2xl shadow-lg p-4 transition hover:shadow-xl">
            <div class="w-full h-40 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg mb-4 flex items-center justify-center text-white font-bold">Savana</div>
            <h3 class="font-semibold">Collezione Oro</h3>
            <p class="text-sm text-gray-500">12 opere • 15% EPP</p>
            </div>
        </div>
        </div>
    </section>

    <!-- Trending EGI -->
    <section class="py-16 bg-gray-100">
        <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-2xl font-bold mb-6">Trending EGI</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Card singola -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition hover:shadow-xl">
            <!-- Placeholder per l'immagine -->
            <div class="w-full h-48 bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold">Luce d'Oriente</div>
            <div class="p-4">
                <div class="flex justify-between items-center mb-2">
                <span class="font-semibold">"Luce d'Oriente"</span>
                <span class="text-xs bg-green-500 text-white px-2 py-1 rounded-full">1 di 1</span>
                </div>
                <p class="text-sm text-gray-600 mb-3">olio su tela • riciclo creativo</p>
                <div class="flex justify-between items-center">
                <span class="text-sm text-green-600">🌱 €20 donati</span>
                <a href="https://egiflorence.com/egi/1" class="text-green-600 hover:underline">Scopri</a>
                </div>
            </div>
            </div>

            <!-- Altra card -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition hover:shadow-xl">
            <div class="w-full h-48 bg-gradient-to-br from-green-500 to-teal-500 flex items-center justify-center text-white font-bold">Foresta Primordiale</div>
            <div class="p-4">
                <div class="flex justify-between items-center mb-2">
                <span class="font-semibold">"Foresta Primordiale"</span>
                <span class="text-xs bg-green-500 text-white px-2 py-1 rounded-full">2 di 5</span>
                </div>
                <p class="text-sm text-gray-600 mb-3">digitale • impatto zero</p>
                <div class="flex justify-between items-center">
                <span class="text-sm text-green-600">🌱 €30 donati</span>
                <a href="https://egiflorence.com/egi/2" class="text-green-600 hover:underline">Scopri</a>
                </div>
            </div>
            </div>

            <!-- Altra card -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition hover:shadow-xl">
            <div class="w-full h-48 bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-white font-bold">Oceano Profondo</div>
            <div class="p-4">
                <div class="flex justify-between items-center mb-2">
                <span class="font-semibold">"Oceano Profondo"</span>
                <span class="text-xs bg-green-500 text-white px-2 py-1 rounded-full">3 di 10</span>
                </div>
                <p class="text-sm text-gray-600 mb-3">acquerello • salvaguardia marina</p>
                <div class="flex justify-between items-center">
                <span class="text-sm text-green-600">🌱 €15 donati</span>
                <a href="https://egiflorence.com/egi/3" class="text-green-600 hover:underline">Scopri</a>
                </div>
            </div>
            </div>
        </div>
        </div>
    </section>

    <!-- Il Tuo Impatto -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-2xl font-bold mb-6">Il Tuo Impatto</h2>
        <div class="bg-white rounded-2xl shadow-lg p-6 flex flex-col sm:flex-row items-center justify-between">
            <div class="text-center sm:text-left space-y-4">
            <p class="text-4xl font-bold">0 kg CO₂</p>
            <p class="text-gray-600">Compensati finora (anonimo)</p>
            </div>
            <div class="text-center sm:text-left space-y-4">
            <p class="text-4xl font-bold">€0</p>
            <p class="text-gray-600">Donati agli EPP</p>
            </div>
            <a href="https://frangette.com/register" class="mt-4 sm:mt-0 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition btn-hover">Registrati per tracciare il tuo impatto</a>
        </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t py-8">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center">
        <p class="text-gray-600">© 2025 Frangette</p>
        <div class="flex items-center space-x-4 mt-4 md:mt-0">
            <span>KG CO₂ compensati: <strong>123.456</strong></span>
            <!-- Placeholder per il badge Algorand -->
            <div class="bg-green-600 text-white text-xs px-3 py-1 rounded-full">Algorand Carbon-Negative</div>
        </div>
        </div>
    </footer>

    <div id="upload-modal" class="modal hidden" aria-hidden="true">
        <div class="relative max-w-4xl w-full mx-4">
            <button id="close-upload-modal" class="absolute top-4 right-4 text-white text-2xl" aria-label="Close modal">
            ×
            </button>
            @include('egimodule::partials.uploading_form_content')
        </div>
    </div>

    <script>
        // --- Configurazione Animazione (Parametri Migliorati e Strato Immagine) ---
        const canvas = document.getElementById('backgroundCanvas');
        const ctx = canvas.getContext('2d');
        const heroSection = document.getElementById('hero-section'); // Usiamo questo per le dimensioni

        let width, height;
        let particles = [];
        let growthElements = [];
        let sparkles = [];
        let leaves = []; // Le foglie fluttuanti sono un'ottima aggiunta organica!

        // Parametri Calibrati per Maggiore Visibilità e Dinamismo
        const numParticles = 150; // Aumentato per maggiore densità
        const particleSize = 3; // Rende i punti ben visibili
        const particleSpeed = 0.2; // Velocità calma
        // Colore più luminoso e con alta opacità per risaltare
        const particleColor = 'rgba(220, 255, 220, 0.95)';

        const connectionLineDistance = 150; // Raggio di connessione più ampio
        // Colore luminoso ma più trasparente delle particelle
        const connectionLineColor = 'rgba(180, 255, 180, 0.6)';
        const connectionLineWidth = 1.5; // Spessore linee aumentato

        const growthChancePerFrame = 0.01; // Probabilità di crescita
        const growthDuration = 80; // Durata animazione crescita (in frames)
        const maxGrowthSize = 35; // Dimensione massima
        // Colore vibrante, opacità piena durante il picco
        const growthColor = 'rgba(100, 255, 120, 0.85)'; // Opacità base alta, poi modulata dalla curva

        const sparkleChance = 0.003; // Probabilità di brillantin
        const sparkleColor = 'rgba(255, 255, 200, 0.9)'; // Giallo luminoso
        const sparkleSize = 4; // Dimensione brillantin
        const sparkleDuration = 40; // Durata brillantin

        const numLeaves = 12; // Numero di foglie fluttuanti
        const leafSizeMin = 15; // Dimensione minima foglie
        const leafSizeMax = 35; // Dimensione massima foglie
        const leafSpeedMin = 0.5; // Velocità minima foglie
        const leafSpeedMax = 1.2; // Velocità massima foglie
        const leafRotationSpeed = 0.008; // Velocità rotazione foglie
        // Colori foglie (possono essere basati sulla palette, magari più scuri della griglia)
        const leafColors = [
            'rgba(50, 160, 50, 0.6)', // Verde scuro trasparente
            'rgba(70, 180, 70, 0.6)',
            'rgba(30, 130, 30, 0.6)',
            'rgba(100, 200, 100, 0.6)',
            'rgba(40, 140, 40, 0.6)'
        ];

        // --- Funzioni di inizializzazione e gestione resize ---
        function init() {
        resizeCanvas();
        createParticles(numParticles);
        createLeaves(numLeaves);
        animate();
        }

        // Funzione per ridimensionare il canvas e mantenere la responsività
        function resizeCanvas() {
            // Ottiene le dimensioni esatte della sezione Hero
            const rect = heroSection.getBoundingClientRect();
            width = rect.width;
            height = rect.height;
            canvas.width = width;
            canvas.height = height;

            // Nota: Questo resetta le particelle e le foglie ogni volta che si ridimensiona.
            // Per un'esperienza utente più fluida, si potrebbe ricalcolare solo le posizioni
            // e le velocità esistenti. Ma per ora, questo approccio è più semplice e sicuro.
            particles = [];
            leaves = [];
            growthElements = []; // Rimuovi anche gli elementi di crescita attivi
            sparkles = []; // Rimuovi anche i brillantin attivi
            createParticles(numParticles);
            createLeaves(numLeaves);
        }

        function createParticles(n) {
        for (let i = 0; i < n; i++) {
            particles.push(new Particle());
        }
        }

        function createLeaves(n) {
        for (let i = 0; i < n; i++) {
            leaves.push(new Leaf());
        }
        }

        // --- Classi per le entità animate ---
        // La classe Particle è già definita e aggiornata nel codice precedente
        class Particle {
        constructor() { this.reset(); this.pulsate = Math.random() * Math.PI * 2; }
        reset() {
            this.x = Math.random() * width; this.y = Math.random() * height;
            this.vx = (Math.random() - 0.5) * particleSpeed * 2; this.vy = (Math.random() - 0.5) * particleSpeed * 2;
            this.size = particleSize; this.color = particleColor;
        }
        update() {
            this.x += this.vx; this.y += this.vy;
            // Fa rimbalzare sui bordi
            if (this.x < 0 || this.x > width) this.vx *= -1;
            if (this.y < 0 || this.y > height) this.vy *= -1;
            this.pulsate += 0.05;
            this.drawSize = this.size * (1 + 0.3 * Math.sin(this.pulsate)); // Pulsazione visiva
        }
        draw() {
            ctx.fillStyle = this.color; ctx.beginPath();
            ctx.arc(this.x, this.y, this.drawSize, 0, Math.PI * 2); ctx.fill();
        }
        }

        // La classe Leaf è già definita e aggiornata nel codice precedente
        class Leaf {
        constructor() {
            this.reset();
            this.phase = Math.random() * Math.PI * 2; // Per movimento ondulato
            this.oscSpd = 0.02 + Math.random() * 0.015; // Velocità oscillazione
            this.wobAmp = 0.1 + Math.random() * 0.2; // Ampiezza oscillazione
            this.color = leafColors[Math.floor(Math.random() * leafColors.length)];
        }

        reset() {
            // Fa entrare le foglie casualmente da uno dei 4 bordi
            const entry = Math.floor(Math.random() * 4);
            const edgeBuffer = 80; // Distanza dal bordo prima del reset
            switch (entry) {
            case 0: // Sinistra
                this.x = -edgeBuffer; this.y = Math.random() * height;
                this.vx = Math.random() * (leafSpeedMax - leafSpeedMin) + leafSpeedMin; this.vy = (Math.random() - 0.5) * 2 * leafSpeedMax * 0.5;
                break;
            case 1: // Destra
                this.x = width + edgeBuffer; this.y = Math.random() * height;
                this.vx = -Math.random() * (leafSpeedMax - leafSpeedMin) - leafSpeedMin; this.vy = (Math.random() - 0.5) * 2 * leafSpeedMax * 0.5;
                break;
            case 2: // Alto
                this.x = Math.random() * width; this.y = -edgeBuffer;
                this.vx = (Math.random() - 0.5) * 2 * leafSpeedMax * 0.5; this.vy = Math.random() * (leafSpeedMax - leafSpeedMin) + leafSpeedMin;
                break;
            default: // Basso
                this.x = Math.random() * width; this.y = height + edgeBuffer;
                this.vx = (Math.random() - 0.5) * 2 * leafSpeedMax * 0.5; this.vy = -Math.random() * (leafSpeedMax - leafSpeedMin) - leafSpeedMin;
            }
            this.size = Math.random() * (leafSizeMax - leafSizeMin) + leafSizeMin;
            this.rotation = Math.random() * Math.PI * 2;
            this.rSpeed = (Math.random() - 0.5) * leafRotationSpeed * 2;
        }

        update() {
            // Movimento base + oscillazione
            this.x += this.vx + Math.sin(this.phase) * this.wobAmp;
            this.y += this.vy + Math.cos(this.phase) * this.wobAmp;
            this.phase += this.oscSpd;
            this.rotation += this.rSpeed;

            // Reset se esce troppo
            const m = this.size * 2; // Buffer di uscita
            if (this.x < -m || this.x > width + m || this.y < -m || this.y > height + m) {
            this.reset();
            }
        }

        draw() {
            ctx.save();
            ctx.translate(this.x, this.y);
            ctx.rotate(this.rotation);
            ctx.fillStyle = this.color;
            const hs = this.size / 2;
            // Disegna una forma a foglia stilizzata o ellisse
            ctx.beginPath();
            // Esempio di foglia più stilizzata: una forma base a punta
            ctx.moveTo(0, -hs * 1.5); // Punta superiore
            ctx.quadraticCurveTo(hs * 0.8, -hs * 0.5, hs * 0.8, 0); // Curva lato destro superiore
            ctx.quadraticCurveTo(hs * 0.8, hs * 0.5, 0, hs * 1.5); // Curva lato destro inferiore (punta inferiore)
            ctx.quadraticCurveTo(-hs * 0.8, hs * 0.5, -hs * 0.8, 0); // Curva lato sinistro inferiore
            ctx.quadraticCurveTo(-hs * 0.8, -hs * 0.5, 0, -hs * 1.5); // Curva lato sinistro superiore (torna alla punta)
            ctx.fill();
            // Alternativa più semplice: ellisse (quella usata prima)
            // ctx.ellipse(0, 0, hs * 0.7, hs, 0, 0, Math.PI * 2); ctx.fill();
            ctx.restore();
        }
        }


        // --- Animazione principale ---
        function animate() {
        // Cancella solo l'area del canvas
        ctx.clearRect(0, 0, width, height);

        // Disegna connessioni tra particelle
        ctx.lineWidth = connectionLineWidth; // Settiamo lo spessore qui

        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
            const a = particles[i], b = particles[j];
            const dx = a.x - b.x, dy = a.y - b.y;
            const dist = Math.hypot(dx, dy);
            if (dist < connectionLineDistance) {
                // Calcola opacità basata su distanza per le linee
                const distanceOpacityFactor = 1 - (dist / connectionLineDistance);
                const baseColorParts = connectionLineColor.match(/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/);
                if (baseColorParts && baseColorParts.length === 5) {
                    const baseAlpha = parseFloat(baseColorParts[4]);
                    const currentAlpha = baseAlpha * distanceOpacityFactor * 1.5; // Moltiplico per 1.5 per renderle un po' più visibili le linee corte
                    ctx.strokeStyle = `rgba(${baseColorParts[1]},${baseColorParts[2]},${baseColorParts[3]},${Math.min(currentAlpha, 1).toFixed(2)})`; // Clamp a 1.0 opacità max
                } else {
                    ctx.strokeStyle = connectionLineColor; // Fallback
                }

                ctx.beginPath();
                ctx.moveTo(a.x, a.y);
                ctx.lineTo(b.x, b.y);
                ctx.stroke();
            }
            }
        }


        // Aggiorna e disegna particelle
        particles.forEach(p => {
            p.update();
            p.draw();
        });


        // Aggiorna e disegna foglie
        leaves.forEach(l => {
            l.update();
            l.draw();
        });


        // Elementi di crescita casuali (ora con tipologie diverse!)
        if (Math.random() < growthChancePerFrame) {
            growthElements.push({
            x: Math.random() * width,
            y: Math.random() * height,
            frame: 0,
            type: Math.floor(Math.random() * 3) // Scegli tra 3 tipi di animazione crescita
            });
        }

        // Aggiorna e disegna elementi di crescita
        growthElements = growthElements.filter(g => {
            g.frame++;
            if (g.frame > growthDuration) return false;

            const progress = g.frame / growthDuration;
            // Curva per la crescita (si espande e si ritira)
            const growCurve = Math.sin(progress * Math.PI);
            const currentSize = maxGrowthSize * growCurve;
            // Opacità che segue la curva di crescita, ma con una base minima
            const opacity = growCurve * 0.8 + 0.1; // Parte da 0.1, arriva a 0.9, torna a 0.1

            ctx.fillStyle = `rgba(100, 255, 120, ${opacity.toFixed(2)})`;

            // Disegno basato sul tipo di elemento di crescita
            ctx.beginPath();
            switch (g.type) {
            case 0: // Cerchio pulsante
                ctx.arc(g.x, g.y, currentSize / 2, 0, Math.PI * 2);
                break;
            case 1: // Forma a foglia/goccia stilizzata
                ctx.ellipse(g.x, g.y, currentSize / 3, currentSize / 1.5, Math.PI / 4, 0, Math.PI * 2);
                break;
            case 2: // Forma a stella/cristallo (per l'energia digitale)
                for (let i = 0; i < 10; i++) { // Stella a 5 punte (10 vertici)
                const angle = (i / 10) * Math.PI * 2;
                const radius = currentSize / 2 * (i % 2 === 0 ? 1 : 0.6); // Raggio alternato per punte
                const x = g.x + Math.cos(angle - Math.PI / 2) * radius; // Ruota per avere una punta in alto
                const y = g.y + Math.sin(angle - Math.PI / 2) * radius;
                if (i === 0) ctx.moveTo(x, y);
                else ctx.lineTo(x, y);
                }
                ctx.closePath();
                break;
            }
            ctx.fill();

            return true;
        });

        // Brillantini occasionali (simboleggiano l'Algo o momenti di "attivazione")
        if (Math.random() < sparkleChance) {
            sparkles.push({
            x: Math.random() * width,
            y: Math.random() * height,
            frame: 0
            });
        }

        sparkles = sparkles.filter(s => {
            s.frame++;
            if (s.frame > sparkleDuration) return false;

            const t = s.frame / sparkleDuration; // Progresso 0 -> 1
            const size = sparkleSize * (1 - t * 0.8); // Si rimpicciolisce, ma non scompare del tutto
            const alpha = Math.sin(t * Math.PI); // Curva di opacità: appare, picco, scompare

            ctx.fillStyle = `rgba(255, 255, 200, ${alpha.toFixed(2)})`; // Giallo luminoso
            ctx.beginPath();
            ctx.arc(s.x, s.y, size, 0, Math.PI * 2);
            ctx.fill();
            return true;
        });


        requestAnimationFrame(animate);
        }

        // Gestione ridimensionamento finestra
        window.addEventListener('resize', () => {
        // Piccolo ritardo per evitare lag eccessivo durante un ridimensionamento rapido
        setTimeout(resizeCanvas, 250);
        });


        // Avvia tutto
        init();
    </script>

    @vite([
        'resources/js/app.js',
        'vendor/ultra/ultra-upload-manager/resources/ts/core/file_upload_manager.ts'
    ])

</body>
</html>
