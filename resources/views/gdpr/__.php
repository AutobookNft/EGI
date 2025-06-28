<!DOCTYPE html>
<html lang="it" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termini di Servizio Interattivi | Collector - FlorenceEGI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Source+Sans+Pro:wght@400;600&display=swap" rel="stylesheet">
    <!-- Chosen Palette: Tuscan Parchment -->
    <!-- Application Structure Plan: Struttura a pagina singola con navigazione laterale e contenuto tematico. Aggiunta una sezione finale di "call to action" per l'accettazione dei termini, rendendo l'interazione un flusso completo dalla lettura all'azione. Questo è stato scelto per integrare il processo di consenso direttamente nell'esperienza informativa. -->
    <!-- Visualization & Content Choices:
        - Riepilogo: Card interattive (HTML/CSS).
        - Flusso di Valore: Diagramma a blocchi (HTML/CSS).
        - Articoli Legali: Accordion (HTML/JS).
        - Rischi: Sezione con stile di avviso (HTML/CSS).
        - Definizioni: Glossario statico (HTML).
        - Sezione di Accettazione (NUOVO): Modulo interattivo con checkbox e bottoni. Goal: Catturare il consenso in modo chiaro e legalmente valido. Method: HTML/JS per l'interattività del bottone. Justification: Trasforma la pagina da informativa ad azionabile. -->
    <!-- CONFIRMATION: NO SVG graphics used. NO Mermaid JS used. -->
    <style>
        body {
            font-family: 'Source Sans Pro', sans-serif;
            background-color: #F8F7F2; /* Sfondo pergamena chiaro */
            color: #4A4A4A;
        }
        h1, h2, h3, h4 {
            font-family: 'Playfair Display', serif;
        }
        .active-nav {
            background-color: #EAE7DC;
            color: #8C6A4A;
            font-weight: 600;
        }
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease-in-out;
        }
        .accordion-button.open .accordion-arrow {
            transform: rotate(180deg);
        }
        #accept-button:disabled {
            background-color: #D1D5DB;
            cursor: not-allowed;
            border-color: #D1D5DB;
        }
    </style>
