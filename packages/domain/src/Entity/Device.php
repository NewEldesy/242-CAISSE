<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

use Caisse\Domain\ValueObject\Uuid;

final class Device extends Entity
{
    private function __construct(
        Uuid $id,
        private Uuid $storeId,
        private string $name,
        private string $identifier,
        private DeviceType $type,
        private DeviceStatus $status
    ) {
        parent::__construct($id);
    }

    public static function create(Uuid $storeId, string $name, string $identifier, DeviceType $type): self
    {
        return new self(Uuid::generate(), $storeId, $name, $identifier, $type, DeviceStatus::ACTIVE);
    }

    public static function reconstitute(
        Uuid $id,
        Uuid $storeId,
        string $name,
        string $identifier,
        DeviceType $type,
        DeviceStatus $status,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ): self {
        $device = new self($id, $storeId, $name, $identifier, $type, $status);
        $device->createdAt = $createdAt;
        $device->updatedAt = $updatedAt;
        return $device;
    }

    public function getStoreId(): Uuid
    {
        return $this->storeId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getType(): DeviceType
    {
        return $this->type;
    }

    public function getStatus(): DeviceStatus
    {
        return $this->status;
    }

    public function deactivate(): void
    {
        $this->status = DeviceStatus::INACTIVE;
        $this->touch();
    }
}
