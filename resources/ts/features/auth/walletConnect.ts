// File: resources/ts/features/auth/walletConnect.ts
/**
 * 📜 Oracode TypeScript Module: Unified FEGI Wallet Connect Handler
 * 🎯 Purpose: Single modal with sections for streamlined FEGI flow
 * 🛡️ Security: FEGI-key based weak authentication
 *
 * @version 6.0.0 (Unified Modal System)
 * @date 2025-05-29
 * @author Padmin D. Curtis For Fabio Cherici
 */

import { AppConfig, ServerErrorResponse, appTranslate } from '../../config/appConfig';
import * as DOMElements from '../../dom/domElements';
import { UEM_Client_TS_Placeholder as UEM } from '../../services/uemClientService';
import {
    setPendingAuthAction,
    consumePendingAuthAction,
    setLastFocusedElement,
    consumeLastFocusedElement,
    setWeakAuthWallet
} from './authService';
import { getCsrfTokenTS } from '../../utils/csrf';

// --- TYPES ---
interface FegiConnectResponse {
    success: boolean;
    message: string;
    wallet_address?: string;
    fegi_key?: string;
    user_status?: string;
    user_name?: string;
    show_credentials_warning?: boolean;
}

// --- STATE ---
let isConnecting: boolean = false;
let currentModalSection: 'mode-selection' | 'fegi-input' | 'create-loading' | 'credentials-display' = 'mode-selection';

/**
 * 📜 Opens unified FEGI connection modal
 */
export function openSecureWalletModal(
    config: AppConfig,
    DOM: typeof DOMElements,
    pendingAction: 'create-egi' | 'create-collection' | null = null
): void {
    if (!DOM.connectWalletModalEl) {
        UEM.handleClientError('CLIENT_DOM_MISSING_CONNECT_MODAL');
        return;
    }

    setPendingAuthAction(pendingAction);
    setLastFocusedElement(document.activeElement as HTMLElement);

    // Reset modal state
    resetModalState();

    // Show modal
    DOM.connectWalletModalEl.classList.remove('hidden');
    DOM.connectWalletModalEl.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    // Animate in
    const content = DOM.connectWalletModalEl.querySelector('#connect-wallet-content');
    if (content) {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }

    // Setup event listeners
    setupModalEventListeners();

    // Show initial section
    showModalSection('mode-selection');

    // Focus first option
    const firstOption = document.getElementById('btn-use-existing-fegi');
    firstOption?.focus();
}

/**
 * 📜 Show specific modal section and update header
 */
function showModalSection(section: typeof currentModalSection): void {
    currentModalSection = section;

    // Hide all sections
    const sections = [
        'section-mode-selection',
        'section-fegi-input',
        'section-create-loading',
        'section-credentials-display'
    ];

    sections.forEach(sectionId => {
        const sectionEl = document.getElementById(sectionId);
        sectionEl?.classList.add('hidden');
    });

    // Show target section
    const targetSection = document.getElementById(`section-${section.replace('-', '-')}`);
    targetSection?.classList.remove('hidden');

    // Update header
    updateModalHeader(section);
}

/**
 * 📜 Update modal header based on current section
 */
