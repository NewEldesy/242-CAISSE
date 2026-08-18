<?php

declare(strict_types=1);

namespace Caisse\Domain\Repository;

use Caisse\Domain\Entity\Stock;
use Caisse\Domain\ValueObject\Uuid;

interface StockRepositoryInterface
{
    public function save(Stock $stock): void;
    public function findById(Uuid $id): ?Stock;
    public function findByStoreAndProduct(Uuid $storeId, Uuid $productId): ?Stock;
    public function findByStoreId(Uuid $storeId): array;
}
