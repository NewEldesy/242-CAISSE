<?php

declare(strict_types=1);

namespace Caisse\Infrastructure\Repository;

use Caisse\Domain\Entity\Stock;
use Caisse\Domain\Entity\StockStatus;
use Caisse\Domain\Repository\StockRepositoryInterface;
use Caisse\Domain\ValueObject\Uuid;
use Caisse\Infrastructure\Database\Connection;

class SqliteStockRepository implements StockRepositoryInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function save(Stock $stock): void
    {
        $this->connection->execute(
            "INSERT OR REPLACE INTO stock (id, store_id, product_id, quantity, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $stock->getId()->getValue(),
                $stock->getStoreId()->getValue(),
                $stock->getProductId()->getValue(),
                $stock->getQuantity(),
                $stock->getStatus()->value,
                $stock->getCreatedAt()->format('c'),
                $stock->getUpdatedAt()->format('c'),
            ]
        );
    }

    public function findById(Uuid $id): ?Stock
    {
        $row = $this->connection->fetchOne(
            "SELECT * FROM stock WHERE id = ?",
            [$id->getValue()]
        );

        return $row ? $this->hydrate($row) : null;
    }

    public function findByStoreAndProduct(Uuid $storeId, Uuid $productId): ?Stock
    {
        $row = $this->connection->fetchOne(
            "SELECT * FROM stock WHERE store_id = ? AND product_id = ?",
            [$storeId->getValue(), $productId->getValue()]
        );

        return $row ? $this->hydrate($row) : null;
    }

    /** @return array<int, Stock> */
    public function findByStoreId(Uuid $storeId): array
    {
        $rows = $this->connection->fetchAll(
            "SELECT * FROM stock WHERE store_id = ?",
            [$storeId->getValue()]
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    private function hydrate(array $row): Stock
    {
        return Stock::reconstitute(
            Uuid::fromString($row['id']),
            Uuid::fromString($row['store_id']),
            Uuid::fromString($row['product_id']),
            (int)$row['quantity'],
            StockStatus::from($row['status']),
            new \DateTimeImmutable($row['created_at']),
            new \DateTimeImmutable($row['updated_at'])
        );
    }
}
