<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

use Caisse\Domain\ValueObject\Uuid;

final class SyncQueue extends Entity
{
    private function __construct(
        Uuid $id,
        private Uuid $eventId,
        private string $aggregateType,
        private Uuid $aggregateId,
        private string $eventName,
        private array $payload,
        private SyncQueueStatus $status,
        private int $retryCount,
        private ?string $lastError
    ) {
        parent::__construct($id);
    }

    public static function create(
        Uuid $eventId,
        string $aggregateType,
        Uuid $aggregateId,
        string $eventName,
        array $payload
    ): self {
        return new self(
            Uuid::generate(),
            $eventId,
            $aggregateType,
            $aggregateId,
            $eventName,
            $payload,
            SyncQueueStatus::PENDING,
            0,
            null
        );
    }

    public static function reconstitute(
        Uuid $id,
        Uuid $eventId,
        string $aggregateType,
        Uuid $aggregateId,
        string $eventName,
        array $payload,
        SyncQueueStatus $status,
        int $retryCount,
        ?string $lastError,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ): self {
        $queue = new self($id, $eventId, $aggregateType, $aggregateId, $eventName, $payload, $status, $retryCount, $lastError);
        $ref = new \ReflectionClass($queue);
        $refProp = $ref->getProperty('createdAt');
        $refProp->setAccessible(true);
        $refProp->setValue($queue, $createdAt);
        $refProp = $ref->getProperty('updatedAt');
        $refProp->setAccessible(true);
        $refProp->setValue($queue, $updatedAt);
        return $queue;
    }

    public function getEventId(): Uuid
    {
        return $this->eventId;
    }

    public function getAggregateType(): string
    {
        return $this->aggregateType;
    }

    public function getAggregateId(): Uuid
    {
        return $this->aggregateId;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getStatus(): SyncQueueStatus
    {
        return $this->status;
    }

    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function markProcessing(): void
    {
        $this->status = SyncQueueStatus::PROCESSING;
        $this->touch();
    }

    public function markCompleted(): void
    {
        $this->status = SyncQueueStatus::COMPLETED;
        $this->touch();
    }

    public function markFailed(string $error): void
    {
        $this->status = SyncQueueStatus::FAILED;
        $this->lastError = $error;
        $this->retryCount++;
        $this->touch();
    }

    public function retry(): void
    {
        $this->status = SyncQueueStatus::PENDING;
        $this->lastError = null;
        $this->touch();
    }
}
