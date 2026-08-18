<?php

declare(strict_types=1);

namespace Caisse\Infrastructure\Repository;

use Caisse\Domain\Entity\Product;
use Caisse\Domain\Entity\ProductStatus;
use Caisse\Domain\Repository\ProductRepositoryInterface;
use Caisse\Domain\ValueObject\Uuid;
use Caisse\Infrastructure\Database\Connection;

class SqliteProductRepository implements ProductRepositoryInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function save(Product $product): void
    {
        $this->connection->execute(
            "INSERT OR REPLACE INTO products (id, store_id, category_id, name, description, reference, selling_price, purchase_price, currency, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $product->getId()->getValue(),
                $product->getStoreId()->getValue(),
                $product->getCategoryId()?->getValue(),
                $product->getName(),
                $product->getDescription(),
                $product->getReference(),
                $product->getSellingPrice()->getAmount(),
                $product->getPurchasePrice()->getAmount(),
                $product->getSellingPrice()->getCurrency(),
                $product->getStatus()->value,
                $product->getCreatedAt()->format('c'),
                $product->getUpdatedAt()->format('c'),
            ]
        );
    }

    public function findById(Uuid $id): ?Product
    {
        $row = $this->connection->fetchOne(
            "SELECT * FROM products WHERE id = ?",
            [$id->getValue()]
        );

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    /** @return array<int, Product> */
    public function findByStoreId(Uuid $storeId): array
    {
        $rows = $this->connection->fetchAll(
            "SELECT * FROM products WHERE store_id = ?",
            [$storeId->getValue()]
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    /** @return array<int, Product> */
    public function findByCategoryId(Uuid $categoryId): array
    {
        $rows = $this->connection->fetchAll(
            "SELECT * FROM products WHERE category_id = ?",
            [$categoryId->getValue()]
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function delete(Uuid $id): void
    {
        $this->connection->execute(
            "DELETE FROM products WHERE id = ?",
            [$id->getValue()]
        );
    }

    private function hydrate(array $row): Product
    {
        return Product::reconstitute(
            Uuid::fromString($row['id']),
            Uuid::fromString($row['store_id']),
            $row['category_id'] ? Uuid::fromString($row['category_id']) : null,
            $row['name'],
            $row['description'],
            $row['reference'],
            \Caisse\Domain\ValueObject\Money::of((int)$row['selling_price'], $row['currency']),
            \Caisse\Domain\ValueObject\Money::of((int)$row['purchase_price'], $row['currency']),
            ProductStatus::from($row['status']),
            new \DateTimeImmutable($row['created_at']),
            new \DateTimeImmutable($row['updated_at'])
        );
    }
}
