<?php

declare(strict_types=1);

namespace App\Validation;

final class LoginValidator
{
    /** @return array<string, string> */
    public static function validate(array $input): array
    {
        $errors = [];

        $email = trim((string) ($input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');

        if ($email === '') {
            $errors['email'] = 'Please enter your email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        } elseif (strlen($email) > 254) {
            $errors['email'] = 'Email is too long.';
        }

        if ($password === '') {
            $errors['password'] = 'Please enter your password.';
        }

        return $errors;
    }
}
