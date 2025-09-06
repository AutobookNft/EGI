export class RequestUpdateNotificationWallet {
    constructor(options = {}) {
        if (RequestUpdateNotificationWallet.instance) {
            console.warn(`⛔ Tentativo di inizializzazione multipla di RequestUpdateNotificationWallet ignorato`);
            return RequestUpdateNotificationWallet.instance;
        }
        this.options = options || { apiBaseUrl: '/notifications' };
        this.bindEvents();
        ensureTranslationsLoaded();
        console.log('🚀 RequestUpdateNotificationWallet initialized');
        RequestUpdateNotificationWallet.instance = this;
        return this;
    }

    /**
     * 🌐 Sistema di traduzione intelligente con fallback
     * Prova prima il sistema moderno appTranslate, poi il sistema deprecato
     */
    translate(key, fallback = key) {
        // Prova prima il sistema moderno
        if (typeof window.appTranslate === 'function') {
            try {
                const result = window.appTranslate(key, fallback);
                if (result && result !== key) {
                    return result;
                }
            } catch (error) {
                console.warn('🔄 appTranslate fallback to getTranslation for key:', key);
            }
        }
        
        // Fallback al sistema deprecato 
        if (typeof window.getTranslation === 'function') {
            return window.getTranslation(key, fallback);
        }
        
        // Ultimo fallback
        return fallback;
    }

    bindEvents() {
        document.querySelectorAll('.update-wallet-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const walletAddress = e.target.dataset.walletAddress;
                const old_royalty_mint = e.target.dataset.royaltyMint;
                const old_royalty_rebind = e.target.dataset.royaltyRebind;
                const collectionId = e.target.dataset.collectionId;
                const userId = parseInt(e.target.dataset.userId, 10);

                console.log("🔍 Valori recuperati:", { collectionId, userId, walletAddress });

                if (!collectionId || isNaN(userId)) {
                    console.error("❌ Errore: Manca collectionId o userId nel dataset!");
                    return;
                }

                await this.openUpdateWalletModal(collectionId, userId, walletAddress, old_royalty_mint, old_royalty_rebind);
            });
        });
    }

    async openUpdateWalletModal(collectionId, userId, walletAddress, old_royalty_mint, old_royalty_rebind) {
        console.log("🔍 Aprendo modale con:", { collectionId, userId, walletAddress });

        try {
            // 🔥 Assicura che le traduzioni siano caricate prima di aprire il modale
            await this.ensureTranslationsLoaded();

            const result = await Swal.fire({
                title: this.translate('collection.wallet.create_the_wallet'),
                html: await this.getCreateModalHtml(walletAddress, old_royalty_mint, old_royalty_rebind),
                showCancelButton: true,
                confirmButtonText: this.translate('collection.wallet.save'),
                cancelButtonText: this.translate('collection.wallet.cancel'),
                customClass: {
                    container: 'wallet-modal-container',
                    popup: 'wallet-modal-popup bg-gray-800',
                    input: 'wallet-modal-input'
                },
                preConfirm: () => this.validateAndCollectData(collectionId, userId, walletAddress, old_royalty_mint, old_royalty_rebind)
            });

            if (result.isConfirmed) {
                // console.log("✅ Dati ricevuti da Swal:", result.value);
                await this.handleUpdateWallet(result.value);
            }
        } catch (error) {
            console.error('Error in wallet modal:', error);
            this.showError(this.translate('collection.wallet.creation_error_generic'));
        }
    }

    validateAndCollectData(collectionId, userId, walletAddress, old_royalty_mint, old_royalty_rebind) {
        const form = document.getElementById('wallet-modal-form');

        if (!form) {
            console.error("❌ Errore: Il form #wallet-modal-form non esiste nel DOM!");
            return null;
        }

        const royaltyMint = parseFloat(form.querySelector('#royaltyMint')?.value) || 0;
        const royaltyRebind = parseFloat(form.querySelector('#royaltyRebind')?.value) || 0;

        if (!walletAddress) {
            console.error("❌ Errore: Indirizzo wallet mancante!");
            Swal.showValidationMessage(this.translate('collection.wallet.validation.address_required'));
            return null;
        }

        const data = { receiver_id: userId, collection_id: collectionId, wallet: walletAddress, royaltyMint, royaltyRebind, old_royalty_mint, old_royalty_rebind };
        console.log("✅ Dati raccolti correttamente:", data);
        return data;
    }

    async getCreateModalHtml(walletAddress, royaltyMint, royaltyRebind) {
        console.log("🔍 Caricamento HTML del modale...");

        return `
            <form id="wallet-modal-form" class="space-y-4">
                <div class="mb-3">
                    <label for="walletAddress" class="block text-sm font-medium text-gray-300">
                        ${this.translate('collection.wallet.address')}
                        <h2 class="text-xl font-medium text-gray-100">${walletAddress}</h2>
                    </label>
                </div>

                <div class="mb-3">
                    <label for="royaltyMint" class="block text-sm font-medium text-gray-300">
                        ${this.translate('collection.wallet.royalty_mint')}
                    </label>
                    <input type="number"
                           id="royaltyMint"
                           value = "${royaltyMint}"
                           class="swal2-input bg-gray-700 text-blue-500"
                           style="width: 90%; max-width: 350px; margin: auto; padding: 8px;"
                           step="0.01"
                           >
                </div>

                <div class="mb-3">
                    <label for="royaltyRebind" class="block text-sm font-medium text-gray-300">
                        ${this.translate('collection.wallet.royalty_rebind')}
                    </label>
                    <input type="number"
                           id="royaltyRebind"
                           value = "${royaltyRebind}"
                           class="swal2-input bg-gray-700 text-blue-500"
                           style="width: 90%; max-width: 350px; margin: auto; padding: 8px;"
                           step="0.01"
                           >
                </div>
            </form>
        `;
    }

    async handleUpdateWallet(data) {

        console.log('Fabio Update wallet with:', data);

        try {
            const response = await fetch(`/collections/${data.collection_id}/wallets/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            console.log('Wallet update successfully, response:', response);
            console.log('Wallet update successfully, result:', result);

            if (!response.ok) {
                throw new Error(result.message || 'Error update wallet');
            } else if (response.ok) {
                this.showSuccess(this.translate('collection.wallet.creation_success'));
                this.updateUI(result.data);
            } else {
                this.showError(result.message);
            }
        } catch (error) {
            console.error('Error update wallet:', error);
            this.showError(error);
        }
    }

    updateUI(data) {
        console.log('data', data);

        // const btn = document.querySelector(`[data-user-id="${data.receiver_id}"].create-wallet-btn`);
        // if (btn) btn.remove();

        window.location.reload();

        Swal.fire({
            icon: 'success',
            title: this.translate('collection.wallet.creation_success'),
            text: this.translate('collection.wallet.creation_success_detail'),
            timer: 3000,
            showConfirmButton: false
        });
    }

    showSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: this.translate('collection.wallet.success_title'),
            text: message,
            timer: 3000
        });
    }

    showError(message) {
        Swal.fire({
            icon: 'error',
            title: this.translate('collection.wallet.error.error_title'),
            text: message
        });
    }

    async ensureTranslationsLoaded() {
        if (!window.translations || Object.keys(window.translations).length === 0) {
            await this.fetchTranslations();
        }
    }
}

// Inizializzazione
export default RequestUpdateNotificationWallet;
