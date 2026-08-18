<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

enum StockMovementType: string
{
    case SALE = 'sale';
    case PURCHASE = 'purchase';
    case ADJUSTMENT = 'adjustment';
    case RETURN = 'return';
    case CANCELLATION = 'cancellation';
}
