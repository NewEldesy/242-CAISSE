<?php

declare(strict_types=1);

namespace Caisse\Domain\Repository;

use Caisse\Domain\Entity\StockMovement;
use Caisse\Domain\ValueObject\Uuid;

interface StockMovementRepositoryInterface
{
    public function save(StockMovement $movement): void;
    public function findByStoreId(Uuid $storeId, ?\DateTimeImmutable $from = null, ?\DateTimeImmutable $to = null): array;
    public function findByProductId(Uuid $productId): array;
}
