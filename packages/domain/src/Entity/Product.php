<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

use Caisse\Domain\ValueObject\Uuid;
use Caisse\Domain\ValueObject\Money;

final class Product extends Entity
{
    private function __construct(
        Uuid $id,
        private Uuid $storeId,
        private ?Uuid $categoryId,
        private string $name,
        private ?string $description,
        private ?string $reference,
        private Money $sellingPrice,
        private Money $purchasePrice,
        private ProductStatus $status
    ) {
        parent::__construct($id);
    }

    public static function create(
        Uuid $storeId,
        string $name,
        Money $sellingPrice,
        Money $purchasePrice,
        ?Uuid $categoryId = null,
        ?string $description = null,
        ?string $reference = null
    ): self {
        return new self(Uuid::generate(), $storeId, $categoryId, $name, $description, $reference, $sellingPrice, $purchasePrice, ProductStatus::ACTIVE);
    }

    public static function reconstitute(
        Uuid $id,
        Uuid $storeId,
        ?Uuid $categoryId,
        string $name,
        ?string $description,
        ?string $reference,
        Money $sellingPrice,
        Money $purchasePrice,
        ProductStatus $status,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ): self {
        $product = new self($id, $storeId, $categoryId, $name, $description, $reference, $sellingPrice, $purchasePrice, $status);
        $ref = new \ReflectionClass($product);
        $refProp = $ref->getProperty('createdAt');
        $refProp->setAccessible(true);
        $refProp->setValue($product, $createdAt);
        $refProp = $ref->getProperty('updatedAt');
        $refProp->setAccessible(true);
        $refProp->setValue($product, $updatedAt);
        return $product;
    }

    public function getStoreId(): Uuid
    {
        return $this->storeId;
    }

    public function getCategoryId(): ?Uuid
    {
        return $this->categoryId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function getSellingPrice(): Money
    {
        return $this->sellingPrice;
    }

    public function getPurchasePrice(): Money
    {
        return $this->purchasePrice;
    }

    public function getStatus(): ProductStatus
    {
        return $this->status;
    }

    public function updatePrice(Money $newSellingPrice): void
    {
        $this->sellingPrice = $newSellingPrice;
        $this->touch();
    }

    public function deactivate(): void
    {
        $this->status = ProductStatus::INACTIVE;
        $this->touch();
    }
}