function updateModalHeader(section: typeof currentModalSection): void {
    const title = document.getElementById('connect-wallet-title');
    const description = document.getElementById('connect-wallet-description');

    // Update icons
    const icons = ['icon-key', 'icon-plus', 'icon-check', 'icon-warning'];
    icons.forEach(iconId => {
        const icon = document.getElementById(iconId);
        icon?.classList.add('hidden');
    });

    switch (section) {
        case 'mode-selection':
            if (title) title.textContent = appTranslate('fegi_connect_title', {});
            if (description) description.textContent = appTranslate('fegi_modal_subtitle', {});
            document.getElementById('icon-key')?.classList.remove('hidden');
            break;

        case 'fegi-input':
            if (title) title.textContent = appTranslate('fegi_use_existing', {});
            if (description) description.textContent = appTranslate('fegi_input_subtitle', {});
            document.getElementById('icon-key')?.classList.remove('hidden');
            break;

        case 'create-loading':
            if (title) title.textContent = appTranslate('fegi_creating_account', {});
            if (description) description.textContent = appTranslate('fegi_please_wait', {});
            document.getElementById('icon-plus')?.classList.remove('hidden');
            break;

        case 'credentials-display':
            if (title) title.textContent = appTranslate('fegi_credentials_generated_title', {});
            if (description) description.textContent = appTranslate('fegi_save_credentials', {});
            document.getElementById('icon-warning')?.classList.remove('hidden');
            break;
    }
}

/**
 * 📜 Setup event listeners for unified modal
 */
function setupModalEventListeners(): void {
    // Remove existing listeners to prevent duplicates
    removeAllEventListeners();

    // Mode selection buttons
    document.getElementById('btn-use-existing-fegi')?.addEventListener('click', handleUseExistingFegi);
    document.getElementById('btn-create-new-account')?.addEventListener('click', handleCreateNewAccount);

    // Back button
    document.getElementById('btn-back-to-selection')?.addEventListener('click', () => showModalSection('mode-selection'));

    // FEGI form
    document.getElementById('fegi-input-form')?.addEventListener('submit', handleFegiSubmit);

    // Credentials actions
    document.getElementById('btn-copy-credentials')?.addEventListener('click', handleCopyCredentials);
    document.getElementById('btn-confirm-credentials-saved')?.addEventListener('click', handleConfirmCredentialsSaved);
}

/**
 * 📜 Remove all event listeners
 */
function removeAllEventListeners(): void {
    // Create new elements to remove all listeners (clean approach)
    const elementsToClean = [
        'btn-use-existing-fegi',
        'btn-create-new-account',
        'btn-back-to-selection',
        'fegi-input-form',
        'btn-copy-credentials',
        'btn-confirm-credentials-saved'
    ];

    elementsToClean.forEach(elementId => {
        const element = document.getElementById(elementId);
        if (element) {
            element.replaceWith(element.cloneNode(true));
        }
    });
}

/**
 * 📜 Handle "Use Existing FEGI" button click
 */
function handleUseExistingFegi(): void {
    showModalSection('fegi-input');

    // Focus FEGI input
    setTimeout(() => {
        const fegiInput = document.getElementById('fegi_key_input') as HTMLInputElement;
        fegiInput?.focus();

        // Check for saved FEGI
        checkSavedFegi();
    }, 100);
}

/**
 * 📜 Handle "Create New Account" button click
 */
function handleCreateNewAccount(): void {
    showModalSection('create-loading');
    submitCreateAccount();
}

/**
 * 📜 Handle FEGI form submission
 */
async function handleFegiSubmit(event: Event): Promise<void> {
    event.preventDefault();

    if (isConnecting) return;

    const form = event.target as HTMLFormElement;
    const formData = new FormData(form);
    const fegiKey = formData.get('fegi_key') as string;

    if (!fegiKey || !isValidFegiFormat(fegiKey)) {
        showError(appTranslate('errorInvalidFegiFormat', {}));
        return;
    }

    await submitFegiAuth(fegiKey);
}

/**
 * 📜 Submit FEGI authentication
 */
async function submitFegiAuth(fegiKey: string): Promise<void> {
    isConnecting = true;
    setLoadingState(true, appTranslate('connecting', {}));
    hideError();

    try {
        const response = await fetch('/wallet/connect', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfTokenTS(),
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                fegi_key: fegiKey
            })
        });

        const data: FegiConnectResponse = await response.json();

        if (!response.ok) {
            showError(data.message || appTranslate('errorConnectionGeneric', {}));
            return;
        }

        // Success - complete connection
        completeConnection(data.wallet_address!);

    } catch (error) {
        console.error('FEGI authentication error:', error);
        showError(appTranslate('errorUnexpected', {}));
    } finally {
        isConnecting = false;
        setLoadingState(false);
    }
}

