<?php


namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class WalletChangeResponse extends Notification
{
    private $approval;
    private $status;

    public function __construct($approval, $status)
    {
        $this->approval = $approval;
        $this->status = $status;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->status === 'approved'
                ? __('Your wallet change has been approved.')
                : __('Your wallet change has been declined. Reason: ') . $this->approval->rejection_reason,
            'wallet_change_approvals_id' => $this->approval->id,
        ];
    }
}
