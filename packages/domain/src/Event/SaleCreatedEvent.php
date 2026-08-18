<?php

declare(strict_types=1);

namespace Caisse\Domain\Event;

use Caisse\Domain\ValueObject\Uuid;

final class SaleCreatedEvent extends DomainEvent
{
    public function __construct(
        private Uuid $saleId,
        private Uuid $storeId,
        private array $payload = []
    ) {
        parent::__construct();
    }

    public function getAggregateId(): Uuid
    {
        return $this->saleId;
    }

    public function getEventName(): string
    {
        return 'sale.created';
    }

    public function getPayload(): array
    {
        return array_merge($this->payload, [
            'sale_id' => $this->saleId->getValue(),
            'store_id' => $this->storeId->getValue(),
        ]);
    }
}
