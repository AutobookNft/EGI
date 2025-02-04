console.log('DeleteProposalInvitation.js caricato');


class DeleteProposalInvitation {
    static instance = null;

    constructor() {
        console.log('DeleteProposalInvitation constructed');
        if (DeleteProposalInvitation.instance) return DeleteProposalInvitation.instance;
        DeleteProposalInvitation.instance = this;
        this.init();
    }

    init() {
        document.addEventListener('click', this.handleClick.bind(this));
        this.invitationList = document.getElementById('invitation-list');
        if (!this.invitationList) return;
        this.invitationList.addEventListener('click', this.handleClick.bind(this));
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
            title: window.translations['collection.invitation.confirmation_title'],
            text: window.translations['collection.invitation.confirmation_text'].replace(':invitationId', invitationId),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: window.translations['collection.invitation.confirm_delete'],
            cancelButtonText: window.translations['collection.invitation.cancel_delete'],
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

    async deleteInvitation(invitationId, collectionId, userId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) {
            console.error('CSRF token non trovato!');
            return;
        }

        try {
            const response = await fetch(`/invitations/${invitationId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ collection_id: collectionId }),
            });

            const data = await response.json();
            if (response.ok) {
                const invitationElement = document.getElementById(`invitation-${invitationId}`);
                if (invitationElement) invitationElement.remove();
                this.showCreateButton(collectionId, userId);
            } else {
                throw new Error(data.message || window.translations['collection.invitation.deletion_error']);
            }
        } catch (error) {
            console.error('Errore:', error);
            alert(error.message || window.translations['collection.invitation.deletion_error_generic']);
        }
    }

    showCreateButton(collectionId, userId) {
        console.log('showCreateButton:', collectionId, userId);
        const userCard = document.querySelector(`[data-user-id="${userId}"][data-collection-id="${collectionId}"]`);
        console.log('userCard:', userCard);
        if (userCard) {
            const existingButton = userCard.querySelector('.create-invitation-btn');
            console.log('existingButton:', existingButton);
            if (!existingButton) {
                const button = document.createElement('button');
                console.log('button:', button);
                button.classList.add('create-invitation-btn', 'btn', 'btn-primary', 'w-full', 'sm:w-auto');
                button.dataset.collectionId = collectionId;
                button.dataset.userId = userId;
                button.textContent = window.translations['collection.invitation.create_invitation'];
                userCard.appendChild(button);
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new DeleteProposalInvitation();
});
