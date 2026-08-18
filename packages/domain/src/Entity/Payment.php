<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

use Caisse\Domain\ValueObject\Uuid;
use Caisse\Domain\ValueObject\Money;

final class Payment extends Entity
{
    private function __construct(
        Uuid $id,
        private Uuid $saleId,
        private PaymentMethod $method,
        private Money $amount,
        private ?string $reference = null
    ) {
        parent::__construct($id);
    }

    public static function create(Uuid $saleId, PaymentMethod $method, Money $amount, ?string $reference = null): self
    {
        if ($amount->getAmount() <= 0) {
            throw new \DomainException("Payment amount must be positive");
        }

        return new self(Uuid::generate(), $saleId, $method, $amount, $reference);
    }

    public function getSaleId(): Uuid
    {
        return $this->saleId;
    }

    public function getMethod(): PaymentMethod
    {
        return $this->method;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }
}
