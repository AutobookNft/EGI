// Funzione per animare i contatori
function animateCounters() {
    const counters = document.querySelectorAll('[data-counter]');

    counters.forEach(counter => {
        // Controlla se il contatore è già stato animato
        if (counter.dataset.animated === 'true') {
            return;
        }
        counter.dataset.animated = 'true'; // Marca come animato

        const target = parseFloat(counter.dataset.counter); // Usa parseFloat per numeri decimali
        const duration = 2000;
        const increment = target / (duration / 16); // 16ms per frame (circa 60fps)
        let current = 0;

        const updateCounter = () => {
            current += increment;
            if (current < target) {
                // Formatta il numero con la virgola come separatore delle migliaia
                // e mantiene i decimali se target è un numero decimale
                counter.textContent = Number.isInteger(target)
                                    ? Math.floor(current).toLocaleString()
                                    : current.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                requestAnimationFrame(updateCounter);
            } else {
                // Assicura che il valore finale sia esattamente target e formattato correttamente
                counter.textContent = Number.isInteger(target)
                                    ? target.toLocaleString()
                                    : target.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            }
        };
        updateCounter();
    });
}

// Funzione per gestire lo stile dell'header allo scroll
function toggleHeaderStyle() {
    const header = document.querySelector('header'); // Assicurati che esista un tag <header>
    if (!header) return;

    const threshold = 100;
    if (window.scrollY > threshold) {
        header.classList.remove('transparent');
        header.classList.add('sticky');
    } else {
        header.classList.add('transparent');
        header.classList.remove('sticky');
    }
}

// Funzione per inizializzare VanillaTilt
function initializeVanillaTilt() {
    if (typeof VanillaTilt !== 'undefined') {
        VanillaTilt.init(document.querySelectorAll(".collection-card-nft"), {
            max: 15,
            speed: 400,
            glare: true,
            "max-glare": 0.2,
        });
    } else {
        console.warn('VanillaTilt is not loaded. Tilt effect will not be applied.');
    }
}

// Funzione per l'effetto di transizione della pagina
function pageTransitionEffects() {
    document.body.classList.add('page-transition');

    setTimeout(function() {
        document.body.classList.add('page-loaded');
    }, 100);

    document.querySelectorAll('a[href^="/"]').forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.hostname === window.location.hostname) {
                e.preventDefault();
                document.body.classList.remove('page-loaded');
                setTimeout(() => {
                    window.location.href = this.href;
                }, 400); // Durata dell'animazione di uscita
            }
        });
    });
}

// Funzione per l'animazione del contatore di impatto in tempo reale
function impactRealtimeCounterAnimation() {
    const impactCounter = document.getElementById('impact-realtime-counter');
    if (impactCounter) {
        let baseText = impactCounter.textContent; // Leggi il valore iniziale dal DOM
        // Estrai solo la parte numerica, rimuovendo separatori di migliaia e convertendo la virgola in punto
        let base = parseFloat(baseText.replace(/\./g, '').replace(',', '.'));

        if (isNaN(base)) { // Fallback se il parsing fallisce
            console.warn("Could not parse initial value for impact-realtime-counter. Defaulting to 89413.24");
            base = 89413.24;
        }


        setInterval(() => {
            base += Math.random() * 0.05;
            impactCounter.textContent = base.toLocaleString(undefined, {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }, 3000);
    }
}


// Esecuzione degli script quando il DOM è pronto
document.addEventListener('DOMContentLoaded', function() {
    // IntersectionObserver per animare i contatori quando visibili
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                // observer.unobserve(entry.target); // Disconnetti solo questo elemento, non l'intero observer
                                                 // Oppure usa un flag sull'elemento per non rianimarlo
            }
        });
    }, { threshold: 0.1 }); // Trigger quando il 10% dell'elemento è visibile

    const statsSection = document.querySelector('.nft-stats-section');
    if (statsSection) {
        observer.observe(statsSection);
    } else {
         // Se .nft-stats-section non c'è, prova ad animare subito i contatori
         // (potrebbero essere in un altro contenitore non osservato)
        animateCounters();
    }


    // Stile header
    toggleHeaderStyle(); // Chiama subito per impostare lo stato iniziale
    window.addEventListener('scroll', toggleHeaderStyle);

    // VanillaTilt
    initializeVanillaTilt();

    // Transizione pagina
    pageTransitionEffects();

    // Contatore impatto
    impactRealtimeCounterAnimation();
});
