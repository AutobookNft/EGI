export class DeleteProposalInvitation {
    static instance = null;

    constructor(options = {}) { // Aggiungi options per uniformità
        console.log('DeleteProposalInvitation constructed');
        if (DeleteProposalInvitation.instance) {
            console.warn(`⛔ Tentativo di inizializzazione multipla di DeleteProposalInvitation ignorato`);
            return DeleteProposalInvitation.instance;
        }
        this.options = options || { apiBaseUrl: '/notifications' }; // Opzionale, per uniformità
        DeleteProposalInvitation.instance = this;
        this.init();
    }

    init() {
        document.addEventListener('click', this.handleClick.bind(this));
    }

    async handleClick(event) {
        const deleteButton = event.target.closest('.delete-proposal-invitation');
        if (!deleteButton) return;

        const invitationId = deleteButton.dataset.id;
        const collectionId = deleteButton.dataset.collection;
        const userId = deleteButton.dataset.user;

        this.showConfirmationModal(invitationId, collectionId, userId);
    }

    async showConfirmationModal(invitationId, collectionId, userId) {
        Swal.fire({
            title: window.getTranslation('collection.invitation.confirmation_title') || "Confirm Deletion",
            text: (window.getTranslation('collection.invitation.confirmation_text') || "Are you sure you want to delete invitation :invitationId?").replace(':invitationId', invitationId),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: window.translations['collection.invitation.confirm_delete'] || "Yes, delete",
            cancelButtonText: window.translations['collection.invitation.cancel_delete'] || "Cancel",
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        }).then(async (result) => {
            if (result.isConfirmed) {
                await this.deleteInvitation(invitationId, collectionId, userId);
            }
        });
    }

    async deleteInvitation(invitationId, id, userId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            console.error('CSRF token non trovato!');
            return;
        }

        try {
            const response = await fetch(`/collections/${id}/invitations/${invitationId}`, {

                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: invitationId }),
            });

            const data = await response.json();
            if (response.ok) {
                const invitationElement = document.querySelector(`[data-user-id="${userId}"][data-invitations-id="${invitationId}"]`);
                if (invitationElement) invitationElement.remove();
                // this.showCreateButton(collectionId, userId);
            } else {
                throw new Error(data.message || window.translations['collection.invitation.deletion_error']);
            }
        } catch (error) {
            console.error('Errore:', error);
            alert(error.message || window.translations['collection.invitation.deletion_error_generic']);
        }
    }

}

