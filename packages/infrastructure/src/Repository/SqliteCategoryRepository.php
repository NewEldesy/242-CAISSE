<?php

declare(strict_types=1);

namespace Caisse\Infrastructure\Repository;

use Caisse\Domain\Entity\Category;
use Caisse\Domain\Repository\CategoryRepositoryInterface;
use Caisse\Domain\ValueObject\Uuid;
use Caisse\Infrastructure\Database\Connection;

class SqliteCategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function save(Category $category): void
    {
        $this->connection->execute(
            "INSERT OR REPLACE INTO categories (id, name, description, parent_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)",
            [
                $category->getId()->getValue(),
                $category->getName(),
                $category->getDescription(),
                $category->getParentId()?->getValue(),
                $category->getCreatedAt()->format('c'),
                $category->getUpdatedAt()->format('c'),
            ]
        );
    }

    public function findById(Uuid $id): ?Category
    {
        $row = $this->connection->fetchOne(
            "SELECT * FROM categories WHERE id = ?",
            [$id->getValue()]
        );

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    /** @return array<int, Category> */
    public function findAll(): array
    {
        $rows = $this->connection->fetchAll("SELECT * FROM categories");
        return array_map([$this, 'hydrate'], $rows);
    }

    public function delete(Uuid $id): void
    {
        $this->connection->execute(
            "DELETE FROM categories WHERE id = ?",
            [$id->getValue()]
        );
    }

    private function hydrate(array $row): Category
    {
        return Category::reconstitute(
            Uuid::fromString($row['id']),
            $row['name'],
            $row['description'],
            $row['parent_id'] ? Uuid::fromString($row['parent_id']) : null,
            new \DateTimeImmutable($row['created_at']),
            new \DateTimeImmutable($row['updated_at'])
        );
    }
}
