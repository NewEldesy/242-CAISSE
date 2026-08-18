<?php

declare(strict_types=1);

namespace Caisse\Domain\Repository;

use Caisse\Domain\Entity\Device;
use Caisse\Domain\ValueObject\Uuid;

interface DeviceRepositoryInterface
{
    public function save(Device $device): void;
    public function findById(Uuid $id): ?Device;
    public function findByStoreId(Uuid $storeId): array;
    public function findByIdentifier(string $identifier): ?Device;
    public function delete(Uuid $id): void;
}
