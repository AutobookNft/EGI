console.log('DeleteProposalWallet.js caricato');

import { ensureTranslationsLoaded } from '../../../js/utils/translations';

/**
 * @translation_abstract Gestisce l'eliminazione delle proposte di wallet e l'aggiornamento dell'UI.
 */
export class DeleteProposalWallet {
    /**
         * @translation_desc Utilizziamo il pattern Singleton per assicurarci che ci sia una sola istanza di questa classe.
         */
    static instance = null;

    /**
        * @translation_desc Costruttore della classe.
        * @translation_desc Inizializza la classe e ne crea una sola istanza.
        */
    constructor(options = {}) { // Aggiungi options per uniformità
        console.log('App constructed delete proposal wallet');
        if (DeleteProposalWallet.instance) {
            console.warn(`⛔ Tentativo di inizializzazione multipla di DeleteProposalWallet ignorato`);
            return DeleteProposalWallet.instance;
        }
        this.options = options || { apiBaseUrl: '/notifications' }; // Opzionale, per uniformità
        DeleteProposalWallet.instance = this;
        this.init();
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

    /**
        * @translation_desc Inizializza i listener degli eventi.
        */
    async init() {
        // Assicura che le traduzioni siano caricate
        await ensureTranslationsLoaded();

        /** @translation_desc Aggiungiamo listener per l'eliminazione di una proposta di wallet */
        document.addEventListener('click', this.handleClick.bind(this));
    }

    /**
     * @translation_desc Gestisce l'evento click sul bottone di eliminazione di una proposta.
     * @param {Event} event - L'evento click.
     */
    async handleClick(event) {
        /** @translation_desc Otteniamo il riferimento al bottone per l'eliminazione */
        const deleteButton = event.target.closest('.delete-proposal-wallet');
        if (!deleteButton) return;

        /** @translation_desc Otteniamo gli id del wallet, della collection e dell'utente dal dataset del bottone */
        const walletId = deleteButton.dataset.id;
        const collectionId = deleteButton.dataset.collection;
        const userId = deleteButton.dataset.user;

        /** @translation_desc Mostriamo una modale di conferma prima di procedere con l'eliminazione */
        this.showConfirmationModal(walletId, collectionId, userId);
    }

    /**
     * @translation_desc Mostra una modale di conferma per l'eliminazione usando SweetAlert2.
     * @param {string} walletId - L'id del wallet da eliminare.
     * @param {string} collectionId - L'id della collection a cui appartiene il wallet.
     * @param {string} userId - L'id dell'utente a cui appartiene il wallet.
     */
    async showConfirmationModal(walletId, collectionId, userId) {
        /** @translation_desc Utilizziamo SweetAlert2 per mostrare la modale di conferma. */
        const result = await Swal.fire({
            title: this.translate('collection.wallet.confirmation_title'),
            text: this.translate('collection.wallet.confirmation_text', { walletId }),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: this.translate('collection.wallet.confirm_delete'),
            cancelButtonText: this.translate('collection.wallet.cancel_delete'),
            customClass: {
                confirmButton: 'btn btn-danger',  /** @translation_desc Stile del pulsante di conferma */
                cancelButton: 'btn btn-secondary'  /** @translation_desc Stile del pulsante di annullamento */
            },
            buttonsStyling: false /** @translation_desc Evitiamo gli stili di default dei pulsanti di SweetAlert2 */
        });

        /** @translation_desc Se l'utente conferma l'eliminazione, procediamo con la chiamata alla funzione deleteWallet */
        if (result.isConfirmed) {
            await this.deleteWallet(walletId, collectionId, userId);
        }
    }

    /**
     * @translation_desc Elimina una proposta di wallet tramite una chiamata fetch al backend.
     * @param {string} walletId - L'id del wallet da eliminare.
     * @param {string} collectionId - L'id della collection a cui appartiene il wallet.
     * @param {string} userId - L'id dell'utente a cui appartiene il wallet.
     */
    async deleteWallet(walletId, collectionId, userId) {
        /** @translation_desc Otteniamo il token CSRF dal meta tag */
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            console.error('CSRF token non trovato!');
            return;
        }

        try {
            /** @translation_desc Effettuiamo la chiamata fetch all'endpoint per l'eliminazione del wallet */
            const response = await fetch(`/collections/${collectionId}/wallets/${walletId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ collection_id: collectionId })
            });

            /** @translation_desc Parsiamo la risposta JSON */
            const data = await response.json();
            if (response.ok) {
                /** @translation_desc Se la risposta è OK, rimuoviamo l'elemento del wallet dalla lista */
                document.getElementById(`wallet-${walletId}`)?.remove();

                /** @translation_desc Chiamiamo la funzione per mostrare il bottone "Crea Wallet" */
                window.location.reload();
                // this.showCreateButton(collectionId, userId);

                Swal.fire({
                    icon: 'success',
                    title: this.translate('collection.wallet.deletion_success'),
                    text: this.translate('collection.wallet.deletion_success_text'),
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                throw new Error(data.message || this.translate('collection.wallet.deletion_error'));
            }
        } catch (error) {
            console.error('Errore:', error);
            Swal.fire({
                icon: 'error',
                title: this.translate('collection.wallet.error_title'),
                text: error.message || this.translate('collection.wallet.deletion_error_generic')
            });
        }
    }

    /**
     * @translation_desc Mostra il bottone "Crea Wallet" nella card dell'utente corretto.
     * @param {string} collectionId - L'id della collection a cui appartiene il wallet.
     * @param {string} userId - L'id dell'utente a cui appartiene il wallet.
     */
    showCreateButton(collectionId, userId) {
        console.log('showCreateButton:', collectionId, userId);
        const userCard = document.querySelector(`[data-user-id="${userId}"][data-collection-id="${collectionId}"]`);
        if (userCard && !userCard.querySelector('.create-wallet-btn')) {
            const button = document.createElement('button');
            button.classList.add('create-wallet-btn', 'btn', 'btn-primary', 'w-full', 'sm:w-auto');
            button.dataset.collectionId = collectionId;
            button.dataset.userId = userId;
            button.textContent = this.translate('collection.wallet.create_the_wallet');
            userCard.appendChild(button);
        }
    }
}

/** @translation_desc Inizializziamo la classe quando il DOM è pronto. */
document.addEventListener('DOMContentLoaded', () => {
    new DeleteProposalWallet();
});
