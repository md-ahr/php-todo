<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

/**
 * Lazily builds a shared PDO connection to MySQL (utf8mb4).
 *
 * If you see HY000 / 2006 "server has gone away", the server dropped TCP (timeouts,
 * restarts, full disk causing mysqld instability, Docker not ready). Prefer fixing
 * the environment; callers may reset and retry once via {@see self::reset()}.
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

    /**
     * MySQL ER_SERVER_GONE_ERROR (2006) or equivalent message after a stall/crash/restart.
     */
    public static function isMySqlGoneAway(PDOException $e): bool
    {
        $driverCode = isset($e->errorInfo[1]) ? (int) $e->errorInfo[1] : 0;

        return $driverCode === 2006
            || str_contains(strtolower($e->getMessage()), 'gone away')
            || str_contains(strtolower($e->getMessage()), 'lost connection');
    }

    public static function reset(): void
    {
        self::$pdo = null;
    }
}
