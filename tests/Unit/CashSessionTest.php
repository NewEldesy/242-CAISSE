<?php

declare(strict_types=1);

namespace Caisse\Tests\Unit;

use Caisse\Domain\Entity\CashSession;
use Caisse\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class CashSessionTest extends TestCase
{
    public function testOpensCashSession(): void
    {
        $session = CashSession::open(
            \Caisse\Domain\ValueObject\Uuid::generate(),
            \Caisse\Domain\ValueObject\Uuid::generate(),
            \Caisse\Domain\ValueObject\Uuid::generate(),
            Money::of(5000)
        );
        
        $this->assertTrue($session->isOpen());
        $this->assertEquals(5000, $session->getOpeningAmount()->getAmount());
    }

    public function testClosesCashSession(): void
    {
        $session = CashSession::open(
            \Caisse\Domain\ValueObject\Uuid::generate(),
            \Caisse\Domain\ValueObject\Uuid::generate(),
            \Caisse\Domain\ValueObject\Uuid::generate(),
            Money::of(5000)
        );
        
        $session->close(Money::of(7500));
        $this->assertFalse($session->isOpen());
        $this->assertEquals(7500, $session->getClosingAmount()->getAmount());
    }

    public function testRejectsDoubleClose(): void
    {
        $this->expectException(\DomainException::class);
        
        $session = CashSession::open(
            \Caisse\Domain\ValueObject\Uuid::generate(),
            \Caisse\Domain\ValueObject\Uuid::generate(),
            \Caisse\Domain\ValueObject\Uuid::generate(),
            Money::of(5000)
        );
        
        $session->close(Money::of(7500));
        $session->close(Money::of(7500));
    }
}
