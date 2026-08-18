<?php

declare(strict_types=1);

namespace Caisse\Infrastructure\Repository;

use Caisse\Domain\Entity\Device;
use Caisse\Domain\Entity\DeviceStatus;
use Caisse\Domain\Entity\DeviceType;
use Caisse\Domain\Repository\DeviceRepositoryInterface;
use Caisse\Domain\ValueObject\Uuid;
use Caisse\Infrastructure\Database\Connection;

class SqliteDeviceRepository implements DeviceRepositoryInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function save(Device $device): void
    {
        $this->connection->execute(
            "INSERT OR REPLACE INTO devices (id, store_id, name, identifier, type, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $device->getId()->getValue(),
                $device->getStoreId()->getValue(),
                $device->getName(),
                $device->getIdentifier(),
                $device->getType()->value,
                $device->getStatus()->value,
                $device->getCreatedAt()->format('c'),
                $device->getUpdatedAt()->format('c'),
            ]
        );
    }

    public function findById(Uuid $id): ?Device
    {
        $row = $this->connection->fetchOne(
            "SELECT * FROM devices WHERE id = ?",
            [$id->getValue()]
        );

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findByStoreId(Uuid $storeId): array
    {
        $rows = $this->connection->fetchAll(
            "SELECT * FROM devices WHERE store_id = ?",
            [$storeId->getValue()]
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function findByIdentifier(string $identifier): ?Device
    {
        $row = $this->connection->fetchOne(
            "SELECT * FROM devices WHERE identifier = ?",
            [$identifier]
        );

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function delete(Uuid $id): void
    {
        $this->connection->execute(
            "DELETE FROM devices WHERE id = ?",
            [$id->getValue()]
        );
    }

    private function hydrate(array $row): Device
    {
        return Device::reconstitute(
            Uuid::fromString($row['id']),
            Uuid::fromString($row['store_id']),
            $row['name'],
            $row['identifier'],
            DeviceType::from($row['type']),
            DeviceStatus::from($row['status']),
            new \DateTimeImmutable($row['created_at']),
            new \DateTimeImmutable($row['updated_at'])
        );
    }
}
