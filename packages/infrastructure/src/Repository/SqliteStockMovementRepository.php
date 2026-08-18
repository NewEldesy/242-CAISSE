<?php

declare(strict_types=1);

namespace Caisse\Infrastructure\Repository;

use Caisse\Domain\Entity\StockMovement;
use Caisse\Domain\Repository\StockMovementRepositoryInterface;
use Caisse\Domain\ValueObject\Uuid;
use Caisse\Infrastructure\Database\Connection;

class SqliteStockMovementRepository implements StockMovementRepositoryInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function save(StockMovement $movement): void
    {
        $this->connection->execute(
            "INSERT INTO stock_movements (id, store_id, product_id, sale_id, type, quantity_before, quantity_after, notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $movement->getId()->getValue(),
                $movement->getStoreId()->getValue(),
                $movement->getProductId()->getValue(),
                $movement->getSaleId()?->getValue(),
                $movement->getType()->value,
                $movement->getQuantityBefore(),
                $movement->getQuantityAfter(),
                $movement->getNotes(),
                $movement->getCreatedAt()->format('c'),
            ]
        );
    }

    /** @return array<int, StockMovement> */
    public function findByStoreId(Uuid $storeId, ?\DateTimeImmutable $from = null, ?\DateTimeImmutable $to = null): array
    {
        $sql = "SELECT * FROM stock_movements WHERE store_id = ?";
        $params = [$storeId->getValue()];

        if ($from !== null) {
            $sql .= " AND created_at >= ?";
            $params[] = $from->format('c');
        }

        if ($to !== null) {
            $sql .= " AND created_at <= ?";
            $params[] = $to->format('c');
        }

        $sql .= " ORDER BY created_at DESC";

        $rows = $this->connection->fetchAll($sql, $params);
        return array_map([$this, 'hydrate'], $rows);
    }

    /** @return array<int, StockMovement> */
    public function findByProductId(Uuid $productId): array
    {
        $rows = $this->connection->fetchAll(
            "SELECT * FROM stock_movements WHERE product_id = ? ORDER BY created_at DESC",
            [$productId->getValue()]
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    private function hydrate(array $row): StockMovement
    {
        return StockMovement::reconstitute(
            Uuid::fromString($row['id']),
            Uuid::fromString($row['store_id']),
            Uuid::fromString($row['product_id']),
            $row['sale_id'] ? Uuid::fromString($row['sale_id']) : null,
            \Caisse\Domain\Entity\StockMovementType::from($row['type']),
            (int)$row['quantity_before'],
            (int)$row['quantity_after'],
            new \DateTimeImmutable($row['created_at']),
            $row['notes']
        );
    }
}
