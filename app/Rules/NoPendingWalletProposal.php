<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\NotificationPayloadWallet;

class NoPendingWalletProposal implements ValidationRule
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Esegue la regola di validazione.
     *
     * @param  \Closure(string, string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = NotificationPayloadWallet::where('user_id', $this->userId)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            $fail('Esiste già una proposta di wallet in sospeso per questo utente.');
        }
    }
}
