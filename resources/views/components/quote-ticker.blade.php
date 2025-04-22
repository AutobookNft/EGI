<div id="quote-ticker" class="card">
    <!-- Link al font Orbitron -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap">

    <!-- Inserisci questo blocco di stile, oppure spostalo nel tuo CSS globale -->
    <style>
        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .animate-marquee {
            animation: marquee 20s linear infinite;
            /* Puoi regolare la durata a tuo piacimento */
        }

        .ticker {
            font-family: 'Orbitron', sans-serif;
            letter-spacing: 2px;
            font-size: 1.00rem;
        }
    </style>

    <!-- Marquee ticker: Testo in movimento in stile tabellone borsa -->
    <div class="my-4 overflow-hidden whitespace-nowrap border-b border-t border-gray-700/50"
        aria-label="Ticker finanziario">
        <div class="animate-marquee inline-block py-2">
            <div class="ticker text-blue-600" aria-label="Ticker ispirazionale">
                Testo iniziale del ticker
            </div>
        </div>
    </div>

    {{-- Script per la gestione delle citazioni --}}
    <script>
        (function() {
            const container = document.getElementById('quote-ticker');
            if (!container) {
                console.error("Container 'quote-ticker' non trovato");
                return;
            }
            const ticker = container.querySelector('.ticker');
            if (!ticker) {
                console.error("Elemento con classe 'ticker' non trovato");
                return;
            }

            async function aggiornaTicker() {
                try {
                    const targetUrl = '/api/quote';
                    const response = await fetch(targetUrl);
                    if (!response.ok) throw new Error('Network response was not ok');
                    const data = await response.json();

                    const newQuote = Array.isArray(data) ?
                        `"${data[0].q}" - ${data[0].a}` :
                        `"${data.content}" - ${data.author}`;

                    ticker.innerText = newQuote;
                } catch (error) {
                    console.error('Errore nel recupero della citazione:', error);
                }
            }

            aggiornaTicker();
            setInterval(aggiornaTicker, 60000);
        })();
    </script>
</div>
