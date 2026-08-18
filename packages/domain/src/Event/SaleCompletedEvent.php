<?php

declare(strict_types=1);

namespace Caisse\Domain\Event;

use Caisse\Domain\ValueObject\Uuid;

final class SaleCompletedEvent extends DomainEvent
{
    public function __construct(
        private Uuid $saleId,
        private Uuid $storeId
    ) {
        parent::__construct();
    }

    public function getAggregateId(): Uuid
    {
        return $this->saleId;
    }

    public function getEventName(): string
    {
        return 'sale.completed';
    }

    public function getPayload(): array
    {
        return [
            'sale_id' => $this->saleId->getValue(),
            'store_id' => $this->storeId->getValue(),
        ];
    }
}
