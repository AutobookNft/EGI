<?php


namespace App\Notifications\Wallets;

use App\Notifications\Channels\CustomDatabaseChannel;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class WalletRejection extends Notification
{
    private $walletChangeApproval;
    private $reason;

    public function __construct($walletChangeApproval, $reason = null)
    {
        $this->walletChangeApproval = $walletChangeApproval;
        $this->reason = $reason;

    }

    public function via($notifiable)
    {
        return [CustomDatabaseChannel::class];;
    }

    public function toCustomDatabase($notifiable)
    {

        $message = __('collection.wallet.wallet_change_rejected');

        // Recupera il nome e il cognome dell'utente ricevente per inserirlo nel messaggio da reinviare al proponente,
        // in questo modo il proponente sa chi ha rifiutato la sua proposta
        $name = $this->walletChangeApproval->receiver->name ?? '';
        $lastName = $this->walletChangeApproval->receiver->last_name ?? '';

        return [
            'model_type' => get_class($this->walletChangeApproval),
            'model_id'   => $this->walletChangeApproval->id,
            'data' =>[
                'message' => $message,
                'reason' => $this->reason,
                'user' => $name . ' ' . $lastName,
            ],
            'outcome' => 'rejected',
        ];
    }
}
