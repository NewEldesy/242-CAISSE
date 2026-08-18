<?php

declare(strict_types=1);

namespace Caisse\Cli\Command;

use Caisse\Domain\Entity\Device;
use Caisse\Domain\Entity\DeviceType;
use Caisse\Infrastructure\Repository\SqliteDeviceRepository;
use Caisse\Infrastructure\Repository\SqliteStoreRepository;

class DeviceCreateCommand extends Command
{
    public function execute(array $args): int
    {
        if (count($args) < 3) {
            echo "Usage: pos device:create <store_code> <name> <identifier> [type]\n";
            return 1;
        }

        [$storeCode, $name, $identifier, $type] = array_pad($args, 4, DeviceType::POS->value);
        
        $storeRepo = new SqliteStoreRepository($this->connection);
        $store = $storeRepo->findByCode($storeCode);
        
        if (!$store) {
            echo "Store not found: {$storeCode}\n";
            return 1;
        }

        $deviceType = DeviceType::tryFrom($type) ?? DeviceType::POS;
        $device = Device::create($store->getId(), $name, $identifier, $deviceType);
        
        $repo = new SqliteDeviceRepository($this->connection);
        $repo->save($device);
        
        echo "Device created: {$device->getId()} ({$device->getName()})\n";
        return 0;
    }

    public function getDescription(): string
    {
        return 'Create a new device in a store';
    }
}
