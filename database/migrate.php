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

    db_retry_once(static function (\PDO $pdo) use ($sql): void {
        $pdo->exec($sql);
    });
} catch (Throwable $e) {
    fwrite(STDERR, 'Migration failed: ' . $e->getMessage() . "\n");
    if ($e instanceof \PDOException && App\Database\Connection::isMySqlGoneAway($e)) {
        fwrite(STDERR, "Tip: Ensure MySQL is running (docker compose up -d), DB_HOST/DB_PORT match, and\n");
        fwrite(STDERR, "     the disk is not full. A single reconnect was attempted automatically.\n");
    }

    exit(1);
}

echo "Database schema applied (" . basename($sqlPath) . ").\n";
