<?php

declare(strict_types=1);

namespace Caisse\Domain\Repository;

use Caisse\Domain\Entity\Sale;
use Caisse\Domain\ValueObject\Uuid;

interface SaleRepositoryInterface
{
    public function save(Sale $sale): void;
    public function findById(Uuid $id): ?Sale;
    public function findByStoreId(Uuid $storeId, ?\DateTimeImmutable $from = null, ?\DateTimeImmutable $to = null): array;
    public function findByCashSessionId(Uuid $cashSessionId): array;
}
