console.log('DeleteProposalwallet.js caricato');

/**
  * @translation_abstract Gestisce l'eliminazione delle proposte di wallet e l'aggiornamento dell'UI.
  */
class DeleteProposalWallet {
    /**
      * @translation_desc Utilizziamo il pattern Singleton per assicurarci che ci sia una sola istanza di questa classe.
      */
    static instance = null;

     /**
      * @translation_desc Costruttore della classe.
      * @translation_desc Inizializza la classe e ne crea una sola istanza.
      */
    constructor() {
        console.log('App constructed');
        if (DeleteProposalWallet.instance) return DeleteProposalWallet.instance;
        DeleteProposalWallet.instance = this;
        this.init();
    }

    /**
     * @translation_desc Inizializza i listener degli eventi.
     */
    init() {
        /** @translation_desc Aggiungiamo listener per l'apertura della modale di creazione wallet */
        document.addEventListener('click', this.handleOpenModal.bind(this));

        /** @translation_desc Otteniamo il riferimento all'elemento della lista dei wallet */
        this.walletList = document.getElementById('wallet-list');
        if (!this.walletList) return;

        /** @translation_desc Aggiungiamo listener per l'eliminazione di una proposta di wallet */
        this.walletList.addEventListener('click', this.handleClick.bind(this));
    }

    /**
     * @translation_desc Gestisce l'apertura della modale di creazione wallet.
     * @param {Event} event - L'evento click.
     */
    async handleOpenModal(event) {
        /** @translation_desc Otteniamo il riferimento al bottone per l'apertura della modale */
        const openModalButton = event.target.closest('.create-wallet-btn');
        if (!openModalButton) return;

         /** @translation_desc Otteniamo gli id della collection e dell'utente dal dataset del bottone */
        const collectionId = openModalButton.dataset.collectionId;
        const userId = openModalButton.dataset.userId;

        /** @translation_desc Emettiamo un evento Livewire per aprire la modale di creazione del wallet */
        Livewire.dispatch('openForCreateNewWallets', { collectionId, userId });
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
        Swal.fire({
             title: window.translations['collection.wallet.confirmation_title'],
            text: window.translations['collection.wallet.confirmation_text'].replace(':walletId', walletId),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText:  window.translations['collection.wallet.confirm_delete'],
            cancelButtonText:  window.translations['collection.wallet.cancel_delete'],
            customClass: {
                confirmButton: 'btn btn-danger',  /** @translation_desc Stile del pulsante di conferma */
                cancelButton: 'btn btn-secondary'  /** @translation_desc Stile del pulsante di annullamento */
            },
            buttonsStyling: false /** @translation_desc Evitiamo gli stili di default dei pulsanti di SweetAlert2 */
        }).then(async (result) => {
             /** @translation_desc Se l'utente conferma l'eliminazione, procediamo con la chiamata alla funzione deleteWallet */
            if (result.isConfirmed) {
                await this.deleteWallet(walletId, collectionId, userId);
            }
        });
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
            const response = await fetch(`/wallets/${walletId}`, {
                method: 'DELETE',  /** @translation_desc Metodo HTTP DELETE */
                headers: {
                    /** @translation_desc Includiamo il token CSRF */
                   'X-CSRF-TOKEN': csrfToken,
                   /** @translation_desc Accettiamo la risposta in formato JSON */
                   'Accept': 'application/json',
                   /** @translation_desc Includiamo il tipo di contenuto JSON nel body */
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ collection_id: collectionId }),  /** @translation_desc Body della richiesta, inviamo solo l'id della collection */
            });

            /** @translation_desc Parsiamo la risposta JSON */
            const data = await response.json();
            if (response.ok) {
                 /** @translation_desc Se la risposta è OK, rimuoviamo l'elemento del wallet dalla lista */
                 const walletElement = document.getElementById(`wallet-${walletId}`);
                if (walletElement) walletElement.remove();

                 /** @translation_desc Chiamiamo la funzione per mostrare il bottone "Crea Wallet" */
                this.showCreateButton(collectionId, userId);
            } else {
                 /** @translation_desc Se la risposta non è OK, lanciamo un errore */
                 throw new Error(data.message || window.translations['collection.wallet.deletion_error']);
             }
         } catch (error) {
            /** @translation_desc Gestiamo gli eventuali errori */
            console.error('Errore:', error);
            alert(error.message ||  window.translations['collection.wallet.deletion_error_generic']);
         }
     }

     /**
      * @translation_desc Mostra il bottone "Crea Wallet" nella card dell'utente corretto.
      * @param {string} collectionId - L'id della collection a cui appartiene il wallet.
      * @param {string} userId - L'id dell'utente a cui appartiene il wallet.
      */
    showCreateButton(collectionId, userId) {
        console.log('showCreateButton:', collectionId, userId);
       /** @translation_desc Otteniamo il riferimento alla card dell'utente */
        const userCard = document.querySelector(`[data-user-id="${userId}"][data-collection-id="${collectionId}"]`);
         console.log('userCard:', userCard);
        if (userCard) {
             /** @translation_desc Otteniamo il riferimento al bottone "Crea Wallet" già esistente */
            const existingButton = userCard.querySelector('.create-wallet-btn');
            console.log('existingButton:', existingButton);
            if (!existingButton) {
               /** @translation_desc Se il bottone non esiste, lo creiamo */
                const button = document.createElement('button');
                console.log('button:', button);
                /** @translation_desc Aggiungiamo le classi necessarie */
                button.classList.add('create-wallet-btn', 'btn', 'btn-primary', 'w-full', 'sm:w-auto');
               /** @translation_desc Impostiamo l'id della collection come dataset */
                button.dataset.collectionId = collectionId;
                /** @translation_desc Impostiamo l'id dell'utente come dataset */
                button.dataset.userId = userId;
                /** @translation_desc Impostiamo il testo del bottone */
                button.textContent =  window.translations['collection.wallet.create_the_wallet'];
                /** @translation_desc Aggiungiamo il bottone alla card dell'utente */
               userCard.appendChild(button);
            }
        }
    }
}

/** @translation_desc Inizializziamo la classe quando il DOM è pronto. */
document.addEventListener('DOMContentLoaded', () => {
    new DeleteProposalWallet();
});
