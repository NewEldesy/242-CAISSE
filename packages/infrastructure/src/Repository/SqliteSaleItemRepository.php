<?php

declare(strict_types=1);

namespace Caisse\Infrastructure\Repository;

use Caisse\Domain\Entity\SaleItem;
use Caisse\Infrastructure\Database\Connection;

class SqliteSaleItemRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function save(SaleItem $item): void
    {
        $this->connection->execute(
            "INSERT INTO sale_items (id, sale_id, product_id, product_name, product_reference, quantity, unit_price, currency, subtotal, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $item->getId()->getValue(),
                $item->getSaleId()->getValue(),
                $item->getProductId()->getValue(),
                $item->getProductName(),
                $item->getProductReference(),
                $item->getQuantity(),
                $item->getUnitPrice()->getAmount(),
                $item->getUnitPrice()->getCurrency(),
                $item->getSubtotal()->getAmount(),
                (new \DateTimeImmutable())->format('c'),
            ]
        );
    }

    /** @return array<int, SaleItem> */
    public function findBySaleId(string $saleId): array
    {
        $rows = $this->connection->fetchAll(
            "SELECT * FROM sale_items WHERE sale_id = ?",
            [$saleId]
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    private function hydrate(array $row): SaleItem
    {
        return SaleItem::create(
            \Caisse\Domain\ValueObject\Uuid::fromString($row['product_id']),
            $row['product_name'],
            (int)$row['quantity'],
            \Caisse\Domain\ValueObject\Money::of((int)$row['unit_price'], $row['currency']),
            $row['product_reference']
        );
    }
}
