<?php

declare(strict_types=1);

/**
 * Apply database/schema.sql via PDO (creates users table if missing).
 * Usage: php database/migrate.php
 */

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

if (!function_exists('base_path')) {
    require BASE_PATH . '/bootstrap/helpers.php';
}

require BASE_PATH . '/bootstrap/load-env.php';

load_env_file(BASE_PATH . '/.env');

$sqlPath = __DIR__ . '/schema.sql';
$sql = file_get_contents($sqlPath);

if ($sql === false) {
    fwrite(STDERR, "Could not read {$sqlPath}\n");
    exit(1);
}

try {
    App\Database\Connection::reset();
    $pdo = db();
    $pdo->exec($sql);
} catch (Throwable $e) {
    fwrite(STDERR, 'Migration failed: ' . $e->getMessage() . "\n");
    exit(1);
}

echo "Database schema applied (" . basename($sqlPath) . ").\n";
