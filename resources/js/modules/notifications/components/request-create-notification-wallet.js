console.log('🚀 RequestNotificationWallet loaded');

export class RequestCreateNotificationWallet {
    constructor() {
        this.bindEvents();
        ensureTranslationsLoaded(); // 🔥 Carica subito le traduzioni!
        console.log('🚀 RequestNotificationWallet initialized');
    }

    bindEvents() {
        document.querySelectorAll('.create-wallet-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const collectionId = e.target.dataset.collectionId;
                const userId = parseInt(e.target.dataset.userId, 10);

                console.log("🔍 Valori recuperati:", { collectionId, userId });

                if (!collectionId || isNaN(userId)) {
                    console.error("❌ Errore: Manca collectionId o userId nel dataset!");
                    return;
                }

                await this.openCreateWalletModal(collectionId, userId);
            });
        });
    }

    async openCreateWalletModal(collectionId, userId) {
        console.log("🔍 Aprendo modale con:", { collectionId, userId });

        try {
            // 🔥 Assicura che le traduzioni siano caricate prima di aprire il modale
            await this.ensureTranslationsLoaded();

            const result = await Swal.fire({
                title: window.getTranslation('collection.wallet.create_the_wallet'),
                html: await this.getCreateModalHtml(),
                showCancelButton: true,
                confirmButtonText: window.getTranslation('collection.wallet.save'),
                cancelButtonText: window.getTranslation('collection.wallet.cancel'),
                customClass: {
                    container: 'wallet-modal-container',
                    popup: 'wallet-modal-popup bg-gray-800',
                    input: 'wallet-modal-input'
                },
                preConfirm: () => this.validateAndCollectData(collectionId, userId)
            });

            if (result.isConfirmed) {
                console.log("✅ Dati ricevuti da Swal:", result.value);
                await this.handleCreateWallet(result.value);
            }
        } catch (error) {
            console.error('Error in wallet modal:', error);
            this.showError(window.getTranslation('collection.wallet.creation_error_generic'));
        }
    }

    validateAndCollectData(collectionId, userId) {
        const form = document.getElementById('wallet-modal-form');

        if (!form) {
            console.error("❌ Errore: Il form #wallet-modal-form non esiste nel DOM!");
            return null;
        }

        const walletAddress = form.querySelector('#walletAddress')?.value.trim();
        const royaltyMint = parseFloat(form.querySelector('#royaltyMint')?.value) || 0;
        const royaltyRebind = parseFloat(form.querySelector('#royaltyRebind')?.value) || 0;

        if (!walletAddress) {
            console.error("❌ Errore: Indirizzo wallet mancante!");
            Swal.showValidationMessage(window.getTranslation('collection.wallet.validation.address_required'));
            return null;
        }

        const data = { receiver_id: userId, collection_id: collectionId, wallet: walletAddress, royaltyMint, royaltyRebind };
        console.log("✅ Dati raccolti correttamente:", data);
        return data;
    }

    async getCreateModalHtml() {
        console.log("🔍 Caricamento HTML del modale...");

        return `
            <form id="wallet-modal-form" class="space-y-4">
                <div class="mb-3">
                    <label for="walletAddress" class="block text-sm font-medium text-gray-300">
                        ${window.getTranslation('collection.wallet.address')}
                    </label>
                    <input type="text"
                           id="walletAddress"
                           class="swal2-input bg-gray-700 text-white"
                           style="width: 90%; max-width: 350px; margin: auto; padding: 8px;"
                           placeholder="${window.getTranslation('collection.wallet.address_placeholder')}">
                </div>

                <div class="mb-3">
                    <label for="royaltyMint" class="block text-sm font-medium text-gray-300">
                        ${window.getTranslation('collection.wallet.royalty_mint')}
                    </label>
                    <input type="number"
                           id="royaltyMint"
                           class="swal2-input bg-gray-700 text-white"
                           style="width: 90%; max-width: 350px; margin: auto; padding: 8px;"
                           step="0.01"
                           placeholder="${window.getTranslation('collection.wallet.royalty_mint_placeholder')}">
                </div>

                <div class="mb-3">
                    <label for="royaltyRebind" class="block text-sm font-medium text-gray-300">
                        ${window.getTranslation('collection.wallet.royalty_rebind')}
                    </label>
                    <input type="number"
                           id="royaltyRebind"
                           class="swal2-input bg-gray-700 text-white"
                           style="width: 90%; max-width: 350px; margin: auto; padding: 8px;"
                           step="0.01"
                           placeholder="${window.getTranslation('collection.wallet.royalty_rebind_placeholder')}">
                </div>
            </form>
        `;
    }

    async handleCreateWallet(data) {
        console.log('Creating wallet with:', data);

        try {
            const response = await fetch(`/collections/${data.collection_id}/wallets/create`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            console.log('Wallet created successfully, response:', response);
            console.log('Wallet created successfully, result:', result);

            if (!response.ok) {
                throw new Error(result.message || 'Error creating wallet');
            } else if (response.ok) {
                this.showSuccess(window.getTranslation('collection.wallet.creation_success'));
                this.updateUI(result.data);
            } else {
                this.showError(result.message);
            }
        } catch (error) {
            console.error('Error creating wallet:', error);
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
            title: window.getTranslation('collection.wallet.creation_success'),
            text: window.getTranslation('collection.wallet.creation_success_detail'),
            timer: 3000,
            showConfirmButton: false
        });
    }

    showSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: window.getTranslation('collection.wallet.success_title'),
            text: message,
            timer: 3000
        });
    }

    showError(message) {
        Swal.fire({
            icon: 'error',
            title: window.getTranslation('collection.wallet.error.error_title'),
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
export default RequestCreateNotificationWallet;
