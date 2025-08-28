<?php

namespace App\Livewire\Notifications\Invitations;

use App\Models\Collection;


use App\Services\Notifications\InvitationService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Spatie\Permission\Models\Role; // Importiamo i ruoli di Spatie
use Livewire\Attributes\Validate;
use App\Traits\HasPermissionTrait;

class InviteUserToCollectionModal extends Component {

    use HasPermissionTrait;

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $role = '';

    public array $roles = [];

    #[Validate('required|exists:collections,id')]
    public int $collectionId;

    public bool $show = false;

    private InvitationService $invitationService;


    public function boot(InvitationService $invitationService) {
        $this->invitationService = $invitationService;
    }

    public function mount($collectionId) {
        $this->collectionId = $collectionId;

        // Carica i ruoli disponibili da Spatie
        $this->roles = Role::pluck('name')->toArray(); // Recupera i nomi dei ruoli dalla tabella 'roles'
    }

    public function invite() {
        $this->validate();

        try {

            $collection = Collection::findOrFail($this->collectionId);

            // Verifica permessi per l'utente autenticato
            if (!$this->hasPermission($collection, 'add_team_member')) {
                session()->flash('error', __('collection.collaborators.add_denied'));
                return;
            }

            // CONTROLLO PREVENTIVO: Verifica se l'utente è già membro della collezione
            $invitedUser = \App\Models\User::where('email', $this->email)->first();


            if ($invitedUser) {
                $existingMember = \App\Models\CollectionUser::where('collection_id', $this->collectionId)
                    ->where('user_id', $invitedUser->id)
                    ->first();

                if ($existingMember) {
                    Log::channel('florenceegi')->warning('Tentativo di invitare utente già membro della collezione', [
                        'invited_email' => $this->email,
                        'invited_user_id' => $invitedUser->id,
                        'collection_id' => $this->collectionId,
                        'existing_role' => $existingMember->role,
                        'inviter_user_id' => auth()->id()
                    ]);

                    $this->addError('email', __('collection.invitation.user_already_member'));
                    return;
                }
            }

            $this->invitationService->createInvitation(
                $collection,
                $this->email,
                $this->role
            );

            $this->resetFields();
            $this->show = false;
            $this->dispatch('collection-member-updated');
        } catch (\Exception $e) {

            Log::channel('florenceegi')->error('Errore invito', [
                'error' => $e->getMessage(),
                'collection_id' => $this->collectionId
            ]);

            $this->addError(name: 'invitation', message: 'Errore durante su invio di invito');
        }

        $this->resetFields();

        $this->show = false;

        $this->dispatch('collection-member-updated'); // Aggiorna il genitore

    }

    #[On('openInviteModal')]
    public function openInviteModal() {
        Log::channel('florenceegi')->info('OpenInviteModal', [
            'collectionId' => $this->collectionId
        ]);
        $this->resetFields(); // Pulisce i campi
        $this->show = true; // Mostra la modale
    }

    public function resetFields() {
        $this->email = '';
        $this->role = '';
    }

    public function closeModal() {
        $this->show = false;
    }

    public function render() {
        return view('livewire.notifications.invitations.invite-user-to-collection-modal');
    }
}
