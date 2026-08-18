<?php

declare(strict_types=1);

namespace Caisse\Infrastructure\Repository;

use Caisse\Domain\Entity\CashSession;
use Caisse\Domain\Repository\CashSessionRepositoryInterface;
use Caisse\Domain\ValueObject\Money;
use Caisse\Domain\ValueObject\Uuid;
use Caisse\Infrastructure\Database\Connection;

class SqliteCashSessionRepository implements CashSessionRepositoryInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function save(CashSession $cashSession): void
    {
        $closedAt = $cashSession->isOpen() ? null : ($cashSession->getClosingAmount() ? (new \DateTimeImmutable())->format('c') : null);

        $this->connection->execute(
            "INSERT OR REPLACE INTO cash_sessions (id, store_id, device_id, user_id, opening_amount, closing_amount, notes, closed_at, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $cashSession->getId()->getValue(),
                $cashSession->getStoreId()->getValue(),
                $cashSession->getDeviceId()->getValue(),
                $cashSession->getUserId()->getValue(),
                $cashSession->getOpeningAmount()->getAmount(),
                $cashSession->getClosingAmount()?->getAmount(),
                $cashSession->getNotes(),
                $closedAt,
                $cashSession->getCreatedAt()->format('c'),
            ]
        );
    }

    public function findById(Uuid $id): ?CashSession
    {
        $row = $this->connection->fetchOne(
            "SELECT * FROM cash_sessions WHERE id = ?",
            [$id->getValue()]
        );

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findOpenByDeviceAndUser(Uuid $deviceId, Uuid $userId): ?CashSession
    {
        $row = $this->connection->fetchOne(
            "SELECT * FROM cash_sessions WHERE device_id = ? AND user_id = ? AND closed_at IS NULL ORDER BY created_at DESC LIMIT 1",
            [$deviceId->getValue(), $userId->getValue()]
        );

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    /** @return array<int, CashSession> */
    public function findByStoreId(Uuid $storeId): array
    {
        $rows = $this->connection->fetchAll(
            "SELECT * FROM cash_sessions WHERE store_id = ? ORDER BY created_at DESC",
            [$storeId->getValue()]
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    private function hydrate(array $row): CashSession
    {
        $closingAmount = $row['closing_amount'] !== null ? Money::of((int)$row['closing_amount']) : null;
        $closedAt = $row['closed_at'] !== null ? new \DateTimeImmutable($row['closed_at']) : null;

        return CashSession::reconstitute(
            Uuid::fromString($row['id']),
            Uuid::fromString($row['store_id']),
            Uuid::fromString($row['device_id']),
            Uuid::fromString($row['user_id']),
            Money::of((int)$row['opening_amount']),
            $closingAmount,
            new \DateTimeImmutable($row['created_at']),
            $closedAt,
            $row['notes']
        );
    }
}
