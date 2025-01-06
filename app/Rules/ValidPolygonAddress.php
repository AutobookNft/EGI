<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidPolygonAddress implements Rule
{
    public function passes($attribute, $value)
    {
        // Verifica se l'indirizzo è un address Polygon valido
        return preg_match('/^0x[a-fA-F0-9]{40}$/', $value);
    }

    public function message()
    {
        return 'Il valore fornito non è un address Polygon valido.';
    }
}
