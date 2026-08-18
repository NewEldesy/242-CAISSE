<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

use Caisse\Domain\ValueObject\Uuid;
use Caisse\Domain\ValueObject\Money;

final class CashSession extends Entity
{
    private ?\DateTimeImmutable $closedAt = null;

    private function __construct(
        Uuid $id,
        private Uuid $storeId,
        private Uuid $deviceId,
        private Uuid $userId,
        private Money $openingAmount,
        private ?Money $closingAmount = null,
        private ?string $notes = null
    ) {
        parent::__construct($id);
    }

    public static function open(Uuid $storeId, Uuid $deviceId, Uuid $userId, Money $openingAmount, ?string $notes = null): self
    {
        return new self(Uuid::generate(), $storeId, $deviceId, $userId, $openingAmount, null, $notes);
    }

    public static function reconstitute(
        Uuid $id,
        Uuid $storeId,
        Uuid $deviceId,
        Uuid $userId,
        Money $openingAmount,
        ?Money $closingAmount,
        \DateTimeImmutable $openedAt,
        ?\DateTimeImmutable $closedAt,
        ?string $notes = null
    ): self {
        $session = new self($id, $storeId, $deviceId, $userId, $openingAmount, $closingAmount, $notes);
        $session->createdAt = $openedAt;
        if ($closedAt) {
            $session->closedAt = $closedAt;
        }
        return $session;
    }

    public function getStoreId(): Uuid
    {
        return $this->storeId;
    }

    public function getDeviceId(): Uuid
    {
        return $this->deviceId;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getOpeningAmount(): Money
    {
        return $this->openingAmount;
    }

    public function getClosingAmount(): ?Money
    {
        return $this->closingAmount;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function isOpen(): bool
    {
        return $this->closedAt === null;
    }

    public function close(Money $closingAmount, ?string $notes = null): void
    {
        if (!$this->isOpen()) {
            throw new \DomainException("Cash session is already closed");
        }

        $this->closingAmount = $closingAmount;
        $this->closedAt = new \DateTimeImmutable();
        $this->notes = $notes ?? $this->notes;
        $this->touch();
    }
}
