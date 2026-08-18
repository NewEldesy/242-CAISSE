<?php

declare(strict_types=1);

namespace Caisse\Cli\Command;

use Caisse\Domain\Entity\Store;
use Caisse\Infrastructure\Repository\SqliteStoreRepository;

class StoreCreateCommand extends Command
{
    public function execute(array $args): int
    {
        if (count($args) < 2) {
            echo "Usage: pos store:create <name> <code> [address] [phone]\n";
            return 1;
        }

        [$name, $code, $address, $phone] = array_pad($args, 4, null);
        
        $store = Store::create($name, $code, $address, $phone);
        $repo = new SqliteStoreRepository($this->connection);
        $repo->save($store);
        
        echo "Store created: {$store->getId()} ({$store->getName()})\n";
        return 0;
    }

    public function getDescription(): string
    {
        return 'Create a new store';
    }
}
