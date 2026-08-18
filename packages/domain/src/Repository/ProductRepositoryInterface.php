<?php
declare(strict_types=1);

namespace Caisse\Domain\Repository;

use Caisse\Domain\Entity\Product;
use Caisse\Domain\ValueObject\Uuid;

interface ProductRepositoryInterface
{
    public function save(Product $product): void;
    public function findById(Uuid $id): ?Product;
    public function findByStoreId(Uuid $storeId): array;
    public function findByCategoryId(Uuid $categoryId): array;
    public function delete(Uuid $id): void;
}