/**
 * 📜 Submit create new account request
 */
async function submitCreateAccount(): Promise<void> {
    isConnecting = true;
    hideError();

    try {
        const response = await fetch('/wallet/connect', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfTokenTS(),
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                create_new: '1'
            })
        });

        const data: FegiConnectResponse = await response.json();

        if (!response.ok) {
            showError(data.message || appTranslate('errorAccountCreationFailed', {}));
            showModalSection('mode-selection'); // Torna alla selezione in caso di errore
            return;
        }

        // Success - show credentials in same modal
        if (data.show_credentials_warning && data.wallet_address && data.fegi_key) {
            showCredentialsInModal(data.wallet_address, data.fegi_key);
        } else {
            completeConnection(data.wallet_address!);
        }

    } catch (error) {
        console.error('Account creation error:', error);
        showError(appTranslate('errorUnexpected', {}));
        showModalSection('mode-selection'); // Torna alla selezione in caso di errore
    } finally {
        isConnecting = false;
    }
}

/**
 * 📜 Show credentials in the same modal (no second modal)
 */
function showCredentialsInModal(walletAddress: string, fegiKey: string): void {
    // Populate credential fields
    const algorandAddressEl = document.getElementById('display-algorand-address');
    const fegiKeyEl = document.getElementById('display-fegi-key');

    if (algorandAddressEl) algorandAddressEl.textContent = walletAddress;
    if (fegiKeyEl) fegiKeyEl.textContent = fegiKey;

    // Store credentials for later use
    (window as any).currentCredentials = { walletAddress, fegiKey };

    // Show credentials section
    showModalSection('credentials-display');
}

/**
 * 📜 Handle copy credentials button
 */
function handleCopyCredentials(): void {
    const credentials = (window as any).currentCredentials;
    if (!credentials) return;

    const credentialsText = `Algorand Address: ${credentials.walletAddress}\nFEGI Key: ${credentials.fegiKey}`;

    navigator.clipboard.writeText(credentialsText).then(() => {
        const copyBtn = document.getElementById('btn-copy-credentials');
        const copyText = document.getElementById('copy-credentials-text');

        if (copyText) {
            const originalText = copyText.textContent;
            copyText.textContent = appTranslate('copied', {}) + ' ✓';

            setTimeout(() => {
                if (copyText) copyText.textContent = originalText;
            }, 2000);
        }
    }).catch(error => {
        console.error('Copy failed:', error);
        showError(appTranslate('errorCopyFailed', {}));
    });
}

/**
 * 📜 Handle confirm credentials saved button
 */
function handleConfirmCredentialsSaved(): void {
    const credentials = (window as any).currentCredentials;
    if (!credentials) return;

    const saveLocally = (document.getElementById('save-fegi-locally') as HTMLInputElement)?.checked;

    if (saveLocally) {
        localStorage.setItem(`fegi_key_${credentials.walletAddress}`, credentials.fegiKey);
    }

    // Complete connection
    completeConnection(credentials.walletAddress);
}

/**
 * 📜 Complete the connection process
 */
