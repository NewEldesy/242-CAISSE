<?php

declare(strict_types=1);

namespace Caisse\Tests\Unit;

use Caisse\Domain\Entity\Sale;
use Caisse\Domain\Entity\SaleItem;
use Caisse\Domain\Entity\SaleStatus;
use Caisse\Domain\Entity\Payment;
use Caisse\Domain\Entity\PaymentMethod;
use Caisse\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class SaleTest extends TestCase
{
    public function testCreatesSale(): void
    {
        $sale = Sale::create(
            \Caisse\Domain\ValueObject\Uuid::generate(),
            \Caisse\Domain\ValueObject\Uuid::generate(),
            \Caisse\Domain\ValueObject\Uuid::generate()
        );
        
        $this->assertEquals(SaleStatus::PENDING, $sale->getStatus());
        $this->assertEquals(0, $sale->getTotalAmount()->getAmount());
    }

    public function testAddsItemAndRecalculatesTotal(): void
    {
        $sale = Sale::create(
            \Caisse\Domain\ValueObject\Uuid::generate(),
            \Caisse\Domain\ValueObject\Uuid::generate(),
            \Caisse\Domain\ValueObject\Uuid::generate()
        );
        
        $item = SaleItem::create(
            \Caisse\Domain\ValueObject\Uuid::generate(),
            'Product A',
            2,
            Money::of(500)
        );
        $item->setSaleId($sale->getId());
        $sale->addItem($item);
        
        $this->assertEquals(1000, $sale->getTotalAmount()->getAmount());
    }

    public function testCompletesSale(): void
    {
        $sale = Sale::create(
            \Caisse\Domain\ValueObject\Uuid::generate(),
            \Caisse\Domain\ValueObject\Uuid::generate(),
            \Caisse\Domain\ValueObject\Uuid::generate()
        );
        
        $item = SaleItem::create(
            \Caisse\Domain\ValueObject\Uuid::generate(),
            'Product A',
            1,
            Money::of(1000)
        );
        $item->setSaleId($sale->getId());
        $sale->addItem($item);
        
        $payment = Payment::create($sale->getId(), PaymentMethod::CASH, Money::of(1000));
        $sale->addPayment($payment);
        $sale->complete();
        
        $this->assertEquals(SaleStatus::COMPLETED, $sale->getStatus());
    }

    public function testRejectsCompletionWithInsufficientPayment(): void
    {
        $this->expectException(\DomainException::class);
        
        $sale = Sale::create(
            \Caisse\Domain\ValueObject\Uuid::generate(),
            \Caisse\Domain\ValueObject\Uuid::generate(),
            \Caisse\Domain\ValueObject\Uuid::generate()
        );
        
        $item = SaleItem::create(
            \Caisse\Domain\ValueObject\Uuid::generate(),
            'Product A',
            1,
            Money::of(1000)
        );
        $item->setSaleId($sale->getId());
        $sale->addItem($item);
        
        $payment = Payment::create($sale->getId(), PaymentMethod::CASH, Money::of(500));
        $sale->addPayment($payment);
        $sale->complete();
    }
}
