<div class="matrix-container">
    <canvas id="matrix-canvas" class="w-full h-full"></canvas>
    <!-- Etichetta informativa -->
    <div class="absolute bottom-4 left-4 bg-black/50 text-white text-xs px-3 py-1.5 rounded-full backdrop-blur-sm border border-purple-500/30 z-10 font-medium">
        Matrix Code
    </div>
</div>
<!-- Etichetta informativa -->
<div class="absolute bottom-4 left-4 bg-black/50 text-white text-xs px-3 py-1.5 rounded-full backdrop-blur-sm border border-purple-500/30 z-10 font-medium">
    Logo Frangette
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
