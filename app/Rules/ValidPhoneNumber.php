<?php

namespace App\Rules;

use App\Support\PhoneNumber;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (PhoneNumber::normalize(is_string($value) ? $value : null) === null) {
            $fail('Nomor HP tidak valid. Gunakan format 08xxxxxxxxxx atau +62xxxxxxxxxx, 10–13 digit.');
        }
    }
}
