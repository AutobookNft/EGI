// resources/ts/components/natan-assistant.ts

/**
 * Natan Assistant UI Component
 * @description Gestisce l'assistente UI Natan che fornisce aiuto contestuale senza interrompere la navigazione
 * @version 2.1.0
 */
export class NatanAssistant {
    private sections: {id: string, element: HTMLElement, suggestion: string}[] = [];
    private currentSection: string | null = null;
    private suggestionTimeout: number | null = null;
    private isThinking: boolean = false;
    private isOpen: boolean = false;
    private currentOpenContentId: string | null = null;
    private toggleButton: HTMLElement | null = null;
    private menuElement: HTMLElement | null = null;
    private debugMode: boolean = true;
    private isProcessingToggle: boolean = false;

    constructor() {
        this.debug('NatanAssistant constructor called');

        // Ottieni riferimenti DOM principali
        this.toggleButton = document.getElementById('natan-assistant-toggle');
        this.menuElement = document.getElementById('natan-assistant-menu');

        if (!this.toggleButton || !this.menuElement) {
            this.debug('ERROR: Critical DOM elements not found', {
                toggleButton: !!this.toggleButton,
                menuElement: !!this.menuElement
            });
            return;
        }

        // Inizializza struttura e funzionalità
        this.setupToggle();
        this.addStyles();
        this.initLearnMoreButtons();

        // Protezione aggiuntiva per eventi esterni
        document.addEventListener('click', (e) => {
            // Ignora click su elementi natan
            if (e.target instanceof Element &&
                (e.target.closest('#natan-assistant-container') ||
                e.target.closest('#natan-suggestion') ||
                e.target.id === 'natan-assistant-toggle' ||
                e.target.closest('#natan-assistant-toggle'))) {
                return;
            }

            // Se il menu è aperto (visibile) ma isOpen è false (stato incoerente)
            // o viceversa, ripristina lo stato corretto
            if (this.menuElement) {
                const menuVisible = !this.menuElement.classList.contains('hidden');
                if (menuVisible !== this.isOpen) {
                    this.debug('Detected state inconsistency, fixing isOpen state');
                    this.isOpen = menuVisible;
                }
            }
        }, true); // Fase di capturing per catturare prima di altri handler

        // Aggiungi reset automatico periodico dello stato
        setInterval(() => {
            this.resetStateIfNeeded();
        }, 2000);

        // Inizializza funzionalità di assistenza contestuale in differita
        window.addEventListener('load', () => {
            setTimeout(() => {
                this.initSections();
                this.initScrollObserver();
                this.initUserActionListeners();
                this.initHoverSuggestions();
                this.checkUserHistory();

                // Mostra il pulse di benvenuto dopo 3 secondi
                setTimeout(() => this.showWelcomePulse(), 3000);
            }, 1000);
        });

        this.debug('NatanAssistant initialization complete');
    }

    /**
     * Resetta lo stato se necessario per riparare incoerenze
     */
    private resetStateIfNeeded(): void {
        if (!this.menuElement) return;

        // Controlla se lo stato visibile del menu corrisponde alla proprietà isOpen
        const menuVisible = !this.menuElement.classList.contains('hidden');

        if (menuVisible !== this.isOpen) {
            this.debug('Fixing state inconsistency in reset');
            this.isOpen = menuVisible;
        }

        // Se il menu è visivamente chiuso ma state dice aperto, reset
        if (!menuVisible && this.isOpen) {
            this.debug('Menu visually closed but state is open - resetting');
            this.isOpen = false;
        }

        // Assicurati che il menu abbia lo stile display corretto
        if (menuVisible && this.menuElement.style.display !== 'flex') {
            this.menuElement.style.display = 'flex';
        } else if (!menuVisible && this.menuElement.style.display !== 'none') {
            this.menuElement.style.display = 'none';
        }

        // Reset isProcessingToggle se fermo da troppo tempo
        if (this.isProcessingToggle) {
            this.debug('Resetting stuck isProcessingToggle flag');
            this.isProcessingToggle = false;
        }
    }

    /**
     * Utility di debug
     */
    private debug(...args: any[]): void {
        if (this.debugMode) {
            console.log('[NatanAssistant]', ...args);
        }
    }

