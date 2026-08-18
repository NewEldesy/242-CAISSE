<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

use Caisse\Domain\ValueObject\Uuid;

final class Category extends Entity
{
    private function __construct(
        Uuid $id,
        private string $name,
        private ?string $description = null,
        private ?Uuid $parentId = null
    ) {
        parent::__construct($id);
    }

    public static function create(string $name, ?string $description = null, ?Uuid $parentId = null): self
    {
        return new self(Uuid::generate(), $name, $description, $parentId);
    }

    public static function reconstitute(
        Uuid $id,
        string $name,
        ?string $description,
        ?Uuid $parentId,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ): self {
        $category = new self($id, $name, $description, $parentId);
        $ref = new \ReflectionClass($category);
        $refProp = $ref->getProperty('createdAt');
        $refProp->setAccessible(true);
        $refProp->setValue($category, $createdAt);
        $refProp = $ref->getProperty('updatedAt');
        $refProp->setAccessible(true);
        $refProp->setValue($category, $updatedAt);
        return $category;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getParentId(): ?Uuid
    {
        return $this->parentId;
    }
}
