// File: resources/ts/dom/domElements.ts

/**
 * 📜 Oracode TypeScript Module: DOMElementReferences
 * Centralizza l'acquisizione e l'esportazione dei riferimenti agli elementi DOM
 * utilizzati dall'applicazione client-side di FlorenceEGI.
 *
 * @version 1.3.0 (Padmin - Added Contextual EGI Button Elements)
 * @date 2025-07-01
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

// --- HELPER PRIVATO PER L'ACQUISIZIONE ---
const getEl = <T extends HTMLElement>(id: string): T | null => document.getElementById(id) as T | null;
const queryEl = <T extends HTMLElement>(selector: string): T | null => document.querySelector(selector) as T | null;
const queryAllEl = <T extends HTMLElement>(selector: string): NodeListOf<T> | null => document.querySelectorAll(selector) as NodeListOf<T> | null;

// --- DICHIARAZIONE DELLE VARIABILI ESPORTATE (inizializzate a null) ---

// Wallet & Auth Modals
export let connectWalletModalEl: HTMLDivElement | null = null;
export let closeConnectWalletButtonEl: HTMLButtonElement | null = null;
export let connectWalletFormEl: HTMLFormElement | null = null;
export let connectWalletAddressInputEl: HTMLInputElement | null = null;
export let walletErrorMessageEl: HTMLParagraphElement | null = null;
export let connectSubmitButtonEl: HTMLButtonElement | null = null;
export let connectWalletButtonStdEl: HTMLButtonElement | null = null;
export let connectWalletButtonMobileEl: HTMLButtonElement | null = null;

// Navbar: Wallet Dropdown & Auth Links
export let walletDropdownContainerEl: HTMLDivElement | null = null;
export let walletDisplayTextEl: HTMLSpanElement | null = null;
export let walletDropdownButtonEl: HTMLButtonElement | null = null;
export let walletDropdownMenuEl: HTMLDivElement | null = null;
export let walletCopyAddressButtonEl: HTMLButtonElement | null = null;
export let walletDisconnectButtonEl: HTMLButtonElement | null = null;
export let loginLinkDesktopEl: HTMLAnchorElement | null = null;
export let registerLinkDesktopEl: HTMLAnchorElement | null = null;
export let mobileAuthButtonsContainerEl: HTMLDivElement | null = null;
export let mobileWalletInfoContainerEl: HTMLDivElement | null = null;
export let mobileWalletAddressEl: HTMLSpanElement | null = null;
export let mobileDashboardLinkEl: HTMLAnchorElement | null = null;
export let mobileCopyAddressButtonEl: HTMLButtonElement | null = null;
export let mobileDisconnectButtonEl: HTMLButtonElement | null = null;
export let logoutFormEl: HTMLFormElement | null = null;

// Navbar: Mobile Menu
export let mobileMenuButtonEl: HTMLButtonElement | null = null;
export let mobileMenuEl: HTMLDivElement | null = null;
export let hamburgerIconEl: HTMLElement | null = null;
export let closeIconEl: HTMLElement | null = null;

// Navbar: Collection Dropdown & Badge
export let collectionListDropdownContainerEl: HTMLDivElement | null = null;
export let collectionListDropdownButtonEl: HTMLButtonElement | null = null;
export let collectionListDropdownMenuEl: HTMLDivElement | null = null;
export let collectionListLoadingEl: HTMLDivElement | null = null;
export let collectionListEmptyEl: HTMLDivElement | null = null;
export let collectionListErrorEl: HTMLDivElement | null = null;
export let currentCollectionBadgeContainerEl: HTMLDivElement | null = null;
export let currentCollectionBadgeLinkEl: HTMLAnchorElement | null = null;
export let currentCollectionBadgeNameEl: HTMLSpanElement | null = null;

// Navbar: Link Generici
export let genericCollectionsLinkDesktopEl: HTMLAnchorElement | null = null;
export let genericCollectionsLinkMobileEl: HTMLAnchorElement | null = null;

// Navbar: Pulsanti Guest e Modali
export let createEgiGuestButtonsEl: NodeListOf<HTMLButtonElement> | null = null;
export let createCollectionGuestButtonsEl: NodeListOf<HTMLButtonElement> | null = null;
export let uploadModalEl: HTMLDivElement | null = null;
export let uploadModalCloseButtonEl: HTMLButtonElement | null = null;
export let uploadModalContentEl: HTMLDivElement | null = null;

// === NUOVI ELEMENTI PER PULSANTE CONTESTUALE "CREA EGI" ===
export let createEgiContextualButtonEl: HTMLButtonElement | null = null;
export let createEgiButtonTextEl: HTMLSpanElement | null = null;
export let createEgiButtonIconEl: SVGSVGElement | null = null;


/**
 * 📜 Oracode Function: initializeDOMReferences
 * 🎯 Acquisisce tutti i riferimenti agli elementi DOM e li assegna
 * alle variabili esportate da questo modulo.
 */