    /**
     * Configura l'interazione toggle principale
     */
    private setupToggle(): void {
        if (!this.toggleButton || !this.menuElement) {
            this.debug('ERROR: setupToggle - Missing critical elements');
            return;
        }

        this.debug('Setting up toggle button and menu');

        // Clona il toggle per rimuovere eventuali listener precedenti
        const newToggleButton = this.toggleButton.cloneNode(true) as HTMLElement;
        if (this.toggleButton.parentNode) {
            this.toggleButton.parentNode.replaceChild(newToggleButton, this.toggleButton);
            this.toggleButton = newToggleButton;
            this.debug('Cloned toggle button to remove existing listeners');
        }

        // Assicurati che il menu sia nascosto inizialmente
        if (!this.menuElement.classList.contains('hidden')) {
            this.menuElement.classList.add('hidden');
            this.debug('Added missing hidden class to menu');
        }

        // Imposta anche display: none per sicurezza
        this.menuElement.style.display = 'none';
        this.isOpen = false; // Assicurati che isOpen sia inizialmente false

        // CRUCIALE: Aggiungi il listener in capturing phase e con stopImmediatePropagation
        this.toggleButton.addEventListener('click', (e: Event) => {
            // Cast dell'evento generico a MouseEvent
            const mouseEvent = e as MouseEvent;

            this.debug('Toggle button clicked');

            // Previeni doppi click o race conditions
            if (this.isProcessingToggle) {
                this.debug('Ignoring click - already processing toggle');
                mouseEvent.stopImmediatePropagation();
                mouseEvent.stopPropagation();
                mouseEvent.preventDefault();
                return;
            }

            this.isProcessingToggle = true;

            // FONDAMENTALE: Ferma la propagazione prima di tutto
            mouseEvent.stopImmediatePropagation();
            mouseEvent.stopPropagation();
            mouseEvent.preventDefault();

            // Gestisci il toggle
            this.handleToggleClick(mouseEvent);
        }, true); // true = capturing phase

        // Aggiungi gestori click per i menu item
        document.querySelectorAll('.natan-item').forEach(item => {
            item.addEventListener('click', (e: Event) => {
                const mouseEvent = e as MouseEvent;
                mouseEvent.stopImmediatePropagation();
                mouseEvent.stopPropagation();
                mouseEvent.preventDefault();
                this.handleMenuItemClick(mouseEvent);
            }, true); // Fase di capturing
        });

        // Handler per click all'esterno - RICONFIGURATO
        document.addEventListener('click', (e: Event) => {
            const mouseEvent = e as MouseEvent;

            // CRUCIALE: Non processare se stiamo già elaborando un toggle
            if (this.isProcessingToggle) {
                this.debug('Ignoring outside click - toggle processing in progress');
                return;
            }

            // CRUCIALE: Ignora se il click è sul toggle o un suo discendente
            if (mouseEvent.target instanceof Node &&
                (this.toggleButton?.contains(mouseEvent.target) || mouseEvent.target === this.toggleButton)) {
                this.debug('Outside click was on toggle button - IGNORING');
                return;
            }

            this.handleOutsideClick(mouseEvent);
        });
    }

    /**
     * Gestisce il click sul pulsante toggle principale
     */
    private handleToggleClick(e: MouseEvent): void {
        this.debug('handleToggleClick executing');

        if (!this.menuElement) {
            this.debug('ERROR: Menu element not found');
            return;
        }

        try {
            // Assicurati che non siamo in uno stato incoerente prima di procedere
            this.resetStateIfNeeded();

            const isHidden = this.menuElement.classList.contains('hidden');
            this.debug('Current menu state - hidden:', isHidden, 'display:', this.menuElement.style.display);

            if (isHidden) {
                // APERTURA MENU
                this.debug('OPENING MENU');
                this.menuElement.classList.remove('hidden');
                this.menuElement.style.display = 'flex';
                this.isOpen = true; // Imposta isOpen a true quando si apre

                // Force reflow per essere sicuri che le modifiche siano applicate
                void this.menuElement.offsetHeight;

                // Log immediato DOM dopo apertura
                this.debug('Menu after opening - hidden class:',
                    this.menuElement.classList.contains('hidden'),
                    'display:', this.menuElement.style.display,
                    'computed display:', window.getComputedStyle(this.menuElement).display
                );

                // Anima entrata elementi
                setTimeout(() => {
                    this.debug('Animating menu items');
                    document.querySelectorAll('.natan-item').forEach((item, index) => {
                        setTimeout(() => {
                            (item as HTMLElement).classList.remove('translate-x-20', 'opacity-0');
                        }, index * 50);
                    });
                }, 100);
            } else {
                // CHIUSURA MENU
                this.debug('CLOSING MENU');

                // Reset menu items
                document.querySelectorAll('.natan-item').forEach(item => {
                    (item as HTMLElement).classList.add('translate-x-20', 'opacity-0');
                });

                // Chiudi menu con leggero ritardo per animazione
                setTimeout(() => {
                    if (this.menuElement) {
                        this.debug('Actually hiding menu after animation');
                        this.menuElement.classList.add('hidden');
                        this.menuElement.style.display = 'none';
                        this.isOpen = false; // FONDAMENTALE: Imposta isOpen a false quando si chiude

                        // Chiudi anche eventuali tooltip aperti
                        this.closeAllContent();
                    }
                }, 300);
            }
        } catch (error) {
            this.debug('ERROR in handleToggleClick:', error);
            // In caso di errore, reset allo stato chiuso
            if (this.menuElement) {
                this.menuElement.classList.add('hidden');
                this.menuElement.style.display = 'none';
            }
            this.isOpen = false;
        } finally {
            // Reset sempre il flag di processing quando terminato
            setTimeout(() => {
                this.isProcessingToggle = false;
            }, 500);
        }
    }

