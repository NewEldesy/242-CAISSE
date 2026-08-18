<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

use Caisse\Domain\ValueObject\Uuid;

final class Stock extends Entity
{
    private function __construct(
        Uuid $id,
        private Uuid $storeId,
        private Uuid $productId,
        private int $quantity,
        private StockStatus $status
    ) {
        parent::__construct($id);
    }

    public static function create(Uuid $storeId, Uuid $productId, int $initialQuantity = 0): self
    {
        return new self(Uuid::generate(), $storeId, $productId, $initialQuantity, StockStatus::AVAILABLE);
    }

    public static function reconstitute(
        Uuid $id,
        Uuid $storeId,
        Uuid $productId,
        int $quantity,
        StockStatus $status,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ): self {
        $stock = new self($id, $storeId, $productId, $quantity, $status);
        $stock->createdAt = $createdAt;
        $stock->updatedAt = $updatedAt;
        return $stock;
    }

    public function getStoreId(): Uuid
    {
        return $this->storeId;
    }

    public function getProductId(): Uuid
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getStatus(): StockStatus
    {
        return $this->status;
    }

    public function adjust(int $quantityChange): void
    {
        $newQuantity = $this->quantity + $quantityChange;
        if ($newQuantity < 0) {
            throw new \DomainException("Stock cannot be negative");
        }
        $this->quantity = $newQuantity;
        $this->touch();
    }

    public function setQuantity(int $quantity): void
    {
        if ($quantity < 0) {
            throw new \DomainException("Stock cannot be negative");
        }
        $this->quantity = $quantity;
        $this->touch();
    }
}
