<?php

namespace App\Livewire\Collections;

use App\Models\Collection;
use App\Models\CollectionUser;
use App\Models\NotificationPayloadInvitation;
use App\Models\Wallet;
use App\Models\WalletChangeApproval;
use App\Models\NotificationPayloadWallet;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Traits\HasPermissionTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CollectionUserMember extends Component
{

    use HasPermissionTrait;

    public $collectionUsers; // Lista membri del team
    public $wallets;
    public $collection;
    public $collectionId;
    public $collectionName;
    public $collectionOwner; // Proprietario della collection
    public $walletProposals;
    public $invitationProposal;
    public $show = false; // Proprietà per gestire la visibilità della modale

    public function mount($id)
    {
        Log::channel('florenceegi')->info('Collection id', [
            'collectionId' => $id
        ]);

        $this->collectionId = $id;

        // Carica la collection e i suoi dati
        $this->loadCollectionData();

        // Carica i collaboratori della collection
        $this->loadTeamUsers();
    }

    public function loadCollectionData()
    {
        $this->collection = Collection::findOrFail($this->collectionId);

        $this->collectionName = $this->collection->collection_name;

        $this->collectionOwner = $this->collection->creator;
    }

    public function loadTeamUsers()
    {
        $this->collectionUsers = CollectionUser::where('collection_id', $this->collectionId)->get();

        $this->wallets = Wallet::where('collection_id', '=', $this->collectionId)->get();

        $this->walletProposals = NotificationPayloadWallet::where('proposer_id', '=', Auth::user()->id)
            ->where('status', 'LIKE', '%pending%')
            ->get();

        $this->invitationProposal = NotificationPayloadInvitation::where('proposer_id', '=', Auth::user()->id)
            ->where('status', 'LIKE', '%pending%')
            ->get();

        // Log::channel('florenceegi')->info('CollectionUsersMembers', [
        //     'collectionId' => $this->collectionId,
        //     'wallets' => $this->wallets,
        //     'walletProposals' => $this->walletProposals
        // ]);
    }

    public function deleteProposalWallet(Request $request, $walletId)
    {

        Log::channel('florenceegi')->info('DeleteProposalWallet', [
            'walletId' => $walletId
        ]);

        $collectionId = $request->collection_id;

        try {

            $wallet = NotificationPayloadWallet::findOrFail($walletId);
            $collection = Collection::findOrFail($collectionId);

            // Verifica permessi per l'utente autenticato
            if (!$this->hasPermission($collection, 'create_wallet')) {
                Log::channel('florenceegi')->error('Utente non autorizzato a cancellare la proposta wallet', [
                    'collectionId' => $collectionId,
                    'walletId' => $walletId
                ]);
                session()->flash('error', __('label.unauthorized_action'));
                return;
            }

            if (!$wallet) {
                Log::channel('florenceegi')->error('Proposta Wallet non trovata', [
                    'walletId' => $walletId
                ]);
                return response()->json(['message' => 'Proposta Wallet non trovata'], 404);
            }

            $wallet->delete();
            return response()->json(['message' => 'Proposta Wallet eliminata con successo'], 200);
        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('Errore durante l\'eliminazione della proposta wallet', [
                'walletId' => $walletId,
                'error' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Errore durante l\'eliminazione'], 500);
        }
    }

    public function deleteProposalInvitation(Request $request, $invitationId)
    {
        Log::channel('florenceegi')->info('DeleteProposalInvitation', [
            'invitationId' => $invitationId
        ]);

        $collectionId = $request->collection_id;

        try {
            $invitation = NotificationPayloadInvitation::findOrFail($invitationId);
            $collection = Collection::findOrFail($collectionId);

            // Verifica permessi per l'utente autenticato
            if (!$this->hasPermission($collection, 'manage_invitations')) {
                Log::channel('florenceegi')->error('Utente non autorizzato a cancellare la proposta di invito', [
                    'collectionId' => $collectionId,
                    'invitationId' => $invitationId
                ]);
                return response()->json(['message' => __('label.unauthorized_action')], 403);
            }

            if (!$invitation) {
                Log::channel('florenceegi')->error('Proposta Invito non trovata', [
                    'invitationId' => $invitationId
                ]);
                return response()->json(['message' => 'Proposta Invito non trovata'], 404);
            }

            $invitation->delete();
            return response()->json(['message' => 'Proposta Invito eliminata con successo'], 200);
        } catch (\Exception $e) {
            Log::channel('florenceegi')->error('Errore durante l\'eliminazione della proposta invito', [
                'invitationId' => $invitationId,
                'error' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Errore durante l\'eliminazione'], 500);
        }
    }


    public function render()
    {
        return view('livewire.collections.collection-user-member');
    }
}

