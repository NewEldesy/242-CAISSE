<?php

declare(strict_types=1);

namespace Caisse\Domain\Event;

use Caisse\Domain\ValueObject\Uuid;

final class PaymentRecordedEvent extends DomainEvent
{
    public function __construct(
        private Uuid $paymentId,
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
        return 'payment.recorded';
    }

    public function getPayload(): array
    {
        return [
            'payment_id' => $this->paymentId->getValue(),
            'sale_id' => $this->saleId->getValue(),
            'store_id' => $this->storeId->getValue(),
        ];
    }
}
