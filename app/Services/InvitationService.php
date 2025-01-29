<?php

namespace App\Services;

use App\Enums\InvitationStatus;
use App\Models\Collection;
use App\Models\NotificationPayloadInvitation;
use App\Models\User;
use App\Services\Notifications\InvitationNotificationHandler;
use App\Services\Notifications\NotificationHandlerFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
* Class InvitationService
* @package App\Services
*/

class InvitationService
{
    public function createInvitation(Collection $collection, string $email, string $role): NotificationPayloadInvitation
    {
        return DB::transaction(function () use ($collection, $email, $role) {

            try{

                // User destinatario della risposta, si verifica se esiste
                $user = User::where('email', $email)->firstOrFail();

                // Status dell'invito
                $status = InvitationStatus::PENDING->value;

                // Dati di payload
                $data = [
                    'collection_id' => $collection->id,
                    'proposer_id' => Auth::id(), // Utente che invita è l'utente autenticato
                    'receiver_id' => $user->id,  // Utente che riceve l'invito, oltre alla mail si aggiunge anche l'id per facilitare le query
                    'email' => $email,
                    'role' => $role, // Ruolo proposto
                    'status' => $status, // Stato dell'invito
                ];

                Log::channel('florenceegi')->info('InvitationService:dati di payload', $data);

                // Scrive i dati di payload nel db
                $invitation = NotificationPayloadInvitation::create($data);

                Log::channel('florenceegi')->info('Invito creato', $invitation->toArray());

                $invitation['collection_name'] = $collection->collection_name;
                $invitation['proposer_name'] = Auth::user()->name . ' ' . Auth::user()->last_name; // Nome di chi fa la proposta
                $invitation['model_id'] = $invitation->id;
                $invitation['model_type'] = get_class($invitation);
                $invitation['message'] = __('Sei stato invitato a partecipare ad una collezione.');
                $invitation['view'] = 'invitations.' . InvitationStatus::REQUEST->value;

                // Gestione notifica
                $handler = NotificationHandlerFactory::getHandler(InvitationNotificationHandler::class);
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