    /**
     * Gestisce il click su un elemento del menu
     */
    private handleMenuItemClick(e: MouseEvent): void {
        this.debug('Menu item clicked');

        const item = e.currentTarget as HTMLElement;
        const id = item.id.replace('natan-item-', '');
        const contentBox = document.getElementById(`natan-content-${id}`);

        if (!contentBox) {
            this.debug('Content box not found for id:', id);
            return;
        }

        const isExpanded = item.getAttribute('aria-expanded') === 'true';
        this.debug('Item expanded state:', isExpanded);

        // Chiudi altri content box aperti
        document.querySelectorAll('[id^="natan-content-"]').forEach(box => {
            if (box !== contentBox) {
                box.classList.add('hidden');
                this.debug('Hiding other content box:', box.id);
            }
        });

        document.querySelectorAll('.natan-item').forEach(menuItem => {
            if (menuItem !== item) {
                menuItem.setAttribute('aria-expanded', 'false');
            }
        });

        // Toggle questo content box
        item.setAttribute('aria-expanded', (!isExpanded).toString());

        if (isExpanded) {
            contentBox.classList.add('hidden');
            this.currentOpenContentId = null;
            this.debug('Hiding content box:', contentBox.id);
        } else {
            contentBox.classList.remove('hidden');
            this.currentOpenContentId = id;
            this.debug('Showing content box:', contentBox.id);

            // Mostra thinking effect quando si apre un content
            this.showThinking(500);

            // Spotlight se appropriato
            setTimeout(() => {
                const spotlightSelector = contentBox.getAttribute('data-spotlight');
                if (spotlightSelector) {
                    this.debug('Content has spotlight selector:', spotlightSelector);
                    this.spotlight(spotlightSelector, 4000);
                }
            }, 700);
        }
    }

    /**
     * Gestisce click esterno per chiudere menu
     */
    private handleOutsideClick(e: MouseEvent): void {
        this.debug('handleOutsideClick processing');

        const container = document.getElementById('natan-assistant-container');
        const suggestionEl = document.getElementById('natan-suggestion');

        // Ignora click su suggerimenti o toggle
        if (e.target instanceof Element &&
            (e.target.closest('#natan-suggestion') ||
            e.target.id === 'natan-assistant-toggle' ||
            e.target.closest('#natan-assistant-toggle'))) {
            this.debug('Outside click on suggestion or toggle - ignoring');
            return;
        }

        // Ignora click strani con target indefinito
        if (!(e.target instanceof Element)) {
            this.debug('Outside click with non-Element target - ignoring');
            return;
        }

        if (!container || !this.menuElement) {
            this.debug('Container or menu element not found');
            return;
        }

        // Verifica se il menu è aperto e il click è fuori dal container
        if (!this.menuElement.classList.contains('hidden') &&
            !container.contains(e.target as Node)) {

            this.debug('Valid outside click detected - closing menu');

            // Reset menu items
            document.querySelectorAll('.natan-item').forEach(item => {
                (item as HTMLElement).classList.add('translate-x-20', 'opacity-0');
            });

            // Chiudi menu
            setTimeout(() => {
                if (this.menuElement) {
                    this.menuElement.classList.add('hidden');
                    this.menuElement.style.display = 'none';
                    this.isOpen = false; // IMPORTANTE: Aggiorna lo stato
                    this.debug('Menu hidden after outside click');
                }
                this.closeAllContent();
            }, 200);
        } else {
            this.debug('Outside click - no action needed');
        }
    }

    /**
     * Chiude tutti i content box
     */
    private closeAllContent(): void {
        this.debug('Closing all content boxes');

        document.querySelectorAll('[id^="natan-content-"]').forEach(box => {
            box.classList.add('hidden');
        });

        document.querySelectorAll('.natan-item').forEach(item => {
            item.setAttribute('aria-expanded', 'false');
        });

        this.currentOpenContentId = null;
    }

