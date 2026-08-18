<?php

declare(strict_types=1);

namespace Caisse\Tests\Unit;

use Caisse\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testCreatesMoneyWithPositiveAmount(): void
    {
        $money = Money::of(1000, 'XAF');
        $this->assertEquals(1000, $money->getAmount());
        $this->assertEquals('XAF', $money->getCurrency());
    }

    public function testRejectsNegativeAmount(): void
    {
        $this->expectException(\DomainException::class);
        Money::of(-100);
    }

    public function testAddsMoney(): void
    {
        $a = Money::of(500);
        $b = Money::of(300);
        $result = $a->add($b);
        $this->assertEquals(800, $result->getAmount());
    }

    public function testSubtractsMoney(): void
    {
        $a = Money::of(500);
        $b = Money::of(300);
        $result = $a->subtract($b);
        $this->assertEquals(200, $result->getAmount());
    }
}
