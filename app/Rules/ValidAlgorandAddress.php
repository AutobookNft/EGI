<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidAlgorandAddress implements Rule
{
    public function passes($attribute, $value)
    {
        // Verifica se l'indirizzo è un address Algorand valido
        return preg_match('/^[A-Z2-7]{58}$/', $value);
    }

    public function message()
    {
        return 'Il valore fornito non è un address Algorand valido.';
    }
}

