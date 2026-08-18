<?php

declare(strict_types=1);

namespace Caisse\Cli\Command;

use Caisse\Domain\Entity\Category;
use Caisse\Domain\Entity\Product;
use Caisse\Domain\ValueObject\Money;
use Caisse\Infrastructure\Repository\SqliteProductRepository;
use Caisse\Infrastructure\Repository\SqliteCategoryRepository;
use Caisse\Infrastructure\Repository\SqliteStoreRepository;

class ProductCreateCommand extends Command
{
    public function execute(array $args): int
    {
        if (count($args) < 4) {
            echo "Usage: pos product:create <store_code> <name> <selling_price> <purchase_price> [category_name] [reference] [description]\n";
            return 1;
        }

        [$storeCode, $name, $sellingPrice, $purchasePrice, $categoryName, $reference, $description] = array_pad($args, 7, null);
        
        $storeRepo = new SqliteStoreRepository($this->connection);
        $store = $storeRepo->findByCode($storeCode);
        
        if (!$store) {
            echo "Store not found: {$storeCode}\n";
            return 1;
        }

        $categoryId = null;
        if ($categoryName !== null) {
            $categoryRepo = new SqliteCategoryRepository($this->connection);
            $categories = $categoryRepo->findAll();
            $category = array_values(array_filter($categories, fn($c) => $c->getName() === $categoryName))[0] ?? null;
            
            if (!$category) {
                $category = Category::create($categoryName);
                $categoryRepo->save($category);
            }
            $categoryId = $category->getId();
        }

        $product = Product::create(
            $store->getId(),
            $name,
            Money::of((int)$sellingPrice),
            Money::of((int)$purchasePrice),
            $categoryId,
            $description,
            $reference
        );
        
        $repo = new SqliteProductRepository($this->connection);
        $repo->save($product);
        
        echo "Product created: {$product->getName()} (ID: {$product->getId()})\n";
        return 0;
    }

    public function getDescription(): string
    {
        return 'Create a new product';
    }
}
