<?php

namespace App\Notifications\Gdpr;

use App\Models\NotificationPayloadGdpr;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * @package   App\Notifications\Gdpr
 * @author    Padmin D. Curtis (for Fabio Cherici)
 * @version   1.0.0
 * @date      2025-06-12
 * @solution  Formats and defines the high-priority security alert email sent to administrators.
 */
class SecurityAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public NotificationPayloadGdpr $payload
    ) {}

    /**
     * Get the notification's delivery channels.
     * The channel is defined by Notification::route() call, this is for clarity.
     *
     * @return array
     */
    public function via(): array
    {
        return ['mail']; // Il target 'security_alert' viene risolto dalla rotta
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable (in this case, the email address from Notification::route)
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $details = [
            'User ID' => $this->user->id,
            'User Email' => $this->user->email,
            'Disavowed Consent Type' => $this->payload->type,
            'IP Address of Disavowal' => request()->ip(),
            'Timestamp (UTC)' => now()->toDateTimeString(),
        ];

        return (new MailMessage)
            ->subject('[CODICE ROSSO] Allarme Sicurezza Account: ' . $this->user->id)
            ->error()
            ->line('**MASSIMA ALLERTA**')
            ->line('Un utente ha utilizzato la funzione "Non riconosco questa azione" per una modifica al consenso. Questo potrebbe indicare un accesso non autorizzato all\'account.')
            ->line('**Azione immediata richiesta dal team di sicurezza.**')
            ->line('---')
            ->line('**Dettagli dell\'evento:**')
            ->line(collect($details)->map(fn($val, $key) => "**{$key}:** `{$val}`")->implode("\n"))
            ->line('---')
            ->action('Visualizza Utente Coinvolto', url('/admin/users/' . $this->user->id)) // Assumendo esista una rotta admin
            ->line('Il team di sicurezza deve investigare immediatamente questo evento per escludere una compromissione dell\'account.');
    }
}