    /**
     * Mostra un suggerimento contestuale
     */
    private showSuggestion(text: string, sectionId: string): void {
        this.debug('Showing suggestion:', text, 'for section:', sectionId);

        // Crea o aggiorna un elemento suggerimento
        let suggestionEl = document.getElementById('natan-suggestion');

        if (!suggestionEl) {
            suggestionEl = document.createElement('div');
            suggestionEl.id = 'natan-suggestion';
            document.getElementById('natan-assistant-container')?.appendChild(suggestionEl);
            this.debug('Created new suggestion element');
        }

        // Adatta posizione e dimensioni per mobile
        if (this.isMobile()) {
            suggestionEl.className = 'absolute top-[-50px] right-0 px-3 py-1.5 text-xs font-medium text-emerald-300 bg-gray-900 border border-emerald-600/30 rounded-full transition-all duration-300 transform translate-y-0 opacity-0 max-w-[180px] whitespace-normal';
        } else {
            suggestionEl.className = 'absolute top-0 px-4 py-2 text-sm font-medium transition-all duration-300 transform translate-y-0 bg-gray-900 border rounded-full opacity-0 right-16 text-emerald-300 border-emerald-600/30 whitespace-nowrap';
        }

        // Imposta il testo e mostra il suggerimento
        suggestionEl.textContent = text;
        suggestionEl.setAttribute('data-section', sectionId);

        // Anima il suggerimento
        setTimeout(() => {
            suggestionEl.classList.remove('translate-y-0', 'opacity-0');
            suggestionEl.classList.add('translate-y-[-120%]', 'opacity-1');
            this.debug('Suggestion animated in');

            // Nascondi dopo 5 secondi
            setTimeout(() => {
                suggestionEl.classList.remove('translate-y-[-120%]', 'opacity-1');
                suggestionEl.classList.add('translate-y-0', 'opacity-0');
                this.debug('Suggestion animated out');
            }, 5000);
        }, 50);

        // Flag per prevenire doppi clic
        let hasHandledClick = false;

        // Aggiungi click handler per mostrare informazioni rilevanti
        suggestionEl.addEventListener('click', (e) => {
            this.debug('Suggestion clicked');

            // Previeni doppi clic nello stesso evento
            if (hasHandledClick) {
                this.debug('Ignoring duplicate suggestion click');
                e.stopPropagation();
                e.preventDefault();
                return;
            }

            hasHandledClick = true;

            // Blocca propagazione per sicurezza
            e.stopPropagation();
            e.preventDefault();

            // Apri SOLO l'assistente, senza aprire automaticamente i content box
            if (!this.isOpen) {
                this.debug('Opening assistant from suggestion (menu only)');
                // NON chiamare toggleAssistant qui, ma gestisci l'apertura direttamente
                if (this.menuElement && this.menuElement.classList.contains('hidden')) {
                    this.menuElement.classList.remove('hidden');
                    this.menuElement.style.display = 'flex';
                    this.isOpen = true;

                    // Anima menu items
                    setTimeout(() => {
                        document.querySelectorAll('.natan-item').forEach((item, index) => {
                            setTimeout(() => {
                                (item as HTMLElement).classList.remove('translate-x-20', 'opacity-0');
                            }, index * 50);
                        });

                        // INSERISCI QUI IL MIGLIORAMENTO AGGIUNTIVO
                        // Highlight suggerito all'apertura del menu
                        setTimeout(() => {
                            // Trova l'item rilevante
                            let relevantItemId = '';
                            switch (sectionId) {
                                case 'hero':
                                    relevantItemId = 'natan-item-what-is-egi';
                                    break;
                                case 'stats':
                                case 'impact':
                                    relevantItemId = 'natan-item-how-impact-works';
                                    break;
                                case 'creator':
                                    relevantItemId = 'natan-item-granular-business';
                                    break;
                                case 'galleries':
                                case 'collections':
                                case 'onboarding':
                                    relevantItemId = 'natan-item-start-without-crypto';
                                    break;
                            }

                            if (relevantItemId) {
                                const relevantItem = document.getElementById(relevantItemId);
                                if (relevantItem) {
                                    this.debug('Highlighting relevant item:', relevantItemId);
                                    // Aggiungi e rimuovi una classe per far lampeggiare leggermente l'elemento
                                    relevantItem.classList.add('natan-item-highlight');

                                    setTimeout(() => {
                                        relevantItem.classList.remove('natan-item-highlight');
                                    }, 1500);
                                }
                            }
                        }, 500);
                    }, 100);
                }
            }

            // Reset flag dopo un timeout
            setTimeout(() => {
                hasHandledClick = false;
            }, 500);
        });
    }

    /**
     * Attiva o disattiva l'assistente programmaticamente
     */
    private toggleAssistant(): void {
        this.debug('toggleAssistant called programmatically');

        if (!this.toggleButton || !this.menuElement || this.isProcessingToggle) {
            this.debug('Toggle not possible now');
            return;
        }

        // Non usare click() per evitare problemi di propagazione
        // Chiama direttamente handleToggleClick con un evento sintetico
        this.handleToggleClick(new MouseEvent('click'));
    }

