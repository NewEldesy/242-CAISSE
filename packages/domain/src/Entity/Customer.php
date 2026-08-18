<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

use Caisse\Domain\ValueObject\Uuid;

final class Customer extends Entity
{
    private function __construct(
        Uuid $id,
        private Uuid $storeId,
        private ?string $firstName = null,
        private ?string $lastName = null,
        private ?string $phone = null,
        private ?string $email = null,
        private CustomerStatus $status = CustomerStatus::ACTIVE
    ) {
        parent::__construct($id);
    }

    public static function create(
        Uuid $storeId,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $phone = null,
        ?string $email = null
    ): self {
        return new self(Uuid::generate(), $storeId, $firstName, $lastName, $phone, $email);
    }

    public function getStoreId(): Uuid
    {
        return $this->storeId;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getFullName(): ?string
    {
        if (!$this->firstName && !$this->lastName) {
            return null;
        }
        return trim(($this->firstName ?? '') . ' ' . ($this->lastName ?? ''));
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getStatus(): CustomerStatus
    {
        return $this->status;
    }
}
