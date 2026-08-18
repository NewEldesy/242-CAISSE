<?php

declare(strict_types=1);

namespace Caisse\Domain\ValueObject;

use Caisse\Domain\Exception\InvalidMoneyException;

final class Money
{
    private function __construct(
        private int $amount,
        private string $currency
    ) {
        if ($amount < 0) {
            throw new InvalidMoneyException("Money amount cannot be negative: {$amount}");
        }

        if (strlen($currency) !== 3) {
            throw new InvalidMoneyException("Currency must be 3 characters: {$currency}");
        }
    }

    public static function of(int $amount, string $currency = 'XAF'): self
    {
        return new self($amount, strtoupper($currency));
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount - $other->amount, $this->currency);
    }

    public function isGreaterThanOrEqual(self $other): bool
    {
        $this->assertSameCurrency($other);
        return $this->amount >= $other->amount;
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidMoneyException("Currency mismatch: {$this->currency} vs {$other->currency}");
        }
    }
}
