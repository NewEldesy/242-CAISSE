<?php

declare(strict_types=1);

namespace Caisse\Domain\Event;

use Caisse\Domain\ValueObject\Uuid;

abstract class DomainEvent
{
    public function __construct(
        private Uuid $eventId = Uuid::generate(),
        private \DateTimeImmutable $occurredAt = new \DateTimeImmutable()
    ) {
    }

    public function getEventId(): Uuid
    {
        return $this->eventId;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }

    abstract public function getAggregateId(): Uuid;
    abstract public function getEventName(): string;
    abstract public function getPayload(): array;
}
