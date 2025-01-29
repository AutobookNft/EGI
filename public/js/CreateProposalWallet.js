console.log('CreateProposalwallet.js  caricato');

/**
 * @translation_abstract Gestisce la creazione di proposte di wallet.
 */
class CreateProposalWallet {

    /**
      * @translation_desc Costruttore della classe.
      * @translation_desc Inizializza la classe, recuperando i riferimenti al form e al token CSRF.
      */
    constructor() {
       /** @translation_desc Ottiene il riferimento al form della modale */
        this.modalForm = document.getElementById('wallet-modal-form');
        console.log('this.modalForm:', this.modalForm);

        /** @translation_desc Ottiene il token CSRF dal meta tag */
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        /** @translation_desc Chiama il metodo init per avviare i listener */
        this.init();
    }

    /**
     * @translation_desc Inizializza i listener degli eventi.
     */
    init() {
        console.log('Class CreateProposalwallet initialized');
        /** @translation_desc Verifica se il form è presente nel DOM */
        if (this.modalForm) {
            console.log('Form trovato');

             /** @translation_desc Aggiunge un listener per l'evento submit del form */
            this.modalForm.addEventListener('submit', this.handleSubmit.bind(this));

             /** @translation_desc Aggiunge un listener per i click sui pulsanti di creazione del wallet */
            document.addEventListener('click', this.handleOpenModal.bind(this));
        }
    }

    /**
     * @translation_desc Gestisce l'apertura della modale per la creazione di un nuovo wallet.
     * @param {Event} event - L'evento click.
     */
    async handleOpenModal(event) {
         /** @translation_desc Ottiene il riferimento al pulsante di apertura della modale più vicino all'elemento cliccato */
        const openModalButton = event.target.closest('.create-wallet-btn');

         console.log('openModalButton:', openModalButton);

        if (!openModalButton) return;

         /** @translation_desc Ottiene gli id della collection e dell'utente dai dataset del pulsante */
        const collectionId = openModalButton.dataset.collectionId;
        const userId = openModalButton.dataset.userId;

         console.log('collectionId:', collectionId);
         console.log('userId:', userId);

         /** @translation_desc Invia un evento Livewire per aprire la modale con gli ID corretti */
        Livewire.dispatch('openForCreateNewWallets', { collectionId, userId });
    }


    /**
     *  @translation_desc Gestisce l'invio del form per la creazione di un nuovo wallet.
     * @param {Event} event - L'evento submit del form.
     */
    async handleSubmit(event) {
         /** @translation_desc Impedisce il comportamento di default del form */
        event.preventDefault();

        /** @translation_desc Crea un oggetto FormData con i dati del form */
        const formData = new FormData(this.modalForm);
           const dataToSend = {
                    collection_id: formData.get('collection_id'),
                    user_id: formData.get('user_id'),
                    royalty_mint: formData.get('royaltyMint'),
                    royalty_rebind: formData.get('royaltyRebind'),
                    wallet_address: formData.get('walletAddress'),
                };

         console.log('Data to send to /wallets/create:', dataToSend);

        try {
            /** @translation_desc Effettua la chiamata fetch all'endpoint per la creazione del wallet */
            const response = await fetch('/wallets/create', {
                method: 'POST',  /** @translation_desc Metodo HTTP POST */
                headers: {
                   /** @translation_desc Includi il token CSRF */
                   'X-CSRF-TOKEN': this.csrfToken,
                    /** @translation_desc Accetta risposta JSON */
                   'Accept': 'application/json',
                   /** @translation_desc Includi il tipo di contenuto JSON nel body */
                     'Content-Type': 'application/json'
                },
                body: JSON.stringify(dataToSend)
            });

            /** @translation_desc Parsa la risposta JSON */
             const data = await response.json();
             console.log('Response from /wallets/create:', data);

            if (response.ok) {
                 /** @translation_desc Se la risposta è ok, ricarica la pagina */
                 location.reload();
             } else {
                 /** @translation_desc Se la risposta non è ok, lancia un errore */
                 const errorMessage = data.message || window.translations['collection.wallet.creation_error'];
                 throw new Error(errorMessage);
             }

         } catch (error) {
            /** @translation_desc Gestisci gli errori */
            console.error('Errore:', error);
           alert(error.message || window.translations['collection.wallet.creation_error_generic']);
        }
    }
}

/** @translation_desc Inizializza la classe quando il DOM è completamente caricato */
document.addEventListener('DOMContentLoaded', () => {
    new CreateProposalWallet();
});