    /**
     * Apre o chiude un box di contenuto specifico
     */
    private toggleContentBox(id: string): void {
        this.debug('toggleContentBox called for:', id);

        const contentBox = document.getElementById(`natan-content-${id}`);
        const button = document.getElementById(`natan-item-${id}`);

        if (!contentBox || !button) {
            this.debug('ERROR: Content box or button not found for id:', id);
            return;
        }

        // Se c'è già un content box aperto e non è questo, chiudilo
        if (this.currentOpenContentId && this.currentOpenContentId !== id) {
            const openBox = document.getElementById(`natan-content-${this.currentOpenContentId}`);
            const openButton = document.getElementById(`natan-item-${this.currentOpenContentId}`);

            if (openBox && openButton) {
                openBox.classList.add('hidden');
                openButton.setAttribute('aria-expanded', 'false');
                this.debug('Closed previously open content:', this.currentOpenContentId);
            }
        }

        // Toggle stato corrente
        const isExpanded = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', (!isExpanded).toString());
        this.debug('Set aria-expanded:', !isExpanded);

        if (!isExpanded) {
            // Aggiungi effetto "thinking" quando si apre un contenuto
            this.showThinking(500);

            // Posiziona il content box in modo diverso su mobile
            if (this.isMobile() && contentBox) {
                contentBox.classList.add('bottom-auto', 'top-full', 'right-0', 'mt-2', '-mb-0');
                contentBox.classList.remove('bottom-full', 'mb-2');
                this.debug('Adjusted content box position for mobile');

                // Sposta l'arrow del tooltip
                const arrow = contentBox.querySelector('div[class*="absolute bottom-0"]');
                if (arrow && arrow instanceof HTMLElement) {
                    arrow.className = 'absolute top-0 right-6 w-3 h-3 -mt-1.5 bg-gray-900 border-l border-t border-emerald-600/30 transform rotate-45';
                    this.debug('Repositioned arrow for mobile');
                }
            }

            // Ritardo breve prima di mostrare il contenuto
            setTimeout(() => {
                contentBox.classList.remove('hidden');
                this.currentOpenContentId = id;
                this.debug('Content box displayed');

                // Aggiungi spotlight per elementi specifici basati sull'ID
                setTimeout(() => {
                    // Prendi il selettore dallo spotlight-data attribute
                    const spotlightSelector = contentBox.getAttribute('data-spotlight');
                    if (spotlightSelector) {
                        this.debug('Applying spotlight from data-attribute:', spotlightSelector);
                        this.spotlight(spotlightSelector, 4000);
                    } else {
                        // Fallback per spotlight basato su ID
                        this.debug('No spotlight selector in data-attribute, using fallback based on ID');
                        switch(id) {
                            case 'what-is-egi':
                                // Spotlight sulla prima collezione EGI
                                this.spotlight('.collection-card-nft:first-child', 4000);
                                break;
                            case 'how-impact-works':
                                // Spotlight sugli elementi di impatto
                                this.spotlight('.nft-stats-section [data-counter]', 4000);
                                break;
                            case 'start-without-crypto':
                                // Spotlight sul pulsante di registrazione
                                this.spotlight('#register-link-desktop, #register-link-mobile', 4000);
                                break;
                            case 'granular-business':
                                // Spotlight su una collezione business (se esiste)
                                const businessCards = document.querySelectorAll('.collection-card-nft');
                                if (businessCards.length > 1) {
                                    this.spotlight('.collection-card-nft:nth-child(2)', 4000);
                                }
                                break;
                        }
                    }
                }, 700);
            }, 500);
        } else {
            contentBox.classList.add('hidden');
            this.currentOpenContentId = null;
            this.debug('Content box hidden');
        }
    }

    /**
     * Mostra effetto "thinking" sull'immagine di Natan
     */
    private showThinking(duration: number = 1500): void {
        if (this.isThinking) {
            this.debug('Already in thinking state, ignoring');
            return;
        }

        this.debug('Showing thinking effect for duration:', duration);
        this.isThinking = true;

        const natanImage = this.toggleButton?.querySelector('img');
        if (natanImage) {
            natanImage.classList.add('natan-thinking');
            this.debug('Added natan-thinking class to image');

            setTimeout(() => {
                natanImage.classList.remove('natan-thinking');
                this.isThinking = false;
                this.debug('Removed natan-thinking class from image');
            }, duration);
        } else {
            this.debug('ERROR: Natan image not found');
            this.isThinking = false; // Reset flag in case of error
        }
    }

    /**
     * Evidenzia un elemento nella pagina
     */
    private spotlight(selector: string, duration: number = 3000): void {
        this.debug('Spotlight called for:', selector, 'duration:', duration);

        const elements = document.querySelectorAll(selector);
        if (!elements || elements.length === 0) {
            this.debug('No elements found for selector:', selector);
            return;
        }

        // Crea overlay spotlight se non esiste
        let spotlightOverlay = document.getElementById('natan-spotlight-overlay');
        if (!spotlightOverlay) {
            spotlightOverlay = document.createElement('div');
            spotlightOverlay.id = 'natan-spotlight-overlay';
            spotlightOverlay.className = 'fixed inset-0 z-40 transition-opacity duration-300 bg-black bg-opacity-50 opacity-0 pointer-events-none';
            document.body.appendChild(spotlightOverlay);
            this.debug('Created spotlight overlay');
        }

        // Prendi il primo elemento se ci sono più selettori
        const element = elements[0] as HTMLElement;
        this.debug('Spotlighting element:', element);

        // Crea highlight se non esiste
        let highlight = document.getElementById('natan-highlight');
        if (!highlight) {
            highlight = document.createElement('div');
            highlight.id = 'natan-highlight';
            highlight.className = 'absolute z-50 transition-all duration-300 border-2 rounded-md shadow-lg opacity-0 pointer-events-none border-emerald-400 shadow-emerald-400/30';
            document.body.appendChild(highlight);
            this.debug('Created spotlight highlight');
        }

        // Posiziona l'highlight sull'elemento
        const rect = element.getBoundingClientRect();
        highlight.style.top = `${rect.top - 4 + window.scrollY}px`;
        highlight.style.left = `${rect.left - 4}px`;
        highlight.style.width = `${rect.width + 8}px`;
        highlight.style.height = `${rect.height + 8}px`;

        this.debug('Positioned highlight at:', {
            top: rect.top - 4 + window.scrollY,
            left: rect.left - 4,
            width: rect.width + 8,
            height: rect.height + 8
        });

        // Mostra l'overlay e l'highlight
        spotlightOverlay.classList.remove('opacity-0');
        highlight.classList.remove('opacity-0');
        this.debug('Made spotlight and overlay visible');

        // Aggiungi pulsazione all'highlight
        highlight.style.animation = 'natan-highlight-pulse 2s ease-in-out infinite';

        // Rimuovi dopo la durata specificata
        setTimeout(() => {
            spotlightOverlay.classList.add('opacity-0');
            highlight.classList.add('opacity-0');
            this.debug('Hiding spotlight after duration');

            setTimeout(() => {
                highlight.style.animation = '';
                this.debug('Removed highlight animation');
            }, 300);
        }, duration);
    }

