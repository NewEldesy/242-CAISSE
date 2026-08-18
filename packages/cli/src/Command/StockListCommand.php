<?php

declare(strict_types=1);

namespace Caisse\Cli\Command;

use Caisse\Infrastructure\Repository\SqliteStockRepository;
use Caisse\Infrastructure\Repository\SqliteStoreRepository;

class StockListCommand extends Command
{
    public function execute(array $args): int
    {
        $storeCode = $args[0] ?? null;
        
        if (!$storeCode) {
            echo "Usage: pos stock:list <store_code>\n";
            return 1;
        }
        
        $storeRepo = new SqliteStoreRepository($this->connection);
        $store = $storeRepo->findByCode($storeCode);
        
        if (!$store) {
            echo "Store not found: {$storeCode}\n";
            return 1;
        }
        
        $repo = new SqliteStockRepository($this->connection);
        $stocks = $repo->findByStoreId($store->getId());
        
        echo str_pad('Product ID', 36) . "  " . str_pad('Quantity', 10) . "  " . str_pad('Status', 12) . "\n";
        echo str_repeat('-', 62) . "\n";
        
        foreach ($stocks as $stock) {
            echo str_pad($stock->getProductId()->getValue(), 36) . "  " .
                 str_pad((string)$stock->getQuantity(), 10) . "  " .
                 $stock->getStatus()->value . "\n";
        }
        
        return 0;
    }

    public function getDescription(): string
    {
        return 'List stock levels';
    }
}
