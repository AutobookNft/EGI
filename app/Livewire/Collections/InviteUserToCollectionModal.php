<?php

namespace App\Livewire\Collections;

use App\Models\CollectionInvitation;
use App\Models\Collection;
use App\Models\User;
use App\Notifications\CollectionInvitationNotification;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\On;
use Spatie\Permission\Models\Role; // Importiamo i ruoli di Spatie

class InviteUserToCollectionModal extends Component
{
    public $email; // Email dell'utente da invitare
    public $role; // Ruolo dell'utente nella collection
    public $roles; // Ruoli disponibili
    public $collectionId; // ID della collection corrente
    public $show = false; // Gestisce la visibilità della modale

    public function mount($collectionId)
    {
        $this->collectionId = $collectionId;

        // Carica i ruoli disponibili da Spatie
        $this->roles = Role::pluck('name')->toArray(); // Recupera i nomi dei ruoli dalla tabella 'roles'
    }

    public function invite()
    {
        $this->validate([
            'email' => 'required|email',
            'role' => 'required|in:' . implode(',', $this->roles), // Validazione sui ruoli definiti
        ]);

        // Verifica se l'utente esiste già nel sistema
        $user = User::where('email', $this->email)->first();

        // Registra l'invito nella tabella `collection_invitations`
        $invitation = CollectionInvitation::create([
            'collection_id' => $this->collectionId,
            'email' => $this->email,
            'role' => $this->role,
            'status' => 'pending',
        ]);

        // Invia notifica all'utente, se esiste
        if ($user) {
            Notification::send($user, new CollectionInvitationNotification($invitation->id));
        }

        Log::channel('florenceegi')->info('Collection Invitation Sent', [
            'collection_id' => $this->collectionId,
            'email' => $this->email,
            'role' => $this->role,
        ]);

        $this->resetFields();
        $this->show = false;
        $this->dispatch('collection-member-updated'); // Aggiorna il genitore
    }

    #[On('openInviteModal')]
    public function showInviteModal()
    {
        $this->resetFields(); // Pulisce i campi
        $this->show = true; // Mostra la modale
    }

    public function resetFields()
    {
        $this->email = '';
        $this->role = '';
    }

    public function closeModal()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.collections.invite-user-to-collection-modal');
    }
}
