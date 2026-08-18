<?php

declare(strict_types=1);

namespace Caisse\Infrastructure\Repository;

use Caisse\Domain\Entity\Store;
use Caisse\Domain\Entity\StoreStatus;
use Caisse\Domain\Repository\StoreRepositoryInterface;
use Caisse\Domain\ValueObject\Uuid;
use Caisse\Infrastructure\Database\Connection;

class SqliteStoreRepository implements StoreRepositoryInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function save(Store $store): void
    {
        $this->connection->execute(
            "INSERT OR REPLACE INTO stores (id, name, code, status, address, phone, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $store->getId()->getValue(),
                $store->getName(),
                $store->getCode(),
                $store->getStatus()->value,
                $store->getAddress(),
                $store->getPhone(),
                $store->getCreatedAt()->format('c'),
                $store->getUpdatedAt()->format('c'),
            ]
        );
    }

    public function findById(Uuid $id): ?Store
    {
        $row = $this->connection->fetchOne(
            "SELECT * FROM stores WHERE id = ?",
            [$id->getValue()]
        );

        if (!$row) {
            return null;
        }

        return Store::reconstitute(
            Uuid::fromString($row['id']),
            $row['name'],
            $row['code'],
            StoreStatus::from($row['status']),
            new \DateTimeImmutable($row['created_at']),
            new \DateTimeImmutable($row['updated_at']),
            $row['address'],
            $row['phone']
        );
    }

    public function findByCode(string $code): ?Store
    {
        $row = $this->connection->fetchOne(
            "SELECT * FROM stores WHERE code = ?",
            [$code]
        );

        if (!$row) {
            return null;
        }

        return Store::reconstitute(
            Uuid::fromString($row['id']),
            $row['name'],
            $row['code'],
            StoreStatus::from($row['status']),
            new \DateTimeImmutable($row['created_at']),
            new \DateTimeImmutable($row['updated_at']),
            $row['address'],
            $row['phone']
        );
    }

    /** @return array<int, Store> */
    public function findAll(): array
    {
        $rows = $this->connection->fetchAll("SELECT * FROM stores");
        $stores = [];

        foreach ($rows as $row) {
            $stores[] = Store::reconstitute(
                Uuid::fromString($row['id']),
                $row['name'],
                $row['code'],
                StoreStatus::from($row['status']),
                new \DateTimeImmutable($row['created_at']),
                new \DateTimeImmutable($row['updated_at']),
                $row['address'],
                $row['phone']
            );
        }

        return $stores;
    }

    public function delete(Uuid $id): void
    {
        $this->connection->execute(
            "DELETE FROM stores WHERE id = ?",
            [$id->getValue()]
        );
    }
}
