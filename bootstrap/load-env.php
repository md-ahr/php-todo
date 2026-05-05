<?php

declare(strict_types=1);

/**
 * Populate $_ENV / getenv from a Laravel-style .env file (KEY=value, # comments).
 * Does not override variables already present in the environment.
 */
function load_env_file(string $path): void
{
    if (!is_readable($path)) {
        return;
    }

    $raw = file_get_contents($path);
    if ($raw === false) {
        return;
    }

    foreach (preg_split('/\r\n|\r|\n/', $raw) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if ($name === '') {
            continue;
        }

        // Do not override keys already provided by the server / SAPI environment.
        if (array_key_exists($name, $_ENV)) {
            continue;
        }

        if (
            strlen($value) >= 2
            && (($value[0] === '"' && $value[strlen($value) - 1] === '"')
                || ($value[0] === "'" && $value[strlen($value) - 1] === "'"))
        ) {
            $value = stripcslashes(substr($value, 1, -1));
        }

        $_ENV[$name] = $value;
        putenv("{$name}={$value}");
    }
}
