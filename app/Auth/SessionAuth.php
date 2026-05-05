<?php

declare(strict_types=1);

namespace App\Auth;

/**
 * Lightweight session-backed auth (no passwords stored in $_SESSION).
 */
final class SessionAuth
{
    public const SESSION_KEY = 'auth';

    public static function check(): bool
    {
        if (!isset($_SESSION[self::SESSION_KEY]['user_id'])) {
            return false;
        }

        $id = $_SESSION[self::SESSION_KEY]['user_id'];

        return is_numeric($id) && (int) $id > 0;
    }

    /** @return array{user_id: int, email: string, name: string}|null */
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        return [
            'user_id' => (int) $_SESSION[self::SESSION_KEY]['user_id'],
            'email' => (string) $_SESSION[self::SESSION_KEY]['email'],
            'name' => (string) $_SESSION[self::SESSION_KEY]['name'],
        ];
    }

    public static function login(int $userId, string $email, string $name): void
    {
        session_regenerate_id(true);
        $_SESSION[self::SESSION_KEY] = [
            'user_id' => $userId,
            'email' => $email,
            'name' => $name,
        ];
    }

    public static function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY], $_SESSION['intended_url']);
        session_regenerate_id(true);
    }
}
