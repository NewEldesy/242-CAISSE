<?php

declare(strict_types=1);

namespace Caisse\Domain\Entity;

use Caisse\Domain\ValueObject\Uuid;

enum StoreStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
