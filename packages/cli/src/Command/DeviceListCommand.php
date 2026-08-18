<?php

declare(strict_types=1);

namespace Caisse\Cli\Command;

use Caisse\Infrastructure\Repository\SqliteDeviceRepository;
use Caisse\Infrastructure\Repository\SqliteStoreRepository;

class DeviceListCommand extends Command
{
    public function execute(array $args): int
    {
        $storeCode = $args[0] ?? null;
        
        $storeRepo = new SqliteStoreRepository($this->connection);
        $store = $storeCode ? $storeRepo->findByCode($storeCode) : null;
        
        if (!$store) {
            echo "Store not found: " . ($storeCode ?? '') . "\n";
            return 1;
        }
        
        $repo = new SqliteDeviceRepository($this->connection);
        $devices = $repo->findByStoreId($store->getId());
        
        echo str_pad('ID', 36) . "  " . str_pad('Name', 20) . "  " . str_pad('Identifier', 20) . "  " . str_pad('Type', 10) . "\n";
        echo str_repeat('-', 90) . "\n";
        
        foreach ($devices as $device) {
            echo str_pad($device->getId()->getValue(), 36) . "  " .
                 str_pad($device->getName(), 20) . "  " .
                 str_pad($device->getIdentifier(), 20) . "  " .
                 $device->getType()->value . "\n";
        }
        
        return 0;
    }

    public function getDescription(): string
    {
        return 'List devices in a store';
    }
}
