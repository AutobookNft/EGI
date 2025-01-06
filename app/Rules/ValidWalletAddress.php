<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidWalletAddress implements Rule
{
    public function passes($attribute, $value)
    {
        // Regola per Algorand
        $isAlgorand = preg_match('/^[A-Z2-7]{58}$/', $value);

        // Regola per Polygon
        $isPolygon = preg_match('/^0x[a-fA-F0-9]{40}$/', $value);

        return $isAlgorand || $isPolygon; // Accetta se è valido per almeno una blockchain
    }

    public function message()
    {
        return 'Il valore fornito non è un address valido per Algorand o Polygon.';
    }
}
