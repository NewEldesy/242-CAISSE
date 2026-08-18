<?php

declare(strict_types=1);

namespace Caisse\Infrastructure\Repository;

use Caisse\Domain\Entity\SyncQueue;
use Caisse\Domain\Entity\SyncQueueStatus;
use Caisse\Infrastructure\Database\Connection;

class SqliteSyncQueueRepository
{
    public function __construct(private Connection $connection)
    {
    }

    public function save(SyncQueue $queue): void
    {
        $this->connection->execute(
            "INSERT OR REPLACE INTO sync_queue (id, event_id, aggregate_type, aggregate_id, event_name, payload, status, retry_count, last_error, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $queue->getId()->getValue(),
                $queue->getEventId()->getValue(),
                $queue->getAggregateType(),
                $queue->getAggregateId()->getValue(),
                $queue->getEventName(),
                json_encode($queue->getPayload()),
                $queue->getStatus()->value,
                $queue->getRetryCount(),
                $queue->getLastError(),
                $queue->getCreatedAt()->format('c'),
                $queue->getUpdatedAt()->format('c'),
            ]
        );
    }

    /** @return array<int, SyncQueue> */
    public function findPending(int $limit = 100): array
    {
        $rows = $this->connection->fetchAll(
            "SELECT * FROM sync_queue WHERE status = ? ORDER BY created_at ASC LIMIT ?",
            [SyncQueueStatus::PENDING->value, $limit]
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    /** @return array<int, SyncQueue> */
    public function findFailed(int $limit = 100): array
    {
        $rows = $this->connection->fetchAll(
            "SELECT * FROM sync_queue WHERE status = ? ORDER BY updated_at ASC LIMIT ?",
            [SyncQueueStatus::FAILED->value, $limit]
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    private function hydrate(array $row): SyncQueue
    {
        return SyncQueue::reconstitute(
            \Caisse\Domain\ValueObject\Uuid::fromString($row['id']),
            \Caisse\Domain\ValueObject\Uuid::fromString($row['event_id']),
            $row['aggregate_type'],
            \Caisse\Domain\ValueObject\Uuid::fromString($row['aggregate_id']),
            $row['event_name'],
            json_decode($row['payload'], true),
            SyncQueueStatus::from($row['status']),
            (int)$row['retry_count'],
            $row['last_error'],
            new \DateTimeImmutable($row['created_at']),
            new \DateTimeImmutable($row['updated_at'])
        );
    }
}
