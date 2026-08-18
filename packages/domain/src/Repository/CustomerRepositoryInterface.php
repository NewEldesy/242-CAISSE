<?php

declare(strict_types=1);

namespace Caisse\Domain\Repository;

use Caisse\Domain\Entity\Customer;
use Caisse\Domain\ValueObject\Uuid;

interface CustomerRepositoryInterface
{
    public function save(Customer $customer): void;
    public function findById(Uuid $id): ?Customer;
    public function findByStoreId(Uuid $storeId): array;
    public function delete(Uuid $id): void;
}
