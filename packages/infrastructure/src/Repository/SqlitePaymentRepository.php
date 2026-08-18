<?php

declare(strict_types=1);

namespace Caisse\Infrastructure\Repository;

use Caisse\Domain\Entity\Payment;
use Caisse\Infrastructure\Database\Connection;

class SqlitePaymentRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function save(Payment $payment): void
    {
        $this->connection->execute(
            "INSERT INTO payments (id, sale_id, method, amount, reference, created_at) VALUES (?, ?, ?, ?, ?, ?)",
            [
                $payment->getId()->getValue(),
                $payment->getSaleId()->getValue(),
                $payment->getMethod()->value,
                $payment->getAmount()->getAmount(),
                $payment->getReference(),
                (new \DateTimeImmutable())->format('c'),
            ]
        );
    }

    /** @return array<int, Payment> */
    public function findBySaleId(string $saleId): array
    {
        $rows = $this->connection->fetchAll(
            "SELECT * FROM payments WHERE sale_id = ?",
            [$saleId]
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    private function hydrate(array $row): Payment
    {
        return Payment::create(
            \Caisse\Domain\ValueObject\Uuid::fromString($row['sale_id']),
            \Caisse\Domain\Entity\PaymentMethod::from($row['method']),
            \Caisse\Domain\ValueObject\Money::of((int)$row['amount']),
            $row['reference']
        );
    }
}