export function initializeDOMReferences(): void {
    connectWalletModalEl = getEl<HTMLDivElement>('connect-wallet-modal');
    closeConnectWalletButtonEl = getEl<HTMLButtonElement>('close-connect-wallet-modal');
    connectWalletFormEl = getEl<HTMLFormElement>('connect-wallet-form');
    connectWalletAddressInputEl = getEl<HTMLInputElement>('wallet_address');
    walletErrorMessageEl = getEl<HTMLParagraphElement>('wallet-error-message');
    connectSubmitButtonEl = connectWalletFormEl?.querySelector<HTMLButtonElement>('button[type="submit"]') || null;
    connectWalletButtonStdEl = getEl<HTMLButtonElement>('connect-wallet-button');
    connectWalletButtonMobileEl = getEl<HTMLButtonElement>('connect-wallet-button-mobile');
    walletDropdownContainerEl = getEl<HTMLDivElement>('wallet-dropdown-container');
    walletDisplayTextEl = getEl<HTMLSpanElement>('wallet-display-text');
    walletDropdownButtonEl = getEl<HTMLButtonElement>('wallet-dropdown-button');
    walletDropdownMenuEl = getEl<HTMLDivElement>('wallet-dropdown-menu');
    walletCopyAddressButtonEl = getEl<HTMLButtonElement>('wallet-copy-address');
    walletDisconnectButtonEl = getEl<HTMLButtonElement>('wallet-disconnect');
    loginLinkDesktopEl = queryEl<HTMLAnchorElement>('header nav.hidden.md\\:flex a[href*="login"]');
    registerLinkDesktopEl = queryEl<HTMLAnchorElement>('header nav.hidden.md\\:flex a[href*="register"]');
    mobileAuthButtonsContainerEl = queryEl<HTMLDivElement>('#mobile-menu .flex.justify-center.gap-3');
    createEgiGuestButtonsEl = queryAllEl<HTMLButtonElement>('[data-action="open-connect-modal-or-create-egi"]');
    createCollectionGuestButtonsEl = queryAllEl<HTMLButtonElement>('[data-action="open-connect-modal-or-create-collection"]');
    uploadModalEl = getEl<HTMLDivElement>('upload-modal');
    uploadModalCloseButtonEl = getEl<HTMLButtonElement>('close-upload-modal');
    uploadModalContentEl = getEl<HTMLDivElement>('upload-container');
    mobileMenuButtonEl = getEl<HTMLButtonElement>('mobile-menu-button');
    mobileMenuEl = getEl<HTMLDivElement>('mobile-menu');
    hamburgerIconEl = getEl<HTMLElement>('hamburger-icon');
    closeIconEl = getEl<HTMLElement>('close-icon');
    collectionListDropdownContainerEl = getEl<HTMLDivElement>('collection-list-dropdown-container');
    collectionListDropdownButtonEl = getEl<HTMLButtonElement>('collection-list-dropdown-button');
    collectionListDropdownMenuEl = getEl<HTMLDivElement>('collection-list-dropdown-menu');
    collectionListLoadingEl = getEl<HTMLDivElement>('collection-list-loading');
    collectionListEmptyEl = getEl<HTMLDivElement>('collection-list-empty');
    collectionListErrorEl = getEl<HTMLDivElement>('collection-list-error');
    currentCollectionBadgeContainerEl = getEl<HTMLDivElement>('current-collection-badge-container');
    currentCollectionBadgeLinkEl = getEl<HTMLAnchorElement>('current-collection-badge-link');
    currentCollectionBadgeNameEl = getEl<HTMLSpanElement>('current-collection-badge-name');
    logoutFormEl = getEl<HTMLFormElement>('logout-form');
    genericCollectionsLinkDesktopEl = queryEl<HTMLAnchorElement>('header nav.hidden.md\\:flex > a[href$="/home/collections"]:not([data-action])');
    genericCollectionsLinkMobileEl = queryEl<HTMLAnchorElement>('#mobile-menu > div:nth-child(1) > a[href$="/home/collections"]:not([data-action])');

    // === ACQUISIZIONE NUOVI ELEMENTI ===
    createEgiContextualButtonEl = queryEl<HTMLButtonElement>('.js-create-egi-contextual-button');
    createEgiButtonTextEl = queryEl<HTMLSpanElement>('.js-create-egi-button-text');
    createEgiButtonIconEl = document.querySelector<SVGSVGElement>('.js-create-egi-button-icon');

    // Mobile wallet info section
    mobileWalletInfoContainerEl = getEl<HTMLDivElement>('mobile-wallet-info-container');
    mobileWalletAddressEl = getEl<HTMLSpanElement>('mobile-wallet-address');
    mobileDashboardLinkEl = getEl<HTMLAnchorElement>('mobile-dashboard-link');
    mobileCopyAddressButtonEl = getEl<HTMLButtonElement>('mobile-copy-address');
    mobileDisconnectButtonEl = getEl<HTMLButtonElement>('mobile-disconnect');

    // console.log('Padmin D. Curtis: DOM references acquired/re-acquired via initializeDOMReferences().');
}

/**
 * 📜 Oracode Function: confirmDOMReferencesLoaded
 * 🎯 Esegue un controllo di base su alcuni elementi DOM critici.
 */
export function confirmDOMReferencesLoaded(): void {
    const criticalElements: { name: string, element: HTMLElement | NodeListOf<HTMLElement> | null }[] = [
        { name: 'mobileMenuButtonEl', element: mobileMenuButtonEl },
        { name: 'mobileMenuEl', element: mobileMenuEl },
        { name: 'connectWalletModalEl', element: connectWalletModalEl },
        { name: 'createEgiContextualButtonEl', element: createEgiContextualButtonEl }, // Aggiunto al check
        // Aggiungi altri se necessario
    ];
    // console.log('Padmin D. Curtis: Confirming critical DOM references post-initialization...');
    criticalElements.forEach(item => {
        if (!item.element || (item.element instanceof NodeList && item.element.length === 0)) {
            // console.warn(`DOMElementReferences Check: Critical element "${item.name}" NOT FOUND or empty.`);
        }
    });
}
