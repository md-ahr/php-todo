<?php

declare(strict_types=1);

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $v = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if ($v === false || $v === '') {
            return $default;
        }

        return match (strtolower((string) $v)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'empty', '(empty)' => '',
            'null', '(null)' => null,
            default => $v,
        };
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        $base = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__);
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, trim($path, '/\\'));
        return $path === '' ? $base : $base . DIRECTORY_SEPARATOR . $path;
    }
}

if (!function_exists('resource_path')) {
    function resource_path(string $path = ''): string
    {
        return base_path($path === '' ? 'resources' : 'resources/' . ltrim($path, '/'));
    }
}

if (!function_exists('view_path')) {
    function view_path(string $relativeViewFile): string
    {
        return base_path('views/' . ltrim($relativeViewFile, '/'));
    }
}

if (!function_exists('config_path')) {
    function config_path(string $file = ''): string
    {
        return base_path($file === '' ? 'config' : 'config/' . ltrim($file, '/'));
    }
}

if (!function_exists('db')) {
    /**
     * Shared MySQL connection (PDO).
     */
    function db(): \PDO
    {
        return \App\Database\Connection::get();
    }
}

if (!function_exists('db_retry_once')) {
    /**
     * Run a closure with PDO; reconnect once after MySQL "gone away" (2006).
     *
     * @template T
     * @param callable(\PDO):T $callback
     * @return T
     */
    function db_retry_once(callable $callback): mixed
    {
        try {
            return $callback(db());
        } catch (\PDOException $e) {
            if (\App\Database\Connection::isMySqlGoneAway($e)) {
                \App\Database\Connection::reset();

                return $callback(db());
            }

            throw $e;
        }
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new \RuntimeException('Session must be started before csrf_token().');
        }

        if (!isset($_SESSION['_csrf_token']) || !is_string($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_validate')) {
    function csrf_validate(mixed $token): bool
    {
        return is_string($token)
            && isset($_SESSION['_csrf_token'])
            && is_string($_SESSION['_csrf_token'])
            && hash_equals($_SESSION['_csrf_token'], $token);
    }
}

if (!function_exists('auth_check')) {
    function auth_check(): bool
    {
        return \App\Auth\SessionAuth::check();
    }
}

if (!function_exists('auth_user')) {
    /**
     * @return array{user_id: int, email: string, name: string}|null
     */
    function auth_user(): ?array
    {
        return \App\Auth\SessionAuth::user();
    }
}
