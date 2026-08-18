<?php

declare(strict_types=1);

namespace Caisse\Tests\Unit;

use Caisse\Domain\Entity\Product;
use Caisse\Domain\Entity\ProductStatus;
use Caisse\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testCreatesProduct(): void
    {
        $product = Product::create(
            \Caisse\Domain\ValueObject\Uuid::generate(),
            'Test Product',
            Money::of(1000),
            Money::of(600)
        );
        
        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals(1000, $product->getSellingPrice()->getAmount());
        $this->assertEquals(ProductStatus::ACTIVE, $product->getStatus());
    }

    public function testUpdatesPrice(): void
    {
        $product = Product::create(
            \Caisse\Domain\ValueObject\Uuid::generate(),
            'Test',
            Money::of(1000),
            Money::of(600)
        );
        
        $product->updatePrice(Money::of(1200));
        $this->assertEquals(1200, $product->getSellingPrice()->getAmount());
    }

    public function testDeactivates(): void
    {
        $product = Product::create(
            \Caisse\Domain\ValueObject\Uuid::generate(),
            'Test',
            Money::of(1000),
            Money::of(600)
        );
        
        $product->deactivate();
        $this->assertEquals(ProductStatus::INACTIVE, $product->getStatus());
    }
}