    /**
     * Rileva se il dispositivo è mobile
     */
    private isMobile(): boolean {
        return window.innerWidth < 768;
    }

    /**
     * Mostra pulse di benvenuto
     */
    private showWelcomePulse(): void {
        this.debug('Showing welcome pulse');

        if (!this.toggleButton) {
            this.debug('ERROR: Toggle button not found');
            return;
        }

        this.toggleButton.classList.add('natan-welcome-pulse');
        this.debug('Added welcome-pulse class');

        setTimeout(() => {
            this.toggleButton?.classList.remove('natan-welcome-pulse');
            this.debug('Removed welcome-pulse class');
        }, 4500);
    }

    /**
     * Inizializza i pulsanti "Scopri di più"
     */
    private initLearnMoreButtons(): void {
        this.debug('Initializing learn more buttons');

        document.querySelectorAll('[data-action]').forEach(button => {
            if (!(button instanceof HTMLElement)) return;

            // Skip se è un pulsante già gestito da altro codice
            if (button.hasAttribute('data-action-initialized')) {
                this.debug('Button already initialized:', button);
                return;
            }

            button.setAttribute('data-action-initialized', 'true');

            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                this.debug('Learn more button clicked:', button.dataset);

                const action = button.dataset.action;
                const target = button.dataset.target;

                if (!action || !target) {
                    this.debug('Missing action or target:', action, target);
                    return;
                }

                switch(action) {
                    case 'spotlight':
                        this.debug('Executing spotlight action for:', target);
                        this.spotlight(target, 4000);
                        break;
                    case 'navigate':
                        this.debug('Executing navigate action to:', target);
                        // ATTESA: Non chiudere immediatamente il menu
                        setTimeout(() => {
                            window.location.href = target;
                        }, 300);
                        break;
                    default:
                        this.debug('Unknown action:', action);
                }

                // MODIFICA CRUCIALE: Non chiudere immediatamente il content
                // Aspetta un po' prima di chiudere
                setTimeout(() => {
                    this.debug('Closing content after learn more action');
                    this.closeAllContent();
                }, 500);
            });

            this.debug('Learn more button initialized:', button.dataset);
        });
    }

    /**
     * Aggiunge gli stili CSS necessari se non esistono
     */
    private addStyles(): void {
        this.debug('Adding required styles');

        if (!document.getElementById('natan-thinking-styles')) {
            const styleEl = document.createElement('style');
            styleEl.id = 'natan-thinking-styles';
            styleEl.textContent = `
                @keyframes natan-thinking {
                    0%, 100% { transform: scale(1); filter: brightness(1); }
                    25% { transform: scale(1.05) rotate(-2deg); filter: brightness(1.1); }
                    75% { transform: scale(1.05) rotate(2deg); filter: brightness(1.1); }
                }

                .natan-thinking {
                    animation: natan-thinking 0.8s ease-in-out infinite;
                }

                @keyframes natan-highlight-pulse {
                    0%, 100% { box-shadow: 0 0 0 rgba(16, 185, 129, 0.4); }
                    50% { box-shadow: 0 0 15px 2px rgba(16, 185, 129, 0.6); }
                }

                @keyframes natan-welcome-pulse {
                    0%, 100% { transform: scale(1); box-shadow: 0 0 0 rgba(16, 185, 129, 0.4); }
                    50% { transform: scale(1.1); box-shadow: 0 0 15px 5px rgba(16, 185, 129, 0.6); }
                }

                .natan-welcome-pulse {
                    animation: natan-welcome-pulse 1.5s ease-in-out 3;
                }

                #natan-assistant-menu:not(.hidden) {
                    display: flex !important;
                }

                /* Style per l'overlay di spotlight */
                #natan-spotlight-overlay {
                    backdrop-filter: blur(2px);
                }

                /* Style per l'highlight di spotlight */
                #natan-highlight {
                    box-shadow: 0 0 20px 5px rgba(16, 185, 129, 0.4);
                }

                /* Miglioramenti per il suggerimento contestuale */
                #natan-suggestion {
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                    position: absolute;
                    z-index: 60;
                    cursor: pointer;
                }

                /* AGGIUNGI QUESTA CLASSE PER IL MIGLIORAMENTO */
                .natan-item-highlight {
                    border-color: rgba(16, 185, 129, 0.7) !important;
                    box-shadow: 0 0 8px rgba(16, 185, 129, 0.5) !important;
                    transform: scale(1.05) !important;
                }
            `;
            document.head.appendChild(styleEl);
            this.debug('Added required styles to document head');
        } else {
            this.debug('Styles already exist');
        }
    }

    /**
     * Inizializza le sezioni principali della pagina
     */
    private initSections(): void {
        this.debug('Initializing page sections');

        // Mappa le sezioni principali della pagina con suggerimenti contestuali
        const sectionMappings = [
            {
                selector: '#hero-section',
                id: 'hero',
                suggestion: "Scopri cos'è un EGI 👆"
            },
            {
                selector: '.nft-stats-section',
                id: 'stats',
                suggestion: "Ti interessa l'impatto ambientale? 🌱"
            },
            {
                selector: 'section[aria-labelledby="latest-galleries-heading"]',
                id: 'galleries',
                suggestion: "Vuoi creare la tua galleria? 🎨"
            },
            {
                selector: 'section[aria-labelledby="environmental-impact-heading"]',
                id: 'impact',
                suggestion: "Ecco come funziona l'impatto 🌍"
            },
            {
                selector: 'section[aria-labelledby="creator-cta-heading"]',
                id: 'creator',
                suggestion: "Scopri il business granulare 💼"
            }
        ];

        // Popola l'array delle sezioni con elementi DOM reali
        this.sections = sectionMappings
            .map(mapping => {
                const element = document.querySelector(mapping.selector);
                if (element) {
                    this.debug('Found section:', mapping.id, 'for selector:', mapping.selector);
                    return {
                        id: mapping.id,
                        element: element as HTMLElement,
                        suggestion: mapping.suggestion
                    };
                } else {
                    this.debug('Section not found for selector:', mapping.selector);
                    return null;
                }
            })
            .filter(section => section !== null) as {id: string, element: HTMLElement, suggestion: string}[];

        this.debug('Initialized sections:', this.sections.length);
    }

    /**
     * Inizializza l'observer per le sezioni visibili
     */
    private initScrollObserver(): void {
        this.debug('Initializing scroll observer');

        // Usa Intersection Observer per rilevare quando le sezioni sono visibili
        if (!('IntersectionObserver' in window)) {
            this.debug('IntersectionObserver not supported in this browser');
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                // Trova la sezione corrispondente
                const section = this.sections.find(s => s.element === entry.target);
                if (!section) return;

                // Se la sezione è visibile e non è quella corrente
                if (entry.isIntersecting && this.currentSection !== section.id) {
                    this.currentSection = section.id;
                    this.debug('Section now visible:', section.id);

                    // Mostra un suggerimento contestuale dopo un breve ritardo
                    // ma solo se l'assistente non è già aperto
                    if (!this.isOpen) {
                        // Cancella eventuali timeout precedenti
                        if (this.suggestionTimeout !== null) {
                            window.clearTimeout(this.suggestionTimeout);
                            this.debug('Cleared previous suggestion timeout');
                        }

                        // Imposta un nuovo timeout per mostrare il suggerimento
                        this.suggestionTimeout = window.setTimeout(() => {
                            this.debug('Showing suggestion for section:', section.id);
                            this.showSuggestion(section.suggestion, section.id);
                        }, 2000); // Mostra dopo 2 secondi nella sezione
                    }
                }
            });
        }, {
            threshold: 0.3 // Trigger quando almeno il 30% della sezione è visibile
        });

        // Osserva tutte le sezioni
        this.sections.forEach(section => {
            observer.observe(section.element);
            this.debug('Observing section:', section.id);
        });
    }

    /**
     * Inizializza listener per azioni utente
     */
    private initUserActionListeners(): void {
        this.debug('Initializing user action listeners');

        // Reagisci quando l'utente clicca su CTA importanti
        document.querySelectorAll('a[href*="register"], a[href*="connect-wallet"], [data-action*="connect"]').forEach(el => {
            el.addEventListener('click', () => {
                // Memorizza che l'utente ha mostrato interesse per la registrazione/connessione
                localStorage.setItem('natan_user_interested', 'true');
                this.debug('User showed interest in registration/connection');

                // Suggerisci l'assistenza appropriata dopo un breve ritardo
                setTimeout(() => {
                    if (!this.isOpen) {
                        this.debug('Showing onboarding suggestion');
                        this.showSuggestion("Posso aiutarti a iniziare! 👋", "onboarding");
                    }
                }, 1000);
            });
        });

        // Reagisci quando l'utente visita una collezione
        document.querySelectorAll('a[href*="collections"], .collection-card-nft a').forEach(el => {
            el.addEventListener('click', () => {
                // Memorizza che l'utente ha mostrato interesse per le collezioni
                localStorage.setItem('natan_viewed_collections', 'true');
                this.debug('User showed interest in collections');
            });
        });
    }

    /**
     * Inizializza suggerimenti al passaggio del mouse
     */
    private initHoverSuggestions(): void {
        this.debug('Initializing hover suggestions');

        // Elementi importanti da monitorare per hover
        const hoverSelectors = [
            // Login/Register e Connect buttons
            'a[href*="register"], a[href*="login"], [data-action*="connect-modal"]',
            // Collezioni e EGI cards
            '.collection-card-nft, a[href*="collections"]',
            // Contatori impatto
            '.nft-stats-section [data-counter]',
            // EPP sections
            'section[aria-labelledby="environmental-impact-heading"] article'
        ];

        hoverSelectors.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            this.debug(`Found ${elements.length} elements for hover selector: ${selector}`);

            elements.forEach(element => {
                // Aggiungi event listeners per mouseenter/mouseleave
                element.addEventListener('mouseenter', () => {
                    // Determina il tipo di elemento per personalizzare il suggerimento
                    let suggestion = '';
                    let sectionId = '';

                    if (element.matches('a[href*="register"], a[href*="login"]')) {
                        suggestion = "Registrati per creare la tua galleria 🎨";
                        sectionId = "register";
                    } else if (element.matches('[data-action*="connect-modal"]')) {
                        suggestion = "Connetti il wallet per iniziare 💼";
                        sectionId = "wallet";
                    } else if (element.matches('.collection-card-nft, a[href*="collections"]')) {
                        suggestion = "Esplora questa collezione di EGI 🔍";
                        sectionId = "collections";
                    } else if (element.matches('.nft-stats-section [data-counter]')) {
                        suggestion = "Impatto reale verificabile 🌱";
                        sectionId = "impact";
                    } else if (element.matches('section[aria-labelledby="environmental-impact-heading"] article')) {
                        suggestion = "Scopri come contribuiamo all'ambiente 🌍";
                        sectionId = "epp";
                    }

                    if (suggestion) {
                        this.debug('Showing quick suggestion on hover:', suggestion);
                        this.showQuickSuggestion(suggestion, sectionId, element as HTMLElement);
                    }
                });

                element.addEventListener('mouseleave', () => {
                    this.hideQuickSuggestion();
                });
            });
        });
    }

    /**
     * Mostra un suggerimento rapido al passaggio del mouse
     */
    private showQuickSuggestion(text: string, sectionId: string, element: HTMLElement): void {
        // Nascondi eventuali suggerimenti esistenti
        this.hideQuickSuggestion();
        this.debug('Showing quick suggestion:', text);

        // Crea l'elemento del suggerimento
        const suggestionEl = document.createElement('div');
        suggestionEl.id = 'natan-quick-suggestion';
        suggestionEl.className = 'absolute bg-gray-900/95 text-xs font-medium text-emerald-300 px-3 py-1.5 rounded-full border border-emerald-600/30 shadow-lg z-[10000] transition-opacity duration-200 opacity-0 pointer-events-none';
        suggestionEl.textContent = text;
        document.body.appendChild(suggestionEl);
        this.debug('Created quick suggestion element');

        // Posiziona il suggerimento vicino all'elemento
        const rect = element.getBoundingClientRect();
        const top = rect.top + window.scrollY - 30; // Sopra l'elemento
        const left = rect.left + rect.width / 2; // Centrato

        suggestionEl.style.top = `${top}px`;
        suggestionEl.style.left = `${left}px`;
        suggestionEl.style.transform = 'translateX(-50%)';
        this.debug('Positioned quick suggestion at:', { top, left });

        // Rendi visibile con un breve ritardo
        setTimeout(() => {
            suggestionEl.style.opacity = '1';
            this.debug('Made quick suggestion visible');
        }, 50);
    }

    /**
     * Nasconde i suggerimenti rapidi
     */
    private hideQuickSuggestion(): void {
        const suggestionEl = document.getElementById('natan-quick-suggestion');
        if (suggestionEl) {
            this.debug('Hiding quick suggestion');
            suggestionEl.style.opacity = '0';
            setTimeout(() => {
                suggestionEl.remove();
                this.debug('Removed quick suggestion element');
            }, 200);
        }
    }

    /**
     * Verifica lo storico dell'utente
     */
    private checkUserHistory(): void {
        this.debug('Checking user history');

        // Controlla se l'utente ha già interagito con il sito
        const hasViewedCollections = localStorage.getItem('natan_viewed_collections') === 'true';
        const isInterested = localStorage.getItem('natan_user_interested') === 'true';

        this.debug('User history:', { hasViewedCollections, isInterested });

        // Personalizza il comportamento di Natan in base alla storia dell'utente
        if (hasViewedCollections && !this.hasOpenedAssistant()) {
            // L'utente ha visto collezioni ma non ha ancora usato l'assistente
            setTimeout(() => {
                this.debug('Showing collection suggestion based on history');
                this.showSuggestion("Ti interessa creare la tua collezione? 🎨", "collections");
            }, 3000);
        } else if (isInterested) {
            // L'utente ha mostrato interesse a registrarsi/connettersi
            setTimeout(() => {
                this.debug('Showing onboarding suggestion based on history');
                this.showSuggestion("Hai bisogno di aiuto per iniziare? 👋", "onboarding");
            }, 3000);
        }
    }

    /**
     * Verifica se l'assistente è stato aperto
     */
    private hasOpenedAssistant(): boolean {
        const hasOpened = localStorage.getItem('natan_assistant_opened') === 'true';
        this.debug('Has user opened assistant before?', hasOpened);
        return hasOpened;
    }
}
