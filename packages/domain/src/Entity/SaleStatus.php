<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

use Caisse\Domain\ValueObject\Uuid;
use Caisse\Domain\ValueObject\Money;

enum SaleStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
