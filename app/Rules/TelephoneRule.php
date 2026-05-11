<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TelephoneRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Numéros valides au Sénégal :
        // Mobile : 77, 76, 75, 70, 71, 78 + 7 chiffres
        // Fixe   : 33 8XX XX XX
        $pattern = '/^((77|76|75|70|71|78)\d{7})|(338\d{6})$/';

        if (!preg_match($pattern, $value)) {
            $fail('Le numéro de téléphone doit être un numéro sénégalais valide (ex: 77XXXXXXX ou 338XXXXXX).');
        }
    }
}