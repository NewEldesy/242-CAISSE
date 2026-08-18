<?php

declare(strict_types=1);

namespace Caisse\Domain\Repository;

use Caisse\Domain\Entity\User;
use Caisse\Domain\ValueObject\Uuid;

interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function findById(Uuid $id): ?User;
    public function findByUsername(string $username): ?User;
    public function findByStoreId(Uuid $storeId): array;
    public function delete(Uuid $id): void;
}
