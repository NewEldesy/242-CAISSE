<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

use Caisse\Domain\ValueObject\Uuid;

final class User extends Entity
{
    private function __construct(
        Uuid $id,
        private Uuid $storeId,
        private string $username,
        private string $email,
        private string $passwordHash,
        private UserRole $role,
        private UserStatus $status,
        private ?string $fullName = null
    ) {
        parent::__construct($id);
    }

    public static function create(
        Uuid $storeId,
        string $username,
        string $email,
        string $passwordHash,
        UserRole $role,
        ?string $fullName = null
    ): self {
        return new self(Uuid::generate(), $storeId, $username, $email, $passwordHash, $role, UserStatus::ACTIVE, $fullName);
    }

    public static function reconstitute(
        Uuid $id,
        Uuid $storeId,
        string $username,
        string $email,
        string $passwordHash,
        UserRole $role,
        UserStatus $status,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
        ?string $fullName = null
    ): self {
        $user = new self($id, $storeId, $username, $email, $passwordHash, $role, $status, $fullName);
        $user->createdAt = $createdAt;
        $user->updatedAt = $updatedAt;
        return $user;
    }

    public function getStoreId(): Uuid
    {
        return $this->storeId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function deactivate(): void
    {
        $this->status = UserStatus::INACTIVE;
        $this->touch();
    }
}
