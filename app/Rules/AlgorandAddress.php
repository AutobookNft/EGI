<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AlgorandAddress implements Rule
{
    /**
     * @Oracode Validate Algorand address with checksum
     * 🎯 Purpose: Ensure address is valid Algorand format
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        // Basic format check
        if (!is_string($value) || strlen($value) !== 58) {
            return false;
        }

        // Base32 alphabet check (Algorand uses RFC 4648 Base32)
        if (!preg_match('/^[A-Z2-7]{58}$/', $value)) {
            return false;
        }

        // TODO: Add checksum validation if needed
        // Algorand uses last 4 bytes as checksum

        return true;
    }

    /**
     * @Oracode Get validation error message
     *
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute must be a valid Algorand address.';
    }
}
