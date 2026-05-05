<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

/**
 * Lazily builds a shared PDO connection to MySQL (utf8mb4).
 */
final class Connection
{
    private static ?PDO $pdo = null;

    public static function get(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        /** @var array{host:string,port:int,database:string,username:string,password:string} $cfg */
        $cfg = require config_path('database.php');

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $cfg['host'],
            $cfg['port'],
            $cfg['database'],
        );

        self::$pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return self::$pdo;
    }

    public static function reset(): void
    {
        self::$pdo = null;
    }
}
