<?php

declare(strict_types=1);

namespace Caisse\Cli\Command;

use Caisse\Domain\Entity\Sale;
use Caisse\Domain\Entity\SaleItem;
use Caisse\Domain\Entity\Payment;
use Caisse\Domain\Entity\PaymentMethod;
use Caisse\Domain\ValueObject\Money;
use Caisse\Infrastructure\Repository\SqliteSaleRepository;
use Caisse\Infrastructure\Repository\SqliteSaleItemRepository;
use Caisse\Infrastructure\Repository\SqlitePaymentRepository;
use Caisse\Infrastructure\Repository\SqliteStoreRepository;
use Caisse\Infrastructure\Repository\SqliteUserRepository;

class SaleCreateCommand extends Command
{
    public function execute(array $args): int
    {
        if (count($args) < 3) {
            echo "Usage: pos sale:create <store_code> <device_id> <user_id> [customer_id] [items...]\n";
            echo "Items format: product_id:quantity\n";
            return 1;
        }

        $storeCode = $args[0];
        $deviceId = $args[1];
        $userId = $args[2];
        $customerId = $args[3] ?? null;
        $itemArgs = array_slice($args, 4);
        
        $storeRepo = new SqliteStoreRepository($this->connection);
        $store = $storeRepo->findByCode($storeCode);
        
        if (!$store) {
            echo "Store not found: {$storeCode}\n";
            return 1;
        }

        $userRepo = new SqliteUserRepository($this->connection);
        $user = $userRepo->findById(\Caisse\Domain\ValueObject\Uuid::fromString($userId));
        
        if (!$user) {
            echo "User not found\n";
            return 1;
        }

        $sale = Sale::create(
            $store->getId(),
            \Caisse\Domain\ValueObject\Uuid::fromString($deviceId),
            $user->getId(),
            $customerId ? \Caisse\Domain\ValueObject\Uuid::fromString($customerId) : null
        );
        
        $saleRepo = new SqliteSaleRepository($this->connection);
        $saleRepo->save($sale);
        
        $saleItemRepo = new SqliteSaleItemRepository($this->connection);
        
        foreach ($itemArgs as $itemArg) {
            [$productId, $quantity] = explode(':', $itemArg);
            
            $product = (new SqliteProductRepository($this->connection))->findById(\Caisse\Domain\ValueObject\Uuid::fromString($productId));
            if (!$product) {
                echo "Product not found: {$productId}\n";
                return 1;
            }
            
            $saleItem = SaleItem::create(
                $product->getId(),
                $product->getName(),
                (int)$quantity,
                $product->getSellingPrice()
            );
            $saleItem->setSaleId($sale->getId());
            $saleItemRepo->save($saleItem);
            
            $sale->addItem($saleItem);
        }
        
        $saleRepo->save($sale);
        
        echo "Sale created: {$sale->getId()} - Total: " . number_format($sale->getTotalAmount()->getAmount()) . "\n";
        return 0;
    }

    public function getDescription(): string
    {
        return 'Create a new sale';
    }
}
