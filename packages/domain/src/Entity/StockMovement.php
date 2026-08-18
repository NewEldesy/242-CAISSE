<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

use Caisse\Domain\ValueObject\Uuid;

final class StockMovement extends Entity
{
    private function __construct(
        Uuid $id,
        private Uuid $storeId,
        private Uuid $productId,
        private ?Uuid $saleId,
        private StockMovementType $type,
        private int $quantityBefore,
        private int $quantityAfter,
        private ?string $notes = null
    ) {
        parent::__construct($id);
    }

    public static function reconstitute(
        Uuid $id,
        Uuid $storeId,
        Uuid $productId,
        ?Uuid $saleId,
        StockMovementType $type,
        int $quantityBefore,
        int $quantityAfter,
        \DateTimeImmutable $createdAt,
        ?string $notes = null
    ): self {
        $movement = new self($id, $storeId, $productId, $saleId, $type, $quantityBefore, $quantityAfter, $notes);
        $ref = new \ReflectionClass($movement);
        $refProp = $ref->getProperty('createdAt');
        $refProp->setAccessible(true);
        $refProp->setValue($movement, $createdAt);
        return $movement;
    }

    public static function create(
        Uuid $storeId,
        Uuid $productId,
        StockMovementType $type,
        int $quantityBefore,
        int $quantityAfter,
        ?Uuid $saleId = null,
        ?string $notes = null
    ): self {
        if ($quantityAfter < 0) {
            throw new \DomainException("Stock after movement cannot be negative");
        }

        return new self(Uuid::generate(), $storeId, $productId, $saleId, $type, $quantityBefore, $quantityAfter, $notes);
    }

    public function getStoreId(): Uuid
    {
        return $this->storeId;
    }

    public function getProductId(): Uuid
    {
        return $this->productId;
    }

    public function getSaleId(): ?Uuid
    {
        return $this->saleId;
    }

    public function getType(): StockMovementType
    {
        return $this->type;
    }

    public function getQuantityBefore(): int
    {
        return $this->quantityBefore;
    }

    public function getQuantityAfter(): int
    {
        return $this->quantityAfter;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function getQuantityChange(): int
    {
        return $this->quantityAfter - $this->quantityBefore;
    }
}
