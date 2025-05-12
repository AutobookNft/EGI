<script>

    window.allowedExtensions = @json(config('AllowedFileType.collection.allowed_extensions', []));
    window.allowedMimeTypes = @json(config('AllowedFileType.collection.allowed_mime_types', []));
    window.maxSize = {{ config('AllowedFileType.collection.max_size', 10 * 1024 * 1024) }};

    // Caricamento configurazione
    fetch('{{ route("global.config") }}', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, {{-- Corretto a 'csrf-token' --}}
        }
    })
    .then(response => response.json())
    .then(config => {
        Object.assign(window, config);
        document.dispatchEvent(new Event('configLoaded'));
    })
    .catch(error => console.error('Error loading configuration:', error))

    // --- Configurazione Animazione ---
    const canvas = document.getElementById('backgroundCanvas');
    const ctx = canvas.getContext('2d');
    const heroSection = document.getElementById('hero-section');

    let width, height;
    let particles = [];
    let growthElements = [];
    let sparkles = [];
    let leaves = [];

    const numParticles = 150;
    const particleSize = 3;
    const particleSpeed = 0.2;
    const particleColor = 'rgba(220, 255, 220, 0.95)';

    const connectionLineDistance = 150;
    const connectionLineColor = 'rgba(180, 255, 180, 0.6)';
    const connectionLineWidth = 1.5;

    const growthChancePerFrame = 0.01;
    const growthDuration = 80;
    const maxGrowthSize = 35;
    const growthColor = 'rgba(100, 255, 120, 0.85)';

    const sparkleChance = 0.003;
    const sparkleColor = 'rgba(255, 255, 200, 0.9)';
    const sparkleSize = 4;
    const sparkleDuration = 40;

    const numLeaves = 12;
    const leafSizeMin = 15;
    const leafSizeMax = 35;
    const leafSpeedMin = 0.5;
    const leafSpeedMax = 1.2;
    const leafRotationSpeed = 0.008;
    const leafColors = [
        'rgba(50, 160, 50, 0.6)',
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

    function resizeCanvas() {
        const rect = heroSection.getBoundingClientRect();
        width = rect.width;
        height = rect.height;
        canvas.width = width;
        canvas.height = height;

        particles.forEach(p => {
            p.x = Math.min(p.x, width);
            p.y = Math.min(p.y, height);
        });
         leaves.forEach(l => {
            l.x = Math.min(l.x, width + l.size);
            l.y = Math.min(l.y, height + l.size);
        });
    }

    function createParticles(n) {
        if (particles.length >= n) return;
        const particlesToAdd = n - particles.length;
        for (let i = 0; i < particlesToAdd; i++) {
            particles.push(new Particle());
        }
    }

    function createLeaves(n) {
         if (leaves.length >= n) return;
         const leavesToAdd = n - leaves.length;
        for (let i = 0; i < leavesToAdd; i++) {
            leaves.push(new Leaf());
        }
    }

    // --- Classi per le entità animate ---
    class Particle {
    constructor() { this.reset(); this.pulsate = Math.random() * Math.PI * 2; }
    reset() {
        this.x = Math.random() * width; this.y = Math.random() * height;
        this.vx = (Math.random() - 0.5) * particleSpeed * 2; this.vy = (Math.random() - 0.5) * particleSpeed * 2;
        this.size = particleSize; this.color = particleColor;
    }
    update() {
        this.x += this.vx; this.y += this.vy;
        if (this.x < 0 || this.x > width) this.vx *= -1;
        if (this.y < 0 || this.y > height) this.vy *= -1;
        this.pulsate += 0.05;
        this.drawSize = this.size * (1 + 0.3 * Math.sin(this.pulsate));
    }
    draw() {
        ctx.fillStyle = this.color; ctx.beginPath();
        ctx.arc(this.x, this.y, this.drawSize, 0, Math.PI * 2); ctx.fill();
    }
    }

    class Leaf {
    constructor() {
        this.reset();
        this.phase = Math.random() * Math.PI * 2;
        this.oscSpd = 0.02 + Math.random() * 0.015;
        this.wobAmp = 0.1 + Math.random() * 0.2;
        this.color = leafColors[Math.floor(Math.random() * leafColors.length)];
    }

    reset() {
        const entry = Math.floor(Math.random() * 4);
        const edgeBuffer = 80;
        switch (entry) {
        case 0:
            this.x = -edgeBuffer; this.y = Math.random() * height;
            this.vx = Math.random() * (leafSpeedMax - leafSpeedMin) + leafSpeedMin; this.vy = (Math.random() - 0.5) * 2 * leafSpeedMax * 0.5;
            break;
        case 1:
            this.x = width + edgeBuffer; this.y = Math.random() * height;
            this.vx = -Math.random() * (leafSpeedMax - leafSpeedMin) - leafSpeedMin; this.vy = (Math.random() - 0.5) * 2 * leafSpeedMax * 0.5;
            break;
        case 2:
            this.x = Math.random() * width; this.y = -edgeBuffer;
            this.vx = (Math.random() - 0.5) * 2 * leafSpeedMax * 0.5; this.vy = Math.random() * (leafSpeedMax - leafSpeedMin) + leafSpeedMin;
            break;
        default:
            this.x = Math.random() * width; this.y = height + edgeBuffer;
            this.vx = (Math.random() - 0.5) * 2 * leafSpeedMax * 0.5; this.vy = -Math.random() * (leafSpeedMax - leafSpeedMin) - leafSpeedMin;
        }
        this.size = Math.random() * (leafSizeMax - leafSizeMin) + leafSizeMin;
        this.rotation = Math.random() * Math.PI * 2;
        this.rSpeed = (Math.random() - 0.5) * leafRotationSpeed * 2;
    }

    update() {
        this.x += this.vx + Math.sin(this.phase) * this.wobAmp;
        this.y += this.vy + Math.cos(this.phase) * this.wobAmp;
        this.phase += this.oscSpd;
        this.rotation += this.rSpeed;

        const m = this.size * 2;
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
        ctx.beginPath();
        ctx.moveTo(0, -hs * 1.5);
        ctx.quadraticCurveTo(hs * 0.8, -hs * 0.5, hs * 0.8, 0);
        ctx.quadraticCurveTo(hs * 0.8, hs * 0.5, 0, hs * 1.5);
        ctx.quadraticCurveTo(-hs * 0.8, hs * 0.5, -hs * 0.8, 0);
        ctx.quadraticCurveTo(-hs * 0.8, -hs * 0.5, 0, -hs * 1.5);
        ctx.fill();
        ctx.restore();
    }
    }

    // --- Animazione principale ---
    function animate() {
        ctx.clearRect(0, 0, width, height);

        ctx.lineWidth = connectionLineWidth;

        for (let i = 0; i < particles.length; i++) {
            for (let j = i + 1; j < particles.length; j++) {
            const a = particles[i], b = particles[j];
            const dx = a.x - b.x, dy = a.y - b.y;
            const dist = Math.hypot(dx, dy);
            if (dist < connectionLineDistance) {
                const distanceOpacityFactor = 1 - (dist / connectionLineDistance);
                const baseColorParts = connectionLineColor.match(/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/);
                if (baseColorParts && baseColorParts.length === 5) {
                    const baseAlpha = parseFloat(baseColorParts[4]);
                    const currentAlpha = baseAlpha * distanceOpacityFactor * 1.5;
                    ctx.strokeStyle = `rgba(${baseColorParts[1]},${baseColorParts[2]},${baseColorParts[3]},${Math.min(currentAlpha, 1).toFixed(2)})`;
                } else {
                    ctx.strokeStyle = connectionLineColor;
                }

                ctx.beginPath();
                ctx.moveTo(a.x, a.y);
                ctx.lineTo(b.x, b.y);
                ctx.stroke();
            }
            }
        }

        particles.forEach(p => {
            p.update();
            p.draw();
        });

        leaves.forEach(l => {
            l.update();
            l.draw();
        });

        if (Math.random() < growthChancePerFrame) {
            growthElements.push({
            x: Math.random() * width,
            y: Math.random() * height,
            frame: 0,
            type: Math.floor(Math.random() * 3)
            });
        }

        growthElements = growthElements.filter(g => {
            g.frame++;
            if (g.frame > growthDuration) return false;

            const progress = g.frame / growthDuration;
            const growCurve = Math.sin(progress * Math.PI);
            const currentSize = maxGrowthSize * growCurve;
            const opacity = growCurve * 0.8 + 0.1;

            ctx.fillStyle = `rgba(100, 255, 120, ${opacity.toFixed(2)})`;

            ctx.beginPath();
            switch (g.type) {
            case 0:
                ctx.arc(g.x, g.y, currentSize / 2, 0, Math.PI * 2);
                break;
            case 1:
                ctx.ellipse(g.x, g.y, currentSize / 3, currentSize / 1.5, Math.PI / 4, 0, Math.PI * 2);
                break;
            case 2:
                for (let i = 0; i < 10; i++) {
                const angle = (i / 10) * Math.PI * 2;
                const radius = currentSize / 2 * (i % 2 === 0 ? 1 : 0.6);
                const x = g.x + Math.cos(angle - Math.PI / 2) * radius;
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

            const t = s.frame / sparkleDuration;
            const size = sparkleSize * (1 - t * 0.8);
            const alpha = Math.sin(t * Math.PI);

            ctx.fillStyle = `rgba(255, 255, 200, ${alpha.toFixed(2)})`;
            ctx.beginPath();
            ctx.arc(s.x, s.y, size, 0, Math.PI * 2);
            ctx.fill();
            return true;
        });

        requestAnimationFrame(animate);
        }

        window.addEventListener('resize', () => {
        setTimeout(resizeCanvas, 250);
        });

        init();
</script>

<!-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> -->