function completeConnection(walletAddress: string): void {
    setWeakAuthWallet(walletAddress, () => {
        // UI update callback - will be handled by main.ts
    });

    closeSecureWalletModal(DOMElements);

    const pendingAction = consumePendingAuthAction();

    // Execute pending action
    if (pendingAction === 'create-egi') {
        const event = new CustomEvent('openUploadModal', { detail: { type: 'egi' } });
        document.dispatchEvent(event);
    } else if (pendingAction === 'create-collection') {
        window.location.href = '/collections/create';
    }

    // Success notification
    if (window.Swal) {
        window.Swal.fire({
            icon: 'success',
            title: appTranslate('walletConnectedTitle', {}),
            text: appTranslate('walletConnectedText', {}),
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    }

    // Trigger UI update event
    const uiUpdateEvent = new CustomEvent('fegiConnectionComplete', {
        detail: { walletAddress }
    });
    document.dispatchEvent(uiUpdateEvent);

    // Refresh della pagina dopo connessione weak completata
    setTimeout(() => {
        window.location.reload();
    }, 500); // Piccolo delay per permettere al toast di mostrarsi
}

/**
 * 📜 Validate FEGI key format
 */
function isValidFegiFormat(fegiKey: string): boolean {
    const fegiPattern = /^FEGI-\d{4}-[A-Z0-9]{15}$/;
    return fegiPattern.test(fegiKey);
}

/**
 * 📜 Check for saved FEGI keys in localStorage
 */
function checkSavedFegi(): void {
    // Look for any saved FEGI keys
    const savedKeys = [];
    for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i);
        if (key && key.startsWith('fegi_key_')) {
            const fegiKey = localStorage.getItem(key);
            if (fegiKey) {
                savedKeys.push(fegiKey);
            }
        }
    }

    // If we have saved FEGI keys, auto-fill the first one
    if (savedKeys.length > 0) {
        const fegiInput = document.getElementById('fegi_key_input') as HTMLInputElement;
        if (fegiInput) {
            fegiInput.value = savedKeys[0];
        }
    }
}

/**
 * 📜 Set loading state for buttons
 */
function setLoadingState(loading: boolean, loadingText?: string): void {
    const submitBtn = document.getElementById('fegi-submit-button') as HTMLButtonElement;
    const submitText = document.getElementById('fegi-submit-text');

    if (submitBtn && submitText) {
        submitBtn.disabled = loading;

        if (loading && loadingText) {
            submitText.textContent = loadingText;
        } else {
            submitText.textContent = appTranslate('fegi_connect_button', {});
        }
    }
}

/**
 * 📜 Show error message
 */
function showError(message: string): void {
    const errorContainer = document.getElementById('wallet-error-container');
    const errorMessage = document.getElementById('wallet-error-message');

    if (errorContainer && errorMessage) {
        errorMessage.textContent = message;
        errorContainer.classList.remove('hidden');

        // Auto-hide after 5 seconds
        setTimeout(() => {
            errorContainer.classList.add('hidden');
        }, 5000);
    }
}

/**
 * 📜 Hide error message
 */
function hideError(): void {
    const errorContainer = document.getElementById('wallet-error-container');
    if (errorContainer) {
        errorContainer.classList.add('hidden');
    }
}

/**
 * 📜 Reset modal to initial state
 */
function resetModalState(): void {
    // Show initial section
    currentModalSection = 'mode-selection';

    // Clear any stored credentials
    delete (window as any).currentCredentials;

    // Clear forms
    const fegiForm = document.getElementById('fegi-input-form') as HTMLFormElement;
    fegiForm?.reset();

    // Reset checkboxes
    const saveCheckbox = document.getElementById('save-fegi-locally') as HTMLInputElement;
    if (saveCheckbox) saveCheckbox.checked = false;

    // Hide errors
    hideError();

    // Reset loading states
    setLoadingState(false);
}

/**
 * 📜 Close wallet connection modal
 */
export function closeSecureWalletModal(DOM: typeof DOMElements): void {
    if (!DOM.connectWalletModalEl) return;

    const content = DOM.connectWalletModalEl.querySelector('#connect-wallet-content');
    if (content) {
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
    }

    setTimeout(() => {
        DOM.connectWalletModalEl?.classList.add('hidden');
        DOM.connectWalletModalEl?.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';

        // Clean up
        resetModalState();
        removeAllEventListeners();

        const lastFocused = consumeLastFocusedElement();
        lastFocused?.focus();
    }, 300);
}
