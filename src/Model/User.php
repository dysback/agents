<?php

declare(strict_types=1);

namespace Dzelenika\Agents\Model;

use Dzelenika\Agents\Config\Database;
use PDO;

/**
 * User model providing database access and password verification.
 *
 * Accepts an optional PDO instance for dependency injection in tests.
 * When no PDO is provided, uses the Database singleton.
 */
class User
{
    /**
     * @param PDO|null $pdo Optional PDO connection; defaults to Database::getConnection().
     */
    public function __construct(private readonly ?PDO $pdo = null) {}

    /**
     * Finds a user record by username.
     *
     * @param string $username The username to search for.
     *
     * @return array{id: int, username: string, password: string}|null The user row, or null if not found.
     */
    public function findByUsername(string $username): ?array
    {
        $pdo  = $this->pdo ?? Database::getConnection();
        $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * Verifies a plain-text password against a bcrypt hash.
     *
     * @param string $plainPassword  The password entered by the user.
     * @param string $hashedPassword The bcrypt hash from the database.
     *
     * @return bool True if the password matches, false otherwise.
     */
    public function verifyPassword(string $plainPassword, string $hashedPassword): bool
    {
        return password_verify($plainPassword, $hashedPassword);
    }
}
