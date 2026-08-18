<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

enum StockStatus: string
{
    case AVAILABLE = 'available';
    case OUT_OF_STOCK = 'out_of_stock';
    case LOW_STOCK = 'low_stock';
}
