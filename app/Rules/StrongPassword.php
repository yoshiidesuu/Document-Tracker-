<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = (string) $value;
        $config = config('security.password');

        if (strlen($value) < $config['min_length']) {
            $fail("The {$attribute} must be at least {$config['min_length']} characters.");
            return;
        }

        if ($config['require_uppercase'] && !preg_match('/[A-Z]/', $value)) {
            $fail("The {$attribute} must contain at least one uppercase letter.");
            return;
        }

        if ($config['require_lowercase'] && !preg_match('/[a-z]/', $value)) {
            $fail("The {$attribute} must contain at least one lowercase letter.");
            return;
        }

        if ($config['require_number'] && !preg_match('/[0-9]/', $value)) {
            $fail("The {$attribute} must contain at least one number.");
            return;
        }

        if ($config['require_symbol'] && !preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?`~]/', $value)) {
            $fail("The {$attribute} must contain at least one special character.");
            return;
        }

        if (preg_match('/(.)\1{3,}/', $value)) {
            $fail("The {$attribute} must not contain repeated characters (4+ times).");
            return;
        }

        $common = ['password', '123456', 'qwerty', 'letmein', 'admin', 'welcome'];
        if (in_array(strtolower($value), $common)) {
            $fail("The {$attribute} is too common and easily guessable.");
            return;
        }

        if (preg_match('/^[A-Za-z]+$/', $value) || preg_match('/^[0-9]+$/', $value)) {
            $fail("The {$attribute} must not be entirely letters or entirely numbers.");
            return;
        }
    }
}
