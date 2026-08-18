<?php

declare(strict_types=1);

namespace Caisse\Cli\Command;

use Caisse\Infrastructure\Repository\SqliteProductRepository;
use Caisse\Infrastructure\Repository\SqliteStoreRepository;

class ProductListCommand extends Command
{
    public function execute(array $args): int
    {
        $storeCode = $args[0] ?? null;
        
        if (!$storeCode) {
            echo "Usage: pos product:list <store_code>\n";
            return 1;
        }
        
        $storeRepo = new SqliteStoreRepository($this->connection);
        $store = $storeRepo->findByCode($storeCode);
        
        if (!$store) {
            echo "Store not found: {$storeCode}\n";
            return 1;
        }
        
        $repo = new SqliteProductRepository($this->connection);
        $products = $repo->findByStoreId($store->getId());
        
        echo str_pad('ID', 36) . "  " . str_pad('Name', 30) . "  " . str_pad('Price', 10) . "\n";
        echo str_repeat('-', 80) . "\n";
        
        foreach ($products as $product) {
            echo str_pad($product->getId()->getValue(), 36) . "  " .
                 str_pad($product->getName(), 30) . "  " .
                 number_format($product->getSellingPrice()->getAmount()) . "\n";
        }
        
        return 0;
    }

    public function getDescription(): string
    {
        return 'List products';
    }
}
