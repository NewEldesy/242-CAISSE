<?php

declare(strict_types=1);

namespace Caisse\Infrastructure\Repository;

use Caisse\Domain\Entity\Sale;
use Caisse\Domain\Entity\SaleStatus;
use Caisse\Domain\Repository\SaleRepositoryInterface;
use Caisse\Domain\ValueObject\Uuid;
use Caisse\Infrastructure\Database\Connection;

class SqliteSaleRepository implements SaleRepositoryInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function save(Sale $sale): void
    {
        $this->connection->execute(
            "INSERT OR REPLACE INTO sales (id, store_id, device_id, user_id, customer_id, cash_session_id, status, total_amount, currency, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $sale->getId()->getValue(),
                $sale->getStoreId()->getValue(),
                $sale->getDeviceId()->getValue(),
                $sale->getUserId()->getValue(),
                $sale->getCustomerId()?->getValue(),
                $sale->getCashSessionId()?->getValue(),
                $sale->getStatus()->value,
                $sale->getTotalAmount()->getAmount(),
                $sale->getTotalAmount()->getCurrency(),
                $sale->getCreatedAt()->format('c'),
                $sale->getUpdatedAt()->format('c'),
            ]
        );
    }

    public function findById(Uuid $id): ?Sale
    {
        $row = $this->connection->fetchOne(
            "SELECT * FROM sales WHERE id = ?",
            [$id->getValue()]
        );

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    /** @return array<int, Sale> */
    public function findByStoreId(Uuid $storeId, ?\DateTimeImmutable $from = null, ?\DateTimeImmutable $to = null): array
    {
        $sql = "SELECT * FROM sales WHERE store_id = ?";
        $params = [$storeId->getValue()];

        if ($from !== null) {
            $sql .= " AND created_at >= ?";
            $params[] = $from->format('c');
        }

        if ($to !== null) {
            $sql .= " AND created_at <= ?";
            $params[] = $to->format('c');
        }

        $rows = $this->connection->fetchAll($sql, $params);
        return array_map([$this, 'hydrate'], $rows);
    }

    /** @return array<int, Sale> */
    public function findByCashSessionId(Uuid $cashSessionId): array
    {
        $rows = $this->connection->fetchAll(
            "SELECT * FROM sales WHERE cash_session_id = ?",
            [$cashSessionId->getValue()]
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    private function hydrate(array $row): Sale
    {
        return Sale::reconstitute(
            Uuid::fromString($row['id']),
            Uuid::fromString($row['store_id']),
            Uuid::fromString($row['device_id']),
            Uuid::fromString($row['user_id']),
            $row['customer_id'] ? Uuid::fromString($row['customer_id']) : null,
            SaleStatus::from($row['status']),
            \Caisse\Domain\ValueObject\Money::of((int)$row['total_amount'], $row['currency']),
            new \DateTimeImmutable($row['created_at']),
            new \DateTimeImmutable($row['updated_at']),
            $row['cash_session_id'] ? Uuid::fromString($row['cash_session_id']) : null
        );
    }
}
