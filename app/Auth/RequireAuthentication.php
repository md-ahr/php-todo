<?php

declare(strict_types=1);

namespace App\Auth;

/**
 * HTTP guard — used from controllers that must be signed in (e.g. /todos).
 */
final class RequireAuthentication
{
    /**
     * If the user is not authenticated, stash a safe internal return URL and redirect to login.
     */
    public static function redirectToLoginIfGuest(string $fallbackPath = '/todos'): void
    {
        if (SessionAuth::check()) {
            return;
        }

        $fallbackPath = trim($fallbackPath);

        if ($fallbackPath === '' || !str_starts_with($fallbackPath, '/') || str_starts_with($fallbackPath, '//')) {
            $fallbackPath = '/todos';
        }

        $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? $fallbackPath);
        $parts = parse_url($requestUri);
        $path = $parts['path'] ?? $fallbackPath;
        $query = isset($parts['query']) ? ('?' . $parts['query']) : '';

        if (!str_starts_with((string) $path, '/') || str_starts_with((string) $path, '//')) {
            $_SESSION['intended_url'] = $fallbackPath;
        } else {
            $target = (string) $path . $query;
            $_SESSION['intended_url'] = strlen($target) > 2048 ? $fallbackPath : $target;
        }

        header('Location: /login', true, 302);
        exit;
    }
}
