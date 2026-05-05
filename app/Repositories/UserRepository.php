<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use PDOException;

final class UserRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function existsByEmail(string $normalizedEmail): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$normalizedEmail]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * For login via password_verify. Email must already be normalized (lowercase trim).
     *
     * @return array{id: int, name: string, email: string, password: string}|null
     */
    public function findForAuthByEmail(string $normalizedEmail): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$normalizedEmail]);
        /** @var array{id: mixed, name: mixed, email: mixed, password: mixed}|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            return null;
        }

        return [
            'id' => (int) $row['id'],
            'name' => (string) $row['name'],
            'email' => (string) $row['email'],
            'password' => (string) $row['password'],
        ];
    }

    /**
     * @throws PDOException on failure (including duplicate email / 1062)
     */
    public function create(string $name, string $normalizedEmail, string $passwordHash): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())'
        );
        $stmt->execute([$name, $normalizedEmail, $passwordHash]);

        return (int) $this->pdo->lastInsertId();
    }
}
