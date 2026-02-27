<?php

declare(strict_types=1);

namespace Dzelenika\Agents\Config;

use PDO;
use PDOException;

/**
 * PDO database connection singleton.
 *
 * Reads DSN credentials from environment variables.
 * Call Database::getConnection() to get the shared PDO instance.
 */
class Database
{
    private static ?PDO $connection = null;

    private function __construct() {}

    /**
     * Returns the shared PDO connection, creating it on first call.
     *
     * @throws PDOException If the connection cannot be established.
     *
     * @return PDO The shared database connection.
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $_ENV['DB_HOST'],
                $_ENV['DB_PORT'],
                $_ENV['DB_NAME']
            );

            self::$connection = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASS'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return self::$connection;
    }

    /**
     * Resets the singleton connection. For use in tests only.
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$connection = null;
    }
}
