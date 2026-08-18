<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

use Caisse\Domain\ValueObject\Uuid;
use Caisse\Domain\ValueObject\Money;

final class Sale extends Entity
{
    /** @var array<int, SaleItem> */
    private array $items = [];
    /** @var array<int, Payment> */
    private array $payments = [];
    private ?Uuid $cashSessionId = null;

    private function __construct(
        Uuid $id,
        private Uuid $storeId,
        private Uuid $deviceId,
        private Uuid $userId,
        private ?Uuid $customerId,
        private SaleStatus $status,
        private Money $totalAmount
    ) {
        parent::__construct($id);
    }

    public static function reconstitute(
        Uuid $id,
        Uuid $storeId,
        Uuid $deviceId,
        Uuid $userId,
        ?Uuid $customerId,
        SaleStatus $status,
        Money $totalAmount,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
        ?Uuid $cashSessionId = null
    ): self {
        $sale = new self(Uuid::generate(), $storeId, $deviceId, $userId, $customerId, $status, $totalAmount);
        // Use reflection to set private id and timestamps
        $ref = new \ReflectionClass($sale);
        $refProp = $ref->getProperty('id');
        $refProp->setAccessible(true);
        $refProp->setValue($sale, $id);
        $refProp = $ref->getProperty('createdAt');
        $refProp->setAccessible(true);
        $refProp->setValue($sale, $createdAt);
        $refProp = $ref->getProperty('updatedAt');
        $refProp->setAccessible(true);
        $refProp->setValue($sale, $updatedAt);
        
        if ($cashSessionId !== null) {
            $refProp = $ref->getProperty('cashSessionId');
            $refProp->setAccessible(true);
            $refProp->setValue($sale, $cashSessionId);
        }
        
        return $sale;
    }

    public static function create(
        Uuid $storeId,
        Uuid $deviceId,
        Uuid $userId,
        ?Uuid $customerId = null
    ): self {
        return new self(Uuid::generate(), $storeId, $deviceId, $userId, $customerId, SaleStatus::PENDING, Money::of(0));
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

    public function getCustomerId(): ?Uuid
    {
        return $this->customerId;
    }

    public function getStatus(): SaleStatus
    {
        return $this->status;
    }

    public function getTotalAmount(): Money
    {
        return $this->totalAmount;
    }

    /** @return array<int, SaleItem> */
    public function getItems(): array
    {
        return $this->items;
    }

    /** @return array<int, Payment> */
    public function getPayments(): array
    {
        return $this->payments;
    }

    public function getCashSessionId(): ?Uuid
    {
        return $this->cashSessionId;
    }

    public function addItem(SaleItem $item): void
    {
        $this->items[] = $item;
        $this->recalculateTotal();
        $this->touch();
    }

    public function addPayment(Payment $payment): void
    {
        $this->payments[] = $payment;
        $this->touch();
    }

    public function complete(): void
    {
        if ($this->status !== SaleStatus::PENDING) {
            throw new \DomainException("Cannot complete sale in status: {$this->status->value}");
        }

        $totalPaid = Money::of(0);
        foreach ($this->payments as $payment) {
            $totalPaid = $totalPaid->add($payment->getAmount());
        }

        if (!$totalPaid->isGreaterThanOrEqual($this->totalAmount)) {
            throw new \DomainException("Insufficient payment for sale completion");
        }

        $this->status = SaleStatus::COMPLETED;
        $this->touch();
    }

    public function cancel(): void
    {
        if ($this->status === SaleStatus::COMPLETED) {
            throw new \DomainException("Cannot cancel a completed sale");
        }

        $this->status = SaleStatus::CANCELLED;
        $this->touch();
    }

    public function setCashSessionId(Uuid $cashSessionId): void
    {
        $this->cashSessionId = $cashSessionId;
        $this->touch();
    }

    private function recalculateTotal(): void
    {
        $total = Money::of(0);
        foreach ($this->items as $item) {
            $total = $total->add($item->getSubtotal());
        }
        $this->totalAmount = $total;
    }
}
