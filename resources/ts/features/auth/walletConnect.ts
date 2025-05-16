    // File: resources/ts/features/auth/walletConnect.ts
    /**
     * 📜 Oracode TypeScript Module: SecureWalletConnectHandler
     * 🎯 Purpose: Manages wallet connection with Secret Link security
     * 🛡️ Security: Two-factor authentication (wallet + secret)
     *
     * @version 2.0.0
     * @date 2025-05-13
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
    interface WalletConnectResponse {
        success: boolean;
        message: string;
        requires_secret?: boolean;
        wallet_address?: string;
        user_status?: string;
        user_name?: string;
        secret?: string;
        show_secret_warning?: boolean;
    }

    // --- STATE ---
    let currentWalletAddress: string = '';
    let isConnecting: boolean = false;

    /**
     * 📜 Opens wallet connection modal with Secret Link system
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

        // Reset form state
        resetModalState(DOM);

        // Show modal
        DOM.connectWalletModalEl.classList.remove('hidden');
        DOM.connectWalletModalEl.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        // Animate in
        const content = DOM.connectWalletModalEl.querySelector('#connect-wallet-content');
        console.log('Modal content element:', content);
        if (content) {
            console.log('Classes before animation:', content.className);

            // IMPORTANTE: Prima rimuovi le classi vecchie
            content.classList.remove('scale-95', 'opacity-0');

            // POI aggiungi le nuove
            content.classList.add('scale-100', 'opacity-100');

            console.log('Classes after animation:', content.className);
        }

        DOM.connectWalletAddressInputEl?.focus();
    }

    /**
     * 📜 Handles wallet connection form submission
     */
    export async function handleSecureWalletSubmit(
        event: Event,
        config: AppConfig,
        DOM: typeof DOMElements,
        uploadModalMgr: any,
        uem: typeof UEM,
        uiUpdateCallback: () => void
    ): Promise<void> {
        event.preventDefault();

        if (isConnecting || !DOM.connectWalletFormEl) return;

        isConnecting = true;
        showLoadingState(DOM, true);
        hideError(DOM);

        try {
            const formData = new FormData(DOM.connectWalletFormEl);
            const walletAddress = formData.get('wallet_address') as string;
            const secret = formData.get('secret') as string;

            // Check for saved secret
            const savedSecret = localStorage.getItem(`secret_${walletAddress}`);
            if (savedSecret && !secret) {
                formData.set('secret', savedSecret);
            }

            currentWalletAddress = walletAddress;

            const response = await fetch(config.routes.walletConnect, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfTokenTS(),
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams(formData as any)
            });

            const data: WalletConnectResponse = await response.json();

            if (!response.ok) {
                showError(DOM, data.message || appTranslate('errorConnectionGeneric', config.translations));
                return;
            }

            if (data.requires_secret) {
                showSecretField(DOM);
                return;
            }

            if (data.show_secret_warning && data.secret) {
                showSecretDisplay(DOM, data.secret, walletAddress, uploadModalMgr, uiUpdateCallback);
                return;
            }

            // Success
            handleConnectionSuccess(data, walletAddress, DOM, uploadModalMgr, uiUpdateCallback);

        } catch (error) {
            console.error('Connection error:', error);
            showError(DOM, appTranslate('errorUnexpected', config.translations));
        } finally {
            isConnecting = false;
            showLoadingState(DOM, false);
        }
    }

    /**
     * 📜 Shows secret input field
     */
    function showSecretField(DOM: typeof DOMElements): void {
        const secretField = document.getElementById('secret-field');
        if (secretField) {
            secretField.classList.remove('hidden');
            DOM.connectWalletAddressInputEl?.setAttribute('readonly', 'true');
            document.getElementById('secret-input')?.focus();
        }
    }

    /**
     * 📜 Completes the connection process
     */
    function completeConnection(
        walletAddress: string,
        uploadModalMgr: any,
        uiUpdateCallback: () => void
    ): void {
        setWeakAuthWallet(walletAddress, uiUpdateCallback);
        closeSecureWalletModal(DOMElements);

        const pendingAction = consumePendingAuthAction();

        if (pendingAction === 'create-egi') {
            uploadModalMgr?.openModal('egi');
        } else if (pendingAction === 'create-collection') {
            window.location.href = '/collections/create';
        }

        // Success notification
        if (window.Swal) {
            window.Swal.fire({
                icon: 'success',
                title: appTranslate('walletConnectedTitle', {}),
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        }
    }

    /**
     * 📜 Displays generated secret for new users
     */
    function showSecretDisplay(
        DOM: typeof DOMElements,
        secret: string,
        walletAddress: string,
        uploadModalMgr: any,        // Aggiungi questo
        uiUpdateCallback: () => void // e questo
    ): void {
        const secretModal = document.getElementById('secret-display-modal');
        const generatedSecretEl = document.getElementById('generated-secret');

        if (!secretModal || !generatedSecretEl) return;

        generatedSecretEl.textContent = secret;
        secretModal.classList.remove('hidden');

        // Copy button
        const copyButton = document.getElementById('copy-secret-button');
        copyButton?.addEventListener('click', () => {
            navigator.clipboard.writeText(secret);
            copyButton.textContent = appTranslate('copied', {}) + ' ✓';
            setTimeout(() => {
                copyButton.textContent = appTranslate('wallet_copy_secret', {});
            }, 2000);
        });

        // Confirm button
        const confirmButton = document.getElementById('confirm-secret-saved');
        confirmButton?.addEventListener('click', () => {
            const saveLocally = (document.getElementById('save-secret-locally') as HTMLInputElement)?.checked;

            if (saveLocally) {
                localStorage.setItem(`secret_${walletAddress}`, secret);
            }

            secretModal.classList.add('hidden');

            // ORA COMPLETA LA CONNESSIONE!
            setWeakAuthWallet(walletAddress, uiUpdateCallback);
            closeSecureWalletModal(DOM);

            const pendingAction = consumePendingAuthAction();

            if (pendingAction === 'create-egi') {
                uploadModalMgr?.openModal('egi');
            } else if (pendingAction === 'create-collection') {
                window.location.href = '/collections/create';
            }

            // Success notification
            if (window.Swal) {
                window.Swal.fire({
                    icon: 'success',
                    title: appTranslate('walletConnectedTitle', {}),
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    }


    /**
     * 📜 Handles successful connection
     */
    function handleConnectionSuccess(
        data: WalletConnectResponse,
        walletAddress: string,
        DOM: typeof DOMElements,
        uploadModalMgr: any,
        uiUpdateCallback: () => void
    ): void {
        setWeakAuthWallet(walletAddress, uiUpdateCallback);
        closeSecureWalletModal(DOM);

        const pendingAction = consumePendingAuthAction();

        if (pendingAction === 'create-egi') {
            uploadModalMgr?.openModal('egi');
        } else if (pendingAction === 'create-collection') {
            window.location.href = '/collections/create';
        }

        // Success notification
        if (window.Swal) {
            window.Swal.fire({
                icon: 'success',
                title: appTranslate('walletConnectedTitle', {}),
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        }
    }

    /**
     * 📜 Shows/hides loading state
     */
    function showLoadingState(DOM: typeof DOMElements, loading: boolean): void {
        const button = document.getElementById('connect-wallet-submit') as HTMLButtonElement;
        const spinner = button?.querySelector('svg');
        const text = document.getElementById('connect-wallet-button-text');

        if (!button || !spinner || !text) return;

        button.disabled = loading;
        spinner.classList.toggle('hidden', !loading);
        text.textContent = loading ?
            appTranslate('connecting', {}) :
            appTranslate('wallet_connect_button', {});
    }

    /**
     * 📜 Shows error message
     */
    function showError(DOM: typeof DOMElements, message: string): void {
        const errorContainer = document.getElementById('wallet-error-container');
        const errorMessage = document.getElementById('wallet-error-message');

        if (errorContainer && errorMessage) {
            errorMessage.textContent = message;
            errorContainer.classList.remove('hidden');
        }
    }

    /**
     * 📜 Hides error message
     */
    function hideError(DOM: typeof DOMElements): void {
        const errorContainer = document.getElementById('wallet-error-container');
        if (errorContainer) {
            errorContainer.classList.add('hidden');
        }
    }

    /**
     * 📜 Resets modal to initial state
     */
    function resetModalState(DOM: typeof DOMElements): void {
        // Reset form
        (DOM.connectWalletFormEl as HTMLFormElement)?.reset();

        // Hide secret field
        const secretField = document.getElementById('secret-field');
        secretField?.classList.add('hidden');

        // Hide errors
        hideError(DOM);

        // Enable wallet input
        DOM.connectWalletAddressInputEl?.removeAttribute('readonly');

        // Check for saved secrets
        DOM.connectWalletAddressInputEl?.addEventListener('blur', checkSavedSecret);
    }

    /**
     * 📜 Checks if secret is saved locally
     */
    function checkSavedSecret(event: Event): void {
        const input = event.target as HTMLInputElement;
        const walletAddress = input.value;

        if (walletAddress && walletAddress.length === 58) {
            const savedSecret = localStorage.getItem(`secret_${walletAddress}`);
            if (savedSecret) {
                const secretInput = document.getElementById('secret-input') as HTMLInputElement;
                if (secretInput) {
                    secretInput.value = savedSecret;
                }
            }
        }
    }

    /**
     * 📜 Closes wallet connection modal
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

            const lastFocused = consumeLastFocusedElement();
            lastFocused?.focus();
        }, 300);
    }
