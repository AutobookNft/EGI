<?php

namespace App\Services;

use App\Enums\InvitationStatus;
use App\Models\Collection;
use App\Models\CollectionInvitation;
use App\Models\User;
use App\Notifications\InvitationProposal;
use App\Services\Notifications\NotificationHandlerFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
* Class InvitationService
* @package App\Services
*/

class InvitationService
{
    public function createInvitation(Collection $collection, string $email, string $role): CollectionInvitation
    {
        return DB::transaction(function () use ($collection, $email, $role) {

            try{

                // Verifica se l'utente esiste
                $user = User::where('email', $email)->firstOrFail();

                // Attraverso collection_id risalgo al creator della collection
                $collectionOwner = $collection->creator;

                // Proposale_name è il nome del creator della collection
                $proposalNeme = $collectionOwner->name . ' ' . $collectionOwner->last_name;

                // Status dell'invito
                $status = InvitationStatus::PENDING->value;

                // Dati di payload
                $data = [
                    'collection_id' => $collection->id,
                    'email' => $email,
                    'proposal_name' => $proposalNeme,
                    'role' => $role,
                    'status' => $status,
                ];

                // Scrive i dati di payload nel db
                $invitation = CollectionInvitation::create($data);

                Log::channel('florenceegi')->info('Invitation created', $data);

                // Gestione notifica
                $handler = NotificationHandlerFactory::getHandler(InvitationProposal::class);
                $handler->handle($user, $invitation);

                return $invitation;

            } catch (\Exception $e) {
                Log::channel('florenceegi')->error('Errore creazione invito', [
                    'error' => $e->getMessage(),
                    'data' => $data
                ]);
                throw $e;
            }
        });
    }
}
