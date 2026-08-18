<?php

declare(strict_types=1);

namespace Caisse\Domain\Repository;

use Caisse\Domain\Entity\Category;
use Caisse\Domain\ValueObject\Uuid;

interface CategoryRepositoryInterface
{
    public function save(Category $category): void;
    public function findById(Uuid $id): ?Category;
    public function findAll(): array;
    public function delete(Uuid $id): void;
}
