<?php


namespace App\Notifications;

use App\Notifications\Channels\CustomDatabaseChannel;
use Illuminate\Notifications\Notification;

class InvitationProposal extends Notification
{
    protected $CollectionInvitation;

    public function __construct($CollectionInvitation)
    {
        $this->CollectionInvitation = $CollectionInvitation;
    }

    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class];
    }

    public function toCustomDatabase($notifiable)
    {
        return [
            'model_type' => get_class($this->CollectionInvitation), // Esempio: App\Models\WalletChangeApproval
            'model_id'   => $this->CollectionInvitation->id,        // L'ID del record
            'data'       => [
                'message' => __('Sei stato invitato a partecipare ad una collezione.'),
                'proposal_name' => $this->CollectionInvitation->proposal_name,
                'collection_name' => $this->CollectionInvitation->collection->collection_name,
                ],
            'outcome' => 'proposal',
        ];
    }
}
