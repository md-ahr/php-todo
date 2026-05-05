<?php

declare(strict_types=1);

namespace App\Validation;

/**
 * Validates registration input (presence, format, length). Email uniqueness belongs in persistence layer.
 */
final class RegisterValidator
{
    private const PASSWORD_MIN = 8;
    private const PASSWORD_MAX_BYTES = 72;

    /** @var array<string, string> field => message */
    public static function validate(array $input): array
    {
        $errors = [];

        $name = trim((string) ($input['name'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');
        $confirmation = (string) ($input['password_confirmation'] ?? '');
        $termsAccepted = (($input['terms_accepted'] ?? false) === true);

        if ($name === '') {
            $errors['name'] = 'Please enter your name.';
        } elseif (mb_strlen($name) < 2) {
            $errors['name'] = 'Name must be at least 2 characters.';
        } elseif (mb_strlen($name) > 255) {
            $errors['name'] = 'Name may not exceed 255 characters.';
        }

        if ($email === '') {
            $errors['email'] = 'Please enter your email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        } elseif (strlen($email) > 254) {
            $errors['email'] = 'Email is too long.';
        }

        if ($password === '') {
            $errors['password'] = 'Please choose a password.';
        } elseif (strlen($password) < self::PASSWORD_MIN) {
            $errors['password'] = 'Password must be at least ' . self::PASSWORD_MIN . ' characters.';
        } elseif (strlen($password) > self::PASSWORD_MAX_BYTES) {
            $errors['password'] = 'Password is too long (max ' . self::PASSWORD_MAX_BYTES . ' characters).';
        }

        if ($confirmation !== $password) {
            $errors['password_confirmation'] = 'Does not match the password above.';
        }

        if (!$termsAccepted) {
            $errors['terms'] = 'You must accept the terms and privacy policy to register.';
        }

        return $errors;
    }
}
