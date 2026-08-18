<?php

declare(strict_types=1);

namespace Caisse\Domain\Repository;

use Caisse\Domain\Entity\CashSession;
use Caisse\Domain\ValueObject\Uuid;

interface CashSessionRepositoryInterface
{
    public function save(CashSession $cashSession): void;
    public function findById(Uuid $id): ?CashSession;
    public function findOpenByDeviceAndUser(Uuid $deviceId, Uuid $userId): ?CashSession;
    public function findByStoreId(Uuid $storeId): array;
}
