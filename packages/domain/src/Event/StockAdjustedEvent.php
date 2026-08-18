<?php

declare(strict_types=1);

namespace Caisse\Domain\Event;

use Caisse\Domain\ValueObject\Uuid;

final class StockAdjustedEvent extends DomainEvent
{
    public function __construct(
        private Uuid $productId,
        private Uuid $storeId,
        private int $quantityChange
    ) {
        parent::__construct();
    }

    public function getAggregateId(): Uuid
    {
        return $this->productId;
    }

    public function getEventName(): string
    {
        return 'stock.adjusted';
    }

    public function getPayload(): array
    {
        return [
            'product_id' => $this->productId->getValue(),
            'store_id' => $this->storeId->getValue(),
            'quantity_change' => $this->quantityChange,
        ];
    }
}
