<?php

declare(strict_types=1);

namespace Caisse\Domain\Repository;

use Caisse\Domain\Entity\Store;
use Caisse\Domain\ValueObject\Uuid;

interface StoreRepositoryInterface
{
    public function save(Store $store): void;
    public function findById(Uuid $id): ?Store;
    public function findByCode(string $code): ?Store;
    public function findAll(): array;
    public function delete(Uuid $id): void;
}
