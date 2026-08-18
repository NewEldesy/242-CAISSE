<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

use Caisse\Domain\ValueObject\Uuid;
use Caisse\Domain\ValueObject\Money;

final class SaleItem
{
    private function __construct(
        private Uuid $id,
        private Uuid $saleId,
        private Uuid $productId,
        private string $productName,
        private int $quantity,
        private Money $unitPrice,
        private ?string $productReference = null
    ) {
        if ($quantity <= 0) {
            throw new \DomainException("Sale item quantity must be positive");
        }
    }

    public static function create(
        Uuid $productId,
        string $productName,
        int $quantity,
        Money $unitPrice,
        ?string $productReference = null
    ): self {
        return new self(Uuid::generate(), Uuid::generate(), $productId, $productName, $quantity, $unitPrice, $productReference);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getSaleId(): Uuid
    {
        return $this->saleId;
    }

    public function getProductId(): Uuid
    {
        return $this->productId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getUnitPrice(): Money
    {
        return $this->unitPrice;
    }

    public function getSubtotal(): Money
    {
        return Money::of($this->unitPrice->getAmount() * $this->quantity, $this->unitPrice->getCurrency());
    }

    public function getProductReference(): ?string
    {
        return $this->productReference;
    }

    public function setSaleId(Uuid $saleId): void
    {
        $this->saleId = $saleId;
    }
}
