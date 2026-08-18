<?php

declare(strict_types=1);

namespace Caisse\Infrastructure\Repository;

use Caisse\Domain\Entity\User;
use Caisse\Domain\Entity\UserRole;
use Caisse\Domain\Entity\UserStatus;
use Caisse\Domain\Repository\UserRepositoryInterface;
use Caisse\Domain\ValueObject\Uuid;
use Caisse\Infrastructure\Database\Connection;

class SqliteUserRepository implements UserRepositoryInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function save(User $user): void
    {
        $this->connection->execute(
            "INSERT OR REPLACE INTO users (id, store_id, username, email, password_hash, role, status, full_name, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $user->getId()->getValue(),
                $user->getStoreId()->getValue(),
                $user->getUsername(),
                $user->getEmail(),
                $user->getPasswordHash(),
                $user->getRole()->value,
                $user->getStatus()->value,
                $user->getFullName(),
                $user->getCreatedAt()->format('c'),
                $user->getUpdatedAt()->format('c'),
            ]
        );
    }

    public function findById(Uuid $id): ?User
    {
        $row = $this->connection->fetchOne(
            "SELECT * FROM users WHERE id = ?",
            [$id->getValue()]
        );

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findByUsername(string $username): ?User
    {
        $row = $this->connection->fetchOne(
            "SELECT * FROM users WHERE username = ?",
            [$username]
        );

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    /** @return array<int, User> */
    public function findByStoreId(Uuid $storeId): array
    {
        $rows = $this->connection->fetchAll(
            "SELECT * FROM users WHERE store_id = ?",
            [$storeId->getValue()]
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function delete(Uuid $id): void
    {
        $this->connection->execute(
            "DELETE FROM users WHERE id = ?",
            [$id->getValue()]
        );
    }

    private function hydrate(array $row): User
    {
        return User::reconstitute(
            Uuid::fromString($row['id']),
            Uuid::fromString($row['store_id']),
            $row['username'],
            $row['email'],
            $row['password_hash'],
            UserRole::from($row['role']),
            UserStatus::from($row['status']),
            new \DateTimeImmutable($row['created_at']),
            new \DateTimeImmutable($row['updated_at']),
            $row['full_name']
        );
    }
}