</head>
<body class="bg-[#F8F7F2]">

    <div class="w-full mx-auto max-w-7xl lg:grid lg:grid-cols-4 lg:gap-12">

        <!-- Sticky Navigation -->
        <aside class="p-4 lg:col-span-1 lg:sticky lg:top-0 lg:h-screen lg:p-8 lg:overflow-y-auto">
            <div class="pb-8">
                <h2 class="text-2xl font-bold text-[#2D5016]">FlorenceEGI</h2>
                <p class="text-sm text-gray-500">Termini per Collezionisti</p>
            </div>
            <nav id="main-nav">
                <ul class="space-y-2">
                    <li><a href="#welcome" class="block py-2 px-3 rounded-md transition-colors duration-200 hover:bg-[#EAE7DC] text-gray-700">Benvenuto e Riepilogo</a></li>
                    <li><a href="#the-pact" class="block py-2 px-3 rounded-md transition-colors duration-200 hover:bg-[#EAE7DC] text-gray-700">Il Patto Legale</a></li>
                    <li><a href="#platform-art" class="block py-2 px-3 rounded-md transition-colors duration-200 hover:bg-[#EAE7DC] text-gray-700">La Piattaforma e la Tua Arte</a></li>
                    <li><a href="#rules-risks" class="block py-2 px-3 rounded-md transition-colors duration-200 hover:bg-[#EAE7DC] text-gray-700">Regole e Rischi</a></li>
                    <li><a href="#definitions" class="block py-2 px-3 rounded-md transition-colors duration-200 hover:bg-[#EAE7DC] text-gray-700">Glossario</a></li>
                    <li><a href="#acceptance" class="block py-2 px-3 rounded-md transition-colors duration-200 hover:bg-[#EAE7DC] text-gray-700">Accettazione</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="p-4 lg:col-span-3 md:p-8">

            <!-- Welcome Section -->
            <section id="welcome" class="mb-16 scroll-mt-20">
                <div class="p-8 border shadow-sm bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
                    <h1 class="text-4xl font-bold text-[#2D5016] mb-4">Il Vostro Patto con il Rinascimento Digitale</h1>
                    <p class="mb-8 text-lg text-gray-600">Questo documento è il Suo accordo con FlorenceEGI. Definisce i Suoi diritti e doveri come Collezionista. Abbiamo estratto i punti più importanti per aiutarLa a comprendere rapidamente il nostro rapporto.</p>

                    <div class="grid gap-6 text-center md:grid-cols-3">
                        <div class="p-6 bg-[#F8F7F2] rounded-xl border border-gray-200/80">
                            <h3 class="text-xl font-bold mb-2 text-[#8C6A4A]">Cosa Possiedi</h3>
                            <p class="text-sm text-gray-600">Acquistando un EGI, possiedi il token sulla blockchain. Questo ti dà il diritto di venderlo o trasferirlo. L'opera d'arte associata rimane proprietà intellettuale del Creatore.</p>
                        </div>
                        <div class="p-6 bg-[#F8F7F2] rounded-xl border border-gray-200/80">
                            <h3 class="text-xl font-bold mb-2 text-[#8C6A4A]">Le Tue Responsabilità</h3>
                            <p class="text-sm text-gray-600">Sei responsabile della sicurezza del tuo account e del tuo wallet. Devi usare la piattaforma in modo lecito e rispettare i diritti dei Creatori.</p>
                        </div>
                        <div class="p-6 bg-[#F8F7F2] rounded-xl border border-gray-200/80">
                            <h3 class="text-xl font-bold mb-2 text-[#8C6A4A]">Rischi Principali</h3>
                            <p class="text-sm text-gray-600">Il valore degli NFT è volatile. La tecnologia blockchain comporta rischi e le normative sono in evoluzione. Partecipi con la consapevolezza di questi fattori.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- The Pact Section -->
            <section id="the-pact" class="mb-16 scroll-mt-20">
                <h2 class="text-3xl font-bold mb-6 text-[#2D5016]">Il Patto Legale</h2>
                <div id="pact-accordion" class="space-y-4">
                     <!-- Accordion items will be injected here by JS -->
                </div>
            </section>

            <!-- Platform & Art Section -->
            <section id="platform-art" class="mb-16 scroll-mt-20">
                <h2 class="text-3xl font-bold mb-6 text-[#2D5016]">La Piattaforma e la Tua Arte</h2>
                 <div id="platform-accordion" class="space-y-4">
                     <!-- Accordion items will be injected here by JS -->
                 </div>
            </section>

             <!-- Rules & Risks Section -->
            <section id="rules-risks" class="mb-16 scroll-mt-20">
                <h2 class="text-3xl font-bold mb-6 text-[#2D5016]">Regole e Rischi</h2>
                 <div id="rules-accordion" class="space-y-4">
                    <!-- Accordion items will be injected here by JS -->
                 </div>
            </section>

            <!-- Definitions Section -->
            <section id="definitions" class="mb-16 scroll-mt-20">
                 <div class="p-8 border shadow-sm bg-white/80 backdrop-blur-lg rounded-2xl border-gray-200/50">
                    <h2 class="text-3xl font-bold mb-6 text-[#2D5016]">Glossario</h2>
                    <div id="definitions-content" class="space-y-4 text-gray-700">
                        <!-- Definitions will be injected here by JS -->
                    </div>
                 </div>
            </section>

            <!-- Acceptance Section -->
            <section id="acceptance" class="scroll-mt-20">
                <div id="acceptance-container" class="bg-[#EAE7DC]/60 backdrop-blur-lg p-8 rounded-2xl shadow-md border border-gray-300/50">
                    <h2 class="text-3xl font-bold text-center mb-4 text-[#2D5016]">Azione Richiesta</h2>
                    <p class="mb-6 text-center text-gray-600">Per continuare a usare i servizi di FlorenceEGI come Collezionista, è richiesta la Sua accettazione di questi termini.</p>
                    <div class="max-w-md mx-auto">
                        <div class="flex items-start mb-6">
                            <input id="consent-checkbox" type="checkbox" class="h-5 w-5 rounded border-gray-300 text-[#2D5016] focus:ring-[#8C6A4A] mt-1">
                            <label for="consent-checkbox" class="ml-3 text-sm text-gray-700">Dichiaro di aver letto, compreso e accettato i Termini di Servizio e l'Informativa sulla Privacy di FlorenceEGI.</label>
                        </div>
                        <div class="flex items-center justify-center space-x-4">
                            <button id="refuse-button" class="px-8 py-3 font-semibold text-gray-700 transition-colors duration-300 bg-transparent border border-gray-400 rounded-lg hover:bg-gray-200">
                                Rifiuto
                            </button>
                            <button id="accept-button" disabled class="px-8 py-3 rounded-lg font-semibold text-white bg-[#2D5016] border border-[#2D5016] hover:bg-opacity-90 transition-colors duration-300">
                                Accetto i Termini
                            </button>
                        </div>
                    </div>
                    <div id="feedback-message" class="hidden mt-6 text-sm font-semibold text-center"></div>
                </div>
            </section>

        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const legalData = {
                pact: [
                    { title: "Art. 1: Accettazione e Modifica", content: "<p><b>1.1. Accettazione.</b> L'utilizzo della Piattaforma costituisce la Sua piena e incondizionata accettazione di questo Contratto e della nostra Informativa sulla Privacy.</p><p><b>1.2. Modifiche.</b> Ci riserviamo il diritto di modificare questo Contratto. In caso di modifiche sostanziali, La notificheremo. L'uso continuato del Servizio dopo le modifiche costituisce accettazione dei nuovi termini.</p>" },
                    { title: "Art. 2: Account e Sicurezza", content: "<p><b>2.1. Registrazione.</b> È necessario creare un account con informazioni accurate.</p><p><b>2.2. Wallet Non-Custodial.</b> FlorenceEGI non controlla le Sue chiavi private. La sicurezza del wallet è Sua responsabilità.</p><p><b>2.3. Responsabilità dell'Account.</b> Lei è responsabile di tutte le attività sul Suo account.</p>" },
                    { title: "Art. 9, 10, 11, 12: Varie", content: "<p><b>Risoluzione (Art. 9):</b> Può terminare l'accordo smettendo di usare il servizio. Possiamo sospendere il suo accesso in caso di violazioni.</p><p><b>Garanzie e Responsabilità (Art. 10):</b> Il servizio è fornito \"così com'è\". La nostra responsabilità per danni è limitata secondo i termini di legge.</p><p><b>Legge Applicabile (Art. 11):</b> L'accordo è regolato dalla legge italiana, con foro competente a Firenze.</p><p><b>Disposizioni Finali (Art. 12):</b> Questo è l'intero accordo. Se una parte è invalida, il resto rimane efficace.</p>" }
                ],
                platform: [
                    { title: "Art. 3: Servizi per Collezionisti", content: "<p><b>3.1. Accesso al Marketplace.</b> Le garantiamo una licenza limitata e personale per usare la Piattaforma per acquistare EGI.</p><p><b>3.2. Visualizzazione degli EGI.</b> Può esplorare liberamente le collezioni e i dettagli delle opere e dei progetti di impatto (EPP).</p>" },
                    { title: "Art. 4: Il Flusso di Valore Trasparente", content: "<p><b>4.1. Processo di Acquisto.</b> Gli acquisti sono transazioni irreversibili sulla blockchain Algorand, eseguite da Smart Contract.</p><p><b>4.2. Flusso di Valore Trasparente.</b> Il meccanismo centrale di FlorenceEGI. Quando un'opera viene rivenduta sul mercato secondario, lo Smart Contract distribuisce automaticamente le royalties al Creatore originale e al Progetto di Progresso Ecologico (EPP) associato.</p><p><b>4.3. Proprietà.</b> La proprietà dell'EGI è Sua, registrata pubblicamente sulla blockchain.</p><div class='mt-6 p-6 bg-[#F8F7F2] rounded-xl border border-gray-200/80'><h4 class='font-bold text-lg mb-4 text-center text-[#8C6A4A]'>Diagramma del Flusso di Valore (Vendita Secondaria)</h4><div class='flex flex-col items-center justify-between space-y-4 text-sm text-center md:flex-row md:space-y-0 md:space-x-4'><div class='p-3 bg-white rounded-lg shadow-sm'><strong>1.</strong> Un Collezionista rivende un EGI</div><div class='text-2xl text-[#8C6A4A]'>→</div><div class='p-3 bg-white rounded-lg shadow-sm'><strong>2.</strong> Lo Smart Contract riceve i fondi e le royalties</div><div class='text-2xl text-[#8C6A4A]'>→</div><div class='flex flex-col space-y-2'><div class='p-2 text-green-800 bg-green-100 rounded-lg shadow-sm'><strong>3a.</strong> Quota al Creatore</div><div class='p-2 text-blue-800 bg-blue-100 rounded-lg shadow-sm'><strong>3b.</strong> Quota all'EPP</div></div></div></div>" },
                    { title: "Art. 5: Proprietà Intellettuale", content: "<p><b>5.1. Proprietà dell'EGI.</b> Lei possiede il token.</p><p><b>5.2. Diritti sull'Opera d'Arte.</b> Il copyright sull'immagine/contenuto rimane del Creatore.</p><p><b>5.3. Licenza d'Uso.</b> Ottiene una licenza per visualizzare l'opera per scopi personali e non commerciali.</p><p><b>5.4. Proprietà della Piattaforma.</b> Il marchio e il codice di FlorenceEGI sono nostri.</p>" }
                ],
                rules: [
                    { title: "Art. 6: Condotta dell'Utente", content: "<p><b>Cose da fare:</b> Usare la piattaforma per scopi leciti.</p><p><b>Cose da non fare:</b> È vietato usare la piattaforma per riciclaggio, manipolare prezzi, violare copyright o danneggiare la piattaforma.</p>" },
                    { title: "Art. 7: Commissioni e Tasse", content: "<p><b>Commissioni di Servizio:</b> Ogni transazione ha una commissione di servizio per FlorenceEGI, mostrata prima dell'acquisto.</p><p><b>Gas Fees:</b> Le transazioni blockchain richiedono \"gas fees\", che vanno alla rete Algorand, non a noi.</p><p><b>Tasse:</b> Lei è responsabile del pagamento di tutte le tasse applicabili sulle sue transazioni.</p>", style: 'default' },
                    { title: "Art. 8: Assunzione del Rischio", content: "<p>Deve essere consapevole dei rischi:</p><ul><li><b>! Volatilità dei Prezzi:</b> Il valore degli asset digitali può cambiare rapidamente.</li><li><b>! Rischi Tecnologici:</b> Bug o attacchi informatici sono possibili.</li><li><b>! Incertezza Normativa:</b> Le leggi sugli asset digitali sono in evoluzione.</li><li><b>! Transazioni Irreversibili:</b> Le operazioni su blockchain non possono essere annullate.</li></ul>", style: 'warning' }
                ],
                definitions: [
                   { term: "Piattaforma", def: "Il marketplace FlorenceEGI." },
                   { term: "Utente Collezionista (Collector)", def: "Un utente registrato per acquistare e collezionare EGI." },
                   { term: "EGI (Ecological Good & Inventive)", def: "Un NFT sulla blockchain Algorand che rappresenta un'opera d'arte e supporta un progetto di impatto." },
                   { term: "EPP (Ecological Progress Pathway)", def: "Un progetto di sostenibilità verificato che riceve una quota delle royalties." },
                   { term: "Smart Contract", def: "Il codice auto-eseguibile su blockchain che gestisce le transazioni e le royalties." },
                ]
            };

            function createAccordionItem(item) {
                const isWarning = item.style === 'warning';
                const bgColor = isWarning ? 'bg-amber-50/80' : 'bg-white/80';
                const borderColor = isWarning ? 'border-amber-200/50' : 'border-gray-200/50';
                const titleColor = isWarning ? 'text-amber-800' : 'text-gray-800';

                return `
                    <div class="accordion-item ${bgColor} backdrop-blur-lg rounded-2xl shadow-sm border ${borderColor}">
                        <button class="flex items-center justify-between w-full p-6 text-left accordion-button">
                            <span class="text-xl font-bold ${titleColor}">${item.title}</span>
                            <span class="transition-transform duration-300 accordion-arrow">▼</span>
                        </button>
                        <div class="accordion-content">
                            <div class="px-6 pb-6 prose max-w-none ${isWarning ? 'prose-p:text-amber-900' : ''}">${item.content}</div>
                        </div>
                    </div>
                `;
            }

            document.getElementById('pact-accordion').innerHTML = legalData.pact.map(createAccordionItem).join('');
            document.getElementById('platform-accordion').innerHTML = legalData.platform.map(createAccordionItem).join('');
            document.getElementById('rules-accordion').innerHTML = legalData.rules.map(createAccordionItem).join('');

            document.getElementById('definitions-content').innerHTML = legalData.definitions.map(d => `<p><b>${d.term}:</b> ${d.def}</p>`).join('');

            // Accordion Logic
            const accordionButtons = document.querySelectorAll('.accordion-button');
            accordionButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const content = button.nextElementSibling;
                    button.classList.toggle('open');
                    if (content.style.maxHeight) {
                        content.style.maxHeight = null;
                    } else {
                        content.style.maxHeight = content.scrollHeight + 'px';
                    }
                });
            });

            // Intersection Observer for active nav link
            const sections = document.querySelectorAll('main section');
            const navLinks = document.querySelectorAll('#main-nav a');

            const observerOptions = { root: null, rootMargin: '0px', threshold: 0.4 };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        navLinks.forEach(link => {
                            link.classList.remove('active-nav');
                            if (link.getAttribute('href').substring(1) === entry.target.id) {
                                link.classList.add('active-nav');
                            }
                        });
                    }
                });
            }, observerOptions);

            sections.forEach(section => { observer.observe(section); });

            // Acceptance Logic
            const consentCheckbox = document.getElementById('consent-checkbox');
            const acceptButton = document.getElementById('accept-button');
            const refuseButton = document.getElementById('refuse-button');
            const feedbackMessage = document.getElementById('feedback-message');
            const acceptanceContainer = document.getElementById('acceptance-container');

            consentCheckbox.addEventListener('change', () => {
                acceptButton.disabled = !consentCheckbox.checked;
            });

            acceptButton.addEventListener('click', () => {
                // In a real application, this would be a fetch() call to the backend.
                console.log('Consent accepted. Hashing and sending to blockchain...');
                feedbackMessage.textContent = 'Grazie! Il suo consenso è stato registrato con successo.';
                feedbackMessage.className = 'mt-6 text-center text-sm font-semibold text-green-700 block';
                acceptanceContainer.classList.add('pointer-events-none', 'opacity-75');
            });

            refuseButton.addEventListener('click', () => {
                console.log('Consent refused.');
                feedbackMessage.textContent = 'La sua scelta è stata registrata. Per utilizzare la piattaforma è necessaria l\'accettazione dei termini.';
                feedbackMessage.className = 'mt-6 text-center text-sm font-semibold text-red-700 block';
                acceptanceContainer.classList.add('pointer-events-none', 'opacity-75');
            });
        });
    </script>
</body>
</html>
