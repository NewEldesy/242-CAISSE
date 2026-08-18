<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

use Caisse\Domain\ValueObject\Uuid;

final class Store extends Entity
{
    private function __construct(
        Uuid $id,
        private string $name,
        private string $code,
        private StoreStatus $status,
        private ?string $address = null,
        private ?string $phone = null
    ) {
        parent::__construct($id);
    }

    public static function create(string $name, string $code, ?string $address = null, ?string $phone = null): self
    {
        return new self(Uuid::generate(), $name, $code, StoreStatus::ACTIVE, $address, $phone);
    }

    public static function reconstitute(
        Uuid $id,
        string $name,
        string $code,
        StoreStatus $status,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
        ?string $address = null,
        ?string $phone = null
    ): self {
        $store = new self($id, $name, $code, $status, $address, $phone);
        $store->createdAt = $createdAt;
        $store->updatedAt = $updatedAt;
        return $store;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getStatus(): StoreStatus
    {
        return $this->status;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function deactivate(): void
    {
        $this->status = StoreStatus::INACTIVE;
        $this->touch();
    }

    public function activate(): void
    {
        $this->status = StoreStatus::ACTIVE;
        $this->touch();
    }
}
