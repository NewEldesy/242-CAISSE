<?php

declare(strict_types=1);

namespace Caisse\Cli\Command;

use Caisse\Infrastructure\Repository\SqliteSaleRepository;
use Caisse\Infrastructure\Repository\SqliteStoreRepository;

class SaleListCommand extends Command
{
    public function execute(array $args): int
    {
        $storeCode = $args[0] ?? null;
        
        if (!$storeCode) {
            echo "Usage: pos sale:list <store_code>\n";
            return 1;
        }
        
        $storeRepo = new SqliteStoreRepository($this->connection);
        $store = $storeRepo->findByCode($storeCode);
        
        if (!$store) {
            echo "Store not found: {$storeCode}\n";
            return 1;
        }
        
        $repo = new SqliteSaleRepository($this->connection);
        $sales = $repo->findByStoreId($store->getId());
        
        echo str_pad('ID', 36) . "  " . str_pad('Status', 12) . "  " . str_pad('Total', 15) . "  " . str_pad('Date', 20) . "\n";
        echo str_repeat('-', 85) . "\n";
        
        foreach ($sales as $sale) {
            echo str_pad($sale->getId()->getValue(), 36) . "  " .
                 str_pad($sale->getStatus()->value, 12) . "  " .
                 str_pad(number_format($sale->getTotalAmount()->getAmount()), 15) . "  " .
                 $sale->getCreatedAt()->format('Y-m-d H:i') . "\n";
        }
        
        return 0;
    }

    public function getDescription(): string
    {
        return 'List sales';
    }
}
