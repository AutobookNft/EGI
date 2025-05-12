// File: resources/ts/dom/domElements.ts

/**
 * 📜 Oracode TypeScript Module: DOMElementReferences
 * Centralizza l'acquisizione e l'esportazione dei riferimenti agli elementi DOM
 * utilizzati dall'applicazione client-side di FlorenceEGI.
 *
 * @version 1.1.0
 * @date 2025-05-10
 * @author Padmin D. Curtis (for Fabio Cherici)
 */

// --- HELPER PRIVATO PER L'ACQUISIZIONE ---
const getEl = <T extends HTMLElement>(id: string): T | null => document.getElementById(id) as T | null;
const queryEl = <T extends HTMLElement>(selector: string): T | null => document.querySelector(selector) as T | null;
const queryAllEl = <T extends HTMLElement>(selector: string): NodeListOf<T> | null => document.querySelectorAll(selector) as NodeListOf<T> | null;

// --- ESPORTAZIONI DIRETTE DEGLI ELEMENTI ---

// Modale Connessione Wallet
export const connectWalletModalEl = getEl<HTMLDivElement>('connect-wallet-modal');
export const closeConnectWalletButtonEl = getEl<HTMLButtonElement>('close-connect-wallet-modal');
export const connectWalletFormEl = getEl<HTMLFormElement>('connect-wallet-form');
export const connectWalletAddressInputEl = getEl<HTMLInputElement>('wallet_address');
export const walletErrorMessageEl = getEl<HTMLParagraphElement>('wallet-error-message');
export const connectSubmitButtonEl = connectWalletFormEl?.querySelector<HTMLButtonElement>('button[type="submit"]') || null;

// Navbar: Bottoni "Connect Wallet"
export const connectWalletButtonStdEl = getEl<HTMLButtonElement>('connect-wallet-button');
export const connectWalletButtonMobileEl = getEl<HTMLButtonElement>('connect-wallet-button-mobile');

// Navbar: Dropdown Wallet (Desktop)
export const walletDropdownContainerEl = getEl<HTMLDivElement>('wallet-dropdown-container');
export const walletDisplayTextEl = getEl<HTMLSpanElement>('wallet-display-text');
export const walletDropdownButtonEl = getEl<HTMLButtonElement>('wallet-dropdown-button');
export const walletDropdownMenuEl = getEl<HTMLDivElement>('wallet-dropdown-menu');
export const walletCopyAddressButtonEl = getEl<HTMLButtonElement>('wallet-copy-address');
export const walletDisconnectButtonEl = getEl<HTMLButtonElement>('wallet-disconnect');

// Navbar: Link Login/Register e Contenitore Mobile
export const loginLinkDesktopEl = queryEl<HTMLAnchorElement>('header nav.hidden.md\\:flex a[href*="login"]');
export const registerLinkDesktopEl = queryEl<HTMLAnchorElement>('header nav.hidden.md\\:flex a[href*="register"]');
export const mobileAuthButtonsContainerEl = queryEl<HTMLDivElement>('#mobile-menu .flex.justify-center.gap-3');

// Navbar: Bottoni "Create EGI" / "Create Collection" (trigger per guest)
export const createEgiGuestButtonsEl = queryAllEl<HTMLButtonElement>('[data-action="open-connect-modal-or-create-egi"]');
export const createCollectionGuestButtonsEl = queryAllEl<HTMLButtonElement>('[data-action="open-connect-modal-or-create-collection"]');

// Modale Upload EGI (Elementi per UploadModalManager)
export const uploadModalEl = getEl<HTMLDivElement>('upload-modal');
export const uploadModalCloseButtonEl = getEl<HTMLButtonElement>('close-upload-modal'); // Bottone "X"
export const uploadModalContentEl = getEl<HTMLDivElement>('upload-container'); // ID del contenuto della modale (da verificare)

// Menu Mobile
export const mobileMenuButtonEl = getEl<HTMLButtonElement>('mobile-menu-button');
export const mobileMenuEl = getEl<HTMLDivElement>('mobile-menu');
export const hamburgerIconEl = getEl<HTMLElement>('hamburger-icon');
export const closeIconEl = getEl<HTMLElement>('close-icon');

// Dropdown "Collection List"
export const collectionListDropdownContainerEl = getEl<HTMLDivElement>('collection-list-dropdown-container');
export const collectionListDropdownButtonEl = getEl<HTMLButtonElement>('collection-list-dropdown-button');
export const collectionListDropdownMenuEl = getEl<HTMLDivElement>('collection-list-dropdown-menu');
export const collectionListLoadingEl = getEl<HTMLDivElement>('collection-list-loading');
export const collectionListEmptyEl = getEl<HTMLDivElement>('collection-list-empty');
export const collectionListErrorEl = getEl<HTMLDivElement>('collection-list-error');

// Badge "Current Collection"
export const currentCollectionBadgeContainerEl = getEl<HTMLDivElement>('current-collection-badge-container');
export const currentCollectionBadgeLinkEl = getEl<HTMLAnchorElement>('current-collection-badge-link');
export const currentCollectionBadgeNameEl = getEl<HTMLSpanElement>('current-collection-badge-name');

// Form Logout (nascosto)
export const logoutFormEl = getEl<HTMLFormElement>('logout-form');

// Link "Collections" Generico (Desktop e Mobile)
export const genericCollectionsLinkDesktopEl = queryEl<HTMLAnchorElement>('header nav.hidden.md\\:flex > a[href$="/home/collections"]:not([data-action])');
export const genericCollectionsLinkMobileEl = queryEl<HTMLAnchorElement>('#mobile-menu > div:nth-child(1) > a[href$="/home/collections"]:not([data-action])'); // Verificare selettore


/**
 * 🎯 Funzione da chiamare in `main.ts` DOPO `DOMContentLoaded` se si preferisce
 *    un'inizializzazione esplicita invece di esportazioni dirette che si auto-eseguono.
 *    Con le esportazioni dirette come sopra, questa funzione non è strettamente necessaria
 *    a meno che non si voglia un log o un punto di controllo.
 */
export function confirmDOMReferencesLoaded(): void {
    // Verifica elementi critici (esempio)
    if (!connectWalletModalEl || !mobileMenuButtonEl || !uploadModalEl) {
        console.warn('DOMElementReferences: Alcuni elementi critici non sono stati trovati. Verifica gli ID e i selettori HTML.');
        // Qui si potrebbe usare UEM_Client se fosse già inizializzato, ma è un problema di dipendenza circolare potenziale.
        // Per ora, un console.warn è sufficiente.
    }
    console.log('Padmin D. Curtis: DOM references acquisition attempted.');
}